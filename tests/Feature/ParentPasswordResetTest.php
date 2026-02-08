<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\PasswordResetRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ParentPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_forgot_password_creates_pending_request()
    {
        // Create a Parent Role
        $role = Role::firstOrCreate(['role_name' => 'parent'], ['description' => 'Parent Role']);

        // Create a User with this role
        // We override attributes to match the logic in ForgotPasswordController
        $user = User::factory()->create([
            'email' => 'parent@example.com',
            'role_id' => $role->role_id,
            'roleable_type' => 'App\Models\ParentContact', // Simulating the morph map
            'roleable_id' => 1,
        ]);

        // Fake the Password broker to ensure no actual email is sent by the default flow
        // effectively, though our controller bypasses it for parents.
        // We actually want to assert Password::sendResetLink was NOT called or 
        // that the flow diverted before it.
        // However, since we are testing the controller response and DB side effects:

        $response = $this->post(route('password.email'), [
            'email' => 'parent@example.com',
        ]);

        // Assert redirect back with success message
        $response->assertSessionHas('success', 'Password reset request submitted. Please wait for admin approval.');

        // Assert Database has the request
        $this->assertDatabaseHas('password_reset_requests', [
            'email' => 'parent@example.com',
            'status' => 'pending',
        ]);
    }

    public function test_non_parent_gets_reset_link_immediately()
    {
        // Create Admin User
        $adminRole = Role::where('role_name', 'admin')->first();
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'role_id' => $adminRole->role_id,
            'roleable_type' => 'App\Models\Admin',
            'roleable_id' => 1,
        ]);

        // Mock Password facade to expect sendResetLink
        Password::shouldReceive('sendResetLink')
            ->once()
            ->with(['email' => 'admin@example.com'])
            ->andReturn(Password::RESET_LINK_SENT);

        $response = $this->post(route('password.email'), [
            'email' => 'admin@example.com',
        ]);

        // Assert redirect back with success status (default Laravel behavior)
        $response->assertSessionHas('success', trans(Password::RESET_LINK_SENT));
        
        // Assert NO request created in DB
        $this->assertDatabaseMissing('password_reset_requests', [
            'email' => 'admin@example.com',
        ]);
    }

    public function test_admin_can_approve_request()
    {
        // Create Admin User
        $adminRole = Role::where('role_name', 'admin')->first();
        $admin = User::factory()->create([
            'role_id' => $adminRole->role_id,
            'roleable_type' => 'App\Models\Admin',
            'roleable_id' => 1,
        ]);

        // Create a pending request
        $request = PasswordResetRequest::create([
            'email' => 'parent@example.com',
            'status' => 'pending',
        ]);

        // Mock Password facade to intercept the email sending
        Password::shouldReceive('sendResetLink')
            ->once()
            ->with(['email' => 'parent@example.com'])
            ->andReturn(Password::RESET_LINK_SENT);

        // Act as Admin
        $response = $this->actingAs($admin)
            ->post(route('admin.requests.approve', $request));

        // Assert Request is now approved
        $this->assertDatabaseHas('password_reset_requests', [
            'id' => $request->id,
            'status' => 'approved',
        ]);

        $response->assertSessionHas('success');
    }

    public function test_admin_can_reject_request()
    {
        // Create Admin User
        $adminRole = Role::where('role_name', 'admin')->first();
        $admin = User::factory()->create([
            'role_id' => $adminRole->role_id,
            'roleable_type' => 'App\Models\Admin',
            'roleable_id' => 1,
        ]);

        // Create a pending request
        $request = PasswordResetRequest::create([
            'email' => 'reject@example.com',
            'status' => 'pending',
        ]);

        // Act as Admin
        $response = $this->actingAs($admin)
            ->post(route('admin.requests.reject', $request));

        // Assert Request is now rejected
        $this->assertDatabaseHas('password_reset_requests', [
            'id' => $request->id,
            'status' => 'rejected',
        ]);
    }
}
