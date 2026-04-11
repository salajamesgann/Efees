<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\SystemSetting;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BulkOperationsController extends Controller
{
    private const LEVELS = [
        'Grade 1',
        'Grade 2',
        'Grade 3',
        'Grade 4',
        'Grade 5',
        'Grade 6',
        'Grade 7',
        'Grade 8',
        'Grade 9',
        'Grade 10',
        'Grade 11',
        'Grade 12',
    ];

    public function index(): View
    {
        $stats = [
            'total_students' => Student::count(),
            'active_students' => Student::where('enrollment_status', 'Enrolled')->count(),
            'graduating_students' => Student::whereIn('level', ['Grade 6', 'Grade 10', 'Grade 12'])->count(),
            'graduated_students' => Student::where('enrollment_status', 'Graduated')->count(),
            'withdrawn_students' => Student::where('enrollment_status', 'Withdrawn')->count(),
            'dropped_students' => Student::where('enrollment_status', 'Dropped')->count(),
        ];

        $activeSY = SystemSetting::getActiveSchoolYear();

        $levels = collect(self::LEVELS)->values();

        $schoolYears = Student::select('school_year')
            ->whereNotNull('school_year')
            ->distinct()
            ->pluck('school_year')
            ->map(fn ($sy) => trim((string) $sy))
            ->filter(function ($sy) {
                if (! preg_match('/^(\d{4})-(\d{4})$/', $sy, $matches)) {
                    return false;
                }

                return ((int) $matches[2]) === (((int) $matches[1]) + 1);
            })
            ->unique()
            ->sortBy(fn ($sy) => (int) substr($sy, 0, 4))
            ->values();

        if ($activeSY && preg_match('/^(\d{4})-(\d{4})$/', $activeSY, $matches) && ((int) $matches[2]) === (((int) $matches[1]) + 1) && ! $schoolYears->contains($activeSY)) {
            $schoolYears = $schoolYears
                ->push($activeSY)
                ->sortBy(fn ($sy) => (int) substr($sy, 0, 4))
                ->values();
        }

        return view('super_admin.bulk_operations', compact('stats', 'levels', 'schoolYears', 'activeSY'));
    }

    public function promote(Request $request)
    {
        $request->validate([
            'from_level' => ['required', 'string', Rule::in(self::LEVELS)],
            'to_level' => ['required', 'string', Rule::in(self::LEVELS)],
            'school_year' => [
                'required',
                'regex:/^\d{4}-\d{4}$/',
                function (string $attribute, string $value, \Closure $fail): void {
                    if (! $this->isValidSchoolYear($value)) {
                        $fail('The '.$attribute.' must be in YYYY-YYYY format with consecutive years.');
                    }
                },
            ],
            'target_school_year' => [
                'required',
                'regex:/^\d{4}-\d{4}$/',
                function (string $attribute, string $value, \Closure $fail): void {
                    if (! $this->isValidSchoolYear($value)) {
                        $fail('The '.$attribute.' must be in YYYY-YYYY format with consecutive years.');
                    }
                },
            ],
            'clear_sections' => 'nullable|string',
            'preview' => 'nullable|boolean',
        ]);

        $from = $request->from_level;
        $to = $request->to_level;
        $sy = $request->school_year;
        $targetSy = $request->target_school_year;
        $clearSections = $request->has('clear_sections');

        if ($from === $to && $sy === $targetSy) {
            return back()->withInput()->with('error', 'Source and target are identical. Choose a different level or school year.');
        }

        $nonPromotableStatuses = ['Withdrawn', 'Archived', 'Graduated', 'Dropped'];

        if ($request->boolean('preview')) {
            $count = Student::where('level', $from)
                ->where('school_year', $sy)
                ->where(function ($query) use ($nonPromotableStatuses) {
                    $query->whereNull('enrollment_status')
                        ->orWhereNotIn('enrollment_status', $nonPromotableStatuses);
                })
                ->count();

            return back()->withInput()->with('warning', "Preview: {$count} promotable students will be moved from {$from} ({$sy}) to {$to} ({$targetSy}).");
        }

        try {
            $count = DB::transaction(function () use ($from, $to, $sy, $targetSy, $clearSections, $nonPromotableStatuses) {
                $students = Student::where('level', $from)
                    ->where('school_year', $sy)
                    ->where(function ($query) use ($nonPromotableStatuses) {
                        $query->whereNull('enrollment_status')
                            ->orWhereNotIn('enrollment_status', $nonPromotableStatuses);
                    })
                    ->get();

                foreach ($students as $student) {
                    $updateData = [
                        'level' => $to,
                        'school_year' => $targetSy
                    ];

                    if ($clearSections) {
                        $updateData['section'] = null;
                    }

                    // Reset strand if moving from SHS to non-SHS (unlikely but safe)
                    // Or reset strand if moving into SHS (needs re-selection)
                    if (!in_array($to, ['Grade 11', 'Grade 12'])) {
                        $updateData['strand'] = null;
                    }

                    $student->update($updateData);
                }
                return $students->count();
            });

            AuditService::log('Batch Promotion', null, "Promoted {$count} students from {$from} ({$sy}) to {$to} ({$targetSy})");

            if ($count === 0) {
                return back()->with('warning', 'No promotable students matched the selected level and school year.');
            }

            return redirect()->route('super_admin.students.index', [
                'level' => $to,
                'school_year' => $targetSy
            ])->with('success', "Successfully promoted {$count} students to {$to} for SY {$targetSy}. You are now viewing the promoted students in Student Management.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to promote students: ' . $e->getMessage());
        }
    }

    public function statusUpdate(Request $request)
    {
        $request->validate([
            'level' => ['required', 'string', Rule::in(self::LEVELS)],
            'school_year' => [
                'required',
                'regex:/^\d{4}-\d{4}$/',
                function (string $attribute, string $value, \Closure $fail): void {
                    if (! $this->isValidSchoolYear($value)) {
                        $fail('The '.$attribute.' must be in YYYY-YYYY format with consecutive years.');
                    }
                },
            ],
            'new_status' => ['required', 'string', Rule::in(['Enrolled', 'Withdrawn', 'Graduated', 'Dropped'])],
            'preview' => 'nullable|boolean',
        ]);

        $level = $request->level;
        $sy = $request->school_year;
        $status = $request->new_status;

        if ($request->boolean('preview')) {
            $count = Student::where('level', $level)
                ->where('school_year', $sy)
                ->count();

            return back()->withInput()->with('warning', "Preview: {$count} students in {$level} ({$sy}) will be updated to status '{$status}'.");
        }

        try {
            $count = DB::transaction(function () use ($level, $sy, $status) {
                $students = Student::where('level', $level)
                    ->where('school_year', $sy)
                    ->get();

                foreach ($students as $student) {
                    $student->update(['enrollment_status' => $status]);
                }
                return $students->count();
            });

            AuditService::log('Bulk Status Update', null, "Updated {$count} students in {$level} ({$sy}) to status: {$status}");

            if ($count === 0) {
                return back()->with('warning', 'No students matched the selected level and school year.');
            }

            return back()->with('success', "Successfully updated {$count} students to '{$status}'. Page stats will refresh automatically.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    public function archive(Request $request)
    {
        $request->validate([
            'level' => ['nullable', 'string', Rule::in(self::LEVELS)],
            'status' => ['nullable', 'string', Rule::in(['Active', 'Enrolled', 'Irregular', 'Withdrawn', 'Graduated', 'Dropped', 'Archived'])],
            'school_year' => [
                'required',
                'regex:/^\d{4}-\d{4}$/',
                function (string $attribute, string $value, \Closure $fail): void {
                    if (! $this->isValidSchoolYear($value)) {
                        $fail('The '.$attribute.' must be in YYYY-YYYY format with consecutive years.');
                    }
                },
            ],
            'confirm_archive_all' => 'nullable|accepted',
            'preview' => 'nullable|boolean',
        ]);

        $isArchiveAllForYear = ! $request->filled('level') && ! $request->filled('status');
        if ($isArchiveAllForYear && ! $request->boolean('confirm_archive_all')) {
            return back()->withInput()->with('error', 'Archive-all for the selected school year requires explicit confirmation. Tick the checkbox and try again.');
        }

        $query = Student::query();
        if ($request->filled('level')) $query->where('level', $request->level);
        if ($request->filled('status')) $query->where('enrollment_status', $request->status);
        $query->where('school_year', $request->school_year);

        if ($request->boolean('preview')) {
            $count = (clone $query)->count();
            $scope = $isArchiveAllForYear ? 'all students for the selected school year' : 'students matching current filters';

            return back()->withInput()->with('warning', "Preview: {$count} {$scope} will be archived.");
        }

        try {
            $count = DB::transaction(function () use ($query) {
                $students = $query->get();
                foreach ($students as $student) {
                    // Set status to Archived so archived students are visible in Student Management
                    $student->update(['enrollment_status' => 'Archived']);
                }
                return $students->count();
            });

            if ($count === 0) {
                return back()->with('warning', 'No students matched the selected filters. Try Status: All Statuses or Active.');
            }

            AuditService::log('Bulk Archiving', null, "Archived {$count} students" . ($request->filled('school_year') ? " from SY {$request->school_year}" : ""));

            return back()->with('success', "Successfully archived {$count} students.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to archive students: ' . $e->getMessage());
        }
    }

    private function isValidSchoolYear(string $value): bool
    {
        if (! preg_match('/^(\d{4})-(\d{4})$/', $value, $matches)) {
            return false;
        }

        return ((int) $matches[2]) === (((int) $matches[1]) + 1);
    }
}
