<?php

namespace App\Http\Controllers;

use App\Models\ParentContact;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\AuditService;
use App\Services\FeeManagementService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminParentController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q', ''));
        $status = $request->get('status', 'active');

        $parents = ParentContact::query()
            ->when($status === 'archived', fn ($qq) => $qq->archived(), fn ($qq) => $qq->active())
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('full_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->orderBy('full_name')
            ->paginate(12)
            ->withQueryString();

        return view('auth.admin_parents_index', compact('parents', 'q', 'status'));
    }

    public function create(): View
    {
        return view('auth.admin_parents_form', ['parent' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'regex:/^09\d{9}$/', 'unique:parents,phone'],
            'phone_secondary' => ['nullable', 'regex:/^09\d{9}$/'],
            'email' => ['nullable', 'email', 'unique:parents,email', 'unique:users,email'],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_barangay' => ['nullable', 'string', 'max:255'],
            'address_city' => ['nullable', 'string', 'max:255'],
            'address_province' => ['nullable', 'string', 'max:255'],
            'address_zip' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            return DB::transaction(function () use ($request, $data) {
                $parent = ParentContact::create([
                    'full_name' => $data['full_name'],
                    'phone' => $data['phone'] ?? null,
                    'phone_secondary' => $data['phone_secondary'] ?? null,
                    'email' => $data['email'] ?? null,
                    'address_street' => $data['address_street'] ?? null,
                    'address_barangay' => $data['address_barangay'] ?? null,
                    'address_city' => $data['address_city'] ?? null,
                    'address_province' => $data['address_province'] ?? null,
                    'address_zip' => $data['address_zip'] ?? null,
                    'account_status' => 'Active',
                ]);

                try {
                    AuditService::log('Parent Created', $parent, "Created parent {$parent->full_name}", null, $parent->toArray());
                } catch (\Throwable $e) {
                }

                try {
                    $svc = app(FeeManagementService::class);
                    $svc->syncToSupabase('parents', [
                        'parent_id' => $parent->id,
                        'full_name' => $parent->full_name,
                        'phone' => $parent->phone,
                        'phone_secondary' => $request->input('phone_secondary'),
                        'email' => $parent->email,
                        'address_street' => $request->input('address_street'),
                        'address_barangay' => $request->input('address_barangay'),
                        'address_city' => $request->input('address_city'),
                        'address_province' => $request->input('address_province'),
                        'address_zip' => $request->input('address_zip'),
                        'account_status' => $parent->account_status,
                        'updated_at' => now()->toISOString(),
                    ], 'parent_id', $parent->id);
                } catch (\Throwable $e) {
                }

                $usesNewUserSchema = Schema::hasColumn('users', 'role_id')
                    && Schema::hasColumn('users', 'roleable_type')
                    && Schema::hasColumn('users', 'roleable_id');

                if ($usesNewUserSchema && ! empty($data['email'])) {
                    $role = Role::firstOrCreate(
                        ['role_name' => 'parent'],
                        ['description' => 'Parent']
                    );

                    $password = ! empty($data['password']) ? $data['password'] : \Illuminate\Support\Str::random(10);

                    $user = User::create([
                        'email' => strtolower($data['email']),
                        'password' => Hash::make($password),
                        'must_change_password' => empty($data['password']),
                        'role_id' => $role->role_id,
                        'roleable_type' => ParentContact::class,
                        'roleable_id' => (string) $parent->id,
                    ]);

                    // Send password reset email if no password was provided
                    if (empty($data['password']) && str_contains(strtolower($data['email']), '@gmail.com')) {
                        try {
                            // Create password reset token
                            $token = Password::createToken($user);
                            
                            // Generate reset URL
                            $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);
                            
                            // Send email with reset link
                            Mail::send('auth.emails.parent-account-created', [
                                'parent' => $parent,
                                'user' => $user,
                                'resetUrl' => $resetUrl,
                            ], function ($message) use ($parent, $user) {
                                $message->to($user->email, $parent->full_name)
                                    ->subject('Your E-Fees Parent Account - Set Your Password');
                            });
                            
                        } catch (\Throwable $e) {
                            // Log error but don't fail the parent creation
                            \Log::error('Failed to send parent account email: ' . $e->getMessage());
                        }
                    } elseif ($parent->phone && empty($data['password'])) {
                        // Fallback to SMS for non-Gmail emails or if email fails
                        try {
                            $message = "Your E-Fees parent account was created.\nEmail: {$user->email}\nTemp Password: {$password}";
                            app(\App\Services\SmsService::class)->send($parent->phone, $message, null);
                        } catch (\Throwable $e) {
                        }
                    }
                }

                return redirect()->route('admin.parents.index')->with('success', 'Parent created successfully. A password setup email has been sent to the Gmail address.');
            });
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to create parent: '.$e->getMessage())->withInput();
        }
    }

    public function edit(ParentContact $parent): View
    {
        return view('auth.admin_parents_form', compact('parent'));
    }

    public function update(Request $request, ParentContact $parent): RedirectResponse
    {
        $user = User::where('roleable_type', ParentContact::class)
            ->where('roleable_id', (string) $parent->id)
            ->first();

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'regex:/^09\d{9}$/', 'unique:parents,phone,'.$parent->id],
            'email' => [
                'nullable',
                'email',
                'unique:parents,email,'.$parent->id,
                Rule::unique('users', 'email')->ignore(optional($user)->user_id, 'user_id'),
            ],
            'phone_secondary' => ['nullable', 'regex:/^09\d{9}$/'],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_barangay' => ['nullable', 'string', 'max:255'],
            'address_city' => ['nullable', 'string', 'max:255'],
            'address_province' => ['nullable', 'string', 'max:255'],
            'address_zip' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            return DB::transaction(function () use ($request, $parent, $data, $user) {
                $old = $parent->getOriginal();
                $parent->update([
                    'full_name' => $data['full_name'],
                    'phone' => $data['phone'] ?? null,
                    'phone_secondary' => $data['phone_secondary'] ?? null,
                    'email' => $data['email'] ?? null,
                    'address_street' => $data['address_street'] ?? null,
                    'address_barangay' => $data['address_barangay'] ?? null,
                    'address_city' => $data['address_city'] ?? null,
                    'address_province' => $data['address_province'] ?? null,
                    'address_zip' => $data['address_zip'] ?? null,
                ]);

                try {
                    AuditService::log('Parent Updated', $parent, "Updated parent {$parent->full_name}", $old, $parent->toArray());
                } catch (\Throwable $e) {
                }

                try {
                    $svc = app(FeeManagementService::class);
                    $svc->syncToSupabase('parents', [
                        'parent_id' => $parent->id,
                        'full_name' => $parent->full_name,
                        'phone' => $parent->phone,
                        'phone_secondary' => $request->input('phone_secondary'),
                        'email' => $parent->email,
                        'address_street' => $request->input('address_street'),
                        'address_barangay' => $request->input('address_barangay'),
                        'address_city' => $request->input('address_city'),
                        'address_province' => $request->input('address_province'),
                        'address_zip' => $request->input('address_zip'),
                        'account_status' => $parent->account_status,
                        'updated_at' => now()->toISOString(),
                    ], 'parent_id', $parent->id);
                } catch (\Throwable $e) {
                }

                $usesNewUserSchema = Schema::hasColumn('users', 'role_id')
                    && Schema::hasColumn('users', 'roleable_type')
                    && Schema::hasColumn('users', 'roleable_id');

                if ($usesNewUserSchema) {
                    $user = User::where('roleable_type', ParentContact::class)
                        ->where('roleable_id', (string) $parent->id)
                        ->first();

                    if ($user) {
                        if (! empty($data['email'])) {
                            $user->email = strtolower($data['email']);
                        }
                        if (! empty($data['password'])) {
                            $user->password = Hash::make($data['password']);
                        }
                        $user->save();
                    } elseif (! empty($data['email']) && ! empty($data['password'])) {
                        $role = Role::firstOrCreate(
                            ['role_name' => 'parent'],
                            ['description' => 'Parent']
                        );
                        User::create([
                            'email' => strtolower($data['email']),
                            'password' => Hash::make($data['password']),
                            'role_id' => $role->role_id,
                            'roleable_type' => ParentContact::class,
                            'roleable_id' => (string) $parent->id,
                        ]);
                    }
                }

                return redirect()->route('admin.parents.index')->with('success', 'Parent updated.');
            });
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to update parent: '.$e->getMessage())->withInput();
        }
    }

    public function destroy(ParentContact $parent): RedirectResponse
    {
        try {
            DB::transaction(function () use ($parent) {
                $user = User::where('roleable_type', ParentContact::class)
                    ->where('roleable_id', (string) $parent->id)
                    ->first();

                if ($user) {
                    $user->delete();
                }

                $parent->delete();

                AuditService::log('Parent Deleted', $parent, "Deleted parent {$parent->full_name}", null, null);
            });

            return redirect()->route('admin.parents.index')->with('success', 'Parent deleted.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Failed to delete parent: '.$e->getMessage());
        }
    }

    public function archive(ParentContact $parent): RedirectResponse
    {
        $parent->update(['account_status' => 'Archived', 'archived_at' => now()]);

        try {
            AuditService::log('Parent Archived', $parent, "Archived parent {$parent->full_name}", null, $parent->toArray());
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.parents.index', ['status' => 'archived'])->with('success', 'Parent archived.');
    }

    public function unarchive(ParentContact $parent): RedirectResponse
    {
        $parent->update(['account_status' => 'Active', 'archived_at' => null]);

        try {
            AuditService::log('Parent Unarchived', $parent, "Unarchived parent {$parent->full_name}", null, $parent->toArray());
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.parents.index')->with('success', 'Parent unarchived.');
    }

    public function link(Request $request, ParentContact $parent): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'string', 'exists:students,student_id'],
            'relationship' => ['required', 'string', 'max:50'],
            'is_primary' => ['nullable', 'boolean'],
        ]);

        $student = Student::where('student_id', $data['student_id'])->first();
        $parent->students()->syncWithoutDetaching([
            $student->student_id => [
                'relationship' => $data['relationship'],
                'is_primary' => (bool) ($data['is_primary'] ?? false),
            ],
        ]);

        try {
            AuditService::log('Parent Linked', $parent, "Linked {$parent->full_name} to {$student->full_name}", null, ['student_id' => $student->student_id, 'relationship' => $data['relationship']]);
        } catch (\Throwable $e) {
        }

        return back()->with('success', 'Linked parent to student.');
    }

    public function unlink(Request $request, ParentContact $parent): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'string', 'exists:students,student_id'],
        ]);

        $student = Student::where('student_id', $data['student_id'])->first();
        $activeYear = SystemSetting::getActiveSchoolYear();
        if ($student && $activeYear && $student->school_year !== $activeYear) {
            return back()->with('error', 'Cannot unlink parent from a student in a locked School Year.');
        }

        $parent->students()->detach($data['student_id']);

        try {
            AuditService::log('Parent Unlinked', $parent, "Unlinked {$parent->full_name} from student {$data['student_id']}", null, ['student_id' => $data['student_id']]);
        } catch (\Throwable $e) {
        }

        return back()->with('success', 'Unlinked parent from student.');
    }

    public function toggleStatus(ParentContact $parent): RedirectResponse
    {
        $oldStatus = $parent->account_status;
        $newStatus = $oldStatus === 'Active' ? 'Inactive' : 'Active';

        $parent->update(['account_status' => $newStatus]);

        try {
            AuditService::log('Parent Status Changed', $parent, "Changed status from {$oldStatus} to {$newStatus}", ['status' => $oldStatus], ['status' => $newStatus]);
        } catch (\Throwable $e) {
        }

        return back()->with('success', "Parent status updated to {$newStatus}.");
    }

    public function resetPassword(ParentContact $parent): RedirectResponse
    {
        $user = User::where('roleable_type', ParentContact::class)
            ->where('roleable_id', $parent->id)
            ->first();

        if (! $user) {
            return back()->with('error', 'No user account associated with this parent.');
        }

        $newPassword = \Illuminate\Support\Str::random(10);
        $user->update(['password' => Hash::make($newPassword)]);

        try {
            AuditService::log('Parent Password Reset', $parent, "Reset password for parent {$parent->full_name}", null, null);

            // Send SMS if phone is available
            if ($parent->phone) {
                $msg = \App\Services\SmsTemplates::getPasswordResetMessage($newPassword);
                app(\App\Services\SmsService::class)->send($parent->phone, $msg);
            }
        } catch (\Throwable $e) {
        }

        return back()->with('success', "Password reset successfully. New password: {$newPassword}");
    }
}
