<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\FeeRecord;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
    /**
     * Display the staff dashboard with search and metrics.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('staff')) {
            abort(403, 'Unauthorized');
        }

        $q = trim((string) $request->input('q', ''));

        // Paginated students for table (with fee records for totals/status)
        $students = Student::with('feeRecords')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('student_id', 'like', "%{$q}%")
                        ->orWhere('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('level', 'like', "%{$q}%");
                });
            })
            ->orderBy('last_name')
            ->paginate(10)
            ->withQueryString();

        // Metrics for chart: paid vs unpaid students
        $all = Student::with('feeRecords')->get();
        $paidCount = 0;
        $unpaidCount = 0;
        foreach ($all as $s) {
            $totalBalance = (float) $s->feeRecords->sum('balance');
            if ($s->feeRecords->count() > 0 && $totalBalance <= 0) {
                $paidCount++;
            } elseif ($totalBalance > 0) {
                $unpaidCount++;
            }
        }

        return view('auth.staff_dashboard', [
            'students' => $students,
            'query' => $q,
            'paidCount' => $paidCount,
            'unpaidCount' => $unpaidCount,
        ]);
    }

    /**
     * Ping/remind a student about their fees (logs an action for now).
     */
    public function remind(Student $student): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('staff')) {
            abort(403, 'Unauthorized');
        }

        // Find the application's user row for this student
        $targetUser = User::where('roleable_type', 'App\\Models\\Student')
            ->where('roleable_id', $student->student_id)
            ->first();

        if (!$targetUser) {
            Log::warning('Reminder failed: no user mapped to student', [
                'student_id' => $student->student_id,
            ]);
            return back()->with('error', 'No user account found for this student.');
        }

        // Insert a realtime notification row (Supabase listens to inserts)
        try {
            DB::table('notifications')->insert([
                'user_id' => $targetUser->user_id,
                'title' => 'Fee Reminder',
                'body' => 'Hello ' . $student->first_name . ', please review your outstanding fees.',
                'created_at' => now(),
            ]);

            Log::info('Fee reminder sent', [
                'staff_user_id' => $user->user_id ?? null,
                'target_user_id' => $targetUser->user_id,
                'student_id' => $student->student_id,
                'timestamp' => now()->toDateTimeString(),
            ]);

            return back()->with('success', 'Reminder sent to ' . $student->full_name . '.');
        } catch (\Throwable $e) {
            Log::error('Failed to create notification', [
                'error' => $e->getMessage(),
            ]);
            return back()->with('error', 'Failed to send reminder.');
        }
    }

    /**
     * Approve payments for a student: mark all outstanding fee records as paid with zero balance.
     */
    public function approve(Student $student): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || !$user->hasRole('staff')) {
            abort(403, 'Unauthorized');
        }

        // Collect outstanding records first to compute total cleared amount
        $outstanding = FeeRecord::where('student_id', $student->student_id)
            ->where(function ($q) {
                $q->where('status', '!=', 'paid')
                  ->orWhereNull('status')
                  ->orWhere('balance', '>', 0);
            })
            ->get();

        $totalCleared = 0.0;
        foreach ($outstanding as $rec) {
            $val = is_numeric($rec->balance) ? (float) $rec->balance : 0.0;
            $totalCleared += $val;
        }

        $affected = 0;
        if ($outstanding->count() > 0) {
            $affected = FeeRecord::where('student_id', $student->student_id)
                ->where(function ($q) {
                    $q->where('status', '!=', 'paid')
                      ->orWhereNull('status')
                      ->orWhere('balance', '>', 0);
                })
                ->update(['status' => 'paid', 'balance' => 0]);
        }

        if ($affected > 0) {
            // Map student to user for transaction and notification
            $targetUser = User::where('roleable_type', 'App\\Models\\Student')
                ->where('roleable_id', $student->student_id)
                ->first();

            if ($targetUser) {
                // Best-effort: record a transaction and notify the student
                try {
                    DB::table('payment_transactions')->insert([
                        'user_id' => $targetUser->user_id,
                        'student_id' => $student->student_id,
                        'amount' => $totalCleared,
                        'type' => 'approval',
                        'note' => 'Payments approved by staff',
                        'staff_user_id' => $user->user_id ?? null,
                        'created_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('payment_transactions insert failed', ['error' => $e->getMessage()]);
                }

                try {
                    DB::table('notifications')->insert([
                        'user_id' => $targetUser->user_id,
                        'title' => 'Payment Approved',
                        'body' => 'Hi ' . $student->first_name . ', your payments have been recorded as paid.',
                        'created_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('notification insert failed (approval)', ['error' => $e->getMessage()]);
                }
            }

            return back()->with('success', 'Approved payments for ' . $student->full_name . '.');
        }
        return back()->with('info', $student->full_name . ' has no outstanding fees.');
    }
}
