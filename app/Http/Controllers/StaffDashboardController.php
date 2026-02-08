<?php

namespace App\Http\Controllers;

<<<<<<< HEAD
use App\Models\FeeRecord;
use App\Models\FeeUpdateAudit;
use App\Models\Student;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\FeeManagementService;
use App\Services\SmsGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
=======
use App\Models\Student;
use App\Models\FeeRecord;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
<<<<<<< HEAD
    protected $smsGateway;

    public function __construct(SmsGatewayService $smsGateway)
    {
        $this->smsGateway = $smsGateway;
    }

    public function updateCategory(Request $request, Student $student): RedirectResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->hasRole('staff')) {
            abort(403, 'Unauthorized');
        }

        $activeYear = SystemSetting::where('key', 'school_year')->value('value');
        if (! $activeYear) {
            return back()->with('error', 'Please set an active School Year to continue.');
        }

        if ($student->school_year && $student->school_year !== $activeYear) {
            return back()
                ->with('error', 'This student record belongs to locked School Year '.$student->school_year.'. Only records in the active School Year '.$activeYear.' can be modified.')
                ->withInput();
        }

        $allowed = ['STEM', 'ABM', 'HUMSS', 'GAS', 'ICT', 'HE', 'IA', 'Agri-Fishery'];
        $isShs = in_array($student->level, ['Grade 11', 'Grade 12']);
        $strand = (string) $request->input('strand', '');
        if ($isShs && ($strand === '' || ! in_array($strand, $allowed, true))) {
            return back()->with('error', 'Please select a valid strand for Senior High.')->withInput();
        }
        if (! $isShs) {
            $strand = '';
        }
        $student->strand = $strand ?: null;
        $student->save();
        try {
            app(\App\Services\FeeManagementService::class)->recomputeStudentLedger($student);
        } catch (\Throwable $e) {
        }

        return back()->with('success', 'Category updated successfully.');
    }

=======
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
    /**
     * Display the staff dashboard with search and metrics.
     */
    public function index(Request $request): View
    {
<<<<<<< HEAD
        $data = $this->getStudentData($request);
        $data['notifications'] = DB::table('notifications')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('auth.staff_dashboard', $data);
    }

    /**
     * Get the student data for the dashboard (JSON).
     */
    public function list(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $this->getStudentData($request);

        return response()->json($data);
    }

    private function getStudentData(Request $request): array
    {
        $user = Auth::user();
        if (! $user || ! $user->hasRole('staff')) {
            abort(403, 'Unauthorized');
        }

        $activeYear = SystemSetting::where('key', 'school_year')->value('value');

        $query = trim($request->input('q', ''));
        $statusFilter = $request->input('status', '');
        $levelFilter = $request->input('level', '');
        $sectionFilter = $request->input('section', '');
        $strandFilter = $request->input('strand', '');
        $sort = $request->input('sort', 'name');
        $direction = strtolower($request->input('direction', 'asc')) === 'desc' ? 'desc' : 'asc';
        $perPage = 15;

        $students = Student::with(['feeRecords', 'user', 'payments'])
            ->when($query !== '' && $query !== null, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('student_id', 'like', "%{$query}%")
                        ->orWhere('first_name', 'like', "%{$query}%")
                        ->orWhere('last_name', 'like', "%{$query}%")
                        ->orWhere('level', 'like', "%{$query}%");
                });
            })
            ->when($levelFilter !== '' && $levelFilter !== null, function ($q) use ($levelFilter) {
                $q->where('level', $levelFilter);
            })
            ->when($sectionFilter !== '' && $sectionFilter !== null, function ($q) use ($sectionFilter) {
                $q->where('section', $sectionFilter);
            })
            ->when($strandFilter !== '' && $strandFilter !== null, function ($q) use ($strandFilter) {
                $q->where('strand', $strandFilter);
            })
            ->orderBy('last_name')
            ->get();

        $svc = app(FeeManagementService::class);
        $studentRecords = $students->map(function ($student) use ($svc) {
            $totals = $svc->computeTotalsForStudent($student);
            $totalFee = (float) ($totals['totalAmount'] ?? 0.0);

            $paidAmount = (float) $student->feeRecords
                ->where('status', 'paid')
                ->sum('amount');

            $dueAmount = max($totalFee - $paidAmount, 0);

            $status = 'unpaid';
            $statusText = 'Unpaid';

            if ($dueAmount <= 0) {
                $status = 'paid';
                $statusText = 'Paid';
            } elseif ($paidAmount > 0 && $dueAmount > 0) {
                $status = 'partially-paid';
                $statusText = 'Partially Paid';
            }

            $latestPayment = $student->feeRecords
                ->where('status', 'paid')
                ->sortByDesc(function ($record) {
                    return $record->payment_date ?? $record->created_at;
                })
                ->first();

            $latestRejectedPayment = $student->payments
                ->where('status', 'rejected')
                ->sortByDesc('created_at')
                ->first();

            $latestTransaction = $student->payments
                ->sortByDesc('created_at')
                ->first();

            return (object) [
                'student' => $student,
                'totalFee' => $totalFee,
                'paidAmount' => $paidAmount,
                'dueAmount' => $dueAmount,
                'status' => $status,
                'statusText' => $statusText,
                'latestPaymentAt' => $latestPayment
                    ? ($latestPayment->payment_date ?? $latestPayment->created_at)
                    : null,
                'latestRejectedPayment' => $latestRejectedPayment,
                'latestTransaction' => $latestTransaction,
            ];
        });

        // Calculate stats before applying status filter
        $stats = [
            'paid' => $studentRecords->where('status', 'paid')->count(),
            'partial' => $studentRecords->where('status', 'partially-paid')->count(),
            'unpaid' => $studentRecords->where('status', 'unpaid')->count(),
        ];

        if ($statusFilter !== '' && $statusFilter !== null) {
            $studentRecords = $studentRecords
                ->filter(fn ($record) => $record->status === $statusFilter)
                ->values();
        }

        $studentRecords = (match ($sort) {
            'due' => $direction === 'desc'
                ? $studentRecords->sortByDesc(fn ($r) => $r->dueAmount)
                : $studentRecords->sortBy(fn ($r) => $r->dueAmount),
            'paid' => $direction === 'desc'
                ? $studentRecords->sortByDesc(fn ($r) => $r->paidAmount)
                : $studentRecords->sortBy(fn ($r) => $r->paidAmount),
            'latest_payment' => $direction === 'desc'
                ? $studentRecords->sortByDesc(fn ($r) => $r->latestPaymentAt ? $r->latestPaymentAt->timestamp : 0)
                : $studentRecords->sortBy(fn ($r) => $r->latestPaymentAt ? $r->latestPaymentAt->timestamp : 0),
            'section' => $direction === 'desc'
                ? $studentRecords->sortByDesc(fn ($r) => $r->student->section)
                : $studentRecords->sortBy(fn ($r) => $r->student->section),
            'status' => $direction === 'desc'
                ? $studentRecords->sortByDesc(fn ($r) => $r->status)
                : $studentRecords->sortBy(fn ($r) => $r->status),
            default => $direction === 'desc'
                ? $studentRecords->sortByDesc(fn ($r) => ($r->student->last_name ?? '').' '.($r->student->first_name ?? ''))
                : $studentRecords->sortBy(fn ($r) => ($r->student->last_name ?? '').' '.($r->student->first_name ?? '')),
        })->values();

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $paginatedRecords = new LengthAwarePaginator(
            $studentRecords->slice(($currentPage - 1) * $perPage, $perPage)->values(),
            $studentRecords->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $levels = Student::select('level')->distinct()->pluck('level')->filter()->values();

        $sectionsQuery = Student::select('section')->distinct();
        if ($levelFilter) {
            $sectionsQuery->where('level', $levelFilter);
        }
        if ($strandFilter) {
            $sectionsQuery->where('strand', $strandFilter);
        }
        $sections = $sectionsQuery->pluck('section')->filter()->values();

        $strandsQuery = Student::select('strand')->distinct();
        if ($levelFilter) {
            $strandsQuery->where('level', $levelFilter);
        }
        $strands = $strandsQuery->pluck('strand')->filter()->values();

        return [
            'studentRecords' => $paginatedRecords,
            'query' => $query,
            'status' => $statusFilter,
            'level' => $levelFilter,
            'section' => $sectionFilter,
            'strand' => $strandFilter,
            'sort' => $sort,
            'direction' => $direction,
            'levels' => $levels,
            'sections' => $sections,
            'strands' => $strands,
            'activeYear' => $activeYear,
            'stats' => $stats,
        ];
=======
        $user = Auth::user();
        if (!$user || !$user->hasRole('staff')) {
            abort(403, 'Unauthorized');
        }

        $q = trim((string) $request->input('q', ''));

        // Paginated students for table (with fee records for totals/status)
        $students = Student::with('feeRecords')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('student_id', 'like', "%{$q}%")
                        ->orWhere('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('level', 'like', "%{$q}%");
                });
            })
            ->orderBy('last_name')
            ->paginate(10)
            ->withQueryString();

        // Metrics for chart: paid vs unpaid students
        $all = Student::with('feeRecords')->get();
        $paidCount = 0;
        $unpaidCount = 0;
        foreach ($all as $s) {
            $totalBalance = (float) $s->feeRecords->sum('balance');
            if ($s->feeRecords->count() > 0 && $totalBalance <= 0) {
                $paidCount++;
            } elseif ($totalBalance > 0) {
                $unpaidCount++;
            }
        }

        return view('auth.staff_dashboard', [
            'students' => $students,
            'query' => $q,
            'paidCount' => $paidCount,
            'unpaidCount' => $unpaidCount,
        ]);
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
    }

    /**
     * Ping/remind a student about their fees (logs an action for now).
     */
    public function remind(Student $student): RedirectResponse
    {
        $user = Auth::user();
<<<<<<< HEAD
        if (! $user || ! $user->hasRole('staff')) {
            abort(403, 'Unauthorized');
        }

        $activeYear = SystemSetting::where('key', 'school_year')->value('value');
        if (! $activeYear) {
            return back()->with('error', 'Please set an active School Year to continue.');
        }

        if ($student->school_year && $student->school_year !== $activeYear) {
            return back()->with('error', 'This student record belongs to locked School Year '.$student->school_year.'. Only records in the active School Year '.$activeYear.' can be modified.');
        }

=======
        if (!$user || !$user->hasRole('staff')) {
            abort(403, 'Unauthorized');
        }

>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
        // Find the application's user row for this student
        $targetUser = User::where('roleable_type', 'App\\Models\\Student')
            ->where('roleable_id', $student->student_id)
            ->first();

<<<<<<< HEAD
        if (! $targetUser) {
            Log::warning('Reminder failed: no user mapped to student', [
                'student_id' => $student->student_id,
            ]);

=======
        if (!$targetUser) {
            Log::warning('Reminder failed: no user mapped to student', [
                'student_id' => $student->student_id,
            ]);
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
            return back()->with('error', 'No user account found for this student.');
        }

        // Insert a realtime notification row (Supabase listens to inserts)
        try {
            DB::table('notifications')->insert([
                'user_id' => $targetUser->user_id,
                'title' => 'Fee Reminder',
<<<<<<< HEAD
                'body' => 'Hello '.$student->first_name.', please review your outstanding fees.',
=======
                'body' => 'Hello ' . $student->first_name . ', please review your outstanding fees.',
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
                'created_at' => now(),
            ]);

            Log::info('Fee reminder sent', [
                'staff_user_id' => $user->user_id ?? null,
                'target_user_id' => $targetUser->user_id,
                'student_id' => $student->student_id,
                'timestamp' => now()->toDateTimeString(),
            ]);

<<<<<<< HEAD
            // Send Real-time SMS if phone number exists
            $guardian = $student->parents->sortByDesc('pivot.is_primary')->first();
            $mobileNumber = $student->mobile_number ?? ($guardian ? $guardian->phone : null);

            if ($mobileNumber) {
                try {
                    $this->smsGateway->send(
                        $mobileNumber,
                        "Hello {$student->first_name}, this is a reminder from ".config('app.name').' to please review your outstanding fees.'
                    );
                } catch (\Exception $e) {
                    Log::warning('SMS Reminder failed', ['error' => $e->getMessage()]);
                }
            }

            return back()->with('success', 'Reminder sent to '.$student->full_name.'.');
=======
            return back()->with('success', 'Reminder sent to ' . $student->full_name . '.');
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
        } catch (\Throwable $e) {
            Log::error('Failed to create notification', [
                'error' => $e->getMessage(),
            ]);
<<<<<<< HEAD

=======
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
            return back()->with('error', 'Failed to send reminder.');
        }
    }

    /**
     * Approve payments for a student: mark all outstanding fee records as paid with zero balance.
     */
    public function approve(Student $student): RedirectResponse
    {
        $user = Auth::user();
<<<<<<< HEAD
        if (! $user || ! $user->hasRole('staff')) {
            abort(403, 'Unauthorized');
        }

        $allowed = SystemSetting::where('key', 'allow_staff_edit_fees')->value('value');
        if ($allowed !== '1') {
            abort(403, 'Editing fee records is disabled by administrator.');
        }

        $activeYear = SystemSetting::where('key', 'school_year')->value('value');
        if (! $activeYear) {
            return back()->with('error', 'Please set an active School Year to continue.');
        }

        if ($student->school_year && $student->school_year !== $activeYear) {
            return back()->with('error', 'This student record belongs to locked School Year '.$student->school_year.'. Only records in the active School Year '.$activeYear.' can be modified.');
        }

        $outstanding = FeeRecord::where('student_id', $student->student_id)
            ->where(function ($q) {
                $q->where('status', '!=', 'paid')
                    ->orWhereNull('status')
                    ->orWhere('balance', '>', 0);
=======
        if (!$user || !$user->hasRole('staff')) {
            abort(403, 'Unauthorized');
        }

        // Collect outstanding records first to compute total cleared amount
        $outstanding = FeeRecord::where('student_id', $student->student_id)
            ->where(function ($q) {
                $q->where('status', '!=', 'paid')
                  ->orWhereNull('status')
                  ->orWhere('balance', '>', 0);
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
            })
            ->get();

        $totalCleared = 0.0;
        foreach ($outstanding as $rec) {
            $val = is_numeric($rec->balance) ? (float) $rec->balance : 0.0;
            $totalCleared += $val;
        }

        $affected = 0;
        if ($outstanding->count() > 0) {
            $affected = FeeRecord::where('student_id', $student->student_id)
                ->where(function ($q) {
                    $q->where('status', '!=', 'paid')
<<<<<<< HEAD
                        ->orWhereNull('status')
                        ->orWhere('balance', '>', 0);
=======
                      ->orWhereNull('status')
                      ->orWhere('balance', '>', 0);
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
                })
                ->update(['status' => 'paid', 'balance' => 0]);
        }

        if ($affected > 0) {
<<<<<<< HEAD
            // Log Audit
            FeeUpdateAudit::create([
                'performed_by_user_id' => $user->user_id,
                'event_type' => 'staff_fee_approve_all',
                'message' => "Approved all outstanding payments for student {$student->student_id}. Total cleared: {$totalCleared}, Records affected: {$affected}",
                'affected_students_count' => 1,
                'school_year' => SystemSetting::where('key', 'school_year')->value('value'),
                'semester' => SystemSetting::where('key', 'semester')->value('value'),
            ]);

=======
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
            // Map student to user for transaction and notification
            $targetUser = User::where('roleable_type', 'App\\Models\\Student')
                ->where('roleable_id', $student->student_id)
                ->first();

            if ($targetUser) {
                // Best-effort: record a transaction and notify the student
                try {
                    DB::table('payment_transactions')->insert([
                        'user_id' => $targetUser->user_id,
                        'student_id' => $student->student_id,
                        'amount' => $totalCleared,
                        'type' => 'approval',
                        'note' => 'Payments approved by staff',
                        'staff_user_id' => $user->user_id ?? null,
                        'created_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('payment_transactions insert failed', ['error' => $e->getMessage()]);
                }

                try {
                    DB::table('notifications')->insert([
                        'user_id' => $targetUser->user_id,
                        'title' => 'Payment Approved',
<<<<<<< HEAD
                        'body' => 'Hi '.$student->first_name.', your payments have been recorded as paid.',
                        'created_at' => now(),
                        'updated_at' => now(),
=======
                        'body' => 'Hi ' . $student->first_name . ', your payments have been recorded as paid.',
                        'created_at' => now(),
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('notification insert failed (approval)', ['error' => $e->getMessage()]);
                }
<<<<<<< HEAD
                try {
                    app(\App\Services\FeeManagementService::class)->recomputeStudentLedger($student);
                } catch (\Throwable $e) {
                    Log::warning('fee recompute failed after approval', ['error' => $e->getMessage()]);
                }
            }

        }

        return back()->with('info', $student->full_name.' has no outstanding fees.');
    }

    /**
     * Display the SMS reminders page with student data and SMS history.
     */
    public function smsReminders(Request $request): View
    {
        $user = Auth::user();
        if (! $user || ! $user->hasRole('staff')) {
            abort(403, 'Unauthorized');
        }

        // Get students with pending payments for SMS
        $studentsWithPendingPayments = Student::with(['feeRecords' => function ($query) {
            $query->where(function ($q) {
                $q->where('status', '!=', 'paid')
                    ->orWhereNull('status')
                    ->orWhere('balance', '>', 0);
            });
        }])
            ->whereHas('feeRecords', function ($query) {
                $query->where(function ($q) {
                    $q->where('status', '!=', 'paid')
                        ->orWhereNull('status')
                        ->orWhere('balance', '>', 0);
                });
            })
            ->get();

        // Get SMS history (using notifications table for now)
        $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
        $smsHistory = DB::table('notifications')
            ->where('title', $operator, '%SMS%')
            ->orWhere('title', $operator, '%Reminder%')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($notification) {
                // Try to extract student info from the notification body
                $studentName = 'Unknown';
                $phoneNumber = null;

                if (preg_match('/Hello\s+([^,]+),/', $notification->body, $matches)) {
                    $studentName = trim($matches[1]);
                }

                return (object) [
                    'id' => $notification->id,
                    'student_name' => $studentName,
                    'phone_number' => $phoneNumber,
                    'message' => $notification->body,
                    'status' => 'sent', // Default status
                    'sent_at' => $notification->created_at,
                    'created_at' => $notification->created_at,
                ];
            });

        return view('auth.staff_sms_reminders', [
            'studentsWithPendingPayments' => $studentsWithPendingPayments,
            'smsHistory' => $smsHistory,
        ]);
    }

    /**
     * Send SMS reminders to students.
     */
    public function sendSMS(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->hasRole('staff')) {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            // 'sms_gateway' => 'required|string', // Deprecated: using system config
            // 'api_key' => 'required|string',     // Deprecated
            // 'sender_id' => 'required|string',   // Deprecated
            'message' => 'required|string|max:160',
            'student_selection' => 'required|string',
            'schedule_date' => 'nullable|date|after:today',
            'schedule_time' => 'nullable|time',
        ]);

        try {
            // Get students based on selection
            if ($request->student_selection === 'all_pending') {
                $students = Student::with(['feeRecords' => function ($query) {
                    $query->where(function ($q) {
                        $q->where('status', '!=', 'paid')
                            ->orWhereNull('status')
                            ->orWhere('balance', '>', 0);
                    });
                }])
                    ->whereHas('feeRecords', function ($query) {
                        $query->where(function ($q) {
                            $q->where('status', '!=', 'paid')
                                ->orWhereNull('status')
                                ->orWhere('balance', '>', 0);
                        });
                    })
                    ->get();
            } else {
                // For manual selection, we'd need checkboxes or multi-select
                // For now, return error
                return back()->with('error', 'Manual student selection is not yet implemented.');
            }

            if ($students->isEmpty()) {
                return back()->with('error', 'No students found with pending payments.');
            }

            $sentCount = 0;
            $failedCount = 0;

            foreach ($students as $student) {
                // Calculate outstanding amount for this student
                $totalOutstanding = (float) $student->feeRecords->sum('balance');

                if ($totalOutstanding <= 0) {
                    continue; // Skip students with no outstanding fees
                }

                // Replace placeholders in message
                $message = str_replace(
                    ['{student_name}', '{amount}'],
                    [$student->first_name, number_format($totalOutstanding, 2)],
                    $request->message
                );

                try {
                    // Send via SMS Gateway Service
                    if ($student->mobile_number) {
                        $this->smsGateway->send($student->mobile_number, $message);
                        $sentCount++;
                    } else {
                        // Fallback to notification only if no phone number
                        $targetUser = User::where('roleable_type', 'App\\Models\\Student')
                            ->where('roleable_id', $student->student_id)
                            ->first();

                        if ($targetUser) {
                            DB::table('notifications')->insert([
                                'user_id' => $targetUser->user_id,
                                'title' => 'SMS Reminder (No Phone)',
                                'body' => $message,
                                'created_at' => now(),
                            ]);
                        }
                    }

                } catch (\Exception $e) {
                    Log::error('SMS sending failed for student: '.$student->student_id, [
                        'error' => $e->getMessage(),
                        'student_id' => $student->student_id,
                    ]);
                    $failedCount++;
                }
            }

            $message = "SMS reminders processed. Sent: $sentCount";
            if ($sentCount > 0) {
                return back()->with('success', $message);
            } else {
                return back()->with('error', $message.' (No valid phone numbers found)');
            }

        } catch (\Exception $e) {
            Log::error('SMS reminder processing failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->user_id ?? null,
            ]);

            return back()->with('error', 'Failed to process SMS reminders. Please try again.');
        }
=======
            }

            return back()->with('success', 'Approved payments for ' . $student->full_name . '.');
        }
        return back()->with('info', $student->full_name . ' has no outstanding fees.');
>>>>>>> 189635dfc80db5078042a6c8e90a3ae1ba032141
    }
}
