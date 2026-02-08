<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_locked_out_after_max_attempts()
    {
        $role = \App\Models\Role::firstOrCreate(['role_name' => 'admin']);

        $user = new User;
        $user->email = 'user@example.com';
        $user->password = Hash::make('password');
        $user->role_id = $role->role_id;
        // Need roleable too based on previous failures
        $staff = new \App\Models\Staff;
        $staff->staff_id = 'S-SEC-001';
        $staff->first_name = 'Test';
        $staff->last_name = 'Sec';
        $staff->contact_number = '09171234567';
        $staff->created_at = now();
        $staff->updated_at = now();
        $staff->save();
        $user->roleable_type = \App\Models\Staff::class;
        $user->roleable_id = $staff->staff_id;

        $user->save();

        // Simulate 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/authenticate', [
                'email' => 'user@example.com',
                'password' => 'wrong-password',
            ]);
            $response->assertSessionHasErrors(['email']);
        }

        // 6th attempt should be locked out
        $response = $this->post('/authenticate', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['email' => 'Account locked. Try again later.']);

        $user->refresh();
        $this->assertNotNull($user->lockout_until);
        $this->assertTrue(now()->lt($user->lockout_until));
    }

    public function test_user_must_change_password_on_expiry()
    {
        $role = \App\Models\Role::firstOrCreate(['role_name' => 'admin']);

        $user = new User;
        $user->email = 'expired@example.com';
        $user->password = Hash::make('password');
        $user->password_expires_at = now()->subDay(); // Expired yesterday
        $user->role_id = $role->role_id;

        $staff = new \App\Models\Staff;
        $staff->staff_id = 'S-SEC-002';
        $staff->first_name = 'Test';
        $staff->last_name = 'Expired';
        $staff->contact_number = '09171234568';
        $staff->created_at = now();
        $staff->updated_at = now();
        $staff->save();
        $user->roleable_type = \App\Models\Staff::class;
        $user->roleable_id = $staff->staff_id;

        $user->save();

        $response = $this->post('/authenticate', [
            'email' => 'expired@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('auth.password.change'));
    }
}
