<?php

namespace App\Http\Controllers;

use App\Models\ParentContact;
use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use App\Services\AuditService;
use App\Services\FeeManagementService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
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
            ->whereHas('role', function ($q) {
                $q->whereIn('role_name', ['staff', 'admin', 'parent']);
            })
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                });
            })
            ->when($status === 'active', function ($q) {
                $q->where(function ($sub) {
                    $sub->where(function ($s) {
                        $s->where('roleable_type', Staff::class)
                            ->whereExists(function ($exists) {
                                $exists->select(DB::raw(1))
                                    ->from('staff')
                                    ->whereColumn('staff.staff_id', 'users.roleable_id')
                                    ->where('is_active', true);
                            });
                    })->orWhere(function ($p) {
                        $p->where('roleable_type', ParentContact::class)
                            ->whereExists(function ($exists) {
                                $exists->select(DB::raw(1))
                                    ->from('parents')
                                    ->where(function ($q) {
                                        if (DB::connection()->getDriverName() === 'pgsql') {
                                            $q->whereRaw('CAST(parents.id AS VARCHAR) = users.roleable_id');
                                        } else {
                                            $q->whereColumn('parents.id', 'users.roleable_id');
                                        }
                                    })
                                    ->where('account_status', 'Active');
                            });
                    });
                });
            })
            ->when($status === 'inactive', function ($q) {
                $q->where(function ($sub) {
                    $sub->where(function ($s) {
                        $s->where('roleable_type', Staff::class)
                            ->whereExists(function ($exists) {
                                $exists->select(DB::raw(1))
                                    ->from('staff')
                                    ->whereColumn('staff.staff_id', 'users.roleable_id')
                                    ->where('is_active', false);
                            });
                    })->orWhere(function ($p) {
                        $p->where('roleable_type', ParentContact::class)
                            ->whereExists(function ($exists) {
                                $exists->select(DB::raw(1))
                                    ->from('parents')
                                    ->where(function ($q) {
                                        if (DB::connection()->getDriverName() === 'pgsql') {
                                            $q->whereRaw('CAST(parents.id AS VARCHAR) = users.roleable_id');
                                        } else {
                                            $q->whereColumn('parents.id', 'users.roleable_id');
                                        }
                                    })
                                    ->where('account_status', '!=', 'Active');
                            });
                    });
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
                'required_if:role_name,staff,admin', // Required for staff/admin, optional for parents
                'nullable', // Allow null for parents
            ],
        ], [
            'password.required_if' => 'Password is required for staff and admin accounts.',
            'email.unique' => 'This email address is already registered.',
        ]);

        // Add password complexity rules only if password is provided
        if ($request->filled('password')) {
            $validator->sometimes('password', [
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
            ], function ($input) {
                return $input->filled('password');
            }, [
                'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            ]);
        }

        // Add phone number uniqueness validation only for parent accounts
        if ($request->input('role_name') === 'parent' && $request->filled('phone_number')) {
            $validator->sometimes('phone_number', [
                'unique:parents,phone',
            ], function ($input) {
                return $input->role_name === 'parent' && $input->filled('phone_number');
            }, [
                'phone_number.unique' => 'This phone number is already registered.',
            ]);
        }

        if ($validator->fails()) {
            return redirect()
                ->route('admin.staff.create')
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $roleName = $request->input('role_name');
            $validated = $validator->validated();
            $email = strtolower($validated['email']); // Define email here for use in success message

            // Pre-transaction checks (outside transaction to avoid failed transaction issues)
            if (User::where('email', $email)->exists()) {
                throw new \Exception('A user with this email already exists.');
            }

            if ($roleName === 'parent') {
                // Check if parent already exists by email
                $existingParent = ParentContact::where('email', $email)->first();
            } else {
                $existingParent = null;
            }

            // Get role outside transaction to avoid transaction issues
            $role = Role::where('role_name', $roleName)->firstOrFail();

            DB::transaction(function () use ($request, $validated, $roleName, $email, $existingParent, $role) {

                if ($roleName === 'parent') {
                    $parentData = [
                        'full_name' => trim($validated['first_name'].' '.($validated['middle_initial'] ?? '').' '.$validated['last_name']),
                        'phone' => $validated['phone_number'] ?? null,
                        'email' => $email,
                        'account_status' => 'Active',
                    ];

                    if (! $existingParent) {
                        // Create Parent Contact
                        $parent = ParentContact::create($parentData);

                        // Sync to Supabase
                        try {
                            $svc = app(FeeManagementService::class);
                            $svc->syncToSupabase('parents', [
                                'parent_id' => (string) $parent->id,
                                'full_name' => $parent->full_name,
                                'phone' => $parent->phone,
                                'phone_secondary' => null,
                                'email' => $parent->email,
                                'account_status' => $parent->account_status,
                                'updated_at' => now()->toISOString(),
                            ], 'parent_id', (string) $parent->id);
                        } catch (\Throwable $e) {
                            // Continue if sync fails
                        }
                    } else {
                        // Update existing parent info
                        $existingParent->update($parentData);
                        $parent = $existingParent; // Use existing parent for user creation
                    }

                    // Create User Account
                    $user = User::create([
                        'email' => $email,
                        'password' => Hash::make($validated['password']),
                        'must_change_password' => empty($validated['password']),
                        'role_id' => $role->role_id,
                        'roleable_type' => ParentContact::class,
                        'roleable_id' => (string) $parent->id,
                    ]);

                    // Send password reset email if no password was provided and it's a Gmail address
                    if (empty($validated['password']) && str_contains(strtolower($email), '@gmail.com')) {
                        try {
                            // Create password reset token
                            $token = Password::createToken($user);
                            
                            // Generate reset URL with production domain
                            $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);
                            
                            // Fix for production: Replace local URL with production URL
                            $resetUrl = str_replace('http://127.0.0.1:8000', 'https://efees.site', $resetUrl);
                            $resetUrl = str_replace('http://localhost', 'https://efees.site', $resetUrl);
                            
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
                            Log::error('Failed to send parent account email: ' . $e->getMessage());
                        }
                    }

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
                    // Check if staff user already exists
                    if (User::where('email', $email)->exists()) {
                        throw new \Exception('A user with this email already exists.');
                    }

                    // Create Staff/Admin
                    $staffData = [
                        'first_name' => $validated['first_name'],
                        'MI' => $validated['middle_initial'] ?? null,
                        'last_name' => $validated['last_name'],
                        'contact_number' => $validated['phone_number'] ?? null,
                        'department' => $request->input('department') ?: 'General Administration', // Default department
                        'position' => $request->input('position') ?: 'Staff', // Default position
                        'is_active' => true,
                    ];

                    $userData = [
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
                
                return true; // Return success value from transaction
            });

            return redirect()
                ->route('admin.staff.index')
                ->with('success', ucfirst($roleName).' account created successfully. '.($roleName === 'parent' && str_contains(strtolower($email), '@gmail.com') && empty($validated['password']) ? 'A password setup email has been sent to the Gmail address.' : ''));
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->user_id.',user_id'],
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
            DB::transaction(function () use ($user, $roleable, $validator) {
                $validated = $validator->validated();
                $fullName = $validated['first_name'].' '.($validated['middle_initial'] ?? '').' '.$validated['last_name'];
                $fullName = trim(str_replace('  ', ' ', $fullName));

                // Update User
                $userData = [
                    'email' => strtolower($validated['email']),
                ];
                if (! empty($validated['password'])) {
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

            if (! $roleable) {
                // Handle case where roleable relationship is not loaded
                if ($user->roleable_type === Staff::class) {
                    $current = DB::table('staff')
                        ->where('staff_id', $user->roleable_id)
                        ->value('is_active');
                    $new = $current === null ? true : ! (bool) $current;
                    DB::table('staff')
                        ->where('staff_id', $user->roleable_id)
                        ->update(['is_active' => $new, 'updated_at' => now()]);
                    $status = $new ? 'activated' : 'deactivated';

                    return back()->with('success', "Account has been {$status} successfully.");
                } elseif ($user->roleable_type === ParentContact::class) {
                    // Handle parent account status update
                    try {
                        $current = DB::table('parents')
                            ->where('id', $user->roleable_id)
                            ->value('account_status');
                        
                        // Parent accounts use 'Active' and 'Archived' status values
                        $newStatus = ($current ?? 'Active') === 'Active' ? 'Archived' : 'Active';
                        
                        DB::table('parents')
                            ->where('id', $user->roleable_id)
                            ->update(['account_status' => $newStatus, 'updated_at' => now()]);
                        
                        $status = ($newStatus === 'Active') ? 'activated' : 'archived';

                        return back()->with('success', "Account has been {$status} successfully.");
                    } catch (\Exception $e) {
                        return back()->with('error', 'Failed to update parent status: ' . $e->getMessage());
                    }
                } else {
                    return back()->with('error', 'Role profile not found.');
                }
            }

            // Update using the loaded roleable model
            if ($user->roleable_type === Staff::class) {
                $roleable->update(['is_active' => ! $roleable->is_active]);
                $status = $roleable->is_active ? 'activated' : 'deactivated';
            } elseif ($user->roleable_type === ParentContact::class) {
                // Parent accounts use 'Active' and 'Archived' status values
                $newStatus = $roleable->account_status === 'Active' ? 'Archived' : 'Active';
                $roleable->update(['account_status' => $newStatus]);
                $status = $newStatus === 'Active' ? 'activated' : 'archived';
            } else {
                return back()->with('error', 'Cannot toggle status for this user type.');
            }

            return back()->with('success', "Account has been {$status} successfully.");

        } catch (\Exception $e) {
            // Add more detailed error information for debugging
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
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
            // Prevent deletion of admin accounts
            if ($user->role && $user->role->role_name === 'admin') {
                return redirect()->back()->with('error', 'Admin accounts cannot be deleted for security reasons.');
            }

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
