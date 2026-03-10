<?php

namespace App\Http\Controllers;

use App\Models\FeeAssignment;
use App\Models\Section;
use App\Models\Strand;
use App\Models\Student;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\AuditService;
use App\Services\FeeManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminStudentController extends Controller
{
    /**
     * Show the form for creating a new student.
     */
    public function create(): View
    {
        // Fetch all parents for the search dropdown
        $existingParents = \App\Models\ParentContact::select('id', 'full_name', 'phone')
            ->orderBy('full_name')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'label' => $p->full_name.' ('.$p->phone.')',
                    'phone' => $p->phone,
                ];
            });

        return view('auth.admin_students_create', [
            'existingParents' => $existingParents,
        ]);
    }

    /**
     * Search students for autocomplete.
     */
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (empty($q)) {
            return response()->json([]);
        }

        $students = Student::where(function ($query) use ($q) {
            $driver = DB::connection()->getDriverName();
            $concat = $driver === 'sqlite' ? "first_name || ' ' || last_name" : "CONCAT(first_name, ' ', last_name)";
            $operator = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';

            $query->where('student_id', $operator, "%{$q}%")
                ->orWhere('first_name', $operator, "%{$q}%")
                ->orWhere('last_name', $operator, "%{$q}%")
                ->orWhereRaw("{$concat} {$operator} ?", ["%{$q}%"]);
        })
            ->select('student_id', 'first_name', 'last_name', 'level', 'section', 'school_year')
            ->limit(10)
            ->get();

        return response()->json($students);
    }

    /**
     * List sections by grade level (and optional strand) for enrollment form.
     */
    public function sectionsList(Request $request)
    {
        $level = $request->query('level');
        $strandName = $request->query('strand');

        if (! $level) {
            return response()->json([]);
        }

        $query = Section::where('level', $level);
        if (in_array($level, ['Grade 11', 'Grade 12'], true) && $strandName) {
            $strand = Strand::where('name', $strandName)->first();
            if ($strand) {
                $query->where('strand_id', $strand->id);
            }
        }

        $sections = $query->orderBy('name')->pluck('name');

        return response()->json($sections);
    }

    /**
     * Show Manage Students page with list and create/edit form.
     */
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $viewAll = $request->boolean('view_all');
        $selectedId = $request->query('id');
        $isCreating = $request->boolean('create');
        $statusFilter = trim((string) $request->query('status', ''));

        // Context Variables
        $level = $request->query('level');
        $section = $request->query('section');
        $strandName = $request->query('strand'); // 'strand' URL param holds the strand name (e.g. STEM)

        // School Year Logic
        $schoolYears = Student::distinct()->whereNotNull('school_year')->orderBy('school_year', 'desc')->pluck('school_year');

        // Active School Year from Settings
        $activeSy = SystemSetting::getActiveSchoolYear();

        // Ensure Active SY is in the list if set
        if ($activeSy && ! $schoolYears->contains($activeSy)) {
            $schoolYears->prepend($activeSy);
        }

        if ($schoolYears->isEmpty()) {
            // Default list if DB is empty and no active SY
            $currentYear = date('Y');
            $schoolYears = collect([
                ($currentYear).'-'.($currentYear + 1),
                ($currentYear - 1).'-'.($currentYear),
            ]);
        }

        // Default to Active School Year, fallback to first in list
        $defaultSy = ($activeSy && $schoolYears->contains($activeSy)) ? $activeSy : $schoolYears->first();

        $currentSchoolYear = $request->query('school_year', $defaultSy);
        $isReadOnly = ($activeSy && $currentSchoolYear !== $activeSy);
        
        // Always filter by the current school year
        $selectedSchoolYear = $currentSchoolYear;

        $selectedStudent = null;
        $recentActivity = [];

        // Fetch all parents for the search dropdown
        $existingParents = \App\Models\ParentContact::select('id', 'full_name', 'phone')
            ->orderBy('full_name')
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'label' => $p->full_name.' ('.$p->phone.')',
                    'phone' => $p->phone,
                ];
            });

        if ($selectedId && ! $isCreating) {
            $selectedId = trim($selectedId);
            $selectedStudent = Student::with(['parents', 'feeRecords', 'payments'])
                ->where('student_id', $selectedId)
                ->first();

            // Auto-set context from selected student
            if ($selectedStudent) {
                $level = $selectedStudent->level;
                $section = $selectedStudent->section;
                $currentSchoolYear = $selectedStudent->school_year; // Force context match
                // We might want to set strand here if student has one, but Student model might not have strand relation yet or it's just a string.
                // Assuming strand is stored in student record or inferred. For now, let's keep it simple.

                // Fetch recent activity (Audit Logs)
                try {
                    $recentActivity = \App\Models\AuditLog::where(function ($query) use ($selectedStudent) {
                        $query->where('model_type', Student::class)
                            ->where('model_id', $selectedStudent->id);
                    })
                        ->orWhere('details', 'like', "%{$selectedStudent->student_id}%")
                        ->latest()
                        ->take(5)
                        ->get();
                } catch (\Exception $e) {
                    $recentActivity = [];
                }
            }
        }

        // Fetch available discounts and fee assignment if student is selected
        $availableDiscounts = collect();
        $availableCharges = collect();
        $feeAdjustments = collect();
        $feeAssignment = null;
        $paidAmount = 0;
        if ($selectedStudent) {
            app(FeeManagementService::class)->recomputeStudentLedger($selectedStudent);

            $feeAssignment = $selectedStudent->getCurrentFeeAssignment();
            if (! $feeAssignment) {
                $feeAssignment = FeeAssignment::assignForStudent($selectedStudent->student_id, $currentSchoolYear, 'N/A');
                if ($feeAssignment) {
                    $feeAssignment->calculateTotal();
                    app(FeeManagementService::class)->recomputeStudentLedger($selectedStudent);
                }
            }

            if ($feeAssignment) {
                $feeAssignment->load(['tuitionFee', 'additionalCharges', 'discounts', 'adjustments']);
            }

            $availableDiscounts = \App\Models\Discount::active()
                ->applicableToGrade($selectedStudent->level)
                ->get()
                ->filter(function ($discount) use ($selectedStudent) {
                    return $discount->isEligibleForStudent($selectedStudent) && $discount->isCurrentlyValid();
                });

            // Filter out already-applied discounts
            if ($feeAssignment) {
                $appliedDiscountIds = $feeAssignment->discounts->pluck('id')->toArray();
                $availableDiscounts = $availableDiscounts->reject(function ($d) use ($appliedDiscountIds) {
                    return in_array($d->id, $appliedDiscountIds);
                });
            }

            // Available additional charges (active, applicable to grade, not already attached)
            $availableCharges = \App\Models\AdditionalCharge::active()
                ->applicableToGrade($selectedStudent->level)
                ->get();
            if ($feeAssignment) {
                $appliedChargeIds = $feeAssignment->additionalCharges->pluck('id')->toArray();
                $availableCharges = $availableCharges->reject(function ($c) use ($appliedChargeIds) {
                    return in_array($c->id, $appliedChargeIds);
                });
            }

            // Fee adjustments
            if ($feeAssignment) {
                $feeAdjustments = $feeAssignment->adjustments()->orderByDesc('created_at')->get();
            }

            // Total paid
            $paidAmount = (float) \App\Models\Payment::where('student_id', $selectedStudent->student_id)
                ->whereIn('status', ['approved', 'paid'])
                ->sum('amount_paid');

            // Fee history / changelog from audit logs
            $feeHistory = \App\Models\AuditLog::where('model_type', 'App\\Models\\Student')
                ->where('model_id', $selectedStudent->student_id)
                ->whereIn('action', [
                    'Charge Added',
                    'Charge Removed',
                    'Discount Assigned',
                    'Discount Removed',
                    'Fee Adjustment Applied',
                    'Fees Recalculated',
                    'DISCOUNT_SYNC_FAILED',
                    'DISCOUNT_CALCULATION_FAILED',
                    'Family Fee Recalculation Failed',
                    'Student Enrolled',
                    'Fee Assignment Created',
                    'Payment Added',
                    'Payment Approved',
                ])
                ->with('user')
                ->orderByDesc('created_at')
                ->limit(50)
                ->get();
        }

        // Determine View State and Data
        $viewState = 'levels';
        $levels = collect();
        $sections = collect();
        $strands = collect();
        $students = null; // Pagination object for students list

        // State Logic
        if ($q !== '' || ($level && $section) || $viewAll) {
            $viewState = 'students';

            $studentsQuery = Student::with(['parents'])
                ->where('school_year', $selectedSchoolYear)
                ->when($level, function ($query) use ($level) {
                    $query->where('level', $level);
                })
                ->when($section, function ($query) use ($section) {
                    $query->where('section', $section);
                })
                ->when($statusFilter !== '', function ($query) use ($statusFilter) {
                    $query->whereRaw('LOWER(enrollment_status) = ?', [strtolower($statusFilter)]);
                })
                ->when($q !== '', function ($query) use ($q) {
                    $query->where(function ($qq) use ($q) {
                        $driver = DB::connection()->getDriverName();
                        $concat = $driver === 'sqlite' ? "first_name || ' ' || last_name" : "CONCAT(first_name, ' ', last_name)";
                        $operator = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';

                        $qq->where('student_id', $operator, "%{$q}%")
                            ->orWhere('first_name', $operator, "%{$q}%")
                            ->orWhere('last_name', $operator, "%{$q}%")
                            ->orWhereRaw("{$concat} {$operator} ?", ["%{$q}%"]);
                    });
                })
                ->orderBy('last_name', 'asc');

            $students = $studentsQuery->paginate(15)->withQueryString();
        } elseif ($level) {
            // Check if level is Grade 11 or 12
            $isSHS = in_array($level, ['Grade 11', 'Grade 12']);

            if ($isSHS && ! $strandName) {
                $viewState = 'strands';
                $strands = Strand::orderBy('name')->get();
            } else {
                $viewState = 'sections';

                $sectionsQuery = Section::where('level', $level);

                if ($isSHS && $strandName) {
                    $strand = Strand::where('name', $strandName)->first();
                    if ($strand) {
                        $sectionsQuery->where('strand_id', $strand->id);
                    }
                }

                $sections = $sectionsQuery->orderBy('name')->get();
            }
        } else {
            $viewState = 'levels';
            // Always show all grade levels regardless of enrollment
            $levels = collect(['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12']);
        }

        // Compute next grade level and per-section student counts for the Promote Section feature
        $gradeOrder = ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6',
                       'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        $nextLevel = null;
        $sectionStudentCounts = [];      // all students (for section card display)
        $sectionPromotableCounts = [];   // Active + Irregular only (for promote modal)
        if ($viewState === 'sections' && $level) {
            $pos = array_search($level, $gradeOrder, true);
            $nextLevel = ($pos !== false && $pos < count($gradeOrder) - 1) ? $gradeOrder[$pos + 1] : null;
            $sectionNames = $sections->pluck('name');
            $sectionStudentCounts = Student::where('school_year', $selectedSchoolYear)
                ->where('level', $level)
                ->whereIn('section', $sectionNames)
                ->selectRaw('section, count(*) as cnt')
                ->groupBy('section')
                ->pluck('cnt', 'section')
                ->toArray();
            // Only Active/Irregular students are eligible for promotion
            $sectionPromotableCounts = Student::where('school_year', $selectedSchoolYear)
                ->where('level', $level)
                ->whereIn('section', $sectionNames)
                ->whereNotIn('enrollment_status', ['Withdrawn', 'Archived'])
                ->selectRaw('section, count(*) as cnt')
                ->groupBy('section')
                ->pluck('cnt', 'section')
                ->toArray();
        }

        // Count students scoped to the selected school year only (not all-time)
        $totalStudents = Student::where('school_year', $selectedSchoolYear)->count();

        // Per-level student counts for the levels view
        $levelStudentCounts = Student::where('school_year', $selectedSchoolYear)
            ->selectRaw('level, count(*) as cnt')
            ->groupBy('level')
            ->pluck('cnt', 'level')
            ->toArray();

        return view('auth.admin_students_index', [
            'students' => $students,
            'q' => $q,
            'totalStudents' => $totalStudents,
            'levelStudentCounts' => $levelStudentCounts,
            'selectedStudent' => $selectedStudent,
            'isCreating' => $isCreating,
            'recentActivity' => $recentActivity,
            'existingParents' => $existingParents,
            'viewState' => $viewState,
            'levels' => $levels,
            'strands' => $strands,
            'sections' => $sections,
            'currentLevel' => $level,
            'currentStrand' => $strandName,
            'currentSection' => $section,
            'schoolYears' => $schoolYears,
            'currentSchoolYear' => $currentSchoolYear,
            'activeSy' => $activeSy,
            'availableDiscounts'    => $availableDiscounts,
            'availableCharges'      => $availableCharges,
            'feeAdjustments'        => $feeAdjustments,
            'feeAssignment'         => $feeAssignment,
            'paidAmount'            => $paidAmount,
            'feeHistory'            => $feeHistory ?? collect(),
            'isReadOnly'            => $isReadOnly,
            'nextLevel'              => $nextLevel,
            'sectionStudentCounts'   => $sectionStudentCounts,
            'sectionPromotableCounts' => $sectionPromotableCounts,
            'statusFilter'           => $statusFilter,
        ]);
    }

    public function storeSection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:255'],
            'strand' => ['nullable', 'string', 'exists:strands,name'],
            'shs_voucher_type' => ['nullable', 'string', 'in:regular,shs_voucher'],
        ]);

        $strandId = null;
        if (! empty($validated['strand'])) {
            $strand = Strand::where('name', $validated['strand'])->first();
            $strandId = $strand ? $strand->id : null;
        }

        $section = Section::firstOrCreate(
            [
                'name' => $validated['name'],
                'level' => $validated['level'],
            ],
            [
                'strand_id' => $strandId,
            ]
        );
        if ($section->strand_id !== $strandId) {
            $section->strand_id = $strandId;
            $section->save();
        }

        $redirectParams = ['level' => $validated['level']];
        if (! empty($validated['strand'])) {
            $redirectParams['strand'] = $validated['strand'];
        }

        return redirect()->route('super_admin.students.index', $redirectParams)
            ->with('success', 'Section added successfully.');
    }

    public function storeStrand(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:strands,name'],
            'level' => ['required', 'string'], // pass level back for redirect
        ]);

        Strand::create([
            'name' => $validated['name'],
        ]);

        return redirect()->route('super_admin.students.index', ['level' => $validated['level']])
            ->with('success', 'Strand added successfully.');
    }

    public function destroySection(Request $request, Section $section): RedirectResponse
    {
        // Check if section has students
        $studentCount = Student::where('section', $section->name)
            ->where('level', $section->level)
            ->count();

        if ($studentCount > 0) {
            return back()->with('error', "Cannot delete section '{$section->name}' because it has {$studentCount} enrolled students.");
        }

        $section->delete();

        return redirect()->route('super_admin.students.index', [
            'level' => $section->level,
            'strand' => $request->input('strand'),
        ])->with('success', "Section '{$section->name}' deleted successfully.");
    }

    /**
     * Promote all students in a section to the next grade level (year-end carry-forward).
     */
    public function promoteSection(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'level'           => ['required', 'string'],
            'section'         => ['required', 'string'],
            'school_year'     => ['required', 'string'],
            'new_school_year' => ['required', 'string'],
            'keep_section'    => ['nullable', 'string'],
        ]);

        $gradeOrder = ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6',
                       'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        $pos = array_search($validated['level'], $gradeOrder, true);

        if ($pos === false || $pos >= count($gradeOrder) - 1) {
            return back()->with('error', 'Grade 12 students cannot be promoted further — consider archiving graduates instead.');
        }

        $nextLevel    = $gradeOrder[$pos + 1];
        $keepSection  = $validated['keep_section'] === '1';
        $newSy        = $validated['new_school_year'];

        $students = Student::where('school_year', $validated['school_year'])
            ->where('level', $validated['level'])
            ->where('section', $validated['section'])
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', "No students found in {$validated['level']} – {$validated['section']} for {$validated['school_year']}.");
        }

        // Skip Withdrawn / Archived — only promote Active and Irregular students
        $eligible = $students->whereNotIn('enrollment_status', ['Withdrawn', 'Archived']);
        $skipped  = $students->count() - $eligible->count();

        if ($eligible->isEmpty()) {
            return back()->with('error', "All {$students->count()} student(s) in this section are Withdrawn or Archived and cannot be promoted.");
        }

        $count = 0;
        foreach ($eligible as $student) {
            $student->update([
                'level'       => $nextLevel,
                'school_year' => $newSy,
                'section'     => $keepSection ? $student->section : null,
            ]);
            $count++;
        }

        AuditService::log(
            'Section Promoted',
            null,
            "Promoted {$count} student(s) from {$validated['level']} – {$validated['section']} ({$validated['school_year']}) → {$nextLevel}" .
                ($keepSection ? " – {$validated['section']}" : ' (section cleared)') . " ({$newSy})" .
                ($skipped > 0 ? "; {$skipped} Withdrawn/Archived skipped" : ''),
            ['level' => $validated['level'], 'section' => $validated['section'], 'school_year' => $validated['school_year']],
            ['level' => $nextLevel, 'school_year' => $newSy, 'keep_section' => $keepSection, 'skipped' => $skipped]
        );

        $redirectParams = ['level' => $nextLevel, 'school_year' => $newSy];
        if ($keepSection) {
            $redirectParams['section'] = $validated['section'];
        }

        $skipNote = $skipped > 0 ? " {$skipped} Withdrawn/Archived student(s) were left in place." : '';

        return redirect()
            ->route('super_admin.students.index', $redirectParams)
            ->with('success', "Promoted {$count} student(s) from {$validated['level']} ({$validated['section']}) → {$nextLevel} for {$newSy}.{$skipNote}" .
                ($keepSection ? '' : ' Section assignment cleared — students can be re-assigned.'));
    }

    /**
     * Download a blank CSV template for bulk student import.
     */
    public function downloadImportTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="students_import_template.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () {
            $f = fopen('php://output', 'w');
            // BOM for Excel UTF-8 compatibility
            fputs($f, "\xEF\xBB\xBF");
            fputcsv($f, [
                'lrn', 'first_name', 'middle_name', 'last_name', 'suffix',
                'sex', 'date_of_birth', 'level', 'section', 'strand',
                'school_year', 'parent_name', 'parent_phone', 'parent_email',
                'relationship', 'shs_voucher',
            ]);
            // One sample row
            fputcsv($f, [
                '123456789012', 'Juan', 'Santos', 'Dela Cruz', '',
                'Male', '2010-06-15', 'Grade 7', 'Section A', '',
                date('Y').'-'.date('Y', strtotime('+1 year')),
                'Maria Dela Cruz', '09171234567', 'parent@example.com',
                'Mother', 'No',
            ]);
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import students in bulk from an uploaded CSV file.
     */
    public function importStudents(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file'   => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
            'school_year' => ['nullable', 'string', 'max:20'],
        ]);

        $activeSy = SystemSetting::getActiveSchoolYear();
        if (! $activeSy) {
            return back()->with('error', 'No active School Year is set. Please configure it first.');
        }

        $overrideSy    = $request->input('school_year', $activeSy) ?: $activeSy;
        $autoGenerate  = SystemSetting::where('key', 'auto_generate_fees_on_enrollment')->value('value');

        $file = $request->file('csv_file');
        $path = $file->getRealPath();

        // Strip BOM if present
        $raw = file_get_contents($path);
        if (str_starts_with($raw, "\xEF\xBB\xBF")) {
            $raw = substr($raw, 3);
        }
        $tmpPath = tempnam(sys_get_temp_dir(), 'efees_import_');
        file_put_contents($tmpPath, $raw);

        $handle = fopen($tmpPath, 'r');
        if (! $handle) {
            return back()->with('error', 'Could not read the uploaded file.');
        }

        $validGrades = [
            'Grade 1','Grade 2','Grade 3','Grade 4','Grade 5','Grade 6',
            'Grade 7','Grade 8','Grade 9','Grade 10','Grade 11','Grade 12',
        ];

        // Read and map header row
        $rawHeaders = fgetcsv($handle);
        if (! $rawHeaders) {
            fclose($handle);
            @unlink($tmpPath);
            return back()->with('error', 'The CSV file appears empty or has no header row.');
        }
        $headerMap = [];
        foreach ($rawHeaders as $idx => $col) {
            $headerMap[strtolower(trim($col))] = $idx;
        }

        $col = function (string $name, array $row) use ($headerMap): string {
            $idx = $headerMap[$name] ?? null;
            return $idx !== null && isset($row[$idx]) ? trim((string) $row[$idx]) : '';
        };

        $imported = 0;
        $skipped  = 0;
        $errors   = [];
        $rowNum   = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            // Skip entirely blank rows
            if (empty(array_filter($row, fn ($v) => trim($v) !== ''))) {
                continue;
            }

            $firstName  = $col('first_name', $row);
            $lastName   = $col('last_name', $row);
            $level      = $col('level', $row);
            $section    = $col('section', $row);
            $sex        = ucfirst(strtolower($col('sex', $row)));
            $dob        = $col('date_of_birth', $row) ?: null;
            $schoolYear = $col('school_year', $row) ?: $overrideSy;
            $strand     = $col('strand', $row) ?: null;
            $lrn        = $col('lrn', $row) ?: null;
            $middleName = $col('middle_name', $row) ?: null;
            $suffix     = $col('suffix', $row) ?: null;
            $parentName = $col('parent_name', $row);
            $parentPhone= $col('parent_phone', $row) ?: null;
            $parentEmail= $col('parent_email', $row) ?: null;
            $relationship = $col('relationship', $row) ?: 'Parent/Guardian';
            $shsVoucher = strtolower($col('shs_voucher', $row));
            $isVoucher  = in_array($shsVoucher, ['yes', 'y', '1', 'true'], true);

            // Required field validation
            $rowErrors = [];
            if (! $firstName)                        $rowErrors[] = 'first_name required';
            if (! $lastName)                         $rowErrors[] = 'last_name required';
            if (! in_array($level, $validGrades, true)) $rowErrors[] = "invalid level '{$level}'";
            if (! $section)                          $rowErrors[] = 'section required';
            if (! in_array($sex, ['Male','Female','Other'], true)) $rowErrors[] = "invalid sex '{$sex}'";

            if ($rowErrors) {
                $errors[] = "Row {$rowNum}: ".implode(', ', $rowErrors);
                $skipped++;
                continue;
            }

            // Skip duplicate: same name + school year
            $duplicate = Student::where('first_name', $firstName)
                ->where('last_name', $lastName)
                ->where('school_year', $schoolYear)
                ->exists();
            if ($duplicate) {
                $errors[] = "Row {$rowNum}: {$firstName} {$lastName} ({$schoolYear}) already enrolled — skipped.";
                $skipped++;
                continue;
            }

            try {
                $deferRecompute = null;
                DB::transaction(function () use (
                    $firstName, $middleName, $lastName, $suffix, $sex, $dob,
                    $level, $section, $schoolYear, $strand, $lrn,
                    $parentName, $parentPhone, $parentEmail, $relationship,
                    $isVoucher, $autoGenerate, &$deferRecompute
                ) {
                    $studentId = $this->generateUniqueStudentId();

                    $mi = null;
                    if ($middleName) {
                        $mi = strtoupper(substr($middleName, 0, 1));
                    }

                    $student = Student::create([
                        'student_id'       => $studentId,
                        'lrn'              => $lrn ?: null,
                        'first_name'       => $firstName,
                        'middle_name'      => $middleName,
                        'middle_initial'   => $mi,
                        'last_name'        => $lastName,
                        'suffix'           => $suffix,
                        'sex'              => $sex,
                        'date_of_birth'    => $dob,
                        'level'            => $level,
                        'section'          => $section,
                        'school_year'      => $schoolYear,
                        'strand'           => $strand,
                        'is_shs_voucher'   => $isVoucher,
                        'enrollment_status' => 'Active',
                    ]);

                    // Link or create parent
                    if ($parentPhone || $parentName) {
                        $parentContact = null;
                        if ($parentPhone) {
                            $parentContact = \App\Models\ParentContact::where('phone', $parentPhone)->first();
                        }
                        if (! $parentContact && $parentName) {
                            $parentContact = \App\Models\ParentContact::create([
                                'full_name'      => $parentName,
                                'phone'          => $parentPhone,
                                'email'          => $parentEmail ?: null,
                                'account_status' => 'Active',
                            ]);
                        }
                        if ($parentContact) {
                            $parentContact->students()->syncWithoutDetaching([
                                $student->student_id => [
                                    'relationship' => $relationship,
                                    'is_primary'   => true,
                                ],
                            ]);
                        }
                    }

                    if ($autoGenerate === '1') {
                        // Defer fee computation to after the transaction commits.
                        // PostgreSQL aborts the entire transaction if any inner query fails,
                        // so we keep only the enrollment data in the transaction.
                        $deferRecompute = $student;
                    }

                    AuditService::log(
                        'Student Imported',
                        $student,
                        "Bulk CSV import: {$student->full_name} ({$student->student_id})",
                        null,
                        $student->toArray()
                    );
                });

                // Run fee computation outside the transaction
                if (isset($deferRecompute)) {
                    try {
                        app(FeeManagementService::class)->recomputeStudentLedger($deferRecompute);
                    } catch (\Throwable $e) {
                        // Fee computation failed but student was imported
                    }
                    $deferRecompute = null;
                }

                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Row {$rowNum}: {$firstName} {$lastName} — ".$e->getMessage();
                $skipped++;
            }
        }

        fclose($handle);
        @unlink($tmpPath);

        $summary = "Import complete: {$imported} student(s) imported, {$skipped} skipped.";
        if ($errors) {
            $summary .= ' Issues: '.implode(' | ', array_slice($errors, 0, 10));
            if (count($errors) > 10) {
                $summary .= ' ...and '.(count($errors) - 10).' more.';
            }
        }

        $flashKey = $skipped > 0 && $imported === 0 ? 'error' : ($skipped > 0 ? 'warning' : 'success');

        return redirect()
            ->route('super_admin.students.index', ['school_year' => $overrideSy])
            ->with($flashKey, $summary);
    }

    /**
     * Export master list of students as CSV.
     */
    public function exportMasterList(Request $request)
    {
        $schoolYear = $request->query('school_year');
        $level = $request->query('level');
        $section = $request->query('section');
        $strand = $request->query('strand');

        $query = Student::query();

        if ($schoolYear) {
            $query->where('school_year', $schoolYear);
        }
        if ($level) {
            $query->where('level', $level);
        }
        if ($section) {
            $query->where('section', $section);
        }
        if ($strand) {
            $query->where('strand', $strand);
        }

        $students = $query->get();

        // Organize by: School Year, Year Level, Strand, Section, Name
        $students = $students->sort(function ($a, $b) {
            // 1. School Year (Descending - newest first)
            if ($a->school_year !== $b->school_year) {
                return $b->school_year <=> $a->school_year;
            }

            // 2. Year Level (Numeric sort)
            $levelA = (int) filter_var($a->level, FILTER_SANITIZE_NUMBER_INT);
            $levelB = (int) filter_var($b->level, FILTER_SANITIZE_NUMBER_INT);
            if ($levelA !== $levelB) {
                return $levelA <=> $levelB;
            }

            // 3. Strand (Alphabetical, empty/null last or first doesn't matter much, but usually grouped)
            $strandA = $a->strand ?? '';
            $strandB = $b->strand ?? '';
            if ($strandA !== $strandB) {
                return strcmp($strandA, $strandB);
            }

            // 4. Section (Alphabetical)
            if ($a->section !== $b->section) {
                return strcmp($a->section, $b->section);
            }

            // 5. Name (Last Name, then First Name)
            if ($a->last_name !== $b->last_name) {
                return strcmp($a->last_name, $b->last_name);
            }

            return strcmp($a->first_name, $b->first_name);
        });

        $filename = "master_list_{$schoolYear}_{$level}_{$section}.csv";
        // Clean filename
        $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$filename",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, ['School Year', 'Level', 'Strand', 'Section', 'Student Name', 'Student ID', 'Gender']);

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->school_year,
                    $student->level,
                    $student->strand,
                    $student->section,
                    $student->last_name.', '.$student->first_name.' '.($student->middle_initial ? $student->middle_initial.'.' : ''),
                    $student->student_id,
                    $student->sex,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Store a newly created Student.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Student Details
            'student_id'  => ['nullable', 'string', 'max:50', 'unique:students,student_id'],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:1'],
            'last_name' => ['required', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'sex' => ['required', 'string', 'in:Male,Female,Other'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'level' => ['required', 'string', 'max:255'],
            'section' => ['required', 'string', 'max:255'],
            'school_year' => ['required', 'string', 'max:20'],
            'strand' => ['nullable', 'string', 'exists:strands,name'],
            'shs_voucher_type' => ['nullable', 'string', 'in:regular,shs_voucher'],

            // Parent Linking
            'parent_mode' => ['required', 'in:new,existing'],

            // New Parent Fields
            'parent_guardian_name' => ['required_if:parent_mode,new', 'nullable', 'string', 'max:255'],
            'parent_contact_number' => ['required_if:parent_mode,new', 'nullable', 'string', 'min:11', 'max:11', 'unique:parents,phone'],
            'parent_email' => ['required_if:parent_mode,new', 'nullable', 'email', 'max:255'], // Required for password reset
            'parent_password' => ['nullable', 'string', 'min:6'], // Optional - will use email reset if empty
            'parent_address' => ['nullable', 'string', 'max:500'],
            'relationship' => ['required', 'string', 'max:50'],

            // Existing Parent Fields
            'existing_parent_id' => ['required_if:parent_mode,existing', 'nullable', 'exists:parents,id'],
        ]);

        if (in_array($validated['level'], ['Grade 11', 'Grade 12'], true) && empty($validated['strand'] ?? '')) {
            return back()
                ->withErrors(['strand' => 'Category is required for Grade 11 and Grade 12.'])
                ->withInput();
        }

        $activeYear = SystemSetting::getActiveSchoolYear();

        if (! $activeYear) {
            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Please set an active School Year to continue.');
        }

        if ($validated['school_year'] !== $activeYear) {
            return back()
                ->withErrors(['school_year' => 'School Year must match the active School Year ('.$activeYear.').'])
                ->withInput();
        }

        // Soft-check for duplicate: same first name + last name + school year (+ DOB if provided)
        if (! $request->boolean('force_save')) {
            $dupeQuery = Student::where('first_name', $validated['first_name'])
                ->where('last_name', $validated['last_name'])
                ->where('school_year', $validated['school_year']);

            if (! empty($validated['date_of_birth'])) {
                $dupeQuery->whereDate('date_of_birth', $validated['date_of_birth']);
            }

            $dupeMatch = $dupeQuery->first();

            if ($dupeMatch) {
                return back()
                    ->withInput()
                    ->with('duplicate_warning', [
                        'student_id'        => $dupeMatch->student_id,
                        'full_name'         => $dupeMatch->full_name,
                        'level'             => $dupeMatch->level,
                        'section'           => $dupeMatch->section,
                        'school_year'       => $dupeMatch->school_year,
                        'enrollment_status' => $dupeMatch->enrollment_status,
                        'date_of_birth'     => $dupeMatch->date_of_birth
                            ? \Carbon\Carbon::parse($dupeMatch->date_of_birth)->format('M d, Y')
                            : null,
                    ]);
            }
        }

        $autoGenerate = SystemSetting::where('key', 'auto_generate_fees_on_enrollment')->value('value');

        // Use admin-supplied ID if provided; otherwise auto-generate from configured format
        $studentId = ! empty($validated['student_id'])
            ? $validated['student_id']
            : $this->generateUniqueStudentId();
        $sendSms = $request->boolean('send_sms');

        $result = DB::transaction(function () use ($validated, $studentId, $autoGenerate) {
            $student = Student::create([
                'student_id' => $studentId,
                'first_name' => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'middle_initial' => $validated['middle_initial'] ?? null,
                'last_name' => $validated['last_name'],
                'suffix' => $validated['suffix'] ?? null,
                'sex' => $validated['sex'],
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'nationality' => $validated['nationality'] ?? null,
                'address' => $validated['address'] ?? null,
                'level' => $validated['level'],
                'section' => $validated['section'],
                'school_year' => $validated['school_year'],
                'strand' => $validated['strand'] ?? null,
                'is_shs_voucher' => (isset($validated['shs_voucher_type']) && $validated['shs_voucher_type'] === 'shs_voucher'),
                'enrollment_status' => 'Active', // Default
            ]);

            $parentContact = null;
            $isNewParent = false;
            $parentPassword = null;

            if ($validated['parent_mode'] === 'new') {
                $isNewParent = true;
                $parentName = $validated['parent_guardian_name'];
                $parentPhone = $validated['parent_contact_number'];
                $parentEmail = $validated['parent_email'] ?? null;

                $parentContact = \App\Models\ParentContact::create([
                    'full_name' => $parentName,
                    'phone' => $parentPhone,
                    'email' => $parentEmail,
                    'address_street' => $validated['parent_address'] ?? null,
                    'account_status' => 'Active',
                ]);

                // Create User Account for Parent
                $parentPassword = $validated['parent_password'] ?? null;
                $parentRole = \App\Models\Role::firstOrCreate(['role_name' => 'parent'], ['description' => 'Parent']);

                // Username is email if provided, otherwise phone (but email is preferred for login)
                $username = ! empty($parentEmail) ? $parentEmail : $parentPhone;

                // Check if user exists (shouldn't if validation passed, but safety check)
                if (! \App\Models\User::where('email', $username)->exists()) {
                    // Generate a random password if none provided
                    if (empty($parentPassword)) {
                        $parentPassword = Str::random(16); // Generate secure random password
                    }

                    $user = \App\Models\User::create([
                        'email' => $username, // Using email as username/email field for login
                        'password' => Hash::make($parentPassword),
                        'must_change_password' => empty($validated['parent_password']), // Force change if no password was provided by admin
                        'role_id' => $parentRole->role_id,
                        'roleable_type' => \App\Models\ParentContact::class,
                        'roleable_id' => (string) $parentContact->id,
                    ]);

                    // Send password reset email if no password was provided by admin and it's a Gmail address
                    if (empty($validated['parent_password']) && str_contains(strtolower($parentEmail), '@gmail.com')) {
                        try {
                            // Create password reset token
                            $token = \Illuminate\Support\Facades\Password::createToken($user);
                            
                            // Generate reset URL with production domain
                            $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);
                            
                            // Fix for production: Replace local URL with production URL
                            $resetUrl = str_replace('http://127.0.0.1:8000', 'https://efees.site', $resetUrl);
                            $resetUrl = str_replace('http://localhost', 'https://efees.site', $resetUrl);
                            
                            // Send professional email with reset link
                            \Illuminate\Support\Facades\Mail::send('auth.emails.parent-account-created', [
                                'parent' => $parentContact,
                                'user' => $user,
                                'resetUrl' => $resetUrl,
                            ], function ($message) use ($parentContact, $user) {
                                $message->to($user->email, $parentContact->full_name)
                                    ->subject('Your E-Fees Parent Account - Set Your Password');
                            });
                        } catch (\Throwable $e) {
                            // Continue if email fails - parent account still created
                        }
                    }
                }

            } else {
                // Existing Parent
                $parentContact = \App\Models\ParentContact::find($validated['existing_parent_id']);
            }

            if ($parentContact) {
                $parentContact->students()->syncWithoutDetaching([
                    $student->student_id => [
                        'relationship' => $validated['relationship'],
                        'is_primary' => true, // Default to true for enrollment
                    ],
                ]);
            }

            return [
                'student' => $student,
                'parentContact' => $parentContact,
                'parentPassword' => $parentPassword,
                'isNewParent' => $isNewParent,
                'autoGenerate' => $autoGenerate,
            ];
        });

        // Fee computation and audit logging run OUTSIDE the transaction.
        // PostgreSQL aborts the entire transaction on any failed query (even
        // if caught by PHP try/catch), so keeping fee recalculation inside
        // the enrollment transaction causes 25P02 cascade failures.
        if ($result['autoGenerate'] === '1') {
            try {
                app(FeeManagementService::class)->recomputeStudentLedger($result['student']);
            } catch (\Throwable $e) {
                // Fee computation failed but student is already enrolled.
                // Fees will be recalculated next time the student is viewed.
            }
        }

        try {
            AuditService::log(
                'Student Enrolled',
                $result['student'],
                "Enrolled student: {$result['student']->full_name} ({$result['student']->student_id})",
                null,
                $result['student']->toArray()
            );
        } catch (\Throwable $e) {
        }

        // 5. Send SMS (if new parent and toggle is on, and email wasn't used for password setup)
        if ($sendSms && $result['isNewParent'] && $result['parentContact'] && $result['parentContact']->phone) {
            // Only send SMS if no email was provided, or if email is not Gmail, or if password was provided
            $parentEmail = $result['parentContact']->email;
            $parentPassword = $result['parentPassword'];
            
            $shouldSendSms = empty($parentEmail) || 
                            !str_contains(strtolower($parentEmail), '@gmail.com') || 
                            !empty($parentPassword);
            
            if ($shouldSendSms) {
                try {
                    $msg = \App\Services\SmsTemplates::getNewAccountMessage($result['parentContact']->phone, $result['parentPassword']);
                    app(\App\Services\SmsService::class)->send($result['parentContact']->phone, $msg, $result['student']->student_id);
                } catch (\Exception $e) {
                    // Don't rollback for SMS failure
                }
            }
        }

        // Build success message
        $successMessage = 'Student enrolled successfully.';
        if ($result['isNewParent'] && $result['parentContact']) {
            $parentEmail = $result['parentContact']->email;
            $parentPassword = $result['parentPassword'];
            
            if (empty($parentPassword) && str_contains(strtolower($parentEmail), '@gmail.com')) {
                $successMessage .= ' A password setup email has been sent to the parent\'s Gmail address.';
            }
        }

        return redirect()
            ->route('super_admin.students.index', ['id' => $result['student']->student_id])
            ->with('success', $successMessage);
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student): View
    {
        $student->load(['user', 'parents']);

        return view('auth.admin_students_edit', [
            'student' => $student,
        ]);
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $student->load(['user', 'parents']);
        $oldValues = $student->toArray();

        $activeYear = SystemSetting::where('key', 'school_year')->value('value');

        if (! $activeYear) {
            return redirect()
                ->route('admin.settings.index')
                ->with('error', 'Please set an active School Year to continue.');
        }

        if ($student->school_year && $student->school_year !== $activeYear) {
            return back()
                ->with('error', 'This student record belongs to locked School Year '.$student->school_year.'. Only records in the active School Year '.$activeYear.' can be modified.')
                ->withInput();
        }

        $validated = $request->validate([
            // Student Details
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'middle_initial' => ['nullable', 'string', 'max:1'],
            'last_name' => ['required', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:20'],
            'sex' => ['required', 'string', 'in:Male,Female,Other'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
            'level' => ['required', 'string', 'max:255'],
            'section' => ['required', 'string', 'max:255'],
            'school_year' => ['nullable', 'string', 'max:20'],
            'strand' => ['nullable', 'string', 'exists:strands,name'],
            'shs_voucher_type' => ['nullable', 'string', 'in:regular,shs_voucher'],

            // Parent Linking
            'parent_mode' => ['nullable', 'in:new,existing,current'],
            'existing_parent_id' => ['nullable', 'required_if:parent_mode,existing', 'exists:parents,id'],

            // Parent Fields (for 'new' or 'current')
            'parent_guardian_name' => ['nullable', 'required_if:parent_mode,new', 'string', 'max:255'],
            'relationship' => ['nullable', 'string', 'max:50'],
            'parent_contact_number' => [
                'nullable',
                'required_if:parent_mode,new',
                'string',
                'min:11',
                'max:11',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('parent_mode') === 'new' && \App\Models\ParentContact::where('phone', $value)->exists()) {
                        $fail('The phone number has already been taken. Please use "Link Existing Parent" to search for this parent.');
                    }
                },
            ],
            'parent_email' => ['required_if:parent_mode,new', 'nullable', 'email', 'max:255'],
            'parent_password' => ['nullable', 'required_if:parent_mode,new', 'string', 'min:6'],
            'parent_address' => ['nullable', 'string', 'max:500'],
            'is_primary_contact' => ['nullable', 'boolean'],
        ]);

        if (isset($validated['school_year']) && $validated['school_year'] !== '' && $validated['school_year'] !== $activeYear) {
            return back()
                ->withErrors(['school_year' => 'School Year must match the active School Year ('.$activeYear.').'])
                ->withInput();
        }

        // Check for existing enrollment (excluding current student)
        $targetSchoolYear = $validated['school_year'] ?? $student->school_year;
        // Only check redundancy if we have a valid school year to check against
        if ($targetSchoolYear) {
            $existingStudent = Student::where('first_name', $validated['first_name'])
                ->where('last_name', $validated['last_name'])
                ->where('school_year', $targetSchoolYear)
                ->where('student_id', '!=', $student->student_id);

            if (! empty($validated['date_of_birth'])) {
                $existingStudent->whereDate('date_of_birth', $validated['date_of_birth']);
            }

            if ($existingStudent->exists()) {
                return back()
                    ->withErrors(['first_name' => 'This student is already enrolled for the selected School Year.'])
                    ->withInput();
            }
        }

        if (in_array($validated['level'], ['Grade 11', 'Grade 12'], true) && empty($validated['strand'] ?? '')) {
            return back()
                ->withErrors(['strand' => 'Category is required for Grade 11 and Grade 12.'])
                ->withInput();
        }

        $hasStrandColumn = Schema::hasColumn('students', 'strand');
        // ... (other schema checks handled dynamically or assumed safe if migration ran)
        // For brevity, I'll assume columns exist as per previous code, but I'll keep the dynamic update logic if I can reusing it,
        // but it's cleaner to just update what we validated.
        // However, I will preserve the column checks to be safe.
        $hasMiddleNameColumn = Schema::hasColumn('students', 'middle_name');
        $hasSuffixColumn = Schema::hasColumn('students', 'suffix');
        $hasDobColumn = Schema::hasColumn('students', 'date_of_birth');
        $hasNationalityColumn = Schema::hasColumn('students', 'nationality');
        $hasAddressColumn = Schema::hasColumn('students', 'address');
        $hasMiddleInitialColumn = Schema::hasColumn('students', 'middle_initial');

        DB::transaction(function () use ($student, $validated, $hasStrandColumn, $hasMiddleNameColumn, $hasSuffixColumn, $hasDobColumn, $hasNationalityColumn, $hasAddressColumn, $hasMiddleInitialColumn) {

            // 1. Update Student
            $updateData = [
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'sex' => $validated['sex'],
                'level' => $validated['level'],
                'section' => $validated['section'],
                'school_year' => $validated['school_year'] ?? null,
                'is_shs_voucher' => (isset($validated['shs_voucher_type']) && $validated['shs_voucher_type'] === 'shs_voucher'),
            ];
            if ($hasMiddleInitialColumn) {
                $middleName = trim((string) ($validated['middle_name'] ?? ''));
                $derivedInitial = $middleName !== '' ? mb_substr($middleName, 0, 1) : null;
                $updateData['middle_initial'] = $validated['middle_initial'] ?? $derivedInitial;
            }
            if ($hasMiddleNameColumn) {
                $updateData['middle_name'] = $validated['middle_name'] ?? null;
            }
            if ($hasSuffixColumn) {
                $updateData['suffix'] = $validated['suffix'] ?? null;
            }
            if ($hasDobColumn) {
                $updateData['date_of_birth'] = $validated['date_of_birth'] ?? null;
            }
            if ($hasNationalityColumn) {
                $updateData['nationality'] = $validated['nationality'] ?? null;
            }
            if ($hasAddressColumn) {
                $updateData['address'] = $validated['address'] ?? null;
            }
            if ($hasStrandColumn) {
                $updateData['strand'] = $validated['strand'] ?? null;
            }

            $student->update($updateData);

            // 2. Handle Parent
            $parentMode = $validated['parent_mode'] ?? 'current';
            $parentContact = null;

            if ($parentMode === 'new') {
                // Create New Parent
                $parentContact = \App\Models\ParentContact::create([
                    'full_name' => $validated['parent_guardian_name'],
                    'phone' => $validated['parent_contact_number'],
                    'email' => $validated['parent_email'] ?? null,
                    'address_street' => $validated['parent_address'] ?? null,
                    'account_status' => 'Active',
                ]);

                // Create User Account
                $parentPassword = $validated['parent_password'] ?? \Illuminate\Support\Str::random(10);
                $parentRole = \App\Models\Role::firstOrCreate(['role_name' => 'parent'], ['description' => 'Parent']);
                $parentEmail = $validated['parent_email'] ?? null;
                $parentPhone = $validated['parent_contact_number'];
                $username = ! empty($parentEmail) ? $parentEmail : $parentPhone;

                if (! \App\Models\User::where('email', $username)->exists()) {
                    \App\Models\User::create([
                        'email' => $username,
                        'password' => Hash::make($parentPassword),
                        'must_change_password' => true,
                        'role_id' => $parentRole->role_id,
                        'roleable_type' => \App\Models\ParentContact::class,
                        'roleable_id' => (string) $parentContact->id,
                    ]);

                    // Send SMS if needed (could check request->boolean('send_sms') here too)
                    // For update, maybe we don't send SMS unless explicitly asked?
                    // Let's leave SMS for enrollment or specific "Resend Creds" action.
                }

            } elseif ($parentMode === 'existing') {
                // Link Existing
                $parentContact = \App\Models\ParentContact::find($validated['existing_parent_id']);

            } else {
                // 'current' - Update the existing primary linked parent
                // Check for ParentContact first
                $currentParent = $student->parents->where('pivot.is_primary', true)->first() ?? $student->parents->first();

                if ($currentParent) {
                    $currentParent->update([
                        'full_name' => $validated['parent_guardian_name'] ?? $currentParent->full_name,
                        'phone' => $validated['parent_contact_number'] ?? $currentParent->phone,
                        'email' => $validated['parent_email'] ?? $currentParent->email,
                        'address_street' => $validated['parent_address'] ?? $currentParent->address_street,
                    ]);
                    // Update pivot relationship if changed
                    if (! empty($validated['relationship'])) {
                        $student->parents()->updateExistingPivot($currentParent->id, [
                            'relationship' => $validated['relationship'],
                        ]);
                    }
                } else {
                    // No parent exists, but fields provided -> Create New
                    if (! empty($validated['parent_guardian_name'])) {
                        // Treat as new
                        // Recursively call? No, just copy logic or assume 'new' logic above.
                        // For simplicity, we create a ParentContact here.
                        $parentContact = \App\Models\ParentContact::create([
                            'full_name' => $validated['parent_guardian_name'],
                            'phone' => $validated['parent_contact_number'] ?? '',
                            'email' => $validated['parent_email'] ?? null,
                            'address_street' => $validated['parent_address'] ?? null,
                            'account_status' => 'Active',
                        ]);
                    }
                }
            }

            // Link the new/existing parent if one was identified (from 'new', 'existing', or created in 'current')
            if ($parentContact) {
                // Set all others to non-primary
                $student->parents()->updateExistingPivot($student->parents->pluck('id'), ['is_primary' => false]);

                // Attach or Update new primary
                $student->parents()->syncWithoutDetaching([
                    $parentContact->id => [
                        'relationship' => $validated['relationship'] ?? 'Parent',
                        'is_primary' => true,
                    ],
                ]);
            }
        });

        try {
            $student->load('user', 'parents');
            // Sync logic might need update to use ParentContact
            // $this->syncSupabaseStudent($student, $student->user, $student->parents->first());
            app(FeeManagementService::class)->recomputeStudentLedger($student);
        } catch (\Throwable $e) {
        }

        // Audit Log
        try {
            AuditService::log(
                'Student Updated',
                $student,
                "Updated student: {$student->full_name} ({$student->student_id})",
                $oldValues,
                $student->toArray()
            );
        } catch (\Throwable $e) {
        }

        return redirect()
            ->route('super_admin.students.index', ['id' => $student->student_id]) // Redirect back to the student
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student): RedirectResponse
    {
        $activeYear = SystemSetting::getActiveSchoolYear();
        if ($activeYear && $student->school_year !== $activeYear) {
            return back()->with('error', 'Cannot delete student from a locked School Year.');
        }

        $student->delete();
        try {
            AuditService::log('Student Deleted', $student, "Deleted student: {$student->full_name} ({$student->student_id})", null, ['deleted_at' => now()]);
        } catch (\Throwable $e) {
        }

        return redirect()->route('super_admin.students.index')->with('success', 'Student deleted.');
    }

    /**
     * Change a student's enrollment status (Active, Irregular, Withdrawn, Archived).
     */
    public function changeStatus(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'enrollment_status' => ['required', 'string', 'in:Active,Irregular,Withdrawn,Archived'],
        ]);

        $oldStatus = $student->enrollment_status;
        $newStatus = $validated['enrollment_status'];

        if ($oldStatus === $newStatus) {
            return back()->with('info', 'Enrollment status unchanged.');
        }

        $student->update(['enrollment_status' => $newStatus]);

        AuditService::log(
            'Enrollment Status Changed',
            $student,
            "Status changed: {$oldStatus} → {$newStatus} for {$student->full_name} ({$student->student_id})",
            ['enrollment_status' => $oldStatus],
            ['enrollment_status' => $newStatus]
        );

        return redirect()
            ->route('super_admin.students.index', ['id' => $student->student_id])
            ->with('success', "{$student->full_name}'s enrollment status updated to {$newStatus}.");
    }

    public function archive(Student $student): RedirectResponse
    {
        $activeYear = SystemSetting::getActiveSchoolYear();
        if ($activeYear && $student->school_year !== $activeYear) {
            return back()->with('error', 'Cannot archive student from a locked School Year.');
        }

        $student->update(['enrollment_status' => 'Archived']);
        try {
            AuditService::log('Student Archived', $student, "Archived student: {$student->full_name} ({$student->student_id})", null, ['enrollment_status' => 'Archived']);
        } catch (\Throwable $e) {
        }

        return redirect()->route('super_admin.students.index')->with('success', 'Student archived.');
    }

    public function unarchive(Student $student): RedirectResponse
    {
        $activeYear = SystemSetting::getActiveSchoolYear();
        if ($activeYear && $student->school_year !== $activeYear) {
            return back()->with('error', 'Cannot unarchive student from a locked School Year.');
        }

        $student->update(['enrollment_status' => 'Active']);
        try {
            AuditService::log('Student Unarchived', $student, "Unarchived student: {$student->full_name} ({$student->student_id})", null, ['enrollment_status' => 'Active']);
        } catch (\Throwable $e) {
        }

        return redirect()->route('super_admin.students.index')->with('success', 'Student unarchived.');
    }

    /**
     * Generate a unique student ID.
     */
    /**
     * AJAX endpoint: returns a freshly generated, unique student ID as JSON.
     */
    public function generateStudentId(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['student_id' => $this->generateUniqueStudentId()]);
    }

    /**
     * Build a unique student ID from the configurable format stored in system_settings.
     *
     * Supported tokens:
     *   {YYYY}  – 4-digit current year
     *   {YY}    – 2-digit current year
     *   {SY}    – 4-digit start-year extracted from active school year (e.g. 2025 from "2025-2026")
     *   {####}  – Auto-incrementing zero-padded number (any number of # characters sets the pad width)
     */
    private function generateUniqueStudentId(): string
    {
        $format = SystemSetting::getValue('student_id_format', 'STU-{SY}-{####}');

        // Replace static tokens
        $activeSy  = SystemSetting::getActiveSchoolYear() ?? (date('Y').'-'.(date('Y') + 1));
        $syStart   = explode('-', $activeSy)[0] ?? date('Y');
        $format    = str_replace('{YYYY}', date('Y'), $format);
        $format    = str_replace('{YY}', date('y'), $format);
        $format    = str_replace('{SY}', $syStart, $format);

        // Handle auto-increment token {####} (any number of # signs)
        if (preg_match('/\{(#+)\}/', $format, $match)) {
            $token  = $match[0];
            $padLen = strlen($match[1]);
            [$prefix, $suffix] = explode($token, $format, 2);

            // Find the highest existing number with this prefix/suffix
            $driver  = DB::connection()->getDriverName();
            $likeOp  = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';
            $maxNum  = Student::where('student_id', $likeOp, $prefix.'%'.($suffix ?: ''))
                ->pluck('student_id')
                ->map(function ($id) use ($prefix, $suffix) {
                    $inner = substr($id, strlen($prefix));
                    if ($suffix !== '') {
                        $inner = substr($inner, 0, -strlen($suffix));
                    }
                    return (int) $inner;
                })
                ->max() ?? 0;

            $next = $maxNum + 1;
            do {
                $studentId = $prefix.str_pad($next, $padLen, '0', STR_PAD_LEFT).$suffix;
                $next++;
            } while (Student::where('student_id', $studentId)->exists());

            return $studentId;
        }

        // Fallback: append random hex when no {####} token is present
        do {
            $studentId = $format.'-'.strtoupper(substr(md5(uniqid()), 0, 6));
        } while (Student::where('student_id', $studentId)->exists());

        return $studentId;
    }

    /**
     * Sync student record to Supabase MCP (student_profiles table), including LRN.
     */
    private function syncSupabaseStudent(Student $student, ?User $user, ?\App\Models\ParentContact $parentContact): void
    {
        $url = env('SUPABASE_URL', '');
        $serviceKey = env('SUPABASE_SERVICE_KEY', '');
        if (! $url || ! $serviceKey) {
            return;
        }
        $endpoint = rtrim($url, '/').'/rest/v1/student_profiles';
        $headers = [
            'Authorization' => 'Bearer '.$serviceKey,
            'apikey' => $serviceKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Prefer' => 'resolution=merge-duplicates',
        ];
        $payload = [
            'student_id' => $student->student_id,
            'lrn' => $student->student_id,
            'first_name' => $student->first_name,
            'middle_initial' => $student->middle_initial,
            'last_name' => $student->last_name,
            'sex' => $student->sex,
            'level' => $student->level,
            'section' => $student->section,
            'strand' => $student->strand,
            'address' => $student->address ?? null,
            'email' => $user ? $user->email : null,
            'profile_picture_url' => $student->profile_picture_url ?? null,
            'guardian_name' => $parentContact?->full_name,
            'guardian_relationship' => $parentContact?->pivot?->relationship ?? 'Parent',
            'guardian_contact_number' => $parentContact?->phone,
            'guardian_email' => $parentContact?->email,
            'guardian_address' => $parentContact?->address_street,
            'updated_at' => now()->toISOString(),
        ];
        try {
            $resp = Http::withHeaders($headers)->patch($endpoint.'?student_id=eq.'.urlencode($student->student_id), $payload);
            if ($resp->status() === 404 || $resp->status() === 406) {
                Http::withHeaders($headers)->post($endpoint, $payload);
            }
        } catch (\Throwable $e) {
        }
    }

    /**
     * Fetch total student count from Supabase MCP (student_profiles).
     */
    private function getSupabaseStudentCount(): ?int
    {
        $url = env('SUPABASE_URL', '');
        $serviceKey = env('SUPABASE_SERVICE_KEY', '');
        if (! $url || ! $serviceKey) {
            return null;
        }
        $endpoint = rtrim($url, '/').'/rest/v1/student_profiles?select=student_id';
        $headers = [
            'Authorization' => 'Bearer '.$serviceKey,
            'apikey' => $serviceKey,
            'Accept' => 'application/json',
            'Prefer' => 'count=exact',
            'Range' => '0-0',
        ];
        try {
            $resp = Http::withHeaders($headers)->get($endpoint);
            $range = $resp->header('Content-Range');
            if (is_string($range) && preg_match('/\\d+-\\d+\\/(\\d+)/', $range, $m)) {
                return (int) $m[1];
            }
            $json = $resp->json();
            if (is_array($json)) {
                return count($json);
            }
        } catch (\Throwable $e) {
        }

        return null;
    }

    // ─── Sibling Linking ────────────────────────────────────────────────

    /**
     * Get siblings of a student (JSON endpoint for AJAX).
     */
    public function siblings(Student $student): \Illuminate\Http\JsonResponse
    {
        $student->load('parents');

        $siblings = $student->getSiblings($student->school_year);

        // Also include siblings from other school years for context
        $allSiblings = $student->getSiblings();

        $primaryParent = $student->parents->where('pivot.is_primary', true)->first()
            ?? $student->parents->first();

        // Check family discount eligibility
        $siblingDiscount = \App\Models\Discount::where('discount_name', 'Sibling Discount')
            ->where('is_active', true)
            ->first();

        $familyCount = $allSiblings->where('enrollment_status', 'Active')
            ->where('school_year', $student->school_year)
            ->count() + 1; // +1 for the student themselves

        $discountEligible = $siblingDiscount && $familyCount >= 2;

        return response()->json([
            'student_id' => $student->student_id,
            'student_name' => $student->full_name,
            'parent_name' => $primaryParent?->full_name,
            'parent_id' => $primaryParent?->id,
            'family_count' => $familyCount,
            'discount_eligible' => $discountEligible,
            'discount_name' => $siblingDiscount?->discount_name,
            'discount_value' => $siblingDiscount ? $siblingDiscount->formatted_value : null,
            'siblings' => $allSiblings->map(function ($sib) use ($student) {
                return [
                    'student_id' => $sib->student_id,
                    'full_name' => $sib->full_name,
                    'level' => $sib->level,
                    'section' => $sib->section,
                    'school_year' => $sib->school_year,
                    'enrollment_status' => $sib->enrollment_status,
                    'same_year' => $sib->school_year === $student->school_year,
                ];
            })->values(),
        ]);
    }

    /**
     * Search for students to link as siblings (AJAX).
     * Excludes students already linked as siblings.
     */
    public function searchForSibling(Request $request): \Illuminate\Http\JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $excludeId = trim((string) $request->query('exclude', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        // Get the student we're linking FROM and their existing siblings
        $excludeIds = [$excludeId];
        if ($excludeId) {
            $baseStudent = Student::with('parents')->where('student_id', $excludeId)->first();
            if ($baseStudent) {
                $existingSiblings = $baseStudent->getSiblings();
                $excludeIds = array_merge($excludeIds, $existingSiblings->pluck('student_id')->toArray());
            }
        }

        $driver = DB::connection()->getDriverName();
        $operator = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';
        $concat = $driver === 'sqlite' ? "first_name || ' ' || last_name" : "CONCAT(first_name, ' ', last_name)";

        $results = Student::with('parents')
            ->whereNotIn('student_id', $excludeIds)
            ->where(function ($query) use ($q, $operator, $concat) {
                $query->where('student_id', $operator, "%{$q}%")
                    ->orWhere('first_name', $operator, "%{$q}%")
                    ->orWhere('last_name', $operator, "%{$q}%")
                    ->orWhereRaw("{$concat} {$operator} ?", ["%{$q}%"]);
            })
            ->orderBy('last_name')
            ->limit(15)
            ->get()
            ->map(function ($s) {
                $parent = $s->parents->where('pivot.is_primary', true)->first() ?? $s->parents->first();
                return [
                    'student_id' => $s->student_id,
                    'full_name' => $s->full_name,
                    'level' => $s->level,
                    'section' => $s->section,
                    'school_year' => $s->school_year,
                    'enrollment_status' => $s->enrollment_status,
                    'parent_name' => $parent?->full_name,
                    'parent_id' => $parent?->id,
                ];
            });

        return response()->json($results);
    }

    /**
     * Link a student as a sibling by assigning them to the same parent.
     * This is the core sibling-linking operation: it shares the primary parent
     * of the current student with the target sibling student.
     */
    public function linkSibling(Request $request, Student $student): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'sibling_id' => ['required', 'string', 'exists:students,student_id'],
        ]);

        $student->load('parents');
        $sibling = Student::with('parents')->where('student_id', $validated['sibling_id'])->firstOrFail();

        // Prevent self-link
        if ($student->student_id === $sibling->student_id) {
            return response()->json(['error' => 'Cannot link a student to themselves.'], 422);
        }

        // Check if already siblings (share a parent)
        $studentParentIds = $student->parents->pluck('id');
        $siblingParentIds = $sibling->parents->pluck('id');
        if ($studentParentIds->intersect($siblingParentIds)->isNotEmpty()) {
            return response()->json(['error' => 'These students are already linked as siblings.'], 422);
        }

        // Get the current student's primary parent
        $primaryParent = $student->parents->where('pivot.is_primary', true)->first()
            ?? $student->parents->first();

        if (! $primaryParent) {
            return response()->json(['error' => 'This student has no parent/guardian linked. Please add a parent first.'], 422);
        }

        DB::transaction(function () use ($sibling, $primaryParent) {
            // Link the sibling to the same parent (as non-primary, preserving their existing primary)
            $siblingPrimaryParent = $sibling->parents->where('pivot.is_primary', true)->first();
            $isPrimary = ! $siblingPrimaryParent; // Only set as primary if sibling has no primary yet

            $sibling->parents()->syncWithoutDetaching([
                $primaryParent->id => [
                    'relationship' => 'Parent',
                    'is_primary' => $isPrimary,
                ],
            ]);
        });

        // Recalculate fees for ALL family members (to apply/update sibling discounts)
        $this->recalculateFamilyFees($student);

        // Audit
        try {
            AuditService::log(
                'Sibling Linked',
                $student,
                "Linked {$sibling->full_name} ({$sibling->student_id}) as sibling of {$student->full_name} ({$student->student_id}) via parent {$primaryParent->full_name}",
                null,
                [
                    'sibling_id' => $sibling->student_id,
                    'parent_id' => $primaryParent->id,
                    'parent_name' => $primaryParent->full_name,
                ]
            );
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => "{$sibling->full_name} has been linked as a sibling.",
            'fees_recalculated' => true,
        ]);
    }

    /**
     * Unlink a sibling by removing the shared parent connection.
     */
    public function unlinkSibling(Request $request, Student $student): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'sibling_id' => ['required', 'string', 'exists:students,student_id'],
        ]);

        $student->load('parents');
        $sibling = Student::with('parents')->where('student_id', $validated['sibling_id'])->firstOrFail();

        // Find shared parents
        $studentParentIds = $student->parents->pluck('id');
        $siblingParentIds = $sibling->parents->pluck('id');
        $sharedParentIds = $studentParentIds->intersect($siblingParentIds);

        if ($sharedParentIds->isEmpty()) {
            return response()->json(['error' => 'These students do not share a parent.'], 422);
        }

        DB::transaction(function () use ($sibling, $sharedParentIds) {
            // Remove the shared parent link from the sibling
            // (We remove the sibling's link to the shared parent, not the current student's)
            foreach ($sharedParentIds as $parentId) {
                $sibling->parents()->detach($parentId);
            }

            // If sibling has no more parents linked, that's okay — admin can re-add later
            // If sibling lost their primary parent, promote another to primary
            $sibling->load('parents');
            if ($sibling->parents->isNotEmpty() && ! $sibling->parents->where('pivot.is_primary', true)->first()) {
                $newPrimary = $sibling->parents->first();
                $sibling->parents()->updateExistingPivot($newPrimary->id, ['is_primary' => true]);
            }
        });

        // Recalculate fees for ALL family members
        $this->recalculateFamilyFees($student);
        // Also recalculate for the unlinked sibling
        try {
            $sibling->load('parents');
            app(FeeManagementService::class)->recomputeStudentLedger($sibling);
        } catch (\Throwable $e) {
        }

        // Audit
        try {
            AuditService::log(
                'Sibling Unlinked',
                $student,
                "Unlinked {$sibling->full_name} ({$sibling->student_id}) from {$student->full_name} ({$student->student_id})",
                ['shared_parent_ids' => $sharedParentIds->toArray()],
                null
            );
        } catch (\Throwable $e) {
        }

        return response()->json([
            'success' => true,
            'message' => "{$sibling->full_name} has been unlinked.",
            'fees_recalculated' => true,
        ]);
    }

    /**
     * Attach an additional charge to a student's fee assignment.
     */
    public function addCharge(Request $request, Student $student): RedirectResponse
    {
        $activeYear = SystemSetting::where('key', 'school_year')->value('value');
        if ($activeYear && $student->school_year !== $activeYear) {
            return back()->with('error', 'Cannot modify charges for a locked School Year.');
        }

        $validated = $request->validate([
            'charge_id' => ['required', 'exists:additional_charges,id'],
        ]);

        $charge = \App\Models\AdditionalCharge::findOrFail($validated['charge_id']);
        $feeAssignment = $student->getCurrentFeeAssignment();

        if (! $feeAssignment) {
            return back()->with('error', 'No active fee assignment found for this student.');
        }

        if ($feeAssignment->additionalCharges()->where('additional_charges.id', $charge->id)->exists()) {
            return back()->with('error', 'This charge is already applied to the student.');
        }

        DB::transaction(function () use ($feeAssignment, $charge, $student) {
            $feeAssignment->additionalCharges()->attach($charge->id);
            $feeAssignment->calculateTotal();
            app(FeeManagementService::class)->recomputeStudentLedger($student);

            AuditService::log(
                'Charge Added',
                $student,
                "Added charge '{$charge->charge_name}' (₱" . number_format($charge->amount, 2) . ") to student.",
                null,
                ['charge_id' => $charge->id, 'charge_name' => $charge->charge_name, 'amount' => $charge->amount]
            );
        });

        return back()->with('success', "Charge '{$charge->charge_name}' added successfully.");
    }

    /**
     * Remove an additional charge from a student's fee assignment.
     */
    public function removeCharge(Request $request, Student $student, \App\Models\AdditionalCharge $charge): RedirectResponse
    {
        $activeYear = SystemSetting::where('key', 'school_year')->value('value');
        if ($activeYear && $student->school_year !== $activeYear) {
            return back()->with('error', 'Cannot modify charges for a locked School Year.');
        }

        $feeAssignment = $student->getCurrentFeeAssignment();

        if (! $feeAssignment) {
            return back()->with('error', 'No active fee assignment found for this student.');
        }

        DB::transaction(function () use ($feeAssignment, $charge, $student) {
            $feeAssignment->additionalCharges()->detach($charge->id);
            $feeAssignment->calculateTotal();
            app(FeeManagementService::class)->recomputeStudentLedger($student);

            AuditService::log(
                'Charge Removed',
                $student,
                "Removed charge '{$charge->charge_name}' from student.",
                null,
                ['charge_id' => $charge->id, 'charge_name' => $charge->charge_name]
            );
        });

        return back()->with('success', "Charge '{$charge->charge_name}' removed successfully.");
    }

    /**
     * Store a manual fee adjustment (one-off charge or discount) from the student management view.
     */
    public function storeAdjustment(Request $request, Student $student): RedirectResponse
    {
        $activeYear = SystemSetting::where('key', 'school_year')->value('value');
        if ($activeYear && $student->school_year !== $activeYear) {
            return back()->with('error', 'Cannot apply fee adjustments to a locked School Year.');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:discount,charge'],
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'remarks' => ['nullable', 'string', 'max:500'],
        ]);

        $feeAssignment = $student->getCurrentFeeAssignment();

        if (! $feeAssignment) {
            return back()->with('error', 'No active fee assignment found for this student.');
        }

        DB::transaction(function () use ($validated, $student, $feeAssignment, $request) {
            \App\Models\StudentFeeAdjustment::create([
                'fee_assignment_id' => $feeAssignment->id,
                'student_id' => $student->student_id,
                'type' => $validated['type'],
                'name' => $validated['name'],
                'amount' => $validated['amount'],
                'remarks' => $validated['remarks'],
                'created_by' => auth()->id(),
            ]);

            $feeAssignment->calculateTotal();

            // Create FeeRecord to track balance impact
            $balanceEffect = $validated['type'] === 'discount'
                ? -abs($validated['amount'])
                : abs($validated['amount']);

            \App\Models\FeeRecord::create([
                'student_id' => $student->student_id,
                'record_type' => 'adjustment',
                'amount' => $validated['amount'],
                'balance' => $balanceEffect,
                'status' => 'pending',
                'notes' => $validated['name'] . ($validated['remarks'] ? " - {$validated['remarks']}" : ''),
                'payment_date' => now(),
            ]);

            app(FeeManagementService::class)->recomputeStudentLedger($student);

            AuditService::log(
                'Fee Adjustment Applied',
                $student,
                "Applied {$validated['type']}: {$validated['name']} (₱" . number_format($validated['amount'], 2) . ")",
                null,
                $validated
            );
        });

        $label = $validated['type'] === 'discount' ? 'Discount' : 'Charge';
        return back()->with('success', "{$label} adjustment '{$validated['name']}' applied successfully.");
    }

    /**
     * Recalculate a student's fees (triggered from Fees tab).
     */
    public function recalculateFees(Student $student): RedirectResponse
    {
        $feeAssignment = $student->getCurrentFeeAssignment();

        if (! $feeAssignment) {
            return back()->with('error', 'No active fee assignment found.');
        }

        try {
            $feeAssignment->calculateTotal();
            app(FeeManagementService::class)->recomputeStudentLedger($student);

            AuditService::log(
                'Fees Recalculated',
                $student,
                "Manually recalculated fees for {$student->full_name}. New total: ₱" . number_format($feeAssignment->fresh()->total_amount ?? 0, 2),
                null,
                ['total_amount' => $feeAssignment->fresh()->total_amount ?? 0]
            );

            return back()->with('success', 'Fees recalculated successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to recalculate fees: ' . $e->getMessage());
        }
    }

    /**
     * Recalculate fees for all family members connected through shared parents.
     * This ensures sibling discounts are applied/removed correctly for everyone.
     */
    private function recalculateFamilyFees(Student $student): void
    {
        $student->load('parents');

        $parentIds = $student->parents->pluck('id');

        if ($parentIds->isEmpty()) {
            return;
        }

        // Get all students linked to any of these parents
        $familyStudents = Student::whereHas('parents', function ($q) use ($parentIds) {
            $q->whereIn('parents.id', $parentIds);
        })
            ->where('enrollment_status', 'Active')
            ->get()
            ->unique('student_id');

        $feeService = app(FeeManagementService::class);

        foreach ($familyStudents as $familyStudent) {
            try {
                $familyStudent->load('parents');
                $feeService->recomputeStudentLedger($familyStudent);
            } catch (\Throwable $e) {
                // Log but don't break the loop
                try {
                    AuditService::log(
                        'Family Fee Recalculation Failed',
                        $familyStudent,
                        "Failed to recalculate fees for {$familyStudent->full_name}: {$e->getMessage()}",
                        null,
                        null
                    );
                } catch (\Throwable $e2) {
                }
            }
        }
    }
}
