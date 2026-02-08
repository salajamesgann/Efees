<?php

namespace Database\Seeders;

use App\Models\FeePeriod;
use Illuminate\Database\Seeder;

class FeePeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schoolYears = config('fees.school_years', []);
        $semesters = config('fees.semesters', []);

        foreach ($schoolYears as $index => $label) {
            FeePeriod::updateOrCreate(
                ['type' => FeePeriod::TYPE_SCHOOL_YEAR, 'label' => $label],
                ['sort_order' => $index, 'is_active' => true]
            );
        }

        foreach ($semesters as $index => $label) {
            FeePeriod::updateOrCreate(
                ['type' => FeePeriod::TYPE_SEMESTER, 'label' => $label],
                ['sort_order' => $index, 'is_active' => true]
            );
        }
    }
}
