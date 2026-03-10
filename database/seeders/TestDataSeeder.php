<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\Student;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(20)->create()->each(function($user) {
            if ($user->role->role_name === 'staff') {
                $user->roleable()->save(Staff::factory()->make());
            } elseif ($user->role->role_name === 'admin') {
                $user->roleable()->save(Admin::factory()->make());
            }
        });

        Student::factory()->count(10)->create(['level' => 'Grade 5']);
        Student::factory()->count(5)->create(['level' => 'Grade 10']);
    }
}
