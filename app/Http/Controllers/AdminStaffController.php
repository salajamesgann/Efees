<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
=======
use App\Models\Staff;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AdminStaffController extends Controller
{
    /**
     * Display a listing of all staff accounts.
     */
    public function index(Request $request): View
    {
        $query = trim($request->input('q', ''));
        $status = $request->input('status', 'all');

<<<<<<< HEAD
        $staffQuery = Staff::with(['user', 'user.role'])
            ->select(['staff_id', 'first_name', 'MI', 'last_name', 'contact_number', 'department', 'position', 'is_active', 'created_at'])
=======
        $staffQuery = Staff::with('user')
            ->select(['staff_id', 'first_name', 'MI', 'last_name', 'contact_number', 'department', 'position', 'salary'])
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%")
                        ->orWhere('staff_id', 'like', "%{$query}%")
                        ->orWhere('position', 'like', "%{$query}%")
                        ->orWhere('department', 'like', "%{$query}%");
                });
            })
            ->when($status === 'active', function ($q) {
<<<<<<< HEAD
                $q->where('is_active', true);
            })
            ->when($status === 'inactive', function ($q) {
                $q->where('is_active', false);
            })
            ->orderBy('created_at', 'desc')
=======
                // Note: is_active column may not exist, filtering handled in view
            })
            ->when($status === 'inactive', function ($q) {
                // Note: is_active column may not exist, filtering handled in view
            })
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
            ->orderBy('first_name', 'asc')
            ->orderBy('last_name', 'asc');

        // Use cursor pagination or manual pagination to avoid created_at dependency
        $staff = $staffQuery->paginate(15);

        return view('auth.admin_staff_index', [
            'staff' => $staff,
            'query' => $query,
            'status' => $status,
        ]);
    }

    /**
     * Show the form for creating a new staff account.
     */
<<<<<<< HEAD
    /**
     * Display the specified staff member's details.
     */
    public function show(Staff $staff): View
    {
        $staff->load('user');

        return view('auth.admin_staff_show', compact('staff'));
    }

    /**
     * Show the form for creating a new staff account.
     */
=======
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
    public function create(): View
    {
        $roles = Role::whereIn('role_name', ['staff', 'admin'])->get();

        return view('auth.admin_staff_create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created staff account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:100'],
            'middle_initial' => ['nullable', 'string', 'max:1'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['nullable', 'string', 'min:7', 'max:15'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
<<<<<<< HEAD
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
=======
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'email.unique' => 'This email address is already registered.',
        ]);

        if ($validator->fails()) {
<<<<<<< HEAD
            if ($request->boolean('from_modal')) {
                return redirect()
                    ->route('admin.students.index')
                    ->withErrors($validator)
                    ->with('error', 'Staff/Admin creation failed. Please review the errors below.')
                    ->withInput();
            }

=======
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
            return redirect()
                ->route('admin.staff.create')
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        try {
<<<<<<< HEAD
            DB::transaction(function () use ($validated, $request) {
                $roleName = strtolower($request->input('role_name', 'staff'));
                if (! in_array($roleName, ['staff', 'admin'], true)) {
                    $roleName = 'staff';
                }
                $role = Role::firstOrCreate(
                    ['role_name' => $roleName],
                    ['description' => ucfirst($roleName)]
                );
                $position = $roleName === 'admin' ? 'Admin' : 'Staff';
                $createdStaff = Staff::createWithAccount([
                    'first_name' => $validated['first_name'],
                    'MI' => $validated['middle_initial'] ?? null,
                    'last_name' => $validated['last_name'],
                    'contact_number' => $validated['phone_number'] ?? '',
                    'department' => 'General',
                    'position' => $position,
=======
            DB::transaction(function () use ($validated) {
                Staff::createWithAccount([
                    'first_name' => $validated['first_name'],
                    'MI' => $validated['middle_initial'] ?? null,
                    'last_name' => $validated['last_name'],
                    'contact_number' => $validated['phone_number'] ?? null,
                    'department' => null, // Default to null since not in form
                    'position' => 'Staff', // Default position
                    'salary' => null, // Default to null since not in form
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
                    'is_active' => true,
                ], [
                    'email' => strtolower($validated['email']),
                    'password' => $validated['password'],
<<<<<<< HEAD
                    'role_id' => $role->role_id,
                ]);

                // Audit Log
                try {
                    AuditService::log(
                        'Staff Account Created',
                        $createdStaff,
                        "Created staff account: {$createdStaff->first_name} {$createdStaff->last_name}",
                        null,
                        $createdStaff->toArray()
                    );
                } catch (\Throwable $e) {
                }
            });

            if ($request->boolean('from_modal')) {
                return redirect()
                    ->route('admin.students.index')
                    ->with('success', 'Account created successfully.');
            }

=======
                    'role_id' => 3, // Staff role (ID 3 based on migration order)
                ]);
            });

>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
            return redirect()
                ->route('admin.staff.index')
                ->with('success', 'Staff account created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.staff.create')
<<<<<<< HEAD
                ->with('error', 'Failed to create staff account. Please try again. Error: '.$e->getMessage())
=======
                ->with('error', 'Failed to create staff account. Please try again. Error: ' . $e->getMessage())
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
                ->withInput();
        }
    }

    /**
     * Show the form for editing the specified staff account.
     */
    public function edit(Staff $staff): View
    {
        $staff->load('user');
        $roles = Role::whereIn('role_name', ['staff', 'admin'])->get();

        return view('auth.admin_staff_edit', [
            'staff' => $staff,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified staff account.
     */
    public function update(Request $request, Staff $staff): RedirectResponse
    {
        $staff->load('user');
<<<<<<< HEAD
        $oldValues = $staff->toArray();
=======
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:100'],
            'middle_initial' => ['nullable', 'string', 'max:1'],
            'last_name' => ['required', 'string', 'max:100'],
<<<<<<< HEAD
            'phone_number' => ['nullable', 'string', 'min:11', 'max:11'],
=======
            'phone_number' => ['nullable', 'string', 'min:7', 'max:15'],
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
<<<<<<< HEAD
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
=======
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if ($validator->fails()) {
<<<<<<< HEAD
            if ($request->boolean('from_modal')) {
                return redirect()
                    ->route('admin.students.index')
                    ->withErrors($validator)
                    ->with('error', 'Staff update failed. Please review the errors below.')
                    ->withInput();
            }

=======
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
            return redirect()
                ->route('admin.staff.edit', $staff)
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        try {
            $userData = [
                'email' => strtolower($validated['email']),
            ];

<<<<<<< HEAD
            if (! empty($validated['password'])) {
=======
            if (!empty($validated['password'])) {
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
                $userData['password'] = $validated['password'];
            }

            $staff->updateWithAccount([
                'first_name' => $validated['first_name'],
                'MI' => $validated['middle_initial'] ?? null,
                'last_name' => $validated['last_name'],
                'contact_number' => $validated['phone_number'] ?? null,
                // Note: department, position, salary not included in simplified edit form
            ], $userData);

<<<<<<< HEAD
            // Audit Log
            try {
                AuditService::log(
                    'Staff Account Updated',
                    $staff,
                    "Updated staff account: {$staff->first_name} {$staff->last_name}",
                    $oldValues,
                    $staff->toArray()
                );
            } catch (\Throwable $e) {
            }

            if ($request->boolean('from_modal')) {
                return redirect()
                    ->route('admin.students.index')
                    ->with('success', 'Staff account updated successfully.');
            }

=======
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
            return redirect()
                ->route('admin.staff.index')
                ->with('success', 'Staff account updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.staff.edit', $staff)
                ->with('error', 'Failed to update staff account. Please try again.')
                ->withInput();
        }
    }

    /**
<<<<<<< HEAD
     * Reset staff password.
     */
    /**
     * Toggle staff account status.
     */
    /**
=======
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
     * Toggle staff account status.
     */
    public function toggleStatus(Staff $staff): RedirectResponse
    {
<<<<<<< HEAD
        $oldStatus = $staff->is_active ? 'Active' : 'Inactive';
        try {
            DB::beginTransaction();

            // Toggle the is_active status
            $staff->update([
                'is_active' => ! $staff->is_active,
            ]);

            DB::commit();

            $status = $staff->is_active ? 'activated' : 'deactivated';

            // Audit Log
            try {
                AuditService::log(
                    'Staff Account Status Changed',
                    $staff,
                    "Changed status for staff: {$staff->first_name} {$staff->last_name} to {$status}",
                    ['is_active' => $oldStatus],
                    ['is_active' => $staff->is_active ? 'Active' : 'Inactive']
                );
            } catch (\Throwable $e) {
            }

            return back()->with('success', "Staff account has been {$status} successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to toggle staff status', [
                'staff_id' => $staff->staff_id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to update staff account status.');
=======
        try {
            // Since is_active column may not exist, we'll handle this in the application layer
            // For now, just return success since we can't actually toggle database status
            $message = 'Status toggle functionality requires database migration.';

            return redirect()
                ->route('admin.staff.index')
                ->with('info', $message);
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.staff.index')
                ->with('error', 'Failed to update staff account status.');
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
        }
    }

    /**
<<<<<<< HEAD
     * Activate a staff account.
     */
    public function activate(Staff $staff): RedirectResponse
    {
        $oldStatus = $staff->is_active ? 'Active' : 'Inactive';
        try {
            DB::beginTransaction();

            // Activate the staff account
            $staff->update(['is_active' => true]);

            DB::commit();

            // Audit Log
            try {
                AuditService::log(
                    'Staff Account Activated',
                    $staff,
                    "Activated staff account: {$staff->first_name} {$staff->last_name}",
                    ['is_active' => $oldStatus],
                    ['is_active' => 'Active']
                );
            } catch (\Throwable $e) {
            }

            return back()->with('success', 'Staff account has been activated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to activate staff account', [
                'staff_id' => $staff->staff_id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to activate staff account.');
        }
    }

    /**
     * Reset staff password.
     */
    public function resetPassword(Staff $staff): RedirectResponse
    {
        try {
            // Generate a random password
            $newPassword = 'Staff@'.rand(1000, 9999);

            // Update the user password
            if ($staff->user) {
                $staff->user->update([
                    'password' => Hash::make($newPassword),
                ]);

                // Log the password reset
                Log::info('Staff password reset', [
                    'staff_id' => $staff->staff_id,
                    'admin_id' => Auth::user()->user_id ?? null,
                ]);

                // Audit Log
                try {
                    AuditService::log(
                        'Staff Password Reset',
                        $staff,
                        "Reset password for staff: {$staff->first_name} {$staff->last_name}",
                        null,
                        null
                    );
                } catch (\Throwable $e) {
                }

                return redirect()
                    ->route('admin.staff.show', $staff)
                    ->with('success', "Password reset successfully. New password: {$newPassword}");
            }

            return redirect()
                ->route('admin.staff.show', $staff)
                ->with('error', 'Staff account not found.');
        } catch (\Exception $e) {
            Log::error('Staff password reset failed', [
                'error' => $e->getMessage(),
                'staff_id' => $staff->staff_id,
            ]);

            return redirect()
                ->route('admin.staff.show', $staff)
                ->with('error', 'Failed to reset password. Please try again.');
        }
=======
     * Show staff account details with confirmation for actions.
     */
    public function show(Staff $staff): View
    {
        $staff->load('user');

        return view('auth.admin_staff_show', [
            'staff' => $staff,
        ]);
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
    }
}
