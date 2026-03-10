<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOnlineConfirmationController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request): View
    {
        $query = Payment::with(['student'])
            ->where('status', 'for_confirmation')
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

        $confirmations = $query->paginate(20);

        return view('admin.online_confirmations.index', compact('confirmations'));
    }

    public function confirm(Payment $payment): RedirectResponse
    {
        if ($payment->status !== 'for_confirmation') {
            return back()->with('error', 'Payment is not awaiting confirmation.');
        }

        try {
            $this->paymentService->approvePayment($payment);
            return back()->with('success', 'Online payment confirmed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to confirm payment: '.$e->getMessage());
        }
    }

    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        if ($payment->status !== 'for_confirmation') {
            return back()->with('error', 'Payment is not awaiting confirmation.');
        }

        $reason = $request->reason ?? 'Admin rejected online payment';

        $payment->update([
            'status' => 'rejected',
            'remarks' => $payment->remarks."\nRejected Reason: ".$reason,
        ]);

        return back()->with('success', 'Online payment rejected.');
    }
}
