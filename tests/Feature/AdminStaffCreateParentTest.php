<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminStaffCreateParentTest extends TestCase
{
    public function test_admin_can_create_parent_account_via_staff_creation_form()
    {
        // 1. Create Admin User
        $adminRole = Role::firstOrCreate(['role_name' => 'admin']);
        $staffRole = Role::firstOrCreate(['role_name' => 'staff']); // Ensure staff role exists too as it might be used
        $parentRole = Role::firstOrCreate(['role_name' => 'parent']); // Ensure parent role exists

        $adminStaff = Staff::create([
            'staff_id' => 'ADMIN-TEST-001',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@example.com',
            'contact_number' => '09123456789',
            'is_active' => true,
        ]);

        $adminUser = User::create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->role_id,
        ]);

        // 2. Act as Admin
        $this->actingAs($adminUser);

        // 3. Define Parent Data
        $parentData = [
            'first_name' => 'John',
            'middle_initial' => 'D',
            'last_name' => 'Doe',
            'phone_number' => '09987654321',
            'email' => 'johndoe@example.com',
            'role_name' => 'parent',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        // 4. Submit Form
        $response = $this->post(route('admin.staff.store'), $parentData);

        // 5. Assert Redirect
        $response->assertRedirect(route('admin.staff.index'));
        $response->assertSessionHas('success', 'Parent account created successfully.');

        // 6. Assert Database Records
        $this->assertDatabaseHas('parents', [
            'full_name' => 'John D Doe',
            'email' => 'johndoe@example.com',
            'phone' => '09987654321',
            'account_status' => 'Active',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'johndoe@example.com',
            'role_id' => $parentRole->role_id,
            'roleable_type' => 'App\Models\ParentContact',
        ]);
    }
}
