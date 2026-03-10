<?php

namespace App\Services;

use App\Models\FeeRecord;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\PaymentVoidRequest;
use App\Models\SmsLog;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    protected $smsGateway;

    public function __construct(SmsGatewayService $smsGateway)
    {
        $this->smsGateway = $smsGateway;
    }

    /**
     * Approve and Process a Payment
     */
    public function approvePayment(Payment $payment)
    {
        return DB::transaction(function () use ($payment) {
            // Re-fetch with lock to prevent race conditions
            $payment = Payment::lockForUpdate()->findOrFail($payment->id);

            if ($payment->status === 'approved') {
                return $payment; // Already approved
            }

            // 1. Update Payment Status
            $payment->update(['status' => 'approved']);

            // 2. Generate Receipt Link
            $fileUrl = 'receipt://'.$payment->id;
            PaymentReceipt::create([
                'payment_id' => $payment->id,
                'file_url' => $fileUrl,
            ]);

            // 3. Create FeeRecord for the payment (Ledger Entry)
            FeeRecord::create([
                'student_id' => $payment->student_id,
                'record_type' => 'payment',
                'amount' => $payment->amount_paid,
                'balance' => 0,
                'status' => 'paid',
                'payment_method' => $payment->method,
                'reference_number' => $payment->reference_number,
                'notes' => $payment->remarks ?? 'Payment received',
                'payment_date' => $payment->paid_at ?? now(),
            ]);

            // 4. Update Student Balances (Distribute payment)
            $this->distributePayment($payment);

            // 5. Recompute Student Ledger to ensure consistency
            try {
                $student = Student::find($payment->student_id);
                if ($student) {
                    app(\App\Services\FeeManagementService::class)->recomputeStudentLedger($student);
                }
            } catch (\Exception $e) {
                Log::error('Failed to recompute ledger after payment: '.$e->getMessage());
            }

            // Audit Log
            try {
                AuditService::log(
                    'Payment Approved',
                    $payment,
                    "Payment of {$payment->amount_paid} approved for {$payment->student_id}",
                    null,
                    $payment->toArray()
                );
            } catch (\Throwable $e) {
            }

            // Send SMS Notification
            $this->sendSmsNotification($payment);

            return $payment;
        });
    }

    protected function distributePayment(Payment $payment): void
    {
        $amountRemaining = (float) $payment->amount_paid;

        // If a specific fee was selected by the staff
        if ($payment->fee_record_id) {
            $targetFee = FeeRecord::find($payment->fee_record_id);
            if ($targetFee && $targetFee->balance > 0) {
                $balance = (float) $targetFee->balance;
                $paymentForRecord = min($balance, $amountRemaining);

                $targetFee->balance = $balance - $paymentForRecord;
                if ($targetFee->balance <= 0) {
                    $targetFee->balance = 0;
                    $targetFee->status = 'paid';
                } else {
                    $targetFee->status = 'partial';
                }
                $targetFee->save();
                $amountRemaining -= $paymentForRecord;
            }
        }

        // Distribute remaining amount to oldest unpaid records
        if ($amountRemaining > 0) {
            $unpaidRecords = FeeRecord::where('student_id', $payment->student_id)
                ->where('balance', '>', 0)
                ->where('record_type', '!=', 'payment')
                ->orderBy('payment_date', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            foreach ($unpaidRecords as $record) {
                if ($amountRemaining <= 0) {
                    break;
                }

                $balance = (float) $record->balance;
                $paymentForRecord = min($balance, $amountRemaining);

                $record->balance = $balance - $paymentForRecord;
                if ($record->balance <= 0) {
                    $record->balance = 0;
                    $record->status = 'paid';
                } else {
                    $record->status = 'partial';
                }
                $record->save();

                $amountRemaining -= $paymentForRecord;
            }
        }
    }

    protected function sendSmsNotification(Payment $payment)
    {
        try {
            $student = Student::with('parents')->where('student_id', $payment->student_id)->first();
            $parent = $student ? $student->parents->sortByDesc('pivot.is_primary')->first() : null;

            if ($parent && $parent->phone) {
                $mobileNumber = $parent->phone;

                // Check preferences
                $prefs = $student->smsPreference;
                if (! $prefs || $prefs->sms_payment_confirm_enabled) {
                    $message = 'Payment Confirmed: PHP '.number_format($payment->amount_paid, 2).
                               ' for '.$student->first_name.'. Ref: '.($payment->reference_number ?? 'N/A').
                               '. Date: '.now()->format('M d, Y').'. Thank you!';

                    $gatewayResponse = $this->smsGateway->send($mobileNumber, $message);

                    SmsLog::create([
                        'student_id' => $payment->student_id,
                        'user_id' => auth()->id(),
                        'mobile_number' => $mobileNumber,
                        'message' => $message,
                        'message_type' => 'payment_confirmation',
                        'status' => $gatewayResponse['success'] ? $gatewayResponse['status'] : 'failed',
                        'sent_at' => now(),
                        'provider_response' => $gatewayResponse['response'] ?? null,
                        'gateway_message_id' => $gatewayResponse['message_id'] ?? null,
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Payment SMS failed: '.$e->getMessage());
        }
    }

    /**
     * Void a payment: reverse all balance distributions, remove the ledger entry,
     * mark the payment as voided, and recompute the student ledger.
     */
    public function voidPayment(PaymentVoidRequest $voidRequest): void
    {
        DB::transaction(function () use ($voidRequest) {
            $payment = $voidRequest->payment;

            // 1. Reverse the fee record distributions
            $this->reversePaymentDistribution($payment);

            // 2. Remove the payment ledger entry (record_type = 'payment')
            FeeRecord::where('student_id', $payment->student_id)
                ->where('record_type', 'payment')
                ->where('reference_number', $payment->reference_number)
                ->delete();

            // 3. Mark payment as voided
            $payment->update([
                'status' => 'voided',
                'remarks' => ($payment->remarks ? $payment->remarks . ' | ' : '') . 'VOIDED: ' . $voidRequest->reason,
            ]);

            // 4. Update the void request
            $voidRequest->update([
                'status' => 'approved',
                'reviewed_by' => auth()->user()->user_id,
                'reviewed_at' => now(),
            ]);

            // 5. Recompute student ledger
            try {
                $student = Student::find($payment->student_id);
                if ($student) {
                    app(\App\Services\FeeManagementService::class)->recomputeStudentLedger($student);
                }
            } catch (\Exception $e) {
                Log::error('Failed to recompute ledger after void: ' . $e->getMessage());
            }

            // 6. Audit log
            AuditService::log(
                'Payment Voided',
                $payment,
                "Payment #{$payment->id} voided (₱" . number_format($payment->amount_paid, 2) . ") for {$payment->student_id} - Reason: {$voidRequest->reason}"
            );
        });
    }

    /**
     * Reverse the balance distributions made by a payment.
     * Adds the paid amounts back to the fee records that were reduced.
     */
    protected function reversePaymentDistribution(Payment $payment): void
    {
        $amountToRestore = (float) $payment->amount_paid;

        // If the payment targeted a specific fee record, restore it first
        if ($payment->fee_record_id) {
            $targetFee = FeeRecord::find($payment->fee_record_id);
            if ($targetFee && $targetFee->record_type !== 'payment') {
                $restoreAmount = min($amountToRestore, (float) $payment->amount_paid);
                $targetFee->balance = (float) $targetFee->balance + $restoreAmount;
                $targetFee->status = $targetFee->balance >= (float) $targetFee->amount ? 'pending' : 'partial';
                $targetFee->save();
                $amountToRestore -= $restoreAmount;
            }
        }

        // For remaining amount, restore to records that were paid/partial
        // We restore in reverse order (newest first) to undo the distribution
        if ($amountToRestore > 0) {
            $records = FeeRecord::where('student_id', $payment->student_id)
                ->whereNotIn('record_type', ['payment', 'refund', 'adjustment'])
                ->where('balance', '<', DB::raw('amount'))
                ->orderBy('payment_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($records as $record) {
                if ($amountToRestore <= 0) break;

                $maxRestore = (float) $record->amount - (float) $record->balance;
                $restoreAmount = min($maxRestore, $amountToRestore);

                $record->balance = (float) $record->balance + $restoreAmount;
                $record->status = $record->balance >= (float) $record->amount ? 'pending' : 'partial';
                $record->save();

                $amountToRestore -= $restoreAmount;
            }
        }
    }
}
