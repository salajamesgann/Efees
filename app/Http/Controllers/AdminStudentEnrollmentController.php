<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\FeeManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminStudentEnrollmentController extends Controller
{
    /**
     * Display a listing of enrolled students.
     */
    public function index(Request $request): RedirectResponse
    {
        return redirect()->route('admin.students.index');
    }

    /**
     * Show the form for editing the student's enrollment details.
     */
    public function edit(Student $student): View
    {
        return view('admin.enrollment.edit', compact('student'));
    }

    /**
     * Update the student's enrollment details.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $activeYear = SystemSetting::getActiveSchoolYear();
        if ($activeYear && $student->school_year !== $activeYear) {
            return back()->withErrors(['error' => 'Cannot update enrollment details for a locked School Year.']);
        }

        $validated = $request->validate([
            'level' => ['required', 'string', 'max:255'],
            'section' => ['required', 'string', 'max:255'],
            'school_year' => ['required', 'string', 'max:20'],
            'enrollment_status' => ['required', 'string', 'in:Active,Inactive,Archived,Graduated,Dropped'],
            'strand' => ['nullable', 'string', 'in:STEM,ABM,HUMSS,GAS'],
        ]);

        if (in_array($validated['level'], ['Grade 11', 'Grade 12'], true) && empty($validated['strand'])) {
            return back()->withErrors(['strand' => 'Strand is required for Grade 11 and 12.'])->withInput();
        }

        DB::transaction(function () use ($student, $validated) {
            $student->update($validated);

            // Log audit
            DB::table('audit_logs')->insert([
                'user_id' => auth()->id() ?? 'SYSTEM',
                'action' => 'update_enrollment',
                'details' => "Updated enrollment for {$student->student_id}: ".json_encode($validated),
                'ip_address' => request()->ip(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.enrollment.index')->with('success', 'Enrollment details updated successfully.');
    }

    /**
     * Display the specified student's profile and fee summary.
     */
    public function show(Student $student): View
    {
        $student->load(['feeRecords', 'payments', 'parents', 'user']);

        $svc = app(FeeManagementService::class);
        $totals = $svc->computeTotalsForStudent($student);
        $totalFees = (float) ($totals['totalAmount'] ?? 0.0);
        $totalPaid = (float) $student->payments->where('status', 'paid')->sum('amount');
        $balance = max(0.0, $totalFees - $totalPaid);

        $feeAssignment = \App\Models\FeeAssignment::where('student_id', $student->student_id)
            ->with(['tuitionFee', 'additionalCharges', 'discounts', 'adjustments'])
            ->orderByDesc('created_at')
            ->first();

        // Fetch available discounts (only those the student is actually eligible for)
        $availableDiscounts = \App\Models\Discount::active()
            ->applicableToGrade($student->level)
            ->get()
            ->filter(function ($discount) use ($student) {
                return $discount->isEligibleForStudent($student) && $discount->isCurrentlyValid();
            });

        // Filter out already applied discounts
        if ($feeAssignment) {
            $appliedDiscountIds = $feeAssignment->discounts->pluck('id')->toArray();
            $availableDiscounts = $availableDiscounts->reject(function ($discount) use ($appliedDiscountIds) {
                return in_array($discount->id, $appliedDiscountIds);
            });
        }

        return view('admin.enrollment.show', compact('student', 'totalFees', 'totalPaid', 'balance', 'feeAssignment', 'availableDiscounts'));
    }

    /**
     * Print the student's statement of account.
     */
    public function printStatement(Student $student): View
    {
        $student->load(['feeRecords', 'payments', 'parents', 'user']);

        $svc = app(FeeManagementService::class);
        $totals = $svc->computeTotalsForStudent($student);
        $totalFees = (float) ($totals['totalAmount'] ?? 0.0);
        $totalPaid = (float) $student->payments->where('status', 'paid')->sum('amount');
        $balance = max(0.0, $totalFees - $totalPaid);

        $feeAssignment = \App\Models\FeeAssignment::where('student_id', $student->student_id)
            ->with(['tuitionFee', 'additionalCharges', 'discounts', 'adjustments'])
            ->orderByDesc('created_at')
            ->first();

        return view('admin.enrollment.print_statement', compact('student', 'totalFees', 'totalPaid', 'balance', 'feeAssignment'));
    }

    /**
     * Deactivate (Archive) the student.
     */
    public function destroy(Student $student): RedirectResponse
    {
        $activeYear = SystemSetting::getActiveSchoolYear();
        if ($activeYear && $student->school_year !== $activeYear) {
            return back()->withErrors(['error' => 'Cannot archive student from a locked School Year.']);
        }

        $student->update(['enrollment_status' => 'Archived']);

        // Also deactivate user account if needed
        if ($student->user) {
            $student->user->update(['is_active' => false]);
        }

        DB::table('audit_logs')->insert([
            'user_id' => auth()->id() ?? 'SYSTEM',
            'action' => 'archive_student',
            'details' => "Archived student {$student->student_id}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.enrollment.index')->with('success', 'Student archived successfully.');
    }
}
