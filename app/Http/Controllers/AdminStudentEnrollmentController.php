<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SystemSetting;
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
    public function index(Request $request): View
    {
        $query = Student::query();
        $search = trim((string) $request->query('q', ''));
        $level = $request->query('level', 'all');
        $strand = $request->query('strand', 'all');
        $status = $request->query('status', 'all');

        // Search functionality
        if ($search !== '') {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'ilike', "%{$search}%")
                  ->orWhere('last_name', 'ilike', "%{$search}%")
                  ->orWhere('student_id', 'ilike', "%{$search}%");
            });
        }

        // Filters
        if ($level !== 'all' && $level !== '') {
            $query->where('level', $level);
        }
        if ($strand !== 'all' && $strand !== '') {
            $query->where('strand', $strand);
        }
        if ($status !== 'all' && $status !== '') {
            $query->where('enrollment_status', $status);
        }

        $students = $query->orderBy('last_name')->paginate(20)->withQueryString();

        return view('admin.enrollment.index', compact('students', 'search', 'level', 'strand', 'status'));
    }

    /**
     * Show the form for editing the student's enrollment details.
     */
    public function edit(Student $student): View
    {
        abort(404);
    }

    /**
     * Update the student's enrollment details.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        abort(404);
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
            'ip_address' => request()->ip(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.enrollment.index')->with('success', 'Student archived successfully.');
    }
}
