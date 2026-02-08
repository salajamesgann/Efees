<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command?->info('Updating staff user credentials...');

        // Hash the new password
        $newPassword = Hash::make('staff');
        $email = 'staff@gmail.com';

        // Try to find and update staff user by role
        try {
            $staffRole = Role::where('role_name', 'staff')->first();
            if ($staffRole) {
                $updated = User::where('role_id', $staffRole->role_id)
                    ->update([
                        'email' => $email,
                        'password' => $newPassword,
                    ]);

                if ($updated > 0) {
                    $this->command?->info("Successfully updated staff user with email: {$email} and password: staff");

                    return;
                }
            }
        } catch (\Throwable $e) {
            $this->command?->warn('Could not find staff role. Trying alternative methods...');
        }

        // If role-based update failed, try to find by email or username
        try {
            $user = User::where('email', 'like', '%staff%')
                ->orWhere('username', 'staff')
                ->first();

            if ($user) {
                $user->update([
                    'email' => $email,
                    'password' => $newPassword,
                ]);
                $this->command?->info("Successfully updated staff user with ID: {$user->id}");
            } else {
                $this->command?->warn('No staff user found to update.');
            }
        } catch (\Throwable $e) {
            $this->command?->error('Failed to update staff user: '.$e->getMessage());
        }
    }
}
