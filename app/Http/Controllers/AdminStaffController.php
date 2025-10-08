<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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

        $staffQuery = Staff::with('user')
            ->select(['staff_id', 'first_name', 'MI', 'last_name', 'contact_number', 'department', 'position', 'salary'])
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
                // Note: is_active column may not exist, filtering handled in view
            })
            ->when($status === 'inactive', function ($q) {
                // Note: is_active column may not exist, filtering handled in view
            })
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
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
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

        $validated = $validator->validated();

        try {
            DB::transaction(function () use ($validated) {
                Staff::createWithAccount([
                    'first_name' => $validated['first_name'],
                    'MI' => $validated['middle_initial'] ?? null,
                    'last_name' => $validated['last_name'],
                    'contact_number' => $validated['phone_number'] ?? null,
                    'department' => null, // Default to null since not in form
                    'position' => 'Staff', // Default position
                    'salary' => null, // Default to null since not in form
                    'is_active' => true,
                ], [
                    'email' => strtolower($validated['email']),
                    'password' => $validated['password'],
                    'role_id' => 3, // Staff role (ID 3 based on migration order)
                ]);
            });

            return redirect()
                ->route('admin.staff.index')
                ->with('success', 'Staff account created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.staff.create')
                ->with('error', 'Failed to create staff account. Please try again. Error: ' . $e->getMessage())
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
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:100'],
            'middle_initial' => ['nullable', 'string', 'max:1'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone_number' => ['nullable', 'string', 'min:7', 'max:15'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'
            ],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if ($validator->fails()) {
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

            if (!empty($validated['password'])) {
                $userData['password'] = $validated['password'];
            }

            $staff->updateWithAccount([
                'first_name' => $validated['first_name'],
                'MI' => $validated['middle_initial'] ?? null,
                'last_name' => $validated['last_name'],
                'contact_number' => $validated['phone_number'] ?? null,
                // Note: department, position, salary not included in simplified edit form
            ], $userData);

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
     * Toggle staff account status.
     */
    public function toggleStatus(Staff $staff): RedirectResponse
    {
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
        }
    }

    /**
     * Show staff account details with confirmation for actions.
     */
    public function show(Staff $staff): View
    {
        $staff->load('user');

        return view('auth.admin_staff_show', [
            'staff' => $staff,
        ]);
    }
}
