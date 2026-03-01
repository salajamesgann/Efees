<?php

namespace App\Http\Controllers;

use App\Models\GeneratedReport;
use App\Models\Payment;
use App\Models\ScheduledReport;
use App\Models\Student;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StaffReportsController extends Controller
{
    public function index(): View
    {
        // Get active school year
        $activeYear = SystemSetting::where('key', 'school_year')->value('value');
        
        // Get unique levels and sections for dropdowns - from current school year only
        $levels = collect(['Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12']);
        $sections = Student::where('school_year', $activeYear)->distinct()->whereNotNull('section')->orderBy('section')->pluck('section');

        // Get scheduled reports for the current user
        $scheduledReports = ScheduledReport::where('created_by', Auth::user()->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get generated reports history
        $generatedReports = GeneratedReport::where('created_by', Auth::user()->user_id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('auth.staff_reports', compact('levels', 'sections', 'scheduledReports', 'generatedReports'));
    }

    public function downloadReport($id)
    {
        $report = GeneratedReport::where('id', $id)->where('created_by', Auth::user()->user_id)->firstOrFail();

        // Extract filename from file_url (assuming file:// format)
        $filename = str_replace('file://', '', $report->file_url);

        // If it's just a filename, assume it's in storage/app
        if (! file_exists($filename) && file_exists(storage_path('app/'.$filename))) {
            $path = storage_path('app/'.$filename);
        } else {
            $path = $filename;
        }

        if (! file_exists($path)) {
            return back()->with('error', 'Report file not found.');
        }

        return response()->download($path);
    }

    public function schedule(Request $request)
    {
        $request->validate([
            'level' => 'nullable|string',
            'section' => 'nullable|string',
            'frequency' => 'required|in:daily,weekly,monthly',
        ]);

        $nextRunAt = now();
        if ($request->frequency === 'daily') {
            $nextRunAt->addDay();
        } elseif ($request->frequency === 'weekly') {
            $nextRunAt->addWeek();
        } elseif ($request->frequency === 'monthly') {
            $nextRunAt->addMonth();
        }

        ScheduledReport::create([
            'report_type' => 'Payment Summary',
            'parameters' => [
                'level' => $request->level,
                'section' => $request->section,
            ],
            'frequency' => $request->frequency,
            'next_run_at' => $nextRunAt,
            'created_by' => Auth::user()->user_id,
            'status' => 'active',
        ]);

        return back()->with('success', 'Report scheduled successfully.');
    }

    public function destroy($id)
    {
        $report = ScheduledReport::where('id', $id)->where('created_by', Auth::user()->user_id)->firstOrFail();
        $report->delete();

        return back()->with('success', 'Scheduled report deleted.');
    }

    public function exportCsv(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'level' => 'nullable|string',
            'section' => 'nullable|string',
        ]);

        $from = $request->get('from');
        $to = $request->get('to');
        $level = $request->get('level');
        $section = $request->get('section');

        $query = Payment::query()
            ->join('students', 'payments.student_id', '=', 'students.student_id')
            ->select('payments.*', 'students.first_name', 'students.last_name', 'students.level', 'students.section');

        if ($from) {
            $query->whereDate('payments.paid_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('payments.paid_at', '<=', $to);
        }
        if ($level) {
            $query->where('students.level', $level);
        }
        if ($section) {
            $query->where('students.section', $section);
        }

        $filename = 'staff_report_'.now()->format('Ymd_His').'.csv';
        $path = storage_path('app/'.$filename);

        $fp = fopen($path, 'w');
        // CSV Header
        fputcsv($fp, ['Student ID', 'Student Name', 'Level', 'Section', 'Amount Paid', 'Method', 'Reference', 'Date']);
        $query->orderBy('payments.paid_at', 'desc')->chunk(1000, function ($chunk) use ($fp) {
            foreach ($chunk as $r) {
                fputcsv($fp, [
                    $r->student_id,
                    $r->last_name.', '.$r->first_name,
                    $r->level,
                    $r->section,
                    (float) $r->amount_paid,
                    $r->method,
                    $r->reference_number,
                    optional($r->paid_at)->toDateTimeString(),
                ]);
            }
        });
        fclose($fp);

        // Log the report generation
        GeneratedReport::create([
            'type' => 'Staff Payment Report',
            'file_url' => 'file://'.$filename,
            'created_by' => optional(Auth::user())->user_id ?? 0,
        ]);

        return response()->download($path, $filename);
    }
}
