<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StaffStudentDetailsController extends Controller
{
    public function show(Student $student): View
    {
        $student->load(['feeRecords', 'user', 'parents', 'feeAssignments', 'payments']);

        $canEditFees = SystemSetting::where('key', 'allow_staff_edit_fees')->value('value') === '1';
        $activeYear = SystemSetting::where('key', 'school_year')->value('value');
        $isLockedYear = $student->school_year && $activeYear && $student->school_year !== $activeYear;

        return view('auth.staff_student_details', compact('student', 'canEditFees', 'activeYear', 'isLockedYear'));
    }

    public function updateCategory(Request $request, Student $student): RedirectResponse
    {
        $user = Auth::user();
        if (! $user || ! $user->hasRole('staff')) {
            abort(403, 'Unauthorized');
        }

        $activeYear = SystemSetting::where('key', 'school_year')->value('value');
        if (! $activeYear) {
            return back()->with('error', 'Please set an active School Year to continue.');
        }

        if ($student->school_year && $student->school_year !== $activeYear) {
            return back()
                ->with('error', 'This student record belongs to locked School Year '.$student->school_year.'. Only records in the active School Year '.$activeYear.' can be modified.')
                ->withInput();
        }

        $allowed = ['STEM', 'ABM', 'HUMSS', 'GAS', 'ICT', 'HE', 'IA', 'Agri-Fishery'];
        $isShs = in_array($student->level, ['Grade 11', 'Grade 12']);
        $strand = (string) $request->input('strand', '');

        if ($isShs && ($strand === '' || ! in_array($strand, $allowed, true))) {
            return back()->with('error', 'Please select a valid strand for Senior High.')->withInput();
        }

        if (! $isShs) {
            $strand = '';
        }

        $student->strand = $strand ?: null;
        $student->save();

        try {
            app(\App\Services\FeeManagementService::class)->recomputeStudentLedger($student);
        } catch (\Throwable $e) {
            // Log error if needed
        }

        return back()->with('success', 'Category updated successfully.');
    }
}
