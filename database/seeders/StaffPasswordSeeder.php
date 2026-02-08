<?php

namespace Database\Seeders;

<<<<<<< HEAD
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
=======
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141

class StaffPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command?->info('Updating staff user credentials...');

        // Hash the new password
<<<<<<< HEAD
        $newPassword = Hash::make('staff');
=======
        $newPassword = Hash::make('staff123');
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
        $email = 'staff@gmail.com';

        // Try to find and update staff user by role
        try {
            $staffRole = Role::where('role_name', 'staff')->first();
            if ($staffRole) {
                $updated = User::where('role_id', $staffRole->role_id)
                    ->update([
                        'email' => $email,
<<<<<<< HEAD
                        'password' => $newPassword,
                    ]);

                if ($updated > 0) {
                    $this->command?->info("Successfully updated staff user with email: {$email} and password: staff");

=======
                        'password' => $newPassword
                    ]);
                
                if ($updated > 0) {
                    $this->command?->info("Successfully updated staff user with email: {$email} and password: staff");
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
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
<<<<<<< HEAD
                    'password' => $newPassword,
=======
                    'password' => $newPassword
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
                ]);
                $this->command?->info("Successfully updated staff user with ID: {$user->id}");
            } else {
                $this->command?->warn('No staff user found to update.');
            }
        } catch (\Throwable $e) {
<<<<<<< HEAD
            $this->command?->error('Failed to update staff user: '.$e->getMessage());
=======
            $this->command?->error('Failed to update staff user: ' . $e->getMessage());
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
        }
    }
}
