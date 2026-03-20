<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\SystemSetting;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BulkOperationsController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_students' => Student::count(),
            'active_students' => Student::where('enrollment_status', 'Enrolled')->count(),
            'graduating_students' => Student::whereIn('level', ['Grade 6', 'Grade 10', 'Grade 12'])->count(),
            'graduated_students' => Student::where('enrollment_status', 'Graduated')->count(),
            'withdrawn_students' => Student::where('enrollment_status', 'Withdrawn')->count(),
            'dropped_students' => Student::where('enrollment_status', 'Dropped')->count(),
        ];

        $levels = Student::select('level')->distinct()->orderBy('level')->pluck('level');
        $schoolYears = Student::select('school_year')->distinct()->whereNotNull('school_year')->orderBy('school_year', 'desc')->pluck('school_year');
        $activeSY = SystemSetting::getActiveSchoolYear();

        return view('super_admin.bulk_operations', compact('stats', 'levels', 'schoolYears', 'activeSY'));
    }

    public function promote(Request $request)
    {
        $request->validate([
            'from_level' => 'required|string',
            'to_level' => 'required|string',
            'school_year' => 'required|string',
            'target_school_year' => 'required|string',
            'clear_sections' => 'nullable|string',
        ]);

        $from = $request->from_level;
        $to = $request->to_level;
        $sy = $request->school_year;
        $targetSy = $request->target_school_year;
        $clearSections = $request->has('clear_sections');

        try {
            $count = DB::transaction(function () use ($from, $to, $sy, $targetSy, $clearSections) {
                $students = Student::where('level', $from)
                    ->where('school_year', $sy)
                    ->where('enrollment_status', 'Enrolled')
                    ->get();

                foreach ($students as $student) {
                    $updateData = [
                        'level' => $to,
                        'school_year' => $targetSy
                    ];

                    if ($clearSections) {
                        $updateData['section'] = null;
                    }

                    // Reset strand if moving from SHS to non-SHS (unlikely but safe)
                    // Or reset strand if moving into SHS (needs re-selection)
                    if (!in_array($to, ['Grade 11', 'Grade 12'])) {
                        $updateData['strand'] = null;
                    }

                    $student->update($updateData);
                }
                return $students->count();
            });

            AuditService::log('Batch Promotion', null, "Promoted {$count} students from {$from} ({$sy}) to {$to} ({$targetSy})");

            return redirect()->route('super_admin.students.index', [
                'level' => $to,
                'school_year' => $targetSy
            ])->with('success', "Successfully promoted {$count} students to {$to} for SY {$targetSy}. You are now viewing the promoted students in Student Management.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to promote students: ' . $e->getMessage());
        }
    }

    public function statusUpdate(Request $request)
    {
        $request->validate([
            'level' => 'required|string',
            'school_year' => 'required|string',
            'new_status' => 'required|string|in:Enrolled,Withdrawn,Graduated,Dropped',
        ]);

        $level = $request->level;
        $sy = $request->school_year;
        $status = $request->new_status;

        try {
            $count = DB::transaction(function () use ($level, $sy, $status) {
                $students = Student::where('level', $level)
                    ->where('school_year', $sy)
                    ->get();

                foreach ($students as $student) {
                    $student->update(['enrollment_status' => $status]);
                }
                return $students->count();
            });

            AuditService::log('Bulk Status Update', null, "Updated {$count} students in {$level} ({$sy}) to status: {$status}");

            return back()->with('success', "Successfully updated {$count} students to '{$status}'.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }

    public function archive(Request $request)
    {
        $request->validate([
            'level' => 'nullable|string',
            'status' => 'nullable|string',
            'school_year' => 'required|string',
        ]);

        $query = Student::query();
        if ($request->filled('level')) $query->where('level', $request->level);
        if ($request->filled('status')) $query->where('enrollment_status', $request->status);
        $query->where('school_year', $request->school_year);

        try {
            $count = DB::transaction(function () use ($query, $request) {
                $students = $query->get();
                foreach ($students as $student) {
                    $student->delete(); // Soft delete
                }
                return $students->count();
            });

            AuditService::log('Bulk Archiving', null, "Archived {$count} students" . ($request->filled('school_year') ? " from SY {$request->school_year}" : ""));

            return back()->with('success', "Successfully archived {$count} students.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to archive students: ' . $e->getMessage());
        }
    }
}
