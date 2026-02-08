<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SmsLog;
use App\Models\SmsTemplate;
use App\Models\Strand;
use App\Models\Student;
use App\Services\SmsGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaffSmsController extends Controller
{
    protected $smsGateway;

    public function __construct(SmsGatewayService $smsGateway)
    {
        $this->smsGateway = $smsGateway;
    }

    public function index(Request $request): View
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $level = $request->get('level');
        $section = $request->get('section');
        $strand = $request->get('strand');

        // Students with pending balance
        $students = Student::with(['feeRecords' => function ($q) {
            $q->where('balance', '>', 0);
        }, 'parents'])
            ->whereHas('feeRecords', function ($q) {
                $q->where('balance', '>', 0);
            })
            ->when($search, function ($q) use ($search) {
                $operator = \Illuminate\Support\Facades\DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
                $q->where(function ($subq) use ($search, $operator) {
                    $subq->where('first_name', $operator, "%{$search}%")
                        ->orWhere('last_name', $operator, "%{$search}%")
                        ->orWhere('student_id', $operator, "%{$search}%");
                });
            })
            ->when($level, fn ($q) => $q->where('level', $level))
            ->when($section, fn ($q) => $q->where('section', $section))
            ->when($strand, fn ($q) => $q->where('strand', $strand))
            ->paginate(10, ['*'], 'students_page');

        // Filter Options
        $levels = Student::distinct()->whereNotNull('level')->orderBy('level')->pluck('level');
        $sections = Student::distinct()->whereNotNull('section')->orderBy('section')->pluck('section');
        $strands = Strand::orderBy('name')->pluck('name');

        // Templates
        $templates = SmsTemplate::all();

        // Recent History (Logs sent by this staff)
        $history = SmsLog::with('student')
            ->where('user_id', auth()->id())
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'history_page');

        return view('auth.staff_sms_reminders', compact(
            'students',
            'templates',
            'history',
            'search',
            'status',
            'levels',
            'sections',
            'strands',
            'level',
            'section',
            'strand'
        ));
    }

    public function send(Request $request): RedirectResponse
    {
        $request->validate([
            'selected_students' => 'required|array',
            'selected_students.*' => 'exists:students,student_id',
            'template_id' => 'required|exists:sms_templates,id',
            'scheduled_at' => 'nullable|date|after:now',
            'custom_message' => 'nullable|string|max:200',
        ]);

        $template = SmsTemplate::find($request->template_id);
        $scheduledAt = $request->scheduled_at ? \Carbon\Carbon::parse($request->scheduled_at) : null;
        $count = 0;

        foreach ($request->selected_students as $id) {
            $student = Student::with(['feeRecords', 'payments', 'parents'])->find($id);
            if (! $student) {
                continue;
            }
            
            $mobileNumber = null;
            $guardianName = 'Parent/Guardian';

            if ($student->parents && $student->parents->isNotEmpty()) {
                $primaryParent = $student->parents->firstWhere('pivot.is_primary', true) ?? $student->parents->first();
                if ($primaryParent) {
                    $mobileNumber = $primaryParent->phone;
                    $guardianName = $primaryParent->full_name;
                }
            }

            if (! $mobileNumber) {
                continue;
            }

            $balance = $student->feeRecords->where('balance', '>', 0)->sum('balance');
            $totalPaid = $student->total_paid;

            // Determine due date from earliest pending fee's payment_date or default +7 days
            $nextDueFee = $student->feeRecords->where('balance', '>', 0)->sortBy('payment_date')->first();
            $dueDate = $nextDueFee && $nextDueFee->payment_date ? \Carbon\Carbon::parse($nextDueFee->payment_date)->format('M d, Y') : now()->addDays(7)->format('M d, Y');
            $termLabel = $nextDueFee && $nextDueFee->notes ? $nextDueFee->notes : 'Tuition';

            $schoolName = config('app.name', 'E-Fees Academy');
            $schoolYear = $student->school_year ?: now()->format('Y');
            $level = $student->level ?: 'N/A';
            $lastPayment = $student->payments()->orderBy('paid_at', 'desc')->first();
            $lastAmountPaid = $lastPayment ? (float) $lastPayment->amount_paid : (float) $totalPaid;
            $lastPaymentDate = $lastPayment && $lastPayment->paid_at ? $lastPayment->paid_at->format('M d, Y') : now()->format('M d, Y');
            $lastReference = $lastPayment && $lastPayment->reference_number ? $lastPayment->reference_number : 'N/A';

            // Replace placeholders
            // Support both old {{ }} and new [ ] styles
            $placeholders = [
                '{{name}}' => $student->full_name,
                '{{student_name}}' => $student->full_name,
                '{{guardian_name}}' => $guardianName,
                '{{balance}}' => number_format($balance, 2),
                '{{remaining_balance}}' => number_format($balance, 2),
                '{{amount}}' => number_format(($nextDueFee ? (float) $nextDueFee->balance : (float) $balance), 2),
                '{{due_date}}' => $dueDate,
                '{{date}}' => now()->format('M d, Y'),
                '{{payment_date}}' => $lastPaymentDate,
                '{{or_number}}' => $lastReference,
                '{{school_name}}' => $schoolName,
                '{{school}}' => $schoolName,
                '{{school_year}}' => $schoolYear,
                '{{grade_level}}' => $level,
                '{{term_or_month}}' => $termLabel,
                '[Student Name]' => $student->full_name,
                '[Guardian Name]' => $guardianName,
                '[Amount]' => number_format($balance, 2),
                '[Due Date]' => $dueDate,
                '[Date]' => now()->format('M d, Y'),
                '[Payment Date]' => $lastPaymentDate,
                '[School Name]' => $schoolName,
                '[Remaining Balance]' => number_format($balance, 2),
                '[Amount Paid]' => number_format($lastAmountPaid, 2),
                '[Balance]' => number_format($balance, 2),
                '[School]' => $schoolName,
                '[Year]' => $schoolYear,
                '[Level]' => $level,
                '[Month/Term]' => $termLabel,
                '[Ref/OR No]' => $lastReference,
            ];

            $baseContent = $template->content;
            $message = str_replace(array_keys($placeholders), array_values($placeholders), $baseContent);

            // Enforce student notification preferences
            $prefs = $student->smsPreference;
            $name = strtolower($template->name);
            $isDueReminder = str_contains($name, 'upcoming due') || str_contains($name, 'installment') || str_contains($name, 'balance update') || str_contains($name, 'graduation');
            $isOverdue = str_contains($name, 'overdue');
            $isPaymentConfirm = str_contains($name, 'payment received') || str_contains($name, 'partial payment');
            $isPreferenceInfo = str_contains($name, 'notification preference') || str_contains($name, 'account update');

            if ($prefs) {
                if ($isDueReminder && ! $prefs->sms_due_reminder_enabled) {
                    continue;
                }
                if ($isOverdue && ! $prefs->sms_overdue_enabled) {
                    continue;
                }
                if ($isPaymentConfirm && ! $prefs->sms_payment_confirm_enabled) {
                    continue;
                }
            }

            $gatewayResponse = null;
            $status = 'queued'; // Default for scheduled

            // If not scheduled, send immediately
            if (! $scheduledAt) {
                $gatewayResponse = $this->smsGateway->send($mobileNumber, $message);
                $status = $gatewayResponse['success'] ? $gatewayResponse['status'] : 'failed';
            } else {
                // Create schedule entry
                \App\Models\SmsSchedule::create([
                    'student_id' => $student->student_id,
                    'schedule_time' => $scheduledAt,
                    'message' => $message,
                    'status' => 'pending',
                ]);
            }

            SmsLog::create([
                'student_id' => $student->student_id,
                'user_id' => auth()->id(),
                'mobile_number' => $mobileNumber,
                'message' => $message,
                'message_type' => 'reminder',
                'status' => $status,
                'sent_at' => $scheduledAt ? null : now(),
                'scheduled_at' => $scheduledAt,
                'provider_response' => $gatewayResponse ? $gatewayResponse['response'] : null,
                'gateway_message_id' => $gatewayResponse ? ($gatewayResponse['message_id'] ?? null) : null,
            ]);
            $count++;
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Sent SMS Reminders',
            'details' => json_encode(['count' => $count, 'scheduled' => (bool) $scheduledAt]),
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "$count message(s) ".($scheduledAt ? 'scheduled' : 'processed').' successfully.');
    }

    public function cancelSchedule($id): RedirectResponse
    {
        $log = SmsLog::where('user_id', auth()->id())->where('id', $id)->firstOrFail();
        if ($log->status === 'queued' && $log->scheduled_at > now()) {
            $log->update(['status' => 'cancelled']);

            return back()->with('success', 'Scheduled SMS cancelled.');
        }

        return back()->with('error', 'Cannot cancel this SMS.');
    }

    public function refreshStatus(Request $request): RedirectResponse
    {
        // Find logs that are 'queued' or 'sent' (but not delivered/failed) and have a gateway ID
        // Note: In real gateway, 'sent' might mean 'sent to carrier', waiting for 'delivered'.
        $logs = SmsLog::where('user_id', auth()->id())
            ->whereNotNull('gateway_message_id')
            ->whereIn('status', ['queued', 'sent'])
            ->whereNull('scheduled_at') // Don't check scheduled ones that haven't run
            ->limit(10) // Limit to avoid timeout
            ->get();

        $updatedCount = 0;

        foreach ($logs as $log) {
            $newStatus = $this->smsGateway->getStatus($log->gateway_message_id);
            if ($newStatus !== $log->status) {
                $log->update(['status' => $newStatus]);
                $updatedCount++;
            }
        }

        return back()->with('success', "Updated status for $updatedCount message(s).");
    }
}
