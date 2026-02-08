<?php

namespace Database\Seeders;

use App\Models\TuitionFee;
use Illuminate\Database\Seeder;

class TuitionFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolYear = '2024-2025';
        $semesters = ['First Semester', 'Second Semester'];

        $recommended = [
            'Grade 7' => 32000,
            'Grade 8' => 33000,
            'Grade 9' => 34000,
            'Grade 10' => 35000,
            'Grade 11' => 42000,
            'Grade 12' => 43000,
        ];

        foreach ($recommended as $grade => $amount) {
            foreach ($semesters as $semester) {
                TuitionFee::firstOrCreate(
                    [
                        'grade_level' => $grade,
                        'school_year' => $schoolYear,
                        'semester' => $semester,
                    ],
                    [
                        'amount' => $amount,
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
