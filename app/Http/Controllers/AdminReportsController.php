<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\FeeRecord;
use App\Models\GeneratedReport;
use App\Models\Payment;
use App\Models\ScheduledReport;
use App\Models\SmsLog;
use App\Models\Student;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminReportsController extends Controller
{
    public function index(Request $request): View
    {
        // Audit Log
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'View Reports & Analytics',
            'details' => json_encode($request->all()),
            'ip_address' => $request->ip(),
        ]);

        // Dropdown data
        $schoolYears = Student::distinct()->whereNotNull('school_year')->orderBy('school_year', 'desc')->pluck('school_year');
        $levels = Student::distinct()->whereNotNull('level')->orderBy('level')->pluck('level');
        $sections = Student::distinct()->whereNotNull('section')->orderBy('section')->pluck('section');

        // Filter Parameters
        $selectedYear = $request->get('school_year');
        $selectedLevel = $request->get('level');
        $selectedSection = $request->get('section');
        $search = $request->get('search');
        $status = $request->get('status');

        // Base student query for filtering dashboard stats
        $statsStudentQuery = Student::query()
            ->when($selectedYear, fn($q) => $q->where('school_year', $selectedYear))
            ->when($selectedLevel, fn($q) => $q->where('level', $selectedLevel))
            ->when($selectedSection, fn($q) => $q->where('section', $selectedSection));

        $statsStudentIds = $statsStudentQuery->pluck('student_id');

        // Dashboard Stats (Filtered)
        $totalCollected = Payment::whereIn('student_id', $statsStudentIds)->sum('amount_paid');
        $pendingApprovals = Payment::whereIn('student_id', $statsStudentIds)->where('status', 'pending')->sum('amount_paid');
        $pendingPayments = FeeRecord::whereIn('student_id', $statsStudentIds)->pending()->sum('balance');
        $overdueBalances = FeeRecord::whereIn('student_id', $statsStudentIds)->overdue()->sum('balance');
        $remindersSent = SmsLog::whereIn('student_id', $statsStudentIds)->where('status', 'sent')->count();

        // Detailed Reports Query
        $studentsQuery = Student::with(['feeAssignments', 'feeRecords', 'payments'])
            ->when($selectedYear, fn ($q) => $q->where('school_year', $selectedYear))
            ->when($selectedLevel, fn ($q) => $q->where('level', $selectedLevel))
            ->when($selectedSection, fn ($q) => $q->where('section', $selectedSection))
            ->when($search, fn ($q) => $q->where(function ($sub) use ($search) {
                $sub->where('student_id', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            }));

        // Filter by Status (Paid, Partial, Overdue) requires checking aggregated data
        if ($status) {
            $studentsQuery->whereHas('feeRecords', function ($q) use ($status) {
                if ($status === 'paid') {
                    $q->where('status', 'paid');
                } elseif ($status === 'overdue') {
                    $q->where('status', 'overdue');
                } elseif ($status === 'partial') {
                    $q->where('status', 'pending')->where('balance', '>', 0);
                }
            });
        }

        $students = $studentsQuery->paginate(15)->withQueryString();

        // Recent SMS Logs (for the view)
        $recentSmsLogs = SmsLog::with('student')->orderBy('sent_at', 'desc')->paginate(10, ['*'], 'sms_page');

        // Scheduled Reports
        $scheduledReports = ScheduledReport::where('created_by', Auth::user()->user_id ?? 0)
            ->orderBy('created_at', 'desc')
            ->get();

        // Generated Reports History
        $generatedReports = GeneratedReport::orderBy('created_at', 'desc')->paginate(10, ['*'], 'history_page');

        return view('auth.admin_reports_index', compact(
            'schoolYears', 'levels', 'sections',
            'totalCollected', 'pendingApprovals', 'pendingPayments', 'overdueBalances', 'remindersSent',
            'students', 'recentSmsLogs', 'scheduledReports', 'generatedReports'
        ));
    }

    public function exportSmsCsv(Request $request)
    {
        // Audit Log
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Export SMS CSV Report',
            'details' => json_encode($request->all()),
            'ip_address' => $request->ip(),
        ]);

        $filename = 'admin_sms_report_'.now()->format('Ymd_His').'.csv';
        $path = storage_path('app/'.$filename);

        $fp = fopen($path, 'w');

        // Headers
        fputcsv($fp, ['Date Sent', 'Student ID', 'Student Name', 'Mobile Number', 'Message', 'Status', 'Provider Response']);

        // Query
        $query = SmsLog::with('student')->orderBy('sent_at', 'desc');
        // Apply filters if needed (e.g. date range), for now export all or limit
        $logs = $query->get();

        foreach ($logs as $log) {
            fputcsv($fp, [
                $log->sent_at,
                $log->student_id,
                $log->student ? $log->student->full_name : 'N/A',
                $log->mobile_number,
                $log->message,
                $log->status,
                $log->provider_response,
            ]);
        }

        fclose($fp);

        GeneratedReport::create([
            'type' => 'SMS Log Report',
            'file_url' => 'file://'.$filename,
            'created_by' => Auth::user()->user_id ?? 0,
        ]);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    public function exportCsv(Request $request)
    {
        // Audit Log
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'Export CSV Report',
            'details' => json_encode($request->all()),
            'ip_address' => $request->ip(),
        ]);

        $format = strtolower($request->input('format', 'csv'));
        $columns = [
            'Student ID', 'Name', 'Level', 'Section', 'School Year',
            'Tuition', 'Charges', 'Discounts', 'Total Due',
            'Total Paid', 'Balance', 'Status',
        ];

        // Re-run query for export (without pagination)
        $query = Student::with(['feeAssignments', 'feeRecords']);

        if ($request->school_year) {
            $query->where('school_year', $request->school_year);
        }
        if ($request->level) {
            $query->where('level', $request->level);
        }
        if ($request->section) {
            $query->where('section', $request->section);
        }
        // ... apply other filters

        $students = $query->get();
        $rows = [];
        foreach ($students as $student) {
            $assignment = $student->getCurrentFeeAssignment($student->school_year);
            $tuition = $assignment ? $assignment->base_tuition : 0;
            $charges = $assignment ? $assignment->additional_charges_total : 0;
            $discounts = $assignment ? $assignment->discounts_total : 0;
            $totalDue = $assignment ? $assignment->total_amount : 0;
            $paid = $student->total_paid;
            $balance = $student->current_balance;
            $status = 'Pending';
            if ($balance <= 0 && $totalDue > 0) {
                $status = 'Paid';
            } elseif ($student->feeRecords()->where('status', 'overdue')->exists()) {
                $status = 'Overdue';
            }
            $rows[] = [
                'Student ID' => $student->student_id,
                'Name' => $student->full_name,
                'Level' => $student->level,
                'Section' => $student->section,
                'School Year' => $student->school_year,
                'Tuition' => $tuition,
                'Charges' => $charges,
                'Discounts' => $discounts,
                'Total Due' => $totalDue,
                'Total Paid' => $paid,
                'Balance' => $balance,
                'Status' => $status,
            ];
        }
        $svc = app(ExportService::class);
        $fname = 'admin_report_'.now()->format('Ymd_His');
        $resp = $svc->exportTable($columns, $rows, $format, $fname, [
            'title' => 'Admin Detailed Report',
            'view' => 'reports.pdf.table',
        ]);
        if ($resp instanceof \Illuminate\Http\JsonResponse) {
            $filename = $fname.'.csv';
            $path = storage_path('app/'.$filename);
            $fp = fopen($path, 'w');
            fputcsv($fp, $columns);
            foreach ($rows as $r) {
                $line = [];
                foreach ($columns as $col) {
                    $line[] = $r[$col] ?? '';
                }
                fputcsv($fp, $line);
            }
            fclose($fp);
            GeneratedReport::create([
                'type' => 'Admin Detailed Report',
                'file_url' => 'file://'.$filename,
                'created_by' => Auth::user()->user_id ?? 0,
            ]);

            return response()->download($path, $filename)->deleteFileAfterSend(true);
        }

        return $resp;
    }

    public function metrics(Request $request): \Illuminate\Http\JsonResponse
    {
        $year = $request->get('school_year');
        $level = $request->get('level');
        $section = $request->get('section');

        // Base student query for filtering
        $studentQuery = Student::query()
            ->when($year, fn($q) => $q->where('school_year', $year))
            ->when($level, fn($q) => $q->where('level', $level))
            ->when($section, fn($q) => $q->where('section', $section));

        $studentIds = $studentQuery->pluck('student_id');

        // Core metrics
        $totalCollected = Payment::whereIn('student_id', $studentIds)->where('status', 'approved')->sum('amount_paid');
        $pendingApprovals = Payment::whereIn('student_id', $studentIds)->where('status', 'pending')->sum('amount_paid');
        $pendingPayments = FeeRecord::whereIn('student_id', $studentIds)->pending()->sum('balance');
        $overdueBalances = FeeRecord::whereIn('student_id', $studentIds)->overdue()->sum('balance');
        $remindersSent = SmsLog::whereIn('student_id', $studentIds)->where('status', 'sent')->count();

        // 1. Collections by Grade/Section
        if ($level) {
            $collectionsByGrade = \Illuminate\Support\Facades\DB::table('payments')
                ->join('students', 'payments.student_id', '=', 'students.student_id')
                ->whereIn('students.student_id', $studentIds)
                ->where('payments.status', 'approved')
                ->select('students.section as label', \Illuminate\Support\Facades\DB::raw('SUM(payments.amount_paid) as total'))
                ->groupBy('students.section')
                ->orderBy('students.section')
                ->get();
        } else {
            $rawGrades = \Illuminate\Support\Facades\DB::table('payments')
                ->join('students', 'payments.student_id', '=', 'students.student_id')
                ->whereIn('students.student_id', $studentIds)
                ->where('payments.status', 'approved')
                ->select('students.level as label', \Illuminate\Support\Facades\DB::raw('SUM(payments.amount_paid) as total'))
                ->groupBy('students.level')
                ->get();

            $allLevels = ['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'];
            $collectionsByGrade = collect($allLevels)->map(function ($lvl) use ($rawGrades) {
                $found = $rawGrades->firstWhere('label', $lvl);
                return [
                    'label' => $lvl,
                    'total' => $found ? (float) $found->total : 0.0,
                ];
            });
        }

        // 2. Payment Trends (Monthly)
        $paymentTrends = \Illuminate\Support\Facades\DB::table('payments')
            ->whereIn('student_id', $studentIds)
            ->where('status', 'approved')
            ->select(
                \Illuminate\Support\Facades\DB::raw("DATE_FORMAT(payment_date, '%Y-%m') as month"),
                \Illuminate\Support\Facades\DB::raw('SUM(amount_paid) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 3. Payment Status Overview
        $statusOverview = [
            ['label' => 'Paid', 'count' => (int) FeeRecord::whereIn('student_id', $studentIds)->where('status', 'paid')->count(), 'color' => '#10b981'],
            ['label' => 'Pending', 'count' => (int) FeeRecord::whereIn('student_id', $studentIds)->where('status', 'pending')->count(), 'color' => '#f59e0b'],
            ['label' => 'Overdue', 'count' => (int) FeeRecord::whereIn('student_id', $studentIds)->where('status', 'overdue')->count(), 'color' => '#ef4444'],
        ];

        return response()->json([
            'totalCollected' => (float) $totalCollected,
            'pendingApprovals' => (float) $pendingApprovals,
            'pendingPayments' => (float) $pendingPayments,
            'overdueBalances' => (float) $overdueBalances,
            'remindersSent' => (int) $remindersSent,
            'collectionsByGrade' => $collectionsByGrade,
            'paymentTrends' => $paymentTrends,
            'statusOverview' => $statusOverview,
        ]);
    }

    public function schedule(Request $request)
    {
        $request->validate([
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
            'report_type' => 'Admin Detailed Report',
            'parameters' => $request->except(['_token', 'frequency']),
            'frequency' => $request->frequency,
            'next_run_at' => $nextRunAt,
            'created_by' => Auth::user()->user_id ?? 0,
            'status' => 'active',
        ]);

        return back()->with('success', 'Report scheduled successfully.');
    }

    public function downloadReport($id)
    {
        $report = GeneratedReport::findOrFail($id);

        $filename = str_replace('file://', '', $report->file_url);
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

    public function destroy($id)
    {
        ScheduledReport::where('id', $id)->delete();

        return back()->with('success', 'Scheduled report deleted.');
    }
}
