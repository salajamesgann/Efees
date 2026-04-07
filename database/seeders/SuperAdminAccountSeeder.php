<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminAccountSeeder extends Seeder
{
    public function run()
    {
        $email = 'superadmin@gmail.com';
        
        // 1. Ensure the super_admin role exists
        $role = Role::firstOrCreate(['role_name' => 'super_admin']);
        
        // 2. Create the Admin profile if it doesn't exist
        $admin = Admin::where('admin_id', 'SA-001')->first();
        if (!$admin) {
            $admin = Admin::create([
                'admin_id' => 'SA-001',
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'contact_number' => '00000000000',
                'department' => 'System',
                'position' => 'Super Admin',
            ]);
        }
        
        // 3. Create the User account if it doesn't exist
        $user = User::where('email', $email)->first();
        if (!$user) {
            User::create([
                'email' => $email,
                'password' => Hash::make('admin123'),
                'role_id' => $role->role_id,
                'roleable_type' => Admin::class,
                'roleable_id' => $admin->admin_id,
            ]);
        } else {
            // Update password if user exists
            $user->update([
                'password' => Hash::make('admin123'),
                'role_id' => $role->role_id,
                'roleable_type' => Admin::class,
                'roleable_id' => $admin->admin_id,
            ]);
        }
    }
}
