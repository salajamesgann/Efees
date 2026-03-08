<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentVoidRequest;
use App\Services\AuditService;
use Illuminate\Http\Request;

class StaffPaymentVoidController extends Controller
{
    public function store(Request $request, Payment $payment)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        // Only approved payments can be voided
        if ($payment->status !== 'approved') {
            return back()->with('error', 'Only approved payments can be voided.');
        }

        // Check if there's already a pending void request for this payment
        $existing = PaymentVoidRequest::where('payment_id', $payment->id)
            ->where('status', 'pending')
            ->first();

        if ($existing) {
            return back()->with('error', 'A void request is already pending for this payment.');
        }

        PaymentVoidRequest::create([
            'payment_id' => $payment->id,
            'requested_by' => auth()->user()->user_id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        AuditService::log(
            'Payment Void Requested',
            $payment,
            "Void requested for payment #{$payment->id} (₱" . number_format($payment->amount_paid, 2) . ") - Reason: {$request->reason}"
        );

        return back()->with('success', 'Void request submitted successfully. Waiting for admin approval.');
    }
}
