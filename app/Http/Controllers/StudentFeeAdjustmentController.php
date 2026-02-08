<?php

namespace App\Http\Controllers;

use App\Models\FeeAssignment;
use App\Models\FeeRecord;
use App\Models\Student;
use App\Models\StudentFeeAdjustment;
use App\Services\SmsService;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentFeeAdjustmentController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function store(Request $request, Student $student): RedirectResponse
    {
        $activeYear = SystemSetting::getActiveSchoolYear();
        if ($activeYear && $student->school_year !== $activeYear) {
            return back()->with('error', 'Cannot apply fee adjustments to a student in a locked School Year.');
        }

        $validated = $request->validate([
            'type' => ['required', 'in:discount,charge'],
            'name' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string'],
            'notify_sms' => ['nullable', 'boolean'],
        ]);

        // Find the active fee assignment for this student
        $feeAssignment = FeeAssignment::where('student_id', $student->student_id)
            ->orderByDesc('created_at')
            ->first();

        if (! $feeAssignment) {
            return back()->with('error', 'No active fee assignment found for this student. Please ensure the student is enrolled in a term.');
        }

        DB::transaction(function () use ($validated, $student, $feeAssignment, $request) {
            $adjustment = StudentFeeAdjustment::create([
                'fee_assignment_id' => $feeAssignment->id,
                'student_id' => $student->student_id,
                'type' => $validated['type'],
                'name' => $validated['name'],
                'amount' => $validated['amount'],
                'remarks' => $validated['remarks'],
                'created_by' => auth()->id(),
            ]);

            // Recalculate totals in FeeAssignment
            $feeAssignment->calculateTotal();

            // Create a corresponding FeeRecord to affect the actual balance
            // For charges: balance increases (positive)
            // For discounts: balance decreases (negative)
            $balanceEffect = $validated['type'] === 'discount'
                ? -abs($validated['amount'])
                : abs($validated['amount']);

            FeeRecord::create([
                'student_id' => $student->student_id,
                'record_type' => 'adjustment',
                'amount' => $validated['amount'], // Amount is always positive magnitude
                'balance' => $balanceEffect,      // Balance reflects debt increase/decrease
                'status' => 'pending',            // Active record
                'notes' => $validated['name'].($validated['remarks'] ? " - {$validated['remarks']}" : ''),
                'payment_date' => now(),          // Date applied
            ]);

            // Audit Log
            if (\Illuminate\Support\Facades\Schema::hasTable('audit_logs')) {
                DB::table('audit_logs')->insert([
                    'user_id' => auth()->id() ?? 0,
                    'action' => 'apply_fee_adjustment',
                    'details' => "Applied {$validated['type']}: {$validated['name']} ({$validated['amount']}) to student {$student->student_id}",
                    'ip_address' => $request->ip(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // SMS Notification
            if (! empty($validated['notify_sms']) && $validated['notify_sms']) {
                $guardian = $student->parents->sortByDesc('pivot.is_primary')->first();
                if ($guardian && $guardian->phone) {
                    $action = $validated['type'] === 'discount' ? 'Discount Applied' : 'Additional Charge';
                    $message = "E-Fees: {$action} of P".number_format($validated['amount'], 2).
                               " has been applied to student {$student->first_name} {$student->last_name}. ".
                               "Reason: {$validated['name']}.";

                    $this->smsService->send($guardian->phone, $message, $student->student_id);
                }
            }
        });

        return back()->with('success', ucfirst($validated['type']).' applied successfully.');
    }
}
