<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\FeeRecord;
use App\Models\ParentContact;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use App\Models\SystemSetting;
use App\Models\TuitionFee;
use App\Models\User;
use App\Services\FeeManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AuthLoginController extends Controller
{
    /**
     * Display the login form.
     */
    public function login(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check lockout before attempting authentication
        $user = User::where('email', $credentials['email'])->first();
        if ($user && $user->lockout_until && now()->lt($user->lockout_until)) {
            return back()->withErrors([
                'email' => 'Account locked. Try again later.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Reset failed login attempts on successful login
            if ($user->failed_login_attempts > 0) {
                $user->failed_login_attempts = 0;
                $user->lockout_until = null;
                $user->save();
            }

            if (! $user->is_active) {
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Your account is inactive.',
                ])->onlyInput('email');
            }

            // Check password expiry
            if ($user->password_expires_at && now()->gt($user->password_expires_at)) {
                return redirect()->route('auth.password.change');
            }

            $roleName = optional($user->role)->role_name;

            // Fallback: Check roleable_type if role_name is missing
            if (! $roleName) {
                if ($user->roleable_type === 'App\Models\Admin') {
                    $roleName = 'admin';
                } elseif ($user->roleable_type === 'App\Models\Staff') {
                    $roleName = 'staff';
                } elseif ($user->roleable_type === 'App\Models\ParentContact') {
                    $roleName = 'parent';
                } elseif ($user->roleable_type === 'App\Models\Student') {
                    $roleName = 'student';
                }
            }

            switch ($roleName) {
                case 'super_admin':
                    return redirect()->intended('super-admin/dashboard');
                case 'admin':
                    return redirect()->intended('admin_dashboard');
                case 'staff':
                    return redirect()->intended('staff_dashboard');
                case 'parent':
                    return redirect()->intended('user_dashboard');
                default:
                    return redirect()->intended('user_dashboard');
            }
        }

        // Increment failed login attempts
        if ($user) {
            $user->failed_login_attempts = ($user->failed_login_attempts ?? 0) + 1;
            if ($user->failed_login_attempts >= 5) {
                $user->lockout_until = now()->addMinutes(15);
            }
            $user->save();

            if ($user->failed_login_attempts >= 5) {
                return back()->withErrors([
                    'email' => 'Account locked. Try again later.',
                ])->onlyInput('email');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Display the user dashboard.
     */
    public function user_dashboard(Request $request): View
    {
        $user = Auth::user();

        $isParent = optional($user->role)->role_name === 'parent' || (($user->roleable_type ?? '') === \App\Models\ParentContact::class);
        $studentId = null;

        // Logic for Student User
        if ($user && ($user->roleable_type ?? null) === 'App\\Models\\Student') {
            $studentId = $user->roleable_id;
        }

        // Logic for Parent User Selecting a Child
        $selectedChild = null;
        $myChildren = collect();

        if ($isParent) {
            $parent = $user->roleable;
            $myChildren = $parent ? $parent->students()->get() : collect();

            if ($request->has('student_id')) {
                $requestedId = $request->input('student_id');
                // Strict isolation: Check if requested ID belongs to parent
                $selectedChild = $myChildren->where('student_id', $requestedId)->first();

                if ($selectedChild) {
                    $studentId = $selectedChild->student_id;
                }
            }
        }

        $upcomingFees = collect();
        $paidFees = collect();
        $transactions = collect();
        $notifications = collect();
        $balanceDue = 0.0;
        $totalPaid = 0.0;
        $childrenSummaries = collect();
        $consolidatedBalanceDue = 0.0;
        $consolidatedTotalPaid = 0.0;

        // Fetch data for specific student (Either logged-in Student or Parent's selected child)
        if ($studentId) {
            $student = Student::where('student_id', $studentId)->first();
            if ($student) {
                $svc = app(FeeManagementService::class);
                $totals = $svc->computeTotalsForStudent($student);
                $totalPaid = (float) ($totals['paidAmount'] ?? 0.0);
                $balanceDue = (float) ($totals['remainingBalance'] ?? max(((float) ($totals['totalAmount'] ?? 0.0)) - $totalPaid, 0.0));
            }
            // Get upcoming/unpaid fees
            $upcomingFees = FeeRecord::where('student_id', $studentId)
                ->where(function ($q) {
                    $q->where('status', '!=', 'paid')
                        ->orWhereNull('status')
                        ->orWhere('balance', '>', 0);
                })
                ->orderBy('payment_date', 'asc')
                ->limit(20)
                ->get();

            // Get paid fees for current student
            $paidFees = FeeRecord::where('student_id', $studentId)
                ->where('status', 'paid')
                ->orderBy('payment_date', 'desc')
                ->limit(20)
                ->get();

            // Get recent transactions (payment history) for this student
            $transactions = \App\Models\Payment::where('student_id', $studentId)
                ->orderBy('paid_at', 'desc')
                ->limit(10)
                ->get();
        }

        // Parent Overview Logic (Only if no specific child is selected, or we want summaries anyway)
        if ($isParent) {
            $svc = app(FeeManagementService::class);

            // $myChildren is already fetched above
            $childrenSummaries = $myChildren->map(function ($child) use ($svc) {
                $totals = $svc->computeTotalsForStudent($child);
                $childTotalPaid = (float) ($totals['paidAmount'] ?? 0.0);
                $childBalanceDue = (float) ($totals['remainingBalance'] ?? max(((float) ($totals['totalAmount'] ?? 0.0)) - $childTotalPaid, 0.0));

                // Optimized fetching for summary
                $childUpcoming = FeeRecord::where('student_id', $child->student_id)
                    ->where(function ($q) {
                        $q->where('status', '!=', 'paid')
                            ->orWhereNull('status')
                            ->orWhere('balance', '>', 0);
                    })
                    ->orderBy('id', 'desc')
                    ->limit(3)
                    ->get();

                return [
                    'student_id' => $child->student_id,
                    'full_name' => trim($child->first_name.' '.($child->middle_name ?? '').' '.$child->last_name),
                    'first_name' => $child->first_name,
                    'last_name' => $child->last_name,
                    'level' => $child->level,
                    'section' => $child->section,
                    'balanceDue' => (float) $childBalanceDue,
                    'totalPaid' => (float) $childTotalPaid,
                    'totalAmount' => (float) ($totals['totalAmount'] ?? 0.0),
                    'upcomingFees' => $childUpcoming,
                ];
            });
            $consolidatedBalanceDue = (float) $childrenSummaries->sum('balanceDue');
            $consolidatedTotalPaid = (float) $childrenSummaries->sum('totalPaid');
        }

        if ($user) {
            // Get notifications for the user
            $notifications = DB::table('notifications')
                ->where('user_id', $user->user_id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // If viewing a specific child, we might want to filter notifications related to them?
            // For now, keeping user-level notifications.
        }

        return view('auth.user_dashboard', [
            'upcomingFees' => $upcomingFees,
            'paidFees' => $paidFees,
            'transactions' => $transactions,
            'notifications' => $notifications,
            'balanceDue' => $balanceDue,
            'totalPaid' => $totalPaid,
            'isParent' => (bool) $isParent,
            'childrenSummaries' => $childrenSummaries,
            'myChildren' => $myChildren, // Pass raw models for sidebar
            'selectedChild' => $selectedChild, // Pass selected child model if any
            'consolidatedBalanceDue' => (float) $consolidatedBalanceDue,
            'consolidatedTotalPaid' => (float) $consolidatedTotalPaid,
        ]);
    }

    public function user_fee_summary(): JsonResponse
    {
        $user = Auth::user();
        if (! $user || ($user->roleable_type ?? '') !== 'App\\Models\\Student') {
            return response()->json(['error' => 'unauthorized'], 403);
        }

        $studentId = $user->roleable_id;

        $student = Student::where('student_id', $studentId)->first();
        $svc = app(FeeManagementService::class);
        $totals = $student ? $svc->computeTotalsForStudent($student) : ['totalAmount' => 0.0];
        $totalPaid = (float) ($totals['paidAmount'] ?? 0.0);
        $balanceDue = (float) ($totals['remainingBalance'] ?? max(((float) ($totals['totalAmount'] ?? 0.0)) - $totalPaid, 0.0));

        $upcomingFees = FeeRecord::where('student_id', $studentId)
            ->where(function ($q) {
                $q->where('status', '!=', 'paid')
                    ->orWhereNull('status')
                    ->orWhere('balance', '>', 0);
            })
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        $paidFees = FeeRecord::where('student_id', $studentId)
            ->where('status', 'paid')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'balanceDue' => (float) $balanceDue,
            'totalPaid' => (float) $totalPaid,
            'upcomingFees' => $upcomingFees->map(function ($rec) {
                return [
                    'id' => $rec->id,
                    'status' => $rec->status,
                    'record_type' => $rec->record_type,
                    'notes' => $rec->notes,
                    'balance' => (float) ($rec->balance ?? 0),
                    'payment_date' => $rec->payment_date ? $rec->payment_date->toDateString() : null,
                    'created_at' => $rec->created_at ? $rec->created_at->toDateTimeString() : null,
                ];
            })->toArray(),
            'paidFees' => $paidFees->map(function ($rec) {
                return [
                    'id' => $rec->id,
                    'status' => $rec->status,
                    'record_type' => $rec->record_type,
                    'notes' => $rec->notes,
                    'balance' => (float) ($rec->balance ?? 0),
                    'payment_date' => $rec->payment_date ? $rec->payment_date->toDateString() : null,
                    'created_at' => $rec->created_at ? $rec->created_at->toDateTimeString() : null,
                ];
            })->toArray(),
        ]);
    }

    public function parent_metrics(): JsonResponse
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'unauthorized'], 403);
        }

        $isParent = optional($user->role)->role_name === 'parent' || (($user->roleable_type ?? '') === \App\Models\ParentContact::class);
        if (! $isParent) {
            return response()->json(['error' => 'unauthorized'], 403);
        }

        $parent = $user->roleable;
        $myChildren = $parent ? $parent->students()->get() : collect();
        $svc = app(FeeManagementService::class);

        // Determine current and previous school year for YoY comparison
        $activeSy = SystemSetting::getActiveSchoolYear();
        $prevSy = null;
        if ($activeSy) {
            $parts = explode('-', $activeSy);
            if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                $prevSy = ((int) $parts[0] - 1).'-'.((int) $parts[1] - 1);
            }
        }

        $childrenSummaries = $myChildren->map(function ($child) use ($svc, $activeSy, $prevSy) {
            $totals = $svc->computeTotalsForStudent($child);
            $childTotalPaid = (float) ($totals['paidAmount'] ?? 0.0);
            $childBalanceDue = (float) ($totals['remainingBalance'] ?? max(((float) ($totals['totalAmount'] ?? 0.0)) - $childTotalPaid, 0.0));

            // Year-over-year fee comparison
            $currentTuition = $activeSy ? TuitionFee::forGrade($child->level)->forSchoolYear($activeSy)->first() : null;
            $prevTuition = $prevSy ? TuitionFee::forGrade($child->level)->forSchoolYear($prevSy)->first() : null;
            $currentBaseFee = $currentTuition ? (float) $currentTuition->amount : (float) ($totals['baseTuition'] ?? 0.0);
            $prevBaseFee = $prevTuition ? (float) $prevTuition->amount : null;
            $changeAmount = $prevBaseFee !== null ? $currentBaseFee - $prevBaseFee : null;
            $changePercent = ($prevBaseFee !== null && $prevBaseFee > 0) ? round(($changeAmount / $prevBaseFee) * 100, 1) : null;

            // Optimized fetching for summary
            $childUpcoming = FeeRecord::where('student_id', $child->student_id)
                ->where(function ($q) {
                    $q->where('status', '!=', 'paid')
                        ->orWhereNull('status')
                        ->orWhere('balance', '>', 0);
                })
                ->orderBy('id', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($fee) {
                    $isOverdue = $fee->payment_date && $fee->payment_date->isPast() && ($fee->status !== 'paid');
                    $daysUntil = $fee->payment_date ? (int) now()->startOfDay()->diffInDays($fee->payment_date->startOfDay(), false) : null;

                    return [
                        'notes' => $fee->notes ?? 'Fee',
                        'balance' => (float) $fee->balance,
                        'payment_date' => $fee->payment_date ? $fee->payment_date->format('M d, Y') : 'N/A',
                        'isOverdue' => $isOverdue,
                        'daysUntil' => $daysUntil,
                    ];
                });

            return [
                'student_id' => $child->student_id,
                'balanceDue' => (float) $childBalanceDue,
                'totalPaid' => (float) $childTotalPaid,
                'totalAmount' => (float) ($totals['totalAmount'] ?? 0.0),
                'upcomingFees' => $childUpcoming,
                'feeComparison' => [
                    'currentYear' => $activeSy,
                    'previousYear' => $prevSy,
                    'currentBaseFee' => $currentBaseFee,
                    'previousBaseFee' => $prevBaseFee,
                    'changeAmount' => $changeAmount,
                    'changePercent' => $changePercent,
                ],
            ];
        });

        $consolidatedBalanceDue = (float) $childrenSummaries->sum('balanceDue');
        $consolidatedTotalPaid = (float) $childrenSummaries->sum('totalPaid');

        return response()->json([
            'consolidatedBalanceDue' => $consolidatedBalanceDue,
            'consolidatedTotalPaid' => $consolidatedTotalPaid,
            'children' => $childrenSummaries,
        ]);
    }

    /**
     * Display the admin dashboard.
     */
    public function admin_dashboard(): View
    {
        $hasStudents = Student::exists();

        $activeSy = SystemSetting::getActiveSchoolYear();

        // Filter Options
        $schoolYears = \App\Models\Student::distinct()->whereNotNull('school_year')->orderBy('school_year', 'desc')->pluck('school_year');

        // Ensure activeSy is in the list (in case no students are enrolled in it yet)
        if ($activeSy && ! $schoolYears->contains($activeSy)) {
            $schoolYears->prepend($activeSy);
        }

        $levels = ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
        $sections = Section::orderBy('name')->pluck('name');

        // Date ranges for Trends
        $startOfMonth = now()->startOfMonth();
        $startOfLastMonth = now()->subMonth()->startOfMonth();
        $endOfLastMonth = now()->subMonth()->endOfMonth();
        $startOfWeek = now()->startOfWeek();
        $startOfLastWeek = now()->subWeek()->startOfWeek();
        $endOfLastWeek = now()->subWeek()->endOfWeek();
        if (! $hasStudents) {
            $totalCollected = 0.0;
            $prevTotalCollected = 0.0;
            $pendingOutstanding = 0.0;
            $pendingApprovals = 0.0;
            $expectedCollection = 0.0;
            $studentsCount = 0;
            $prevStudentsCount = 0;
            $smsSentThisWeek = 0;
            $smsSentLastWeek = 0;
            $recentTransactions = collect();
            $pendingPayments = collect();
        } else {
            // Reconcile ledger for students in active School Year so metrics reflect assignment totals
            try {
                $svc = app(\App\Services\FeeManagementService::class);
                if ($activeSy) {
                    \App\Models\Student::where('school_year', $activeSy)->get(['student_id'])->each(function ($s) use ($svc) {
                        try {
                            $svc->recomputeStudentLedger($s);
                        } catch (\Throwable $e) {
                        }
                    });
                }
            } catch (\Throwable $e) {
            }
            // 1. Total Collected (This Month vs Last Month)
            $totalCollected = \App\Models\Payment::where('paid_at', '>=', $startOfMonth)->sum('amount_paid');
            $prevTotalCollected = \App\Models\Payment::whereBetween('paid_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount_paid');

            // Pending Approvals (Real-time)
            $pendingApprovals = \App\Models\Payment::where('status', 'pending')->sum('amount_paid');

            // 2. Pending Outstanding (use student Net Payable basis: totalAmount - paidAmount)
            $svc = app(\App\Services\FeeManagementService::class);
            $pendingOutstanding = 0.0;
            \App\Models\Student::select(['student_id', 'level', 'school_year'])->chunk(200, function ($chunk) use (&$pendingOutstanding, $svc) {
                foreach ($chunk as $stu) {
                    $t = $svc->computeTotalsForStudent($stu);
                    $pendingOutstanding += max(0.0, ((float) ($t['totalAmount'] ?? 0)) - ((float) ($t['paidAmount'] ?? 0)));
                }
            });

            // Expected Collection (Total Fees Assigned)
            // Note: This is an estimate. For strict accuracy, sum of all FeeRecords amounts?
            // Keeping original logic for Expected Collection for now, or updating it to match FeeRecords.
            $totalExpected = \App\Models\FeeRecord::sum('amount');
            $expectedCollection = $totalCollected + $pendingOutstanding;

            // 3. Students Count (excludes Withdrawn/Archived — only active/irregular students)
            $studentsCount = \App\Models\Student::whereNotIn('enrollment_status', ['Withdrawn', 'Archived'])->count();
            $prevStudentsCount = \App\Models\Student::where('created_at', '<', $startOfMonth)->whereNotIn('enrollment_status', ['Withdrawn', 'Archived'])->count();

            // 4. SMS Sent (This Week vs Last Week)
            $smsSentThisWeek = \App\Models\SmsLog::where('status', 'sent')
                ->where('sent_at', '>=', $startOfWeek)
                ->count();
            $smsSentLastWeek = \App\Models\SmsLog::where('status', 'sent')
                ->whereBetween('sent_at', [$startOfLastWeek, $endOfLastWeek])
                ->count();

            // Recent Transactions (mapped for server-side render)
            $recentTransactions = \App\Models\Payment::with('student')
                ->orderBy('paid_at', 'desc')
                ->orderBy('id', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($pay) {
                    $ipAddress = null;
                    if (class_exists('\\App\\Models\\AuditLog')) {
                        $audit = \App\Models\AuditLog::where('model_type', 'App\\Models\\Payment')
                            ->where('model_id', $pay->id)
                            ->latest()
                            ->first();
                        if ($audit) {
                            $ipAddress = $audit->ip_address;
                        }
                    }

                    return [
                        'student_id' => $pay->student_id,
                        'student_name' => $pay->student ? $pay->student->full_name : 'Unknown',
                        'paid_at' => $pay->paid_at ? $pay->paid_at->format('M d, Y H:i') : 'N/A',
                        'method' => $pay->method ?? 'N/A',
                        'reference_number' => $pay->reference_number ?? 'N/A',
                        'amount_paid' => (float) ($pay->amount_paid ?? 0),
                        'ip_address' => $ipAddress,
                    ];
                });

            // Pending Payments — filtered by active school year, aggregated per student
            $activeSyStudentIds = \App\Models\Student::where('school_year', $activeSy)
                ->whereNotIn('enrollment_status', ['Withdrawn', 'Archived'])
                ->pluck('student_id');
            $allPending = \App\Models\FeeRecord::where('balance', '>', 0)
                ->whereIn('student_id', $activeSyStudentIds)
                ->where('record_type', '!=', 'adjustment')
                ->with(['student.parents', 'student.feeAssignments'])
                ->get()
                ->groupBy('student_id')
                ->map(function ($records) {
                    $rec          = $records->first();
                    $totalBalance = (float) $records->sum(fn ($r) => (float) $r->balance);
                    $earliest     = $records->filter(fn ($r) => $r->payment_date)->sortBy('payment_date')->first() ?? $rec;
                    $dueDate      = $earliest->payment_date
                        ? \Carbon\Carbon::parse($earliest->payment_date)
                        : $earliest->created_at;
                    $daysOverdue  = $dueDate && $dueDate->isPast() ? (int) $dueDate->diffInDays(now()) : 0;

                    $parentName = 'N/A';
                    if ($rec->student && $rec->student->parents->isNotEmpty()) {
                        $primary    = $rec->student->parents->where('pivot.is_primary', true)->first()
                            ?? $rec->student->parents->first();
                        $parentName = $primary->full_name ?? 'N/A';
                    }

                    $rawLevel  = $rec->student->level ?? '';
                    $levelAbbr = $rawLevel ? preg_replace('/Grade\s*/i', 'G', $rawLevel) : '—';

                    // Whole tuition payable from fee assignment
                    $feeAssignment = $rec->student ? $rec->student->feeAssignments->sortByDesc('created_at')->first() : null;
                    $totalTuition  = $feeAssignment ? (float) $feeAssignment->total_amount : 0;

                    // Actual payments made by the student
                    $totalPaid = (float) \App\Models\Payment::where('student_id', $rec->student_id)
                        ->whereIn('status', ['approved', 'paid'])
                        ->sum('amount_paid');

                    return [
                        'student_id'    => $rec->student_id,
                        'record_type'   => 'Total',
                        'total_tuition' => $totalTuition,
                        'total_paid'    => $totalPaid,
                        'balance'       => max(0, $totalTuition - $totalPaid),
                        'student_name'  => $rec->student ? $rec->student->full_name : 'Unknown',
                        'parent_name'   => $parentName,
                        'level'         => $levelAbbr,
                        'due_date'      => $dueDate ? $dueDate->format('M d, Y') : 'N/A',
                        'days_overdue'  => $daysOverdue,
                    ];
                })
                ->sortByDesc('balance');
            $pendingPaymentsTotal = $allPending->count();
            $pendingPayments       = $allPending->take(10)->values();
        }

        return view('auth.admin_dashboard', [
            'totalCollected' => (float) $totalCollected,
            'prevTotalCollected' => (float) $prevTotalCollected,
            'pendingOutstanding' => (float) $pendingOutstanding,
            'pendingApprovals' => $pendingApprovals,
            'expectedCollection' => (float) $expectedCollection,
            'studentsCount' => (int) $studentsCount,
            'prevStudentsCount' => (int) $prevStudentsCount,
            'smsSentThisWeek' => (int) $smsSentThisWeek,
            'smsSentLastWeek' => (int) $smsSentLastWeek,
            'recentTransactions' => $recentTransactions,
            'pendingPayments' => $pendingPayments,
            'pendingPaymentsTotal' => $pendingPaymentsTotal ?? 0,
            'schoolYears' => $schoolYears,
            'levels' => $levels,
            'sections' => $sections,
            'activeSy' => $activeSy,
        ]);
    }

    public function admin_metrics(Request $request): \Illuminate\Http\JsonResponse
    {
        // Apply Filters
        $schoolYear = $request->input('school_year');
        $level = $request->input('level');
        $section = $request->input('section');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Save filters to session
        session([
            'admin_dashboard_filters' => [
                'school_year' => $schoolYear,
                'level' => $level,
                'section' => $section,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);

        $studentQuery = \App\Models\Student::query()
            ->whereNotIn('enrollment_status', ['Withdrawn', 'Archived']);
        if ($schoolYear) {
            $studentQuery->where('school_year', $schoolYear);
        }
        if ($level) {
            $studentQuery->where('level', $level);
        }
        if ($section) {
            $studentQuery->where('section', $section);
        }

        $studentIds = $studentQuery->pluck('student_id');

        if ($studentIds->isEmpty()) {
            return response()->json([
                'totalCollected' => 0.0,
                'prevTotalCollected' => 0.0,
                'pendingOutstanding' => 0.0,
                'expectedCollection' => 0.0,
                'studentsCount' => 0,
                'prevStudentsCount' => 0,
                'smsSentThisWeek' => 0,
                'smsSentLastWeek' => 0,
                'collectionsByGrade' => [],
                'paymentTrends' => [],
                'pendingPayments' => [],
                'recentTransactions' => [],
            ]);
        }

        // Date ranges for Trends
        $startOfMonth = now()->startOfMonth();
        $startOfLastMonth = now()->subMonth()->startOfMonth();
        $endOfLastMonth = now()->subMonth()->endOfMonth();

        // 1. Total Collected (Filtered)
        $paymentQuery = \App\Models\Payment::whereIn('student_id', $studentIds);
        if ($startDate && $endDate) {
            $paymentQuery->whereBetween('paid_at', [$startDate, $endDate]);
        } else {
            $paymentQuery->where('paid_at', '>=', $startOfMonth);
        }
        $totalCollected = $paymentQuery->sum('amount_paid');

        // Pending Approvals (Filtered)
        $pendingApprovals = \App\Models\Payment::where('status', 'pending')
            ->whereIn('student_id', $studentIds)
            ->sum('amount_paid');

        // Previous collected (for trend)
        $prevTotalCollected = 0;
        if (! $startDate) {
            $prevTotalCollected = \App\Models\Payment::whereIn('student_id', $studentIds)
                ->whereBetween('paid_at', [$startOfLastMonth, $endOfLastMonth])
                ->sum('amount_paid');
        }

        // 2. Pending Outstanding (use student Net Payable basis across filtered students)
        $svc = app(\App\Services\FeeManagementService::class);
        $pendingOutstanding = 0.0;
        \App\Models\Student::whereIn('student_id', $studentIds)->select(['student_id', 'level', 'school_year'])->chunk(200, function ($chunk) use (&$pendingOutstanding, $svc) {
            foreach ($chunk as $stu) {
                $t = $svc->computeTotalsForStudent($stu);
                $pendingOutstanding += max(0.0, ((float) ($t['totalAmount'] ?? 0)) - ((float) ($t['paidAmount'] ?? 0)));
            }
        });

        // Calculate expected collection based on fee records total amount if possible,
        // or simplistic approximation: collected + outstanding.
        // For accurate 'Total Expected' irrespective of payments:
        $totalExpected = \App\Models\FeeRecord::whereIn('student_id', $studentIds)->sum('amount');
        // But to maintain logic 'Collected + Outstanding':
        $expectedCollection = $totalCollected + $pendingOutstanding;

        // 3. Students Count
        $studentsCount = $studentQuery->count();
        $prevStudentsCount = \App\Models\Student::where('created_at', '<', $startOfMonth)->count();

        // 4. SMS Sent
        $smsSentThisWeek = \App\Models\SmsLog::where('status', 'sent')
            ->whereBetween('sent_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $smsSentLastWeek = \App\Models\SmsLog::where('status', 'sent')
            ->whereBetween('sent_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->count();

        // 5. Collections by Grade (or Section if Level is selected)
        if ($level) {
            $collectionsByGrade = DB::table('payments')
                ->join('students', 'payments.student_id', '=', 'students.student_id')
                ->whereIn('students.student_id', $studentIds)
                ->select('students.section as label', DB::raw('SUM(payments.amount_paid) as total'))
                ->groupBy('students.section')
                ->orderBy('students.section')
                ->get();
        } else {
            $rawGrades = DB::table('payments')
                ->join('students', 'payments.student_id', '=', 'students.student_id')
                ->whereIn('students.student_id', $studentIds)
                ->select('students.level as label', DB::raw('SUM(payments.amount_paid) as total'))
                ->groupBy('students.level')
                ->get();

            // Ensure Grade 1 to 12 are displayed in correct order
            $allLevels = ['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
            $collectionsByGrade = collect($allLevels)->map(function ($lvl) use ($rawGrades) {
                $found = $rawGrades->firstWhere('label', $lvl);

                return [
                    'label' => $lvl,
                    'total' => $found ? (float) $found->total : 0.0,
                ];
            });
        }

        // 6. Payment Trends (Jan-Dec Current Year)
        $rawTrends = DB::table('payments')
            ->join('students', 'payments.student_id', '=', 'students.student_id')
            ->whereIn('students.student_id', $studentIds)
            ->select(
                DB::raw("TO_CHAR(paid_at, 'MM') as month_num"),
                DB::raw("TO_CHAR(paid_at, 'Mon') as month"),
                DB::raw('SUM(amount_paid) as total')
            )
            ->whereYear('paid_at', now()->year)
            ->groupBy('month_num', 'month')
            ->orderBy('month_num')
            ->get();

        // Fill all 12 months
        $paymentTrends = collect();
        for ($m = 1; $m <= 12; $m++) {
            $monthNum = str_pad($m, 2, '0', STR_PAD_LEFT);
            $monthName = \Carbon\Carbon::createFromDate(now()->year, $m, 1)->format('M');

            $found = $rawTrends->firstWhere('month_num', $monthNum);

            $paymentTrends->push([
                'month' => $monthName,
                'total' => $found ? (float) $found->total : 0.0,
            ]);
        }

        // 7. Recent Pending Payments — aggregated per student with whole tuition balance
        $pendingPaymentsTotal = \App\Models\FeeRecord::where('balance', '>', 0)
            ->whereIn('student_id', $studentIds)
            ->where('record_type', '!=', 'adjustment')
            ->distinct('student_id')
            ->count('student_id');
        $pendingPayments = \App\Models\FeeRecord::where('balance', '>', 0)
            ->whereIn('student_id', $studentIds)
            ->where('record_type', '!=', 'adjustment')
            ->with(['student.parents', 'student.feeAssignments'])
            ->get()
            ->groupBy('student_id')
            ->map(function ($records) {
                $rec          = $records->first();
                $totalBalance = (float) $records->sum(fn ($r) => (float) $r->balance);
                $earliest     = $records->filter(fn ($r) => $r->payment_date)->sortBy('payment_date')->first() ?? $rec;
                $dueDate      = $earliest->payment_date ? \Carbon\Carbon::parse($earliest->payment_date) : $earliest->created_at;
                $daysOverdue  = $dueDate && $dueDate->isPast() ? (int) $dueDate->diffInDays(now()) : 0;

                $parentName = 'N/A';
                if ($rec->student && $rec->student->parents->isNotEmpty()) {
                    $parent = $rec->student->parents->where('pivot.is_primary', true)->first() ?? $rec->student->parents->first();
                    $parentName = $parent->full_name ?? 'N/A';
                }

                $rawLevel  = $rec->student->level ?? '';
                $levelAbbr = $rawLevel ? preg_replace('/Grade\s*/i', 'G', $rawLevel) : '—';

                // Whole tuition payable from fee assignment
                $feeAssignment = $rec->student ? $rec->student->feeAssignments->sortByDesc('created_at')->first() : null;
                $totalTuition  = $feeAssignment ? (float) $feeAssignment->total_amount : 0;

                // Actual payments made by the student
                $totalPaid = (float) \App\Models\Payment::where('student_id', $rec->student_id)
                    ->whereIn('status', ['approved', 'paid'])
                    ->sum('amount_paid');

                return [
                    'student_id'    => $rec->student_id,
                    'record_type'   => 'Total',
                    'total_tuition' => $totalTuition,
                    'total_paid'    => $totalPaid,
                    'balance'       => max(0, $totalTuition - $totalPaid),
                    'student_name'  => $rec->student ? $rec->student->full_name : 'Unknown',
                    'parent_name'   => $parentName,
                    'level'         => $levelAbbr,
                    'due_date'      => $dueDate ? $dueDate->format('M d, Y') : 'N/A',
                    'days_overdue'  => (int) $daysOverdue,
                ];
            })
            ->sortByDesc('balance')
            ->take(10)
            ->values();

        // 8. Recent Transactions
        $recentTransactions = \App\Models\Payment::whereIn('student_id', $studentIds)
            ->with('student')
            ->orderBy('paid_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($pay) {
                // Try to find IP in AuditLog
                $ipAddress = null;
                // Only try to find audit log if AuditLog model exists
                if (class_exists('\App\Models\AuditLog')) {
                    $audit = \App\Models\AuditLog::where('model_type', 'App\Models\Payment')
                        ->where('model_id', $pay->id)
                        ->first();
                    if ($audit) {
                        $ipAddress = $audit->ip_address;
                    }
                }

                return [
                    'student_id' => $pay->student_id,
                    'amount_paid' => (float) ($pay->amount_paid ?? 0),
                    'status' => 'Completed',
                    'student_name' => $pay->student ? $pay->student->full_name : 'Unknown',
                    'paid_at' => $pay->paid_at ? $pay->paid_at->format('M d, Y H:i') : 'N/A',
                    'method' => $pay->method ?? 'N/A',
                    'reference_number' => $pay->reference_number ?? 'N/A',
                    'ip_address' => $ipAddress,
                ];
            });

        return response()->json([
            'totalCollected' => (float) $totalCollected,
            'prevTotalCollected' => (float) $prevTotalCollected,
            'pendingApprovals' => (float) $pendingApprovals,
            'pendingOutstanding' => (float) $pendingOutstanding,
            'expectedCollection' => (float) $expectedCollection,
            'studentsCount' => (int) $studentsCount,
            'prevStudentsCount' => (int) $prevStudentsCount,
            'smsSentThisWeek' => (int) $smsSentThisWeek,
            'smsSentLastWeek' => (int) $smsSentLastWeek,
            'collectionsByGrade' => $collectionsByGrade,
            'paymentTrends' => $paymentTrends,
            'pendingPayments'      => $pendingPayments,
            'pendingPaymentsTotal' => (int) $pendingPaymentsTotal,
            'recentTransactions'   => $recentTransactions,
        ]);
    }

    /**
     * Display the staff dashboard.
     */
    public function staff_dashboard(): View
    {
        return view('auth.staff_dashboard');
    }

    public function student_payments(): View
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $studentId = optional($user)->roleable_type === 'App\\Models\\Student' ? $user->roleable_id : null;
        $payments = $studentId
            ? \App\Models\Payment::where('student_id', $studentId)->with('receipt')->orderBy('paid_at', 'desc')->paginate(15)
            : collect();

        $transactions = collect();
        if ($user) {
            try {
                $transactions = DB::table('payment_transactions')
                    ->where('user_id', $user->user_id)
                    ->orderBy('created_at', 'desc')
                    ->limit(50)
                    ->get();
            } catch (\Throwable $e) {
                $transactions = collect();
            }
        }

        return view('auth.student_payments', [
            'payments' => $payments,
            'transactions' => $transactions,
        ]);
    }

    public function downloadReceipt(\App\Models\Payment $payment)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (! $user) {
            abort(403);
        }
        $isStudent = ($user->roleable_type ?? '') === 'App\\Models\\Student';
        $isParent = optional($user->role)->role_name === 'parent' || (($user->roleable_type ?? '') === \App\Models\ParentContact::class);
        if ($isStudent) {
            if ($payment->student_id !== ($user->roleable_id ?? null)) {
                abort(403);
            }
        } elseif ($isParent) {
            $parent = $user->roleable;
            $linkedIds = $parent ? $parent->students()->pluck('students.student_id') : collect();
            if (! $linkedIds->contains($payment->student_id)) {
                abort(403);
            }
        } else {
            abort(403);
        }

        $receipt = $payment->receipt;
        if (! $receipt || ! ($receipt->file_url ?? '')) {
            abort(404);
        }

        $fileUrl = $receipt->file_url;

        if (str_starts_with($fileUrl, 'http://') || str_starts_with($fileUrl, 'https://')) {
            return redirect()->away($fileUrl);
        }

        if (str_starts_with($fileUrl, 'supabase://')) {
            $supabaseUrl = env('SUPABASE_URL', '');
            $serviceKey = env('SUPABASE_SERVICE_KEY', '');
            if ($supabaseUrl && $serviceKey) {
                $path = substr($fileUrl, strlen('supabase://'));
                $parts = explode('/', $path, 2);
                $bucket = $parts[0] ?? '';
                $objectPath = $parts[1] ?? '';
                if ($bucket && $objectPath) {
                    $signEndpoint = rtrim($supabaseUrl, '/').'/storage/v1/object/sign/'.$bucket.'/'.$objectPath;
                    $resp = Http::withHeaders([
                        'Authorization' => 'Bearer '.$serviceKey,
                        'Content-Type' => 'application/json',
                    ])->post($signEndpoint, [
                        'expiresIn' => 300,
                    ]);
                    if ($resp->successful()) {
                        $signed = $resp->json('signedURL') ?? $resp->json('signedUrl') ?? $resp->json('url') ?? null;
                        if ($signed) {
                            $downloadUrl = rtrim($supabaseUrl, '/').$signed;

                            return redirect()->away($downloadUrl);
                        }
                    }
                }
            }
            abort(403);
        }

        if (str_starts_with($fileUrl, 'receipt://')) {
            return view('auth.student_receipt', [
                'payment' => $payment,
            ]);
        }

        $disk = Storage::disk('public');
        $relativePath = ltrim($fileUrl, '/');
        if ($disk->exists($relativePath)) {
            $absolutePath = $disk->path($relativePath);

            return response()->file($absolutePath, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        if (is_file($fileUrl)) {
            return response()->file($fileUrl, [
                'Content-Type' => 'application/pdf',
            ]);
        }

        abort(404);
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
     * Handle logout request.
     */
    public function logout(Request $request): RedirectResponse
    {
        // Audit Log
        try {
            $user = Auth::user();
            if ($user) {
                \App\Services\AuditService::log(
                    'User Logout',
                    $user,
                    "User logged out: {$user->email}",
                    null,
                    ['ip' => $request->ip()]
                );
            }
        } catch (\Throwable $e) {
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function changePassword(): View
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (! $user) {
            abort(403);
        }

        return view('auth.change_password');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (! $user) {
            abort(403);
        }
        $data = $request->validate([
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $user->password = \Illuminate\Support\Facades\Hash::make($data['new_password']);
        $user->must_change_password = false;
        $user->save();

        try {
            \App\Services\AuditService::log(
                'Password Changed',
                $user,
                "User changed password: {$user->email}",
                null,
                ['ip' => $request->ip()]
            );
        } catch (\Throwable $e) {
        }

        return redirect()->route('user_dashboard')->with('success', 'Password updated successfully.');
    }

    /**
     * Submit a link-student request (pending admin approval).
     */
    public function linkStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string|exists:students,student_id',
            'relationship' => 'nullable|string|max:50',
            'reason' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $parent = $user->roleable;

        if (! $parent || ! ($parent instanceof ParentContact)) {
            return back()->with('error', 'Unauthorized action.');
        }

        $studentId = $request->input('student_id');

        // Check if already linked
        if ($parent->students()->where('students.student_id', $studentId)->exists()) {
            return back()->with('error', 'Student is already linked to your account.');
        }

        // Check if a pending request already exists
        $pendingExists = \App\Models\StudentLinkRequest::where('parent_id', $parent->id)
            ->where('student_id', $studentId)
            ->where('type', 'link')
            ->where('status', 'pending')
            ->exists();

        if ($pendingExists) {
            return back()->with('error', 'You already have a pending link request for this student.');
        }

        \App\Models\StudentLinkRequest::create([
            'parent_id' => $parent->id,
            'student_id' => $studentId,
            'type' => 'link',
            'relationship' => $request->input('relationship', 'Parent'),
            'reason' => $request->input('reason'),
            'status' => 'pending',
        ]);

        // Notify admins
        try {
            $student = \App\Models\Student::where('student_id', $studentId)->first();
            $adminUsers = \App\Models\User::whereHas('role', fn ($q) => $q->where('role_name', 'admin'))->get();
            foreach ($adminUsers as $admin) {
                \Illuminate\Support\Facades\DB::table('notifications')->insert([
                    'user_id' => $admin->user_id,
                    'title' => 'Student Link Request',
                    'body' => $parent->full_name . ' requests to link student ' . ($student ? $student->full_name : $studentId) . '.',
                    'created_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Link request notification failed: ' . $e->getMessage());
        }

        return redirect()->route('parent.dashboard')
            ->with('success', 'Link request submitted! An administrator will review your request shortly.');
    }

    /**
     * Submit an unlink-student request (pending admin approval).
     */
    public function unlinkStudent(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string',
            'reason' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $parent = $user->roleable;

        if (! $parent || ! ($parent instanceof ParentContact)) {
            return back()->with('error', 'Unauthorized action.');
        }

        $studentId = $request->input('student_id');

        // Must be currently linked
        if (! $parent->students()->where('students.student_id', $studentId)->exists()) {
            return back()->with('error', 'Student is not linked to your account.');
        }

        // Check if a pending request already exists
        $pendingExists = \App\Models\StudentLinkRequest::where('parent_id', $parent->id)
            ->where('student_id', $studentId)
            ->where('type', 'unlink')
            ->where('status', 'pending')
            ->exists();

        if ($pendingExists) {
            return back()->with('error', 'You already have a pending unlink request for this student.');
        }

        \App\Models\StudentLinkRequest::create([
            'parent_id' => $parent->id,
            'student_id' => $studentId,
            'type' => 'unlink',
            'reason' => $request->input('reason'),
            'status' => 'pending',
        ]);

        return redirect()->route('parent.dashboard', ['section' => 'students'])
            ->with('success', 'Unlink request submitted! An administrator will review your request shortly.');
    }
}
