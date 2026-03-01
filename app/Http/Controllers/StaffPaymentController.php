<?php

namespace App\Http\Controllers;

use App\Models\FeeRecord;
use App\Models\Payment;
use App\Models\Student;
use App\Models\SystemSetting;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffPaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->has('action')) {
            if ($request->action === 'fetch_students') {
                $query = Student::select('student_id', 'first_name', 'last_name', 'level', 'section', 'strand', 'enrollment_status')
                    ->withSum(['feeRecords as total_balance' => function ($q) {
                        $q->select(DB::raw('SUM(balance)'));
                    }], 'balance')
                    ->with(['payments' => function ($q) {
                        $q->latest()->limit(1);
                    }]);

                if ($request->school_year) {
                    $query->where('school_year', $request->school_year);
                } else {
                    // Default to active school year
                    $activeYear = SystemSetting::where('key', 'school_year')->value('value');
                    if ($activeYear) {
                        $query->where('school_year', $activeYear);
                    }
                }
                if ($request->level) {
                    $query->where('level', $request->level);
                }
                if ($request->strand) {
                    $query->where('strand', $request->strand);
                }
                if ($request->section) {
                    $query->where('section', $request->section);
                }
                if ($request->search) {
                    $search = $request->search;
                    $operator = DB::connection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
                    $query->where(function ($q) use ($search, $operator) {
                        $q->where('first_name', $operator, "%{$search}%")
                            ->orWhere('last_name', $operator, "%{$search}%")
                            ->orWhere('student_id', $operator, "%{$search}%");
                    });
                }

                // Removed mandatory balance filter so newly created students can be found
                // $query->whereHas('feeRecords', function ($q) {
                //     $q->where('balance', '>', 0);
                // });

                return response()->json($query->orderBy('last_name')->paginate(10));
            }

            if ($request->action === 'fetch_fees') {
                $fees = FeeRecord::where('student_id', $request->student_id)
                    ->where(function ($q) {
                        $q->where('balance', '>', 0)
                            ->orWhere('balance', '<', 0);
                    })
                    ->where('record_type', '!=', 'payment')
                    ->orderByRaw("CASE WHEN record_type = 'discount' THEN 1 ELSE 0 END ASC")
                    ->orderBy('payment_date', 'asc')
                    ->get()
                    ->map(function ($fee) {
                        return [
                            'id' => $fee->id,
                            'name' => ucwords(str_replace('_', ' ', $fee->record_type)).($fee->notes ? ' - '.$fee->notes : ''),
                            'balance' => $fee->balance,
                            'amount' => $fee->amount,
                            'record_type' => $fee->record_type,
                            'date' => $fee->payment_date ? $fee->payment_date->format('Y-m-d') : null,
                        ];
                    });

                return response()->json($fees);
            }

            if ($request->action === 'fetch_reference') {
                $ref = 'PAY-'.strtoupper(substr(md5(uniqid()), 0, 8));

                return response()->json(['reference_number' => $ref]);
            }
        }

        $activeYear = SystemSetting::where('key', 'school_year')->value('value');
        
        $schoolYears = Student::distinct()->whereNotNull('school_year')->orderBy('school_year', 'desc')->pluck('school_year');
        
        // Filter dropdowns by active school year only
        $levels = Student::where('school_year', $activeYear)->distinct()->whereNotNull('level')->pluck('level')
            ->merge(['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'])
            ->unique()
            ->sort(function ($a, $b) {
                return strnatcmp($a, $b);
            })
            ->values();
        $strands = Student::where('school_year', $activeYear)->distinct()->whereNotNull('strand')->pluck('strand');
        $sections = Student::where('school_year', $activeYear)->distinct()->whereNotNull('section')->pluck('section');

        $notifications = DB::table('notifications')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('auth.staff_payment_processing', compact('schoolYears', 'levels', 'strands', 'sections', 'notifications', 'activeYear'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'string', 'exists:students,student_id'],
            'amount_paid' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', 'string', Rule::in(['Cash'])],
            'reference_number' => ['nullable', 'string', Rule::unique('payments', 'reference_number')],
            'remarks' => ['nullable', 'string'],
            'paid_at' => ['nullable', 'date'],
            'fee_record_id' => ['nullable', 'exists:fee_records,id'],
        ]);

        $activeYear = SystemSetting::where('key', 'school_year')->value('value');
        if (! $activeYear) {
            return back()->with('error', 'Please set an active School Year to continue.');
        }

        $student = Student::where('student_id', $data['student_id'])->first();
        if ($student && $student->school_year && $student->school_year !== $activeYear) {
            return back()
                ->with('error', 'This student record belongs to locked School Year '.$student->school_year.'. Only records in the active School Year '.$activeYear.' can be modified.')
                ->withInput();
        }

        return DB::transaction(function () use ($data) {
            // 1. Create Payment Transaction with PENDING status
            $payment = Payment::create([
                'student_id' => $data['student_id'],
                'amount_paid' => $data['amount_paid'],
                'status' => 'pending', // Pending Admin Approval
                'method' => $data['method'],
                'fee_record_id' => $data['fee_record_id'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'paid_at' => $data['paid_at'] ?? now(),
            ]);

            // Audit Log for Submission
            try {
                AuditService::log(
                    'Payment Submitted',
                    $payment,
                    "Payment of {$data['amount_paid']} submitted for approval for {$data['student_id']}",
                    null,
                    $payment->toArray()
                );
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed to create audit log for payment submission: '.$e->getMessage());
            }

            return redirect()->route('staff.payment_processing')->with('success', 'Payment submitted successfully! Waiting for Admin Approval.');
        });
    }

    public function showReceipt(Payment $payment): View
    {
        return view('auth.staff_payment_receipt', compact('payment'));
    }
}
