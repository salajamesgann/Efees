<?php

namespace App\Http\Controllers;

use App\Models\PaymentVoidRequest;
use App\Services\AuditService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPaymentVoidController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $status = $request->get('status', 'pending');

        $query = PaymentVoidRequest::with(['payment.student', 'requester.roleable'])
            ->orderBy('created_at', 'asc');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->whereHas('payment', function ($q) use ($search) {
                $q->where('reference_number', 'ilike', "%{$search}%")
                    ->orWhere('student_id', 'ilike', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('first_name', 'ilike', "%{$search}%")
                            ->orWhere('last_name', 'ilike', "%{$search}%");
                    });
            });
        }

        $voidRequests = $query->paginate(20)->withQueryString();

        return view('admin.void_approvals.index', compact('voidRequests', 'search', 'status'));
    }

    public function approve(PaymentVoidRequest $voidRequest)
    {
        if ($voidRequest->status !== 'pending') {
            return back()->with('error', 'This void request has already been processed.');
        }

        $paymentService = app(PaymentService::class);
        $paymentService->voidPayment($voidRequest);

        return back()->with('success', 'Payment has been voided successfully.');
    }

    public function reject(Request $request, PaymentVoidRequest $voidRequest)
    {
        if ($voidRequest->status !== 'pending') {
            return back()->with('error', 'This void request has already been processed.');
        }

        $request->validate([
            'admin_remarks' => 'nullable|string|max:1000',
        ]);

        $voidRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->user()->user_id,
            'admin_remarks' => $request->admin_remarks,
            'reviewed_at' => now(),
        ]);

        AuditService::log(
            'Payment Void Rejected',
            $voidRequest->payment,
            "Void request rejected for payment #{$voidRequest->payment_id}" .
            ($request->admin_remarks ? " - Reason: {$request->admin_remarks}" : '')
        );

        return back()->with('success', 'Void request has been rejected.');
    }
}
