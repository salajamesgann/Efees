<?php

namespace App\Http\Controllers;

use App\Mail\PaymentReceiptMail;
use App\Models\FeeRecord;
use App\Models\ParentContact;
use App\Models\Payment;
use App\Models\PaymentReceipt;
use App\Models\SystemSetting;
use App\Services\AuditService;
use App\Services\FeeManagementService;
use App\Services\PayMongoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ParentPaymentController extends Controller
{
    protected $payMongoService;

    public function __construct(PayMongoService $payMongoService)
    {
        $this->payMongoService = $payMongoService;
    }

    public function show(Request $request): View
    {
        $user = Auth::user();
        $roleName = optional($user->role)->role_name;

        if (! $user || ($roleName !== 'parent' && $user->roleable_type !== \App\Models\ParentContact::class)) {
            abort(403);
        }
        $parent = $user->roleable instanceof ParentContact ? $user->roleable : null;
        $students = $parent ? $parent->students()->orderBy('last_name')->get() : collect();
        $selectedStudentId = $request->query('student_id');
        $selectedChild = $selectedStudentId ? $students->firstWhere('student_id', $selectedStudentId) : null;

        $currentBalance = 0.0;
        if ($selectedChild) {
            $svc = app(FeeManagementService::class);
            $totals = $svc->computeTotalsForStudent($selectedChild);
            $currentBalance = $totals['remainingBalance'] ?? 0.0;
        }

        return view('auth.parent_payment', [
            'students' => $students, // matches myChildren conceptually, but view uses 'students'
            'isParent' => true,
            'myChildren' => $students,
            'selectedChild' => $selectedChild,
            'selectedStudentId' => $selectedStudentId,
            'currentBalance' => $currentBalance,
        ]);
    }

    public function history(Request $request): View
    {
        $user = Auth::user();
        $roleName = optional($user->role)->role_name;

        if (! $user || ($roleName !== 'parent' && $user->roleable_type !== \App\Models\ParentContact::class)) {
            abort(403);
        }
        $parent = $user->roleable instanceof ParentContact ? $user->roleable : null;
        $students = $parent ? $parent->students()->get() : collect();
        $studentIds = $students->pluck('student_id');

        $filterStudentId = (string) $request->query('student_id', '');
        if ($filterStudentId !== '' && $studentIds->contains($filterStudentId)) {
            $studentIds = collect([$filterStudentId]);
        }

        $payments = Payment::whereIn('student_id', $studentIds)
            ->with('student')
            ->orderBy('paid_at', 'desc')
            ->paginate(15);

        return view('auth.parent_payment_history', [
            'payments' => $payments,
            'isParent' => true,
            'myChildren' => $students,
            'selectedChild' => null, // No specific child selected for sidebar context unless filtered
        ]);
    }

    public function showReceipt(Payment $payment): View
    {
        $user = Auth::user();
        $roleName = optional($user->role)->role_name;

        if (! $user || ($roleName !== 'parent' && $user->roleable_type !== \App\Models\ParentContact::class)) {
            abort(403);
        }
        $parent = $user->roleable instanceof ParentContact ? $user->roleable : null;

        // Verify ownership
        if (! $parent || ! $parent->students()->where('students.student_id', $payment->student_id)->exists()) {
            abort(403);
        }

        $myChildren = $parent->students()->get();
        $isParent = true;
        $selectedChild = $payment->student;

        $schoolName = (string) (SystemSetting::where('key', 'school_name')->value('value') ?: config('app.name'));
        $contactNumber = (string) ($parent->phone ?? '');
        $schoolYear = (string) (SystemSetting::where('key', 'school_year')->value('value') ?: '');

        return view('auth.parent_receipt', compact('payment', 'myChildren', 'isParent', 'selectedChild', 'schoolName', 'contactNumber', 'schoolYear'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $roleName = optional($user->role)->role_name;

        if (! $user || ($roleName !== 'parent' && $user->roleable_type !== \App\Models\ParentContact::class)) {
            abort(403);
        }
        $parent = $user->roleable instanceof ParentContact ? $user->roleable : null;
        $studentId = (string) $request->input('student_id');
        if (! $parent || ! $parent->students()->where('students.student_id', $studentId)->exists()) {
            abort(403);
        }

        $data = $request->validate([
            'student_id' => ['required', 'string', 'exists:students,student_id'],
            'amount_paid' => ['required', 'numeric', 'min:20'], // PayMongo minimum is 20 PHP
            'method' => ['required', 'string', 'in:gcash,paymaya,card,grab_pay'],
        ]);

        // Amount in centavos
        $amountInCentavos = (int) ($data['amount_paid'] * 100);

        // Prepare line items
        $lineItems = [
            [
                'currency' => 'PHP',
                'amount' => $amountInCentavos,
                'description' => 'Tuition/Fee Payment',
                'name' => 'School Fee Payment',
                'quantity' => 1,
            ],
        ];

        // Payment Method Types mapping
        $paymentMethods = match ($data['method']) {
            'card' => ['card'],
            'paymaya' => ['paymaya'],
            'grab_pay' => ['grab_pay'],
            default => ['gcash'],
        };

        try {
            // Create Checkout Session
            $session = $this->payMongoService->createCheckoutSession([
                'line_items' => $lineItems,
                'payment_method_types' => $paymentMethods,
                'success_url' => route('parent.pay.success'),
                'cancel_url' => route('parent.pay.cancel'),
                'description' => "Payment for Student ID: {$studentId}",
                'metadata' => [
                    'student_id' => $studentId,
                    'amount_paid' => $data['amount_paid'],
                    'method' => $data['method'],
                ],
            ]);

            $sessionId = $session['data']['id'];
            $checkoutUrl = $session['data']['attributes']['checkout_url'];

            // Store session ID for verification on return
            session([
                'paymongo_checkout_id' => $sessionId,
                'payment_student_id' => $studentId,
                'payment_amount' => $data['amount_paid'],
                'payment_method' => $data['method'],
            ]);

            // Redirect user to PayMongo
            return redirect()->away($checkoutUrl);

        } catch (\Exception $e) {
            Log::error('PayMongo Init Error: '.$e->getMessage());

            return back()->with('error', 'Failed to initialize payment gateway. Please try again later.');
        }
    }

    /**
     * Handle Success Redirect from PayMongo
     */
    public function success(Request $request): RedirectResponse
    {
        $sessionId = session('paymongo_checkout_id');
        $studentId = session('payment_student_id');
        $amountPaid = session('payment_amount');
        $method = session('payment_method');

        // PayMongo may append id= query param on redirect
        if (! $sessionId) {
            $sessionId = $request->query('id') ?: $request->query('session_id');
        }

        // Debug logging
        Log::info('PayMongo Success Callback Started', [
            'session_id' => $sessionId,
            'student_id' => $studentId,
            'amount' => $amountPaid,
            'method' => $method,
            'session_lost' => empty($studentId),
        ]);

        if (! $sessionId) {
            Log::error('Payment session missing: no session ID found.');
            return redirect()->route('parent.pay')->with('error', 'Payment session expired or invalid.');
        }

        try {
            // Verify status with PayMongo API
            Log::info('Retrieving PayMongo session', ['session_id' => $sessionId]);
            $sessionData = $this->payMongoService->retrieveCheckoutSession($sessionId);
            Log::info('PayMongo session data', ['data' => $sessionData]);

            // If browser session was lost, recover from PayMongo metadata
            if (! $studentId) {
                Log::info('Single payment: recovering data from PayMongo metadata (session lost).');
                $metadata = $sessionData['data']['attributes']['metadata'] ?? [];
                $studentId = $metadata['student_id'] ?? '';
                $amountPaid = $metadata['amount_paid'] ?? 0;
                $method = $metadata['method'] ?? 'gcash';

                // Also try description as fallback
                if (empty($studentId)) {
                    $description = $sessionData['data']['attributes']['description'] ?? '';
                    if (preg_match('/Student ID:\s*([A-Za-z0-9\-]+)/', $description, $matches)) {
                        $studentId = $matches[1];
                    }
                }

                // Recover amount from line items if needed
                if ($amountPaid <= 0) {
                    $lineItems = $sessionData['data']['attributes']['line_items'] ?? [];
                    foreach ($lineItems as $item) {
                        $amountPaid += ((float) ($item['amount'] ?? 0)) / 100;
                    }
                }
            }

            if (! $studentId) {
                Log::error('Payment: could not determine student_id.', ['session_id' => $sessionId]);
                return redirect()->route('parent.pay')->with('error', 'Payment session expired or invalid.');
            }

            // Check multiple possible status values
            $paymentStatus = $sessionData['data']['attributes']['payment_status'] ?? 'unpaid';
            $paymentIntentData = $sessionData['data']['attributes']['payment_intent'] ?? [];
            $paymentIntentStatus = is_array($paymentIntentData) && isset($paymentIntentData['attributes']['status']) 
                ? $paymentIntentData['attributes']['status'] 
                : '';

            Log::info('Payment status detected', [
                'payment_status' => $paymentStatus,
                'payment_intent_status' => $paymentIntentStatus,
            ]);

            // Accept multiple paid statuses
            if (in_array($paymentStatus, ['paid', 'succeeded', 'completed']) || in_array($paymentIntentStatus, ['succeeded', 'paid'])) {
                Log::info('Payment confirmed as paid', [
                    'status' => $paymentStatus,
                    'intent_status' => $paymentIntentStatus,
                    'amount' => $amountPaid,
                ]);

                $amountPaid = session('payment_amount');
                $method = session('payment_method');
                $reference = 'PAYMONGO-'.$sessionId;

                $this->processSuccessfulPayment($studentId, (float) $amountPaid, (string) $method, $reference);

                // Send Notifications
                try {
                    $user = Auth::user();
                    $prefs = DB::table('user_preferences')->where('user_id', $user->user_id)->first();

                    // In-App Notification
                    DB::table('notifications')->insert([
                        'user_id' => $user->user_id,
                        'title' => 'Payment Successful',
                        'body' => 'We received your payment of ₱'.number_format($amountPaid, 2)." for {$studentId}. Ref: {$reference}",
                        'created_at' => now(),
                    ]);

                    // Payment Confirmation Preference Check
                    if ($prefs && $prefs->payment_reminders) {
                        // SMS Confirmation
                        if ($prefs->sms_reminders && $user->roleable && $user->roleable->phone) {
                            Log::info("SMS sent to {$user->roleable->phone}: Payment received ₱".number_format($amountPaid, 2)." for {$studentId}. Ref: {$reference}");
                        }

                        // Email Confirmation
                        if ($prefs->email_notifications && $user->email) {
                            try {
                                $schoolName = (string) (SystemSetting::where('key', 'school_name')->value('value') ?: config('app.name'));
                                $schoolYear = (string) (SystemSetting::where('key', 'school_year')->value('value') ?: '');
                                $emailPayment = Payment::with('student')->where('reference_number', $reference)->first();
                                if ($emailPayment) {
                                    Mail::to($user->email)->send(new PaymentReceiptMail($emailPayment, $schoolName, $schoolYear));
                                    Log::info("Payment receipt email sent to {$user->email} for reference {$reference}");
                                }
                            } catch (\Throwable $mailError) {
                                Log::error("Failed to send payment receipt email: " . $mailError->getMessage());
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error('Payment Notification Failed: '.$e->getMessage());
                }

                // Clear session
                session()->forget(['paymongo_checkout_id', 'payment_student_id', 'payment_amount', 'payment_method']);

                return redirect()->route('parent.pay')->with('success', 'Payment successful! Your balance has been updated.');
            } else {
                return redirect()->route('parent.pay')->with('error', 'Payment was not completed.');
            }

        } catch (\Exception $e) {
            Log::error('Payment Verification Failed: '.$e->getMessage());

            return redirect()->route('parent.pay')->with('error', 'Error verifying payment status.');
        }
    }

    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('paymongo-signature') ?? $request->header('Paymongo-Signature');
        $secret = (string) config('services.paymongo.webhook_secret', '');

        if ($secret !== '' && $signature) {
            $parts = collect(explode(',', $signature))->mapWithKeys(function ($item) {
                $kv = explode('=', trim($item), 2);
                return count($kv) === 2 ? [$kv[0] => $kv[1]] : [];
            });
            $timestamp = (string) ($parts['t'] ?? '');
            $hash = (string) ($parts['v1'] ?? '');
            if ($timestamp === '' || $hash === '') {
                return response()->json(['ok' => false], 400);
            }
            $expected = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);
            if (! hash_equals($expected, $hash)) {
                return response()->json(['ok' => false], 400);
            }
        }

        $body = $request->json()->all();
        $eventType = (string) data_get($body, 'data.attributes.type', '');
        $eventData = data_get($body, 'data.attributes.data', []);
        $attributes = data_get($eventData, 'attributes', []);
        $metadata = data_get($attributes, 'metadata', []);

        $studentId = (string) data_get($metadata, 'student_id', '');
        if ($studentId === '') {
            $description = (string) data_get($attributes, 'description', '');
            if (preg_match('/Student ID:\s*([A-Za-z0-9\-]+)/', $description, $matches)) {
                $studentId = $matches[1];
            }
        }

        $amountPaid = (float) data_get($metadata, 'amount_paid', 0);
        if ($amountPaid <= 0) {
            $lineItems = data_get($attributes, 'line_items', []);
            $sum = 0.0;
            foreach ($lineItems as $item) {
                $sum += (float) data_get($item, 'amount', 0);
            }
            if ($sum > 0) {
                $amountPaid = $sum / 100;
            }
        }

        $method = (string) data_get($metadata, 'method', 'gcash');
        $sessionId = (string) data_get($eventData, 'id', '');
        $reference = $sessionId !== '' ? 'PAYMONGO-'.$sessionId : '';

        // Handle multi-child payments from webhook
        $isMultiChild = data_get($metadata, 'multi_child', false);
        if ($isMultiChild && $eventType !== '' && str_contains($eventType, 'payment.paid') && $reference !== '') {
            $allocationsJson = data_get($metadata, 'allocations', '[]');
            $allocations = is_string($allocationsJson) ? json_decode($allocationsJson, true) : $allocationsJson;
            if (is_array($allocations) && ! empty($allocations)) {
                foreach ($allocations as $idx => $alloc) {
                    $ref = $reference . '-' . ($idx + 1);
                    $this->processSuccessfulPayment(
                        (string) ($alloc['student_id'] ?? ''),
                        (float) ($alloc['amount'] ?? 0),
                        $method,
                        $ref
                    );
                }
            }
            return response()->json(['ok' => true]);
        }

        if ($eventType !== '' && str_contains($eventType, 'payment.paid') && $studentId !== '' && $amountPaid > 0 && $reference !== '') {
            $this->processSuccessfulPayment($studentId, $amountPaid, $method, $reference);
        }

        return response()->json(['ok' => true]);
    }

    private function processSuccessfulPayment(string $studentId, float $amountPaid, string $method, string $reference): void
    {
        DB::transaction(function () use ($studentId, $amountPaid, $method, $reference) {
            $payment = Payment::where('reference_number', $reference)->first();

            if (! $payment) {
                $payment = Payment::create([
                    'student_id' => $studentId,
                    'amount_paid' => $amountPaid,
                    'method' => $method,
                    'reference_number' => $reference,
                    'remarks' => 'Online Payment via PayMongo',
                    'paid_at' => now(),
                    'status' => 'approved',
                ]);
            } elseif ($payment->status !== 'approved') {
                $payment->status = 'approved';
                $payment->paid_at = $payment->paid_at ?? now();
                $payment->save();
            }

            PaymentReceipt::firstOrCreate(
                ['payment_id' => $payment->id],
                ['file_url' => 'receipt://'.$payment->id]
            );

            $ledgerExists = FeeRecord::where('student_id', $studentId)
                ->where('record_type', 'payment')
                ->where('reference_number', $reference)
                ->exists();

            if (! $ledgerExists) {
                FeeRecord::create([
                    'student_id' => $studentId,
                    'record_type' => 'payment',
                    'amount' => $amountPaid,
                    'balance' => 0,
                    'status' => 'paid',
                    'payment_method' => $method,
                    'reference_number' => $reference,
                    'notes' => 'Online Payment via PayMongo',
                    'payment_date' => now(),
                ]);

                $this->distributePayment($studentId, $amountPaid);
            }

            try {
                AuditService::log('Online Payment Success', $payment, "Paid via PayMongo ({$method})", null, $payment->toArray());
            } catch (\Throwable $e) {
            }
        });

        $student = \App\Models\Student::where('student_id', $studentId)->first();
        if ($student) {
            try {
                app(FeeManagementService::class)->recomputeStudentLedger($student);
            } catch (\Throwable $e) {
            }
        }
    }

    public function cancel(): RedirectResponse
    {
        return redirect()->route('parent.pay')->with('info', 'Payment cancelled by user.');
    }

    // ═══════════════════════════════════════════════════════════════
    //  MULTI-CHILD (BULK) PAYMENT
    // ═══════════════════════════════════════════════════════════════

    /**
     * Show the multi-child combined payment page.
     */
    public function multiShow(): View
    {
        $user = Auth::user();
        $roleName = optional($user->role)->role_name;

        if (! $user || ($roleName !== 'parent' && $user->roleable_type !== ParentContact::class)) {
            abort(403);
        }

        $parent = $user->roleable instanceof ParentContact ? $user->roleable : null;
        $students = $parent ? $parent->students()->orderBy('last_name')->get() : collect();

        $svc = app(FeeManagementService::class);
        $childrenData = [];

        foreach ($students as $student) {
            $totals = $svc->computeTotalsForStudent($student);
            $childrenData[] = [
                'student' => $student,
                'balance' => $totals['remainingBalance'] ?? 0.0,
            ];
        }

        return view('auth.parent_payment_multi', [
            'isParent' => true,
            'myChildren' => $students,
            'childrenData' => $childrenData,
        ]);
    }

    /**
     * Store a multi-child combined payment via PayMongo.
     */
    public function multiStore(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $roleName = optional($user->role)->role_name;

        if (! $user || ($roleName !== 'parent' && $user->roleable_type !== ParentContact::class)) {
            abort(403);
        }

        $parent = $user->roleable instanceof ParentContact ? $user->roleable : null;
        if (! $parent) {
            abort(403);
        }

        $data = $request->validate([
            'students' => ['required', 'array', 'min:1'],
            'students.*.student_id' => ['required', 'string', 'exists:students,student_id'],
            'students.*.amount' => ['required', 'numeric', 'min:1'],
            'method' => ['required', 'string', 'in:gcash,paymaya,card,grab_pay'],
        ]);

        // Verify all students belong to this parent
        $parentStudentIds = $parent->students()->pluck('students.student_id')->toArray();
        $allocations = [];
        $grandTotal = 0.0;

        foreach ($data['students'] as $entry) {
            if (! in_array($entry['student_id'], $parentStudentIds)) {
                abort(403, 'You do not have access to one of the selected students.');
            }
            $amt = round((float) $entry['amount'], 2);
            if ($amt > 0) {
                $allocations[] = ['student_id' => $entry['student_id'], 'amount' => $amt];
                $grandTotal += $amt;
            }
        }

        if (empty($allocations) || $grandTotal < 20) {
            return back()->withErrors(['students' => 'Total payment must be at least ₱20.00.'])->withInput();
        }

        $amountInCentavos = (int) ($grandTotal * 100);

        // Build description
        $studentLabels = collect($allocations)->map(fn ($a) => $a['student_id'] . ' (₱' . number_format($a['amount'], 2) . ')')->implode(', ');
        $description = "Multi-child payment: {$studentLabels}";

        $lineItems = [[
            'currency' => 'PHP',
            'amount' => $amountInCentavos,
            'description' => $description,
            'name' => 'Combined School Fee Payment',
            'quantity' => 1,
        ]];

        $paymentMethods = match ($data['method']) {
            'card' => ['card'],
            'paymaya' => ['paymaya'],
            'grab_pay' => ['grab_pay'],
            default => ['gcash'],
        };

        try {
            $session = $this->payMongoService->createCheckoutSession([
                'line_items' => $lineItems,
                'payment_method_types' => $paymentMethods,
                'success_url' => route('parent.pay.multi.success'),
                'cancel_url' => route('parent.pay.multi.cancel'),
                'description' => $description,
                'metadata' => [
                    'multi_child' => true,
                    'allocations' => json_encode($allocations),
                    'method' => $data['method'],
                    'grand_total' => $grandTotal,
                ],
            ]);

            $sessionId = $session['data']['id'];
            $checkoutUrl = $session['data']['attributes']['checkout_url'];

            session([
                'paymongo_checkout_id' => $sessionId,
                'multi_child_payment' => true,
                'multi_allocations' => $allocations,
                'payment_amount' => $grandTotal,
                'payment_method' => $data['method'],
            ]);

            return redirect()->away($checkoutUrl);

        } catch (\Exception $e) {
            Log::error('PayMongo Multi-Child Init Error: ' . $e->getMessage());

            return back()->with('error', 'Failed to initialize payment gateway. Please try again later.');
        }
    }

    /**
     * Handle Success Redirect from PayMongo for multi-child payments.
     */
    public function multiSuccess(Request $request): RedirectResponse
    {
        // 1. Try browser session first
        $sessionId = session('paymongo_checkout_id');
        $isMulti = session('multi_child_payment', false);
        $allocations = session('multi_allocations', []);
        $grandTotal = session('payment_amount');
        $method = session('payment_method');

        // PayMongo may append id= query param on redirect
        if (! $sessionId) {
            $sessionId = $request->query('id') ?: $request->query('session_id');
        }

        if (! $sessionId) {
            Log::error('Multi-child payment: no session ID found in session or query params.');
            return redirect()->route('parent.pay.multi')->with('error', 'Payment session expired or invalid.');
        }

        // 2. Retrieve checkout session from PayMongo (for both verification AND fallback data)
        try {
            $sessionData = $this->payMongoService->retrieveCheckoutSession($sessionId);
        } catch (\Exception $e) {
            Log::error('Multi-Child: Failed to retrieve PayMongo session: ' . $e->getMessage());
            return redirect()->route('parent.pay.multi')->with('error', 'Error verifying payment status.');
        }

        // 3. If browser session was lost, recover allocations from PayMongo metadata
        if (! $isMulti || empty($allocations)) {
            Log::info('Multi-child payment: recovering data from PayMongo metadata (session lost).');
            $metadata = $sessionData['data']['attributes']['metadata'] ?? [];

            if (! empty($metadata['multi_child'])) {
                $isMulti = true;
                $allocations = is_string($metadata['allocations'] ?? null)
                    ? json_decode($metadata['allocations'], true)
                    : ($metadata['allocations'] ?? []);
                $method = $metadata['method'] ?? 'gcash';
                $grandTotal = (float) ($metadata['grand_total'] ?? 0);
            }
        }

        if (! $isMulti || empty($allocations)) {
            Log::error('Multi-child payment: could not recover allocations.', [
                'session_id' => $sessionId,
                'metadata' => $sessionData['data']['attributes']['metadata'] ?? [],
            ]);
            return redirect()->route('parent.pay.multi')->with('error', 'Payment session expired or invalid.');
        }

        try {
            $paymentStatus = $sessionData['data']['attributes']['payment_status'] ?? 'unpaid';
            $paymentIntentData = $sessionData['data']['attributes']['payment_intent'] ?? [];
            $paymentIntentStatus = is_array($paymentIntentData) && isset($paymentIntentData['attributes']['status'])
                ? $paymentIntentData['attributes']['status']
                : '';

            if (in_array($paymentStatus, ['paid', 'succeeded', 'completed']) || in_array($paymentIntentStatus, ['succeeded', 'paid'])) {
                $baseReference = 'PAYMONGO-' . $sessionId;

                // Process each child's allocation
                foreach ($allocations as $idx => $alloc) {
                    $ref = $baseReference . '-' . ($idx + 1);
                    $this->processSuccessfulPayment(
                        $alloc['student_id'],
                        (float) $alloc['amount'],
                        (string) $method,
                        $ref
                    );
                }

                // Send notifications for the combined payment
                try {
                    $user = Auth::user();
                    $totalFormatted = number_format($grandTotal, 2);
                    $studentCount = count($allocations);

                    DB::table('notifications')->insert([
                        'user_id' => $user->user_id,
                        'title' => 'Multi-Child Payment Successful',
                        'body' => "We received your combined payment of ₱{$totalFormatted} for {$studentCount} student(s). Ref: {$baseReference}",
                        'created_at' => now(),
                    ]);

                    // Email per child
                    $prefs = DB::table('user_preferences')->where('user_id', $user->user_id)->first();
                    if ($prefs && $prefs->email_notifications && $user->email) {
                        $schoolName = (string) (SystemSetting::where('key', 'school_name')->value('value') ?: config('app.name'));
                        $schoolYear = (string) (SystemSetting::where('key', 'school_year')->value('value') ?: '');
                        foreach ($allocations as $idx => $alloc) {
                            try {
                                $ref = $baseReference . '-' . ($idx + 1);
                                $emailPayment = Payment::with('student')->where('reference_number', $ref)->first();
                                if ($emailPayment) {
                                    Mail::to($user->email)->send(new PaymentReceiptMail($emailPayment, $schoolName, $schoolYear));
                                }
                            } catch (\Throwable $mailError) {
                                Log::error("Multi-child receipt email error: " . $mailError->getMessage());
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    Log::error('Multi-child Notification Failed: ' . $e->getMessage());
                }

                session()->forget(['paymongo_checkout_id', 'multi_child_payment', 'multi_allocations', 'payment_amount', 'payment_method']);

                return redirect()->route('parent.pay.multi')->with('success', "Combined payment of ₱" . number_format($grandTotal, 2) . " for " . count($allocations) . " student(s) was successful!");
            } else {
                return redirect()->route('parent.pay.multi')->with('error', 'Payment was not completed.');
            }

        } catch (\Exception $e) {
            Log::error('Multi-Child Payment Verification Failed: ' . $e->getMessage());

            return redirect()->route('parent.pay.multi')->with('error', 'Error verifying payment status.');
        }
    }

    /**
     * Handle Cancel Redirect from PayMongo for multi-child payments.
     */
    public function multiCancel(): RedirectResponse
    {
        session()->forget(['paymongo_checkout_id', 'multi_child_payment', 'multi_allocations', 'payment_amount', 'payment_method']);

        return redirect()->route('parent.pay.multi')->with('info', 'Payment cancelled by user.');
    }

    /**
     * Download Receipt as PDF
     */
    public function receiptPdf(Payment $payment)
    {
        $user = Auth::user();
        $roleName = optional($user->role)->role_name;

        if (! $user || ($roleName !== 'parent' && $user->roleable_type !== ParentContact::class)) {
            abort(403);
        }
        $parent = $user->roleable instanceof ParentContact ? $user->roleable : null;
        if (! $parent || ! $parent->students()->where('students.student_id', $payment->student_id)->exists()) {
            abort(403);
        }

        $schoolName = (string) (SystemSetting::where('key', 'school_name')->value('value') ?: config('app.name'));
        $schoolYear = (string) (SystemSetting::where('key', 'school_year')->value('value') ?: date('Y') . '-' . (date('Y') + 1));

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.receipt', [
            'payment' => $payment,
            'schoolName' => $schoolName,
            'schoolYear' => $schoolYear,
        ]);

        $pdf->setPaper('A4', 'portrait');

        $ref = $payment->reference_number ?? 'REC-' . str_pad($payment->id, 8, '0', STR_PAD_LEFT);

        return $pdf->download("Receipt_{$ref}.pdf");
    }

    /**
     * Distribute payment to oldest unpaid fee records
     */
    private function distributePayment($studentId, $amountPaid)
    {
        $amountRemaining = (float) $amountPaid;

        // Find unpaid records (excluding payments)
        $unpaidRecords = FeeRecord::where('student_id', $studentId)
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
