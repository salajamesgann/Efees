<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminStaffManagementTest extends TestCase
{
    public function test_admin_can_create_staff_account()
    {
        // 1. Create Admin User
        $adminRole = Role::firstOrCreate(['role_name' => 'admin']);
        $staffRole = Role::firstOrCreate(['role_name' => 'staff']);

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
            'roleable_type' => Staff::class,
            'roleable_id' => $adminStaff->staff_id,
        ]);

        // 2. Act as Admin
        $this->actingAs($adminUser);

        // 3. Define Staff Data
        $staffData = [
            'first_name' => 'New',
            'middle_initial' => 'S',
            'last_name' => 'Staff',
            'phone_number' => '09123456780',
            'email' => 'newstaff@example.com',
            'role_name' => 'staff',
            'department' => 'Finance',
            'position' => 'Cashier',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        // 4. Submit Form
        $response = $this->post(route('admin.staff.store'), $staffData);

        // 5. Assert Redirect
        $response->assertRedirect(route('admin.staff.index'));
        $response->assertSessionHas('success', 'Staff account created successfully.');

        // 6. Assert Database Records
        $this->assertDatabaseHas('staff', [
            'first_name' => 'New',
            'last_name' => 'Staff',
            'contact_number' => '09123456780',
            'department' => 'Finance',
            'position' => 'Cashier',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newstaff@example.com',
            'role_id' => $staffRole->role_id,
            'roleable_type' => 'App\Models\Staff',
        ]);
    }
}
