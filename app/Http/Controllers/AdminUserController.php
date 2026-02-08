<?php

namespace App\Http\Controllers;

use App\Models\ParentContact;
use App\Models\Role;
use App\Models\Staff;
use App\Models\Student;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display a listing of all users.
     */
    public function index(Request $request): View
    {
        $query = trim($request->input('q', ''));
        $roleFilter = $request->input('role', 'all');

        $usersQuery = User::with(['role', 'roleable'])
            ->whereHas('role', function ($q) {
                $q->whereIn('role_name', ['admin', 'staff', 'parent']);
            })
            ->when($query !== '', function ($q) use ($query) {
                $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
                $q->where('email', $operator, "%{$query}%")
                    ->orWhereHas('roleable', function ($sq) use ($query, $operator) {
                        // Dynamically search based on roleable type
                        $sq->where('first_name', $operator, "%{$query}%")
                            ->orWhere('last_name', $operator, "%{$query}%");
                    });
            })
            ->when($roleFilter !== 'all', function ($q) use ($roleFilter) {
                $q->whereHas('role', function ($rq) use ($roleFilter) {
                    $rq->where('role_name', $roleFilter);
                });
            })
            ->orderBy('user_id', 'desc');

        $users = $usersQuery->paginate(15)->withQueryString();
        $roles = Role::whereIn('role_name', ['admin', 'staff', 'parent'])->get();

        return view('auth.admin_users_index', [
            'users' => $users,
            'query' => $query,
            'roleFilter' => $roleFilter,
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new user (Admin/Staff only).
     */
    public function create(): View
    {
        $roles = Role::whereIn('role_name', ['staff', 'admin', 'parent'])->get();

        return view('auth.admin_users_create', ['roles' => $roles]);
    }

    /**
     * Store a newly created user (Admin/Staff only).
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'min:7', 'max:15'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            ],
            'role_name' => ['required', 'string', 'in:staff,admin,parent'],
        ];

        // Conditional validation rules
        if ($request->input('role_name') === 'parent') {
            $rules['full_name'] = ['required', 'string', 'max:100'];
        } else {
            $rules['first_name'] = ['required', 'string', 'max:100'];
            $rules['last_name'] = ['required', 'string', 'max:100'];
            $rules['middle_initial'] = ['nullable', 'string', 'max:1'];
        }

        $validator = Validator::make($request->all(), $rules, [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'email.unique' => 'This email address is already registered.',
            'full_name.required' => 'Parent Name is required.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        try {
            DB::transaction(function () use ($validated) {
                $roleName = strtolower($validated['role_name']);
                $role = Role::firstOrCreate(
                    ['role_name' => $roleName],
                    ['description' => ucfirst($roleName)]
                );

                if ($roleName === 'parent') {
                    // Create ParentContact
                    $parent = ParentContact::create([
                        'full_name' => $validated['full_name'],
                        'email' => strtolower($validated['email']),
                        'phone' => $validated['phone_number'] ?? null,
                        'account_status' => 'Active',
                    ]);

                    // Create User linked to ParentContact
                    User::create([
                        'email' => strtolower($validated['email']),
                        'password' => Hash::make($validated['password']),
                        'role_id' => $role->role_id,
                        'roleable_type' => ParentContact::class,
                        'roleable_id' => $parent->id,
                    ]);

                    $auditSubject = $parent;
                } else {
                    // Create Staff
                    $position = $roleName === 'admin' ? 'Admin' : 'Staff';

                    // Use Staff::createWithAccount to create Staff and User records
                    $createdStaff = Staff::createWithAccount([
                        'first_name' => $validated['first_name'],
                        'MI' => $validated['middle_initial'] ?? null,
                        'last_name' => $validated['last_name'],
                        'contact_number' => $validated['phone_number'] ?? '',
                        'department' => 'General',
                        'position' => $position,
                        'is_active' => true,
                    ], [
                        'email' => strtolower($validated['email']),
                        'password' => $validated['password'], // createWithAccount hashes this
                        'role_id' => $role->role_id,
                    ]);

                    $auditSubject = $createdStaff;
                }

                // Audit Log
                // try {
                AuditService::log(
                    'User Created',
                    $auditSubject,
                    "Created {$roleName} user: {$validated['email']}",
                    null,
                    $auditSubject->toArray()
                );
                // } catch (\Throwable $e) {
                // }
            });

            return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create user: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        $user->load(['role', 'roleable']);

        return view('auth.admin_users_show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        $user->load(['role', 'roleable']);
        $roles = Role::whereIn('role_name', ['staff', 'admin'])->get();

        return view('auth.admin_users_edit', ['user' => $user, 'roles' => $roles]);
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $rules = [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->user_id.',user_id'],
            'phone_number' => ['nullable', 'string', 'min:7', 'max:15'],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            ],
        ];

        // Conditional validation based on user type
        if ($user->roleable_type === Staff::class || $user->roleable_type === Student::class) {
            $rules['first_name'] = ['required', 'string', 'max:100'];
            $rules['last_name'] = ['required', 'string', 'max:100'];
            $rules['middle_initial'] = ['nullable', 'string', 'max:1'];
        } elseif ($user->roleable_type === ParentContact::class) {
            $rules['full_name'] = ['required', 'string', 'max:100'];
        }

        $validator = Validator::make($request->all(), $rules, [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        try {
            DB::transaction(function () use ($validated, $user) {
                // Update User email/password
                $userData = [
                    'email' => strtolower($validated['email']),
                ];
                if (! empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }
                $user->update($userData);

                // Update Roleable details
                if ($user->roleable) {
                    if ($user->roleable_type === Staff::class) {
                        $user->roleable->update([
                            'first_name' => $validated['first_name'],
                            'last_name' => $validated['last_name'],
                            'MI' => $validated['middle_initial'] ?? null,
                            'contact_number' => $validated['phone_number'] ?? null,
                        ]);
                    } elseif ($user->roleable_type === Student::class) {
                        $user->roleable->update([
                            'first_name' => $validated['first_name'],
                            'last_name' => $validated['last_name'],
                            'middle_initial' => $validated['middle_initial'] ?? null,
                        ]);
                    } elseif ($user->roleable_type === ParentContact::class) {
                        $user->roleable->update([
                            'full_name' => $validated['full_name'],
                            'phone' => $validated['phone_number'] ?? null,
                        ]);
                    }
                }

                AuditService::log('User Updated', $user, "Updated user {$validated['email']}");
            });

            return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update user: '.$e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            DB::transaction(function () use ($user) {
                // Delete roleable if exists
                if ($user->roleable) {
                    if ($user->roleable_type === Staff::class) {
                        $user->roleable->delete();
                    } elseif ($user->roleable_type === ParentContact::class) {
                        $user->roleable->delete();
                    } elseif ($user->roleable_type === Student::class) {
                        // For students, we might want to be careful.
                        // If we delete the user, the student record might still be needed for historical data.
                        // But if this is a "delete user" action, let's assume it removes access.
                        // The requirement doesn't specify soft deletes for students, so let's keep the student record
                        // but remove the user account association? Or delete both?
                        // Let's assume we delete the user account but KEEP the student record for academic history,
                        // unless the student record was created *only* for the user.
                        // Actually, usually Student record is primary. Let's NOT delete the student record, just the user.
                        // However, for consistency with Staff, maybe we should?
                        // Let's opt to NOT delete the Student/Parent record itself if it has other dependencies,
                        // but since we are replacing "Parent Management" and "Staff Management",
                        // for Staff we delete. For Student, it's safer to keep the record and just remove login access.

                        // BUT, if the admin explicitly clicks "Delete User", they expect the user to be gone.
                        // If the User model is the authentication, deleting User is enough to revoke access.
                        // If we delete the Student model, we lose enrollment history.
                        // DECISION: Only delete Staff and ParentGuardian roleables. Keep Student records.
                    }
                }
                $user->delete();

                AuditService::log('User Deleted', $user, "Deleted user {$user->email}");
            });

            return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete user: '.$e->getMessage());
        }
    }
}
