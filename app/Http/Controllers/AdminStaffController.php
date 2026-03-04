<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Admin;
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
                $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
                $q->where(function ($sub) use ($query, $operator) {
                    $sub->where('email', $operator, "%{$query}%")
                        ->orWhere(function ($sq) use ($query, $operator) {
                            $sq->where(function ($sq1) use ($query, $operator) {
                                $sq1->whereIn('roleable_type', [Staff::class, Admin::class])
                                    ->whereHasMorph('roleable', [Staff::class, Admin::class], function ($q) use ($query, $operator) {
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
                    // Admin-type users are always considered active (no is_active field)
                    $sub->orWhere('roleable_type', Admin::class);
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
                    // Admin-type users are always active, so exclude them from inactive filter
                });
            })
            ->orderBy('user_id', 'desc');

        $users = $usersQuery->paginate(15)->withQueryString();

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
    public function store(StoreUserRequest $request): RedirectResponse
    {
        try {
            $roleName = $request->input('role_name');
            $validated = $request->validated();
            $email = strtolower($validated['email']); // Define email here for use in success message

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
                    // NOTE: Do NOT Hash::make() — the User model's 'hashed' cast handles it automatically
                    $user = User::create([
                        'email' => $email,
                        'password' => $validated['password'] ?? \Illuminate\Support\Str::random(32),
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
                ->with('success', rtrim(ucfirst($roleName).' account created successfully.'.($roleName === 'parent' && str_contains(strtolower($email), '@gmail.com') && empty($validated['password']) ? ' A password setup email has been sent to the Gmail address.' : '')));
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
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->load(['role', 'roleable']);
        $roleable = $user->roleable;

        $validated = $request->validated();

        try {
            DB::transaction(function () use ($user, $roleable, $validated) {
                $fullName = $validated['first_name'].' '.($validated['middle_initial'] ?? '').' '.$validated['last_name'];
                $fullName = trim(str_replace('  ', ' ', $fullName));

                // Update User
                // NOTE: Do NOT Hash::make() — the User model's 'hashed' cast handles it automatically
                $userData = [
                    'email' => strtolower($validated['email']),
                ];
                if (! empty($validated['password'])) {
                    $userData['password'] = $validated['password'];
                }
                $user->update($userData);

                // Update Roleable
                if ($user->roleable_type === ParentContact::class) {
                    $roleable->update([
                        'full_name' => $fullName,
                        'phone' => $validated['phone_number'] ?? null,
                        'email' => strtolower($validated['email']),
                    ]);
                } elseif ($user->roleable_type === Staff::class || $user->roleable_type === Admin::class) {
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
            // Generate a cryptographically secure random password (16 chars, mixed complexity)
            $newPassword = \Illuminate\Support\Str::password(16);

            // NOTE: Do NOT Hash::make() — the User model's 'hashed' cast handles it automatically
            $user->update([
                'password' => $newPassword,
                'must_change_password' => true,
            ]);

            // Attempt to email the new password to the user instead of showing in flash
            try {
                Mail::raw(
                    "Your password has been reset by an administrator.\n\nNew password: {$newPassword}\n\nPlease log in and change your password immediately.",
                    function ($message) use ($user) {
                        $message->to($user->email)
                            ->subject('Your Password Has Been Reset');
                    }
                );

                return redirect()
                    ->route('admin.staff.show', $user)
                    ->with('success', 'Password reset successfully. The new password has been sent to the user\'s email.');
            } catch (\Throwable $mailError) {
                Log::warning('Password reset email failed for user '.$user->email.': '.$mailError->getMessage());

                // Only show password in flash as last resort if email fails
                return redirect()
                    ->route('admin.staff.show', $user)
                    ->with('success', 'Password reset successfully but email delivery failed. New temporary password: '.$newPassword)
                    ->with('temp_password', $newPassword);
            }
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
