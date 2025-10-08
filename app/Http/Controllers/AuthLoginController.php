<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Models\Student;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\Role;
use App\Models\FeeRecord;

class AuthLoginController extends Controller
{
    /**
     * Display the login form.
     */
    public function login(): View
    {
        return view('auth.login');
    }
    
    /**
     * Display the signup form.
     */
    public function signup(): View
    {
        return view('auth.signup');
    }

    /**
     * Display the user dashboard.
     */
    public function user_dashboard(): View
    {
        $user = Auth::user();

        $studentId = null;
        if ($user && ($user->roleable_type ?? null) === 'App\\Models\\Student') {
            $studentId = $user->roleable_id;
        }

        $upcomingFees = collect();
        $paidFees = collect();
        $transactions = collect();
        $notifications = collect();
        $balanceDue = 0.0;
        $totalPaid = 0.0;

        if ($studentId) {
            // Get upcoming/unpaid fees
            $upcomingFees = FeeRecord::where('student_id', $studentId)
                ->where(function ($q) {
                    $q->where('status', '!=', 'paid')
                      ->orWhereNull('status')
                      ->orWhere('balance', '>', 0);
                })
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            // Calculate total balance due
            $balanceDue = $upcomingFees->reduce(function ($carry, $item) {
                $val = is_numeric($item->balance) ? (float) $item->balance : 0.0;
                return $carry + $val;
            }, 0.0);

            // Get paid fees for current student
            $paidFees = FeeRecord::where('student_id', $studentId)
                ->where('status', 'paid')
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get();

            // Calculate total paid amount
            $totalPaid = $paidFees->reduce(function ($carry, $item) {
                $val = is_numeric($item->balance) ? (float) $item->balance : 0.0;
                return $carry + $val;
            }, 0.0);
        }

        if ($user) {
            // Get notifications for the user
            $notifications = DB::table('notifications')
                ->where('user_id', $user->user_id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // Get recent transactions (payment history)
            try {
                $transactions = DB::table('payment_transactions')
                    ->where('user_id', $user->user_id)
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            } catch (\Throwable $e) {
                // Table might not exist yet; leave empty silently
                $transactions = collect();
            }
        }

        return view('auth.user_dashboard', [
            'upcomingFees' => $upcomingFees,
            'paidFees' => $paidFees,
            'transactions' => $transactions,
            'notifications' => $notifications,
            'balanceDue' => $balanceDue,
            'totalPaid' => $totalPaid,
        ]);
    }

    /**
     * Display the admin dashboard.
     */
    public function admin_dashboard(): View
    {
        return view('auth.admin_dashboard');
    }

    /**
     * Display the staff dashboard.
     */
    public function staff_dashboard(): View
    {
        return view('auth.staff_dashboard');
    }

    /**
     * Handle the login request.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Normalize email to lowercase for case-insensitive auth
        $credentials['email'] = strtolower($credentials['email']);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Role-based redirect using new role system
            $user = Auth::user();
            $roleName = $user->role->role_name;
            
            switch ($roleName) {
                case 'admin':
                    return redirect()->intended('admin_dashboard');
                case 'staff':
                    return redirect()->intended('staff_dashboard');
                case 'student':
                default:
                    return redirect()->intended('user_dashboard');
            }
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle the signup request.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:1'],
            'last_name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:20'],
            'sex' => ['required', 'string', 'in:Male,Female'],
            'level' => ['required', 'string', 'max:255'],
            'section' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Generate a unique student ID
        $studentId = $this->generateUniqueStudentId();

        // Create new student record
        $student = Student::create([
            'student_id' => $studentId,
            'first_name' => $validated['first_name'],
            'middle_initial' => $validated['middle_initial'] ?? null,
            'last_name' => $validated['last_name'],
            'contact_number' => $validated['contact_number'],
            'sex' => $validated['sex'],
            'level' => $validated['level'],
            'section' => $validated['section'],
        ]);

        // Get the student role
        $studentRole = Role::where('role_name', 'student')->first();

        // Create user record linked to the new student
        $user = User::create([
            // Store emails in lowercase to match DB uniqueness
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role_id' => $studentRole->role_id,
            'roleable_type' => 'App\\Models\\Student',
            'roleable_id' => $student->student_id,
        ]);

        return redirect()->route('login')->with('success', 'Account created successfully! Please log in.');
    }

    /**
     * Generate a unique student ID.
     */
    private function generateUniqueStudentId(): string
    {
        do {
            // Generate student ID using timestamp and random string
            $studentId = 'STU' . date('Y') . strtoupper(substr(md5(uniqid()), 0, 8));
        } while (Student::where('student_id', $studentId)->exists());

        return $studentId;
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
