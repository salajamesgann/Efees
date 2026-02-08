<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckStaffCreation extends Command
{
    protected $signature = 'mcp:check-staff';

    protected $description = 'Attempt to create a staff account to verify database constraints; cleans up afterward';

    public function handle(): int
    {
        $email = 'staff_check_'.uniqid().'@example.com';
        $this->info('Checking staff creation workflow...');

        try {
            DB::beginTransaction();

            $roleId = Role::where('role_name', 'staff')->value('role_id');
            if (! $roleId) {
                $roleId = Role::insertGetId([
                    'role_name' => 'staff',
                    'description' => 'Staff',
                ]);
                $this->line("Created missing 'staff' role with role_id={$roleId}");
            }

            $staff = Staff::createWithAccount([
                'first_name' => 'Check',
                'MI' => 'C',
                'last_name' => 'Worker',
                'contact_number' => '',
                'department' => 'General',
                'position' => 'Staff',
                'is_active' => true,
            ], [
                'email' => $email,
                'password' => 'Aa1!pass_test',
                'role_id' => $roleId,
            ]);

            $user = User::where('roleable_type', Staff::class)
                ->where('roleable_id', $staff->staff_id)
                ->where('email', $email)
                ->first();

            if (! $user) {
                DB::rollBack();
                $this->error('User record not created for staff.');

                return self::FAILURE;
            }

            DB::rollBack();
            $this->info('Staff creation passed and transaction rolled back successfully.');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Staff creation failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
