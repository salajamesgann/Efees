<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminStaffManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Manual Schema Setup to avoid broken migrations
        Schema::create('users', function ($table) {
            $table->increments('user_id');
            $table->string('email');
            $table->string('password');
            $table->integer('role_id')->nullable();
            $table->string('roleable_type')->nullable();
            $table->string('roleable_id')->nullable();
            $table->rememberToken();
            //$table->timestamps(); // Matches User model $timestamps = false;
        });

        Schema::create('roles', function ($table) {
            $table->increments('role_id');
            $table->string('role_name');
            $table->string('description')->nullable();
        });

        Schema::create('staff', function ($table) {
            $table->string('staff_id')->primary();
            $table->string('first_name');
            $table->string('MI')->nullable();
            $table->string('last_name');
            $table->string('contact_number')->nullable();
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('parents', function ($table) {
            $table->increments('id');
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('phone_secondary')->nullable();
            $table->string('email')->nullable();
            $table->string('address_street')->nullable();
            $table->string('address_barangay')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_province')->nullable();
            $table->string('address_zip')->nullable();
            $table->string('account_status')->default('Active');
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });
    }

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
