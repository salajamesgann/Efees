<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Admin;
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
        $statusFilter = $request->input('status', 'all');

        $usersQuery = User::with(['role', 'roleable'])
            ->whereHas('role', function ($q) {
                $q->whereIn('role_name', ['admin', 'staff', 'parent']);
            })
            ->when($query !== '', function ($q) use ($query) {
                $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
                $q->where(function ($sub) use ($query, $operator) {
                    $sub->where('email', $operator, "%{$query}%")
                        ->orWhere(function ($sq) use ($query, $operator) {
                            $sq->where(function ($sq1) use ($query, $operator) {
                                $sq1->whereIn('roleable_type', [Staff::class, Admin::class, Student::class])
                                    ->whereHasMorph('roleable', [Staff::class, Admin::class, Student::class], function ($q) use ($query, $operator) {
                                        $q->where('first_name', $operator, "%{$query}%")
                                            ->orWhere('last_name', $operator, "%{$query}%");
                                    });
                            })->orWhere(function ($sq2) use ($query, $operator) {
                                $sq2->where('roleable_type', ParentContact::class)
                                    ->whereExists(function ($exists) use ($query, $operator) {
                                        $exists->select(DB::raw(1))
                                            ->from('parents')
                                            ->where(function ($q) {
                                                if (DB::connection()->getDriverName() === 'pgsql') {
                                                    $q->whereRaw('CAST(parents.id AS VARCHAR) = users.roleable_id');
                                                } else {
                                                    $q->whereColumn('parents.id', 'users.roleable_id');
                                                }
                                            })
                                            ->where('full_name', $operator, "%{$query}%");
                                    });
                            });
                        });
                });
            })
            ->when($roleFilter !== 'all', function ($q) use ($roleFilter) {
                $q->whereHas('role', function ($rq) use ($roleFilter) {
                    $rq->where('role_name', $roleFilter);
                });
            })
            ->when($statusFilter !== 'all', function ($q) use ($statusFilter) {
                $q->where(function ($sub) use ($statusFilter) {
                    $isActive = $statusFilter === 'active';
                    $sub->where(function ($s) use ($isActive) {
                        $s->where('roleable_type', Staff::class)
                            ->whereExists(function ($exists) use ($isActive) {
                                $exists->select(DB::raw(1))
                                    ->from('staff')
                                    ->whereColumn('staff.staff_id', 'users.roleable_id')
                                    ->where('is_active', $isActive);
                            });
                    })->orWhere(function ($p) use ($isActive) {
                        $p->where('roleable_type', ParentContact::class)
                            ->whereExists(function ($exists) use ($isActive) {
                                $exists->select(DB::raw(1))
                                    ->from('parents')
                                    ->where(function ($q) {
                                        if (DB::connection()->getDriverName() === 'pgsql') {
                                            $q->whereRaw('CAST(parents.id AS VARCHAR) = users.roleable_id');
                                        } else {
                                            $q->whereColumn('parents.id', 'users.roleable_id');
                                        }
                                    })
                                    ->where(function ($w) use ($isActive) {
                                        if ($isActive) {
                                            $w->where('account_status', 'Active');
                                        } else {
                                            $w->where('account_status', '!=', 'Active');
                                        }
                                    });
                            });
                    });
                    // Admin-type users are always considered active (no is_active field)
                    if ($isActive) {
                        $sub->orWhere('roleable_type', Admin::class);
                    }
                });
            })
            ->orderBy('user_id', 'desc');

        $users = $usersQuery->paginate(15)->withQueryString();
        $roles = Role::whereIn('role_name', ['admin', 'staff', 'parent'])->get();

        return view('auth.admin_users_index', [
            'users' => $users,
            'query' => $query,
            'roleFilter' => $roleFilter,
            'statusFilter' => $statusFilter,
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
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated) {
                $roleName = strtolower($validated['role_name']);
                $role = Role::firstOrCreate(
                    ['role_name' => $roleName],
                    ['description' => ucfirst($roleName)]
                );

                if ($roleName === 'parent') {
                    $email = strtolower($validated['email']);

                    // Check if parent already exists by email
                    $parent = ParentContact::where('email', $email)->first();

                    $fullName = trim($validated['first_name'].' '.($validated['middle_initial'] ?? '').' '.$validated['last_name']);
                    $fullName = trim(str_replace('  ', ' ', $fullName));

                    $parentData = [
                        'full_name' => $fullName,
                        'email' => $email,
                        'phone' => $validated['phone_number'] ?? null,
                        'account_status' => 'Active',
                    ];

                    if (! $parent) {
                        // Create ParentContact
                        $parent = ParentContact::create($parentData);
                    } else {
                        // Update existing parent info
                        $parent->update($parentData);
                    }

                    // Create User linked to ParentContact
                    // NOTE: Do NOT Hash::make() — the User model's 'hashed' cast handles it automatically
                    User::create([
                        'email' => $email,
                        'password' => $validated['password'],
                        'role_id' => $role->role_id,
                        'roleable_type' => ParentContact::class,
                        'roleable_id' => (string) $parent->id,
                    ]);

                    $auditSubject = $parent;
                } else {
                    $email = strtolower($validated['email']);

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
                try {
                    AuditService::log(
                        'User Created',
                        $auditSubject,
                        "Created {$roleName} user: {$validated['email']}",
                        null,
                        $auditSubject->toArray()
                    );
                } catch (\Throwable $e) {
                    // Continue if audit logging fails
                }
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
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $user) {
                // Update User email/password
                // NOTE: Do NOT Hash::make() — the User model's 'hashed' cast handles it automatically
                $userData = [
                    'email' => strtolower($validated['email']),
                ];
                if (! empty($validated['password'])) {
                    $userData['password'] = $validated['password'];
                }
                $user->update($userData);

                // Update Roleable details
                if ($user->roleable) {
                    if ($user->roleable_type === Staff::class || $user->roleable_type === Admin::class) {
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
                        $fullName = trim($validated['first_name'].' '.($validated['middle_initial'] ?? '').' '.$validated['last_name']);
                        $fullName = trim(str_replace('  ', ' ', $fullName));
                        $user->roleable->update([
                            'full_name' => $fullName,
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
            // Prevent deletion of admin accounts
            if ($user->role && $user->role->role_name === 'admin') {
                return redirect()->back()->with('error', 'Admin accounts cannot be deleted for security reasons.');
            }

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

    public function toggleStatus(User $user): RedirectResponse
    {
        try {
            $user->load('roleable');
            $roleable = $user->roleable;
            if (! $roleable) {
                return back()->with('error', 'Role profile not found.');
            }
            if ($user->roleable_type === Staff::class) {
                $roleable->update(['is_active' => ! (bool) $roleable->is_active]);
                $status = $roleable->is_active ? 'activated' : 'deactivated';
            } elseif ($user->roleable_type === ParentContact::class) {
                $newStatus = ($roleable->account_status ?? 'Active') === 'Active' ? 'Inactive' : 'Active';
                $roleable->update(['account_status' => $newStatus]);
                $status = $newStatus === 'Active' ? 'activated' : 'deactivated';
            } else {
                return back()->with('error', 'Cannot toggle status for this user type.');
            }

            return back()->with('success', "Account has been {$status} successfully.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update status.');
        }
    }

    public function activate(User $user): RedirectResponse
    {
        try {
            $user->load('roleable');
            $roleable = $user->roleable;
            if ($user->roleable_type === Staff::class) {
                $roleable->update(['is_active' => true]);
            } elseif ($user->roleable_type === ParentContact::class) {
                $roleable->update(['account_status' => 'Active']);
            } else {
                return back()->with('error', 'Cannot activate this user type.');
            }

            return back()->with('success', 'Account has been activated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to activate account.');
        }
    }
}
