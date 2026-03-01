<?php

namespace App\Services;

use App\Models\Staff;
use App\Models\Student;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SchoolYearUpdateService
{
    /**
     * Update staff and students when school year changes
     */
    public static function updateSchoolYear(string $newSchoolYear, string $oldSchoolYear = null): array
    {
        $results = [
            'staff_updated' => 0,
            'students_updated' => 0,
            'students_preserved' => 0,
            'errors' => []
        ];

        try {
            // Update all active staff to the new school year
            $staffUpdated = Staff::query()->update(['school_year' => $newSchoolYear]);
            $results['staff_updated'] = $staffUpdated;

            Log::info('Staff school year updated', [
                'new_school_year' => $newSchoolYear,
                'staff_count' => $staffUpdated
            ]);

            // DO NOT UPDATE STUDENTS - Preserve their original enrollment school year
            // Students should remain in their original school year for record-keeping
            $totalStudents = Student::count();
            $results['students_preserved'] = $totalStudents;

            Log::info('Students school year preserved', [
                'total_students' => $totalStudents,
                'new_school_year' => $newSchoolYear,
                'note' => 'Students remain in their original enrollment year'
            ]);

        } catch (\Exception $e) {
            Log::error('School year update failed', [
                'error' => $e->getMessage(),
                'new_school_year' => $newSchoolYear,
                'old_school_year' => $oldSchoolYear
            ]);
            
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Get current school year from system settings
     */
    public static function getCurrentSchoolYear(): ?string
    {
        return SystemSetting::where('key', 'school_year')->value('value');
    }

    /**
     * Check if school year has changed and trigger updates
     */
    public static function handleSchoolYearChange(string $newSchoolYear): array
    {
        $currentSchoolYear = self::getCurrentSchoolYear();
        
        // Only update if the school year is actually changing
        if ($currentSchoolYear === $newSchoolYear) {
            return [
                'staff_updated' => 0,
                'students_updated' => 0,
                'errors' => ['School year is already set to ' . $newSchoolYear]
            ];
        }

        return self::updateSchoolYear($newSchoolYear, $currentSchoolYear);
    }
}
