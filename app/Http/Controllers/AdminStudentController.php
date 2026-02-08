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
use Illuminate\Support\Facades\Schema;
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

        // Context Variables
        $level = $request->query('level');
        $section = $request->query('section');
        $strandName = $request->query('strand'); // 'strand' URL param holds the strand name (e.g. STEM)

        // School Year Logic
        $schoolYears = Student::distinct()->whereNotNull('school_year')->orderBy('school_year', 'desc')->pluck('school_year');
        
        // Active School Year from Settings
        $activeSy = SystemSetting::getActiveSchoolYear();

        // Ensure Active SY is in the list if set
        if ($activeSy && !$schoolYears->contains($activeSy)) {
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
        $feeAssignment = null;
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

            $availableDiscounts = \App\Models\Discount::active()
                ->applicableToGrade($selectedStudent->level)
                ->get()
                ->filter(function ($discount) use ($selectedStudent) {
                    return $discount->isEligibleForStudent($selectedStudent) && $discount->isCurrentlyValid();
                });
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
                ->when($currentSchoolYear, function ($query) use ($currentSchoolYear) {
                    $query->where('school_year', $currentSchoolYear);
                })
                ->when($level, function ($query) use ($level) {
                    $query->where('level', $level);
                })
                ->when($section, function ($query) use ($section) {
                    $query->where('section', $section);
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
            $levels = collect(['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12']);
        }

        $totalStudents = Student::count();

        return view('auth.admin_students_index', [
            'students' => $students,
            'q' => $q,
            'totalStudents' => $totalStudents,
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
            'availableDiscounts' => $availableDiscounts,
            'feeAssignment' => $feeAssignment,
            'isReadOnly' => $isReadOnly,
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

        return redirect()->route('admin.students.index', $redirectParams)
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

        return redirect()->route('admin.students.index', ['level' => $validated['level']])
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

        return redirect()->route('admin.students.index', [
            'level' => $section->level,
            'strand' => $request->input('strand'),
        ])->with('success', "Section '{$section->name}' deleted successfully.");
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
            'parent_email' => ['nullable', 'email', 'max:255'],
            'parent_password' => ['required_if:parent_mode,new', 'nullable', 'string', 'min:6'],
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

        // Check for existing enrollment for the same student in the same school year
        $existingStudent = Student::where('first_name', $validated['first_name'])
            ->where('last_name', $validated['last_name'])
            ->where('school_year', $validated['school_year']);

        if (! empty($validated['date_of_birth'])) {
            $existingStudent->whereDate('date_of_birth', $validated['date_of_birth']);
        }

        if ($existingStudent->exists()) {
            return back()
                ->withErrors(['first_name' => 'This student is already enrolled for the selected School Year.'])
                ->withInput();
        }

        $autoGenerate = SystemSetting::where('key', 'auto_generate_fees_on_enrollment')->value('value');

        $studentId = $this->generateUniqueStudentId();
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
                $parentPassword = $validated['parent_password'];
                $parentRole = \App\Models\Role::firstOrCreate(['role_name' => 'parent'], ['description' => 'Parent']);

                // Username is phone (or email if phone missing, but phone is required)
                $username = $parentPhone;

                // Check if user exists (shouldn't if validation passed, but safety check)
                if (! \App\Models\User::where('email', $username)->exists()) {
                    \App\Models\User::create([
                        'email' => $username, // Using phone as username/email field for login
                        'password' => Hash::make($parentPassword),
                        'must_change_password' => true,
                        'role_id' => $parentRole->role_id,
                        'roleable_type' => \App\Models\ParentContact::class,
                        'roleable_id' => $parentContact->id,
                    ]);
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

            if ($autoGenerate === '1') {
                try {
                    app(FeeManagementService::class)->recomputeStudentLedger($student);
                } catch (\Throwable $e) {
                    throw $e;
                }
            }

            try {
                AuditService::log(
                    'Student Enrolled',
                    $student,
                    "Enrolled student: {$student->full_name} ({$student->student_id})",
                    null,
                    $student->toArray()
                );
            } catch (\Throwable $e) {
            }

            return [
                'student' => $student,
                'parentContact' => $parentContact,
                'parentPassword' => $parentPassword,
                'isNewParent' => $isNewParent,
            ];
        });

        // 5. Send SMS (if new parent and toggle is on)
        if ($sendSms && $result['isNewParent'] && $result['parentContact'] && $result['parentContact']->phone) {
            try {
                $msg = \App\Services\SmsTemplates::getNewAccountMessage($result['parentContact']->phone, $result['parentPassword']);
                app(\App\Services\SmsService::class)->send($result['parentContact']->phone, $msg, $result['student']->student_id);
            } catch (\Exception $e) {
                // Don't rollback for SMS failure
            }
        }

        return redirect()
            ->route('admin.students.index', ['id' => $result['student']->student_id])
            ->with('success', 'Student enrolled successfully.');
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
            'parent_email' => ['nullable', 'email', 'max:255'],
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
                $username = $validated['parent_contact_number'];

                if (! \App\Models\User::where('email', $username)->exists()) {
                    \App\Models\User::create([
                        'email' => $username,
                        'password' => Hash::make($parentPassword),
                        'must_change_password' => true,
                        'role_id' => $parentRole->role_id,
                        'roleable_type' => \App\Models\ParentContact::class,
                        'roleable_id' => $parentContact->id,
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
            ->route('admin.students.index', ['id' => $student->student_id]) // Redirect back to the student
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
            AuditService::log('Student Deactivated', $student, "Deactivated student: {$student->full_name} ({$student->student_id})", null, ['deleted_at' => now()]);
        } catch (\Throwable $e) {
        }

        return redirect()->route('admin.students.index')->with('success', 'Student deactivated.');
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

        return redirect()->route('admin.students.index')->with('success', 'Student archived.');
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

        return redirect()->route('admin.students.index')->with('success', 'Student unarchived.');
    }

    /**
     * Generate a unique student ID.
     */
    private function generateUniqueStudentId(): string
    {
        do {
            // Generate student ID using timestamp and random string
            $studentId = 'STU'.date('Y').strtoupper(substr(md5(uniqid()), 0, 8));
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
}
