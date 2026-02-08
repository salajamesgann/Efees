<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminPasswordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command?->info('Setting admin users\' password to "admin"...');

        // Hash the new password
        $newPassword = Hash::make('admin');

        // Prefer filtering by role_id if the Role model/table is aligned
        $updated = 0;
        try {
            $adminRole = Role::where('role_name', 'admin')->first();
            if ($adminRole) {
                $updated = User::where('role_id', $adminRole->role_id)
                    ->update(['password' => $newPassword]);
                $this->command?->info("Updated by role_id: {$updated} row(s).");
            }
        } catch (\Throwable $e) {
            $this->command?->warn('Could not query roles via Eloquent (possible table name mismatch). Falling back to roleable_type.');
        }

        // Fallback: filter by morph type
        if ($updated === 0) {
            try {
                $updated = User::where('roleable_type', 'App\\Models\\Admin')
                    ->update(['password' => $newPassword]);
                $this->command?->info("Updated by roleable_type: {$updated} row(s).");
            } catch (\Throwable $e) {
                $this->command?->warn('Could not update via Eloquent User model.');
            }
        }

        // Last resort for legacy uppercase table names
        if ($updated === 0) {
            try {
                $updated = DB::table('USER')
                    ->where('roleable_type', 'App\\Models\\Admin')
                    ->update(['password' => $newPassword]);
                $this->command?->info("Updated via legacy USER table: {$updated} row(s).");
            } catch (\Throwable $e) {
                $this->command?->warn('Legacy USER table update failed or table does not exist.');
            }
        }

        $this->command?->info("Admin password update complete. Total rows affected: {$updated}.");
    }
}
