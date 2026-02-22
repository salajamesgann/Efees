<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\SystemSetting;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPaymentApprovalController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request): View
    {
        $query = Payment::with(['student', 'submitAuditLog.user.roleable'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc');

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        $pendingPayments = $query->paginate(20);

        return view('admin.payment_approvals.index', compact('pendingPayments'));
    }

    public function approve(Payment $payment): RedirectResponse
    {
        $activeYear = SystemSetting::getActiveSchoolYear();
        if ($activeYear && $payment->student && $payment->student->school_year !== $activeYear) {
            return back()->with('error', 'Cannot approve payment for a student in a locked School Year.');
        }

        if ($payment->status !== 'pending') {
            return back()->with('error', 'Payment is not pending approval.');
        }

        try {
            $this->paymentService->approvePayment($payment);

            return back()->with('success', 'Payment approved successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to approve payment: '.$e->getMessage());
        }
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $activeYear = SystemSetting::getActiveSchoolYear();
        if ($activeYear && $payment->student && $payment->student->school_year !== $activeYear) {
            return back()->with('error', 'Cannot reject payment for a student in a locked School Year.');
        }

        if ($payment->status !== 'pending') {
            return back()->with('error', 'Payment is not pending approval.');
        }

        $reason = $request->reason ?? 'Admin rejected';

        $payment->update([
            'status' => 'rejected',
            'remarks' => $payment->remarks."\nRejected Reason: ".$reason,
        ]);

        // Notify Staff who submitted the payment
        try {
            $submitLog = $payment->submitAuditLog;
            if ($submitLog && $submitLog->user_id) {
                $studentName = $payment->student ? ($payment->student->first_name.' '.$payment->student->last_name) : $payment->student_id;

                \Illuminate\Support\Facades\DB::table('notifications')->insert([
                    'user_id' => $submitLog->user_id,
                    'title' => 'Payment Rejected',
                    'body' => 'Payment of '.number_format($payment->amount_paid, 2)." for {$studentName} was rejected. Reason: {$reason}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send rejection notification: '.$e->getMessage());
        }

        return back()->with('success', 'Payment rejected.');
    }
}
