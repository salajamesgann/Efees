<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
<<<<<<< HEAD
            AdminPasswordSeeder::class,
            StaffPasswordSeeder::class,
            TuitionFeeSeeder::class,
            FeePeriodSeeder::class,
            SystemSettingSeeder::class,
=======
            StudentSeeder::class,
            AdminPasswordSeeder::class,
            StaffPasswordSeeder::class,
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
        ]);
    }
}
