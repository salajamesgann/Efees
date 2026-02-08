<?php

namespace Tests\Feature;

use App\Models\ParentContact;
use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UnifiedUserManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Manual Schema Setup to avoid broken migrations
        // Users Table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function ($table) {
                $table->increments('user_id');
                $table->string('email');
                $table->string('password');
                $table->string('name')->nullable();
                $table->integer('role_id')->nullable();
                $table->string('roleable_type')->nullable();
                $table->string('roleable_id')->nullable();
                $table->rememberToken();
            });
        }

        // Roles Table
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function ($table) {
                $table->increments('role_id');
                $table->string('role_name');
                $table->string('description')->nullable();
            });
        }

        // Staff Table
        if (!Schema::hasTable('staff')) {
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
        }

        // Parents Table
        if (!Schema::hasTable('parents')) {
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
    }

    public function test_admin_can_view_unified_user_list_containing_staff_and_parents()
    {
        // 1. Create Roles
        $adminRole = Role::firstOrCreate(['role_name' => 'admin']);
        $staffRole = Role::firstOrCreate(['role_name' => 'staff']);
        $parentRole = Role::firstOrCreate(['role_name' => 'parent']);

        // 2. Create Admin User (Actor)
        $adminStaff = Staff::create([
            'staff_id' => 'ADMIN-001',
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'admin@test.com',
            'is_active' => true,
        ]);

        $adminUser = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->role_id,
            'roleable_type' => Staff::class,
            'roleable_id' => $adminStaff->staff_id,
        ]);

        // 3. Create a Staff User
        $staffMember = Staff::create([
            'staff_id' => 'STAFF-001',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'department' => 'Academics',
            'position' => 'Teacher',
            'contact_number' => '09123456789',
            'is_active' => true,
        ]);

        $staffUser = User::create([
            'name' => 'John Doe',
            'email' => 'staff@test.com',
            'password' => Hash::make('password'),
            'role_id' => $staffRole->role_id,
            'roleable_type' => Staff::class,
            'roleable_id' => $staffMember->staff_id,
        ]);

        // 4. Create a Parent User
        $parentContact = ParentContact::create([
            'full_name' => 'Jane Smith',
            'phone' => '09987654321',
            'email' => 'parent@test.com',
            'account_status' => 'Active',
        ]);

        $parentUser = User::create([
            'name' => 'Jane Smith',
            'email' => 'parent@test.com',
            'password' => Hash::make('password'),
            'role_id' => $parentRole->role_id,
            'roleable_type' => ParentContact::class,
            'roleable_id' => $parentContact->id,
        ]);

        // 5. Act as Admin and Visit Index Page
        $response = $this->actingAs($adminUser)->get(route('admin.staff.index'));

        // 6. Assertions
        $response->assertStatus(200);
        $response->assertSee('User Management');
        
        // Check for Staff presence and details
        $response->assertSee('John Doe');
        $response->assertSee('Academics'); // Department
        $response->assertSee('Teacher');   // Position
        $response->assertSee('STAFF-001');

        // Check for Parent presence and details
        $response->assertSee('Jane Smith');
        $response->assertSee('P-' . $parentUser->user_id); // Parent ID format
        $response->assertSee('09987654321'); // Phone
        $response->assertSee('Guardian Account'); // New label
    }
}
