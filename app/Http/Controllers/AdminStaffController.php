<?php

namespace App\Http\Controllers;

use App\Models\ParentContact;
use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use App\Services\AuditService;
use App\Services\FeeManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class AdminStaffController extends Controller
{
    /**
     * Display a listing of all users (staff and parents).
     */
    public function index(Request $request): View
    {
        $query = trim($request->input('q', ''));
        $status = $request->input('status', 'all');

        $usersQuery = User::with(['role', 'roleable'])
            ->whereHas('role', function($q) {
                $q->whereIn('role_name', ['staff', 'admin', 'parent']);
            })
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->when($status === 'active', function ($q) {
                $q->whereHasMorph('roleable', [Staff::class, ParentContact::class], function ($q, $type) {
                    if ($type === Staff::class) {
                        $q->where('is_active', true);
                    } elseif ($type === ParentContact::class) {
                        $q->where('account_status', 'Active');
                    }
                });
            })
            ->when($status === 'inactive', function ($q) {
                $q->whereHasMorph('roleable', [Staff::class, ParentContact::class], function ($q, $type) {
                    if ($type === Staff::class) {
                        $q->where('is_active', false);
                    } elseif ($type === ParentContact::class) {
                        $q->where('account_status', '!=', 'Active');
                    }
                });
            })
            ->orderBy('user_id', 'desc');

        $users = $usersQuery->paginate(15);

        return view('auth.admin_staff_index', [
            'users' => $users,
            'query' => $query,
            'status' => $status,
        ]);
    }

    /**
     * Show the form for creating a new user account.
     */
    public function create(): View
    {
        $roles = Role::whereIn('role_name', ['staff', 'admin', 'parent'])->get();
        return view('auth.admin_staff_create', compact('roles'));
    }

    /**
     * Store a newly created user account in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:5'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role_name' => ['required', 'string', 'exists:roles,role_name'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'email.unique' => 'This email address is already registered.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.staff.create')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $roleName = $request->input('role_name');

            DB::transaction(function () use ($request, $validator, $roleName) {
                $validated = $validator->validated();
                $role = Role::where('role_name', $roleName)->firstOrFail();

                if ($roleName === 'parent') {
                    // Create Parent Contact
                    $parentData = [
                        'full_name' => trim($validated['first_name'] . ' ' . ($validated['middle_initial'] ?? '') . ' ' . $validated['last_name']),
                        'phone' => $validated['phone_number'] ?? null,
                        'email' => strtolower($validated['email']),
                        'account_status' => 'Active',
                    ];
                    
                    $parent = ParentContact::create($parentData);

                    // Sync to Supabase
                    try {
                        $svc = app(FeeManagementService::class);
                        $svc->syncToSupabase('parents', [
                            'parent_id' => $parent->id,
                            'full_name' => $parent->full_name,
                            'phone' => $parent->phone,
                            'phone_secondary' => null,
                            'email' => $parent->email,
                            'address_street' => null,
                            'address_barangay' => null,
                            'address_city' => null,
                            'address_province' => null,
                            'address_zip' => null,
                            'account_status' => $parent->account_status,
                            'updated_at' => now()->toISOString(),
                        ], 'parent_id', $parent->id);
                    } catch (\Throwable $e) {
                        // Continue if sync fails
                    }

                    // Create User Account
                    User::create([
                        'name' => $parentData['full_name'],
                        'email' => strtolower($validated['email']),
                        'password' => Hash::make($validated['password']),
                        'role_id' => $role->role_id,
                        'roleable_type' => ParentContact::class,
                        'roleable_id' => $parent->id,
                    ]);

                    // Audit Log
                    try {
                        AuditService::log(
                            'Parent Account Created',
                            $parent,
                            "Created parent account: {$parent->full_name}",
                            null,
                            $parent->toArray()
                        );
                    } catch (\Throwable $e) {
                    }

                } else {
                    // Create Staff/Admin
                    $staffData = [
                        'first_name' => $validated['first_name'],
                        'MI' => $validated['middle_initial'] ?? null,
                        'last_name' => $validated['last_name'],
                        'contact_number' => $validated['phone_number'] ?? null,
                        'department' => $request->input('department'),
                        'position' => $request->input('position'),
                        'is_active' => true,
                    ];

                    $userData = [
                        'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                        'email' => strtolower($validated['email']),
                        'password' => $validated['password'],
                        'role_id' => $role->role_id,
                    ];

                    $createdStaff = Staff::createWithAccount($staffData, $userData);

                    // Audit Log
                    try {
                        AuditService::log(
                            'Staff Account Created',
                            $createdStaff,
                            "Created staff account: {$createdStaff->first_name} {$createdStaff->last_name} (Role: {$roleName})",
                            null,
                            $createdStaff->toArray()
                        );
                    } catch (\Throwable $e) {
                    }
                }
            });

            return redirect()
                ->route('admin.staff.index')
                ->with('success', ucfirst($roleName) . ' account created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.staff.create')
                ->with('error', 'Failed to create account. Please try again. Error: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified user details.
     */
    public function show(User $user): View
    {
        $user->load(['role', 'roleable']);
        // For compatibility with view, we might need to pass specific vars
        // But better to update view to use $user
        return view('auth.admin_staff_show', compact('user'));
    }

    /**
     * Show the form for editing the specified user account.
     */
    public function edit(User $user): View
    {
        $user->load(['role', 'roleable']);
        $roles = Role::whereIn('role_name', ['staff', 'admin'])->get(); // Can we change role to parent? Probably not easily.

        return view('auth.admin_staff_edit', [
            'user' => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Update the specified user account.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $user->load(['role', 'roleable']);
        $roleable = $user->roleable;

        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:5'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->user_id . ',user_id'],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('admin.staff.edit', $user)
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::transaction(function () use ($request, $user, $roleable, $validator) {
                $validated = $validator->validated();
                $fullName = $validated['first_name'] . ' ' . ($validated['middle_initial'] ?? '') . ' ' . $validated['last_name'];
                $fullName = trim(str_replace('  ', ' ', $fullName));

                // Update User
                $userData = [
                    'name' => $fullName,
                    'email' => strtolower($validated['email']),
                ];
                if (!empty($validated['password'])) {
                    $userData['password'] = Hash::make($validated['password']);
                }
                $user->update($userData);

                // Update Roleable
                if ($user->roleable_type === ParentContact::class) {
                    $roleable->update([
                        'full_name' => $fullName,
                        'phone' => $validated['phone_number'] ?? null,
                        'email' => strtolower($validated['email']),
                    ]);
                } elseif ($user->roleable_type === Staff::class) {
                    $roleable->update([
                        'first_name' => $validated['first_name'],
                        'MI' => $validated['middle_initial'] ?? null,
                        'last_name' => $validated['last_name'],
                        'contact_number' => $validated['phone_number'] ?? null,
                    ]);
                }
            });

            return redirect()
                ->route('admin.staff.index')
                ->with('success', 'Account updated successfully.');

        } catch (\Exception $e) {
            return redirect()
                ->route('admin.staff.edit', $user)
                ->with('error', 'Failed to update account. Error: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Toggle user account status.
     */
    public function toggleStatus(User $user): RedirectResponse
    {
        try {
            $user->load('roleable');
            $roleable = $user->roleable;
            
            if (!$roleable) {
                return back()->with('error', 'Role profile not found.');
            }

            if ($user->roleable_type === Staff::class) {
                $roleable->update(['is_active' => !$roleable->is_active]);
                $status = $roleable->is_active ? 'activated' : 'deactivated';
            } elseif ($user->roleable_type === ParentContact::class) {
                $newStatus = $roleable->account_status === 'Active' ? 'Inactive' : 'Active';
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

    /**
     * Activate a user account.
     */
    public function activate(User $user): RedirectResponse
    {
        try {
            $user->load('roleable');
            $roleable = $user->roleable;

            if ($user->roleable_type === Staff::class) {
                $roleable->update(['is_active' => true]);
            } elseif ($user->roleable_type === ParentContact::class) {
                $roleable->update(['account_status' => 'Active']);
            }

            return back()->with('success', 'Account has been activated successfully.');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to activate account.');
        }
    }

    /**
     * Reset user password.
     */
    public function resetPassword(User $user): RedirectResponse
    {
        try {
            // Generate a random password
            $newPassword = 'User@'.rand(1000, 9999);

            $user->update([
                'password' => Hash::make($newPassword),
            ]);

            return redirect()
                ->route('admin.staff.show', $user)
                ->with('success', "Password reset successfully. New password: {$newPassword}");
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.staff.show', $user)
                ->with('error', 'Failed to reset password. Please try again.');
        }
    }

    /**
     * Remove the specified user account.
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            DB::transaction(function () use ($user) {
                $roleable = $user->roleable;
                
                // Audit Log
                try {
                    AuditService::log(
                        'Account Deleted',
                        $user,
                        "Deleted account: {$user->name}",
                        $user->toArray(),
                        null
                    );
                } catch (\Throwable $e) {
                }

                $user->delete();
                if ($roleable) {
                    $roleable->delete();
                }
            });

            return redirect()->route('admin.staff.index')->with('success', 'Account deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete account.');
        }
    }
}
