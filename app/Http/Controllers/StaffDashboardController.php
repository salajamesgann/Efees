<?php

namespace App\Http\Controllers;

use App\Models\FeeRecord;
use App\Models\FeeUpdateAudit;
use App\Models\Student;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\FeeManagementService;
use App\Services\SmsGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class StaffDashboardController extends Controller
{
    protected $smsGateway;

    public function __construct(SmsGatewayService $smsGateway)
    {
        $this->smsGateway = $smsGateway;
    }

    /**
     * Display the staff dashboard with student records.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        if (! $user || ! $user->hasRole('staff')) {
            abort(403, 'Unauthorized');
        }

        $activeYear = SystemSetting::where('key', 'school_year')->value('value');

        // Notifications
        $notifications = DB::table('notifications')
            ->where('user_id', $user->user_id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Filters
        $query = Student::query();

        // Search
        if ($request->filled('q')) {
            $term = $request->input('q');
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('student_id', 'like', "%{$term}%");
            });
        }

        // Dropdown Filters
        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }
        if ($request->filled('strand')) {
            $query->where('strand', $request->input('strand'));
        }
        if ($request->filled('section')) {
            $query->where('section', $request->input('section'));
        }

        // Sorting
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');

        if ($sort === 'name') {
            $query->orderBy('last_name', $direction)->orderBy('first_name', $direction);
        } elseif ($sort === 'level') {
            $query->orderBy('level', $direction);
        } elseif ($sort === 'section') {
            $query->orderBy('section', $direction);
        } else {
            // Default sort
            $query->orderBy('last_name', 'asc');
        }

        // Get distinct values for dropdowns
        $levels = Student::distinct()->pluck('level')->sort()->values();
        $strands = Student::distinct()->whereNotNull('strand')->where('strand', '!=', '')->pluck('strand')->sort()->values();
        $sections = Student::distinct()->whereNotNull('section')->where('section', '!=', '')->pluck('section')->sort()->values();

        // Pagination
        $students = $query->paginate(15);
        $svc = app(FeeManagementService::class);

        // Transform collection
        $transformedCollection = $students->getCollection()->map(function ($student) use ($svc) {
            $totals = $svc->computeTotalsForStudent($student);
            $totalFee = (float) ($totals['totalAmount'] ?? 0);
            $paidAmount = (float) ($totals['paidAmount'] ?? 0);
            $dueAmount = (float) ($totals['remainingBalance'] ?? max($totalFee - $paidAmount, 0));

            $status = 'unpaid';
            $statusText = 'Unpaid';
            if ($dueAmount <= 0 && $totalFee > 0) {
                $status = 'paid';
                $statusText = 'Fully Paid';
            } elseif ($paidAmount > 0) {
                $status = 'partially-paid';
                $statusText = 'Partially Paid';
            } elseif ($totalFee == 0) {
                $status = 'paid'; // No fees = paid
                $statusText = 'No Fees';
            }

            // Latest transaction
            $latestTransaction = \App\Models\Payment::where('student_id', $student->student_id)
                ->orderBy('created_at', 'desc')
                ->first();

            return (object) [
                'student' => $student,
                'totalFee' => $totalFee,
                'paidAmount' => $paidAmount,
                'dueAmount' => $dueAmount,
                'status' => $status,
                'statusText' => $statusText,
                'latestTransaction' => $latestTransaction,
            ];
        });

        // Filter by status (Post-query filter - only filters current page, implies pagination might look weird but better than crashing)
        if ($request->filled('status')) {
            $statusFilter = $request->input('status');
            $transformedCollection = $transformedCollection->filter(function ($record) use ($statusFilter) {
                return $record->status === $statusFilter;
            });
        }

        // Replace collection in paginator
        $students->setCollection($transformedCollection);

        // Calculate Stats using raw SQL for performance
        $statsResult = DB::select("
            SELECT
                SUM(CASE WHEN balance <= 0 THEN 1 ELSE 0 END) as paid,
                SUM(CASE WHEN balance > 0 AND paid > 0 THEN 1 ELSE 0 END) as partial,
                SUM(CASE WHEN balance > 0 AND paid = 0 THEN 1 ELSE 0 END) as unpaid
            FROM (
                SELECT 
                    s.student_id,
                    COALESCE((SELECT SUM(balance) FROM fee_records fr WHERE fr.student_id = s.student_id AND fr.status != 'cancelled'), 0) as balance,
                    COALESCE((SELECT SUM(amount_paid) FROM payments p WHERE p.student_id = s.student_id AND (p.status IN ('approved','paid') OR p.status IS NULL)), 0) as paid
                FROM students s
                WHERE s.deleted_at IS NULL
            ) as totals
        ")[0];

        $stats = [
            'paid' => (int) ($statsResult->paid ?? 0),
            'partial' => (int) ($statsResult->partial ?? 0),
            'unpaid' => (int) ($statsResult->unpaid ?? 0),
        ];

        return view('auth.staff_dashboard', [
            'studentRecords' => $students,
            'activeYear' => $activeYear,
            'notifications' => $notifications,
            'stats' => $stats,
            'levels' => $levels,
            'strands' => $strands,
            'sections' => $sections,
            'query' => $request->input('q'),
            'level' => $request->input('level'),
            'strand' => $request->input('strand'),
            'section' => $request->input('section'),
            'status' => $request->input('status'),
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function list()
    {
        return redirect()->route('staff_dashboard');
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

    /**
     * Ping/remind a student about their fees (logs an action for now).
     */
    public function remind(Student $student): RedirectResponse
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
            return back()->with('error', 'This student record belongs to locked School Year '.$student->school_year.'. Only records in the active School Year '.$activeYear.' can be modified.');
        }

        // Find the application's user row for this student
        $targetUser = User::where('roleable_type', 'App\\Models\\Student')
            ->where('roleable_id', $student->student_id)
            ->first();

        if (! $targetUser) {
            Log::warning('Reminder failed: no user mapped to student', [
                'student_id' => $student->student_id,
            ]);

            return back()->with('error', 'No user account found for this student.');
        }

        // Insert a realtime notification row (Supabase listens to inserts)
        try {
            DB::table('notifications')->insert([
                'user_id' => $targetUser->user_id,
                'title' => 'Fee Reminder',
                'body' => 'Hello '.$student->first_name.', please review your outstanding fees.',
                'created_at' => now(),
            ]);

            Log::info('Fee reminder sent', [
                'staff_user_id' => $user->user_id ?? null,
                'target_user_id' => $targetUser->user_id,
                'student_id' => $student->student_id,
                'timestamp' => now()->toDateTimeString(),
            ]);

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
        } catch (\Throwable $e) {
            Log::error('Failed to create notification', [
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to send reminder.');
        }
    }

    /**
     * Approve/Clear all outstanding fees for a student.
     */
    public function approve(Student $student): RedirectResponse
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
            return back()->with('error', 'This student record belongs to locked School Year '.$student->school_year.'. Only records in the active School Year '.$activeYear.' can be modified.');
        }

        $outstanding = FeeRecord::where('student_id', $student->student_id)
            ->where(function ($q) {
                $q->where('status', '!=', 'paid')
                    ->orWhereNull('status')
                    ->orWhere('balance', '>', 0);
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
                        ->orWhereNull('status')
                        ->orWhere('balance', '>', 0);
                })
                ->update(['status' => 'paid', 'balance' => 0]);
        }

        if ($affected > 0) {
            // Log Audit
            FeeUpdateAudit::create([
                'performed_by_user_id' => $user->user_id,
                'event_type' => 'staff_fee_approve_all',
                'message' => "Approved all outstanding payments for student {$student->student_id}. Total cleared: {$totalCleared}, Records affected: {$affected}",
                'affected_students_count' => 1,
                'school_year' => $activeYear,
                'semester' => SystemSetting::where('key', 'semester')->value('value'),
            ]);

            // Find the application's user row for this student
            $targetUser = User::where('roleable_type', 'App\\Models\\Student')
                ->where('roleable_id', $student->student_id)
                ->first();

            if ($targetUser) {
                try {
                    DB::table('notifications')->insert([
                        'user_id' => $targetUser->user_id,
                        'title' => 'Fees Cleared',
                        'body' => 'Hello '.$student->first_name.', all your outstanding fees have been approved/cleared.',
                        'created_at' => now(),
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('notification insert failed (approval)', ['error' => $e->getMessage()]);
                }
            }

            try {
                app(\App\Services\FeeManagementService::class)->recomputeStudentLedger($student);
            } catch (\Throwable $e) {
                Log::warning('fee recompute failed after approval', ['error' => $e->getMessage()]);
            }

            return back()->with('success', "Cleared outstanding fees for {$student->full_name}. Total: {$totalCleared}");
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
    }
}
