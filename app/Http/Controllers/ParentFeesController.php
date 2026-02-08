<?php

namespace App\Http\Controllers;

use App\Models\ParentContact;
use App\Models\Student;
use App\Services\FeeManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ParentFeesController extends Controller
{
    public function show(Request $request, Student $student): View
    {
        $user = Auth::user();
        $roleName = optional($user->role)->role_name;

        if (! $user || ($roleName !== 'parent' && $user->roleable_type !== \App\Models\ParentContact::class)) {
            abort(403);
        }
        $parent = $user->roleable instanceof ParentContact ? $user->roleable : null;
        if (! $parent || ! $parent->students()->where('students.student_id', $student->student_id)->exists()) {
            abort(403);
        }

        $svc = app(FeeManagementService::class);
        $totals = $svc->computeTotalsForStudent($student);

        // Data for sidebar/layout
        $myChildren = $parent->students()->get();

        return view('auth.parent_fee_breakdown', [
            'student' => $student,
            'totals' => $totals,
            'isParent' => true,
            'myChildren' => $myChildren,
            'selectedChild' => $student,
        ]);
    }

    public function soa(Request $request, Student $student): View
    {
        $user = Auth::user();
        $roleName = optional($user->role)->role_name;

        if (! $user || ($roleName !== 'parent' && $user->roleable_type !== \App\Models\ParentContact::class)) {
            abort(403);
        }
        $parent = $user->roleable instanceof ParentContact ? $user->roleable : null;
        if (! $parent || ! $parent->students()->where('students.student_id', $student->student_id)->exists()) {
            abort(403);
        }

        $svc = app(FeeManagementService::class);
        $totals = $svc->computeTotalsForStudent($student);

        // Fetch all transactions for running balance
        $transactions = $student->feeRecords()
            ->orderBy('payment_date')
            ->orderBy('created_at')
            ->get();

        // Data for sidebar/layout
        $myChildren = $parent->students()->get();

        return view('auth.parent_soa', [
            'student' => $student,
            'totals' => $totals,
            'transactions' => $transactions,
            'parent' => $parent,
            'isParent' => true,
            'myChildren' => $myChildren,
            'selectedChild' => $student,
        ]);
    }
}
