<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Student;
use App\Models\SystemSetting;
use App\Services\AuditService;
use App\Services\FeeManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentDiscountController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Student $student, SmsService $smsService): RedirectResponse
    {
        $activeYear = SystemSetting::getActiveSchoolYear();
        if ($activeYear && $student->school_year !== $activeYear) {
            return back()->withErrors(['error' => 'Cannot assign discounts to a locked School Year.']);
        }

        $validated = $request->validate([
            'discount_id' => ['required', 'exists:discounts,id'],
        ]);

        $discount = Discount::findOrFail($validated['discount_id']);

        if (! $discount->is_active || ! $discount->appliesToGrade($student->level) || ! $discount->isEligibleForStudent($student) || ! $discount->isCurrentlyValid()) {
            return back()->withErrors(['error' => 'This student is not eligible for the selected discount.']);
        }
        $feeAssignment = $student->getCurrentFeeAssignment();

        if (! $feeAssignment) {
            $feeAssignment = \App\Models\FeeAssignment::assignForStudent($student->student_id, $student->school_year ?? 'N/A', 'N/A');
            if ($feeAssignment) {
                $feeAssignment->calculateTotal();
                app(FeeManagementService::class)->recomputeStudentLedger($student);
            } else {
                return back()->withErrors(['error' => 'No active fee assignment found for this student.']);
            }
        }

        // Check if discount is already applied
        if ($feeAssignment->discounts()->where('discounts.id', $discount->id)->exists()) {
            return back()->withErrors(['error' => 'This discount is already applied to the student.']);
        }

        DB::transaction(function () use ($feeAssignment, $discount, $student, $smsService) {
            // Attach discount
            $feeAssignment->discounts()->attach($discount->id);

            // Recompute totals
            $feeAssignment->calculateTotal();

            // Recompute ledger/records
            app(FeeManagementService::class)->recomputeStudentLedger($student);

            // Audit Log
            AuditService::log(
                'Discount Assigned',
                $student,
                "Assigned discount '{$discount->discount_name}' to student.",
                null,
                ['discount_id' => $discount->id, 'discount_name' => $discount->discount_name]
            );

            $guardian = $student->parents->sortByDesc('pivot.is_primary')->first();
            $contact = $guardian ? $guardian->phone : null;
            if ($contact) {
                $summary = 'Discount applied: '.$discount->discount_name.' ('.$discount->formatted_value.')';
                $smsService->send($contact, $summary, $student->student_id);
            }
        });

        return back()->with('success', 'Discount assigned successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student, Discount $discount, SmsService $smsService): RedirectResponse
    {
        $activeYear = SystemSetting::getActiveSchoolYear();
        if ($activeYear && $student->school_year !== $activeYear) {
            return back()->withErrors(['error' => 'Cannot remove discounts from a locked School Year.']);
        }

        $feeAssignment = $student->getCurrentFeeAssignment();

        if (! $feeAssignment) {
            return back()->withErrors(['error' => 'No active fee assignment found for this student.']);
        }

        DB::transaction(function () use ($feeAssignment, $discount, $student, $smsService) {
            // Detach discount
            $feeAssignment->discounts()->detach($discount->id);

            // Recompute totals
            $feeAssignment->calculateTotal();

            // Recompute ledger/records
            app(FeeManagementService::class)->recomputeStudentLedger($student);

            // Audit Log
            AuditService::log(
                'Discount Removed',
                $student,
                "Removed discount '{$discount->discount_name}' from student.",
                null,
                ['discount_id' => $discount->id, 'discount_name' => $discount->discount_name]
            );

            $guardian = $student->parents->sortByDesc('pivot.is_primary')->first();
            $contact = $guardian ? $guardian->phone : null;
            if ($contact) {
                $message = "Discount removed: {$discount->discount_name}. Please review your updated balance.";
                $smsService->send($contact, $message, $student->student_id);
            }
        });

        return back()->with('success', 'Discount removed successfully.');
    }
}
