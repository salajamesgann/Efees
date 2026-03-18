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

        return view('super_admin.bulk_operations', compact('stats', 'levels'));
    }

    public function promote(Request $request)
    {
        $request->validate([
            'from_level' => 'required|string',
            'to_level' => 'required|string',
        ]);

        $from = $request->from_level;
        $to = $request->to_level;

        try {
            $count = DB::transaction(function () use ($from, $to) {
                $students = Student::where('level', $from)->where('enrollment_status', 'Enrolled')->get();
                foreach ($students as $student) {
                    $student->update(['level' => $to]);
                }
                return $students->count();
            });

            AuditService::log('Batch Promotion', null, "Promoted {$count} students from {$from} to {$to}");

            return back()->with('success', "Successfully promoted {$count} students to {$to}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to promote students: ' . $e->getMessage());
        }
    }

    public function archive(Request $request)
    {
        $request->validate([
            'level' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $query = Student::query();
        if ($request->filled('level')) $query->where('level', $request->level);
        if ($request->filled('status')) $query->where('enrollment_status', $request->status);

        try {
            $count = DB::transaction(function () use ($query) {
                $students = $query->get();
                foreach ($students as $student) {
                    $student->delete(); // Soft delete
                }
                return $students->count();
            });

            AuditService::log('Bulk Archiving', null, "Archived {$count} students");

            return back()->with('success', "Successfully archived {$count} students.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to archive students: ' . $e->getMessage());
        }
    }
}
