<?php

namespace App\Console\Commands;

use App\Models\GeneratedReport;
use App\Models\Payment;
use App\Models\ScheduledReport;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RunScheduledReports extends Command
{
    protected $signature = 'reports:run';

    protected $description = 'Run scheduled reports and generate files';

    public function handle()
    {
        $reports = ScheduledReport::where('status', 'active')
            ->where('next_run_at', '<=', now())
            ->get();

        $this->info('Found '.$reports->count().' reports to run.');

        foreach ($reports as $report) {
            $this->info("Running report #{$report->id} ({$report->frequency})");

            try {
                $this->generateReport($report);

                // Update next run time
                $nextRun = Carbon::parse($report->next_run_at);
                if ($report->frequency === 'daily') {
                    $nextRun->addDay();
                } elseif ($report->frequency === 'weekly') {
                    $nextRun->addWeek();
                } elseif ($report->frequency === 'monthly') {
                    $nextRun->addMonth();
                }

                // Ensure next run is in future if we missed multiple cycles
                while ($nextRun <= now()) {
                    if ($report->frequency === 'daily') {
                        $nextRun->addDay();
                    } elseif ($report->frequency === 'weekly') {
                        $nextRun->addWeek();
                    } elseif ($report->frequency === 'monthly') {
                        $nextRun->addMonth();
                    }
                }

                $report->update(['next_run_at' => $nextRun]);

            } catch (\Exception $e) {
                $this->error("Failed to run report #{$report->id}: ".$e->getMessage());
            }
        }
    }

    private function generateReport(ScheduledReport $report)
    {
        $params = $report->parameters ?? [];
        $level = $params['level'] ?? null;
        $section = $params['section'] ?? null;

        $query = Payment::query()
            ->join('students', 'payments.student_id', '=', 'students.student_id')
            ->select('payments.*', 'students.first_name', 'students.last_name', 'students.level', 'students.section');

        if ($level) {
            $query->where('students.level', $level);
        }
        if ($section) {
            $query->where('students.section', $section);
        }

        // For scheduled reports, maybe we want "recent" payments?
        // Or all payments for that class?
        // Let's assume "all payments" for now as per the "Payment Summary" type,
        // but typically scheduled reports might be "last week" etc.
        // Given the user request "payment status summaries", a full list is safer.

        $rows = $query->orderBy('payments.paid_at', 'desc')->get();

        $filename = 'scheduled_report_'.$report->id.'_'.now()->format('Ymd_His').'.csv';
        $path = storage_path('app/'.$filename);

        $fp = fopen($path, 'w');
        fputcsv($fp, ['Student ID', 'Student Name', 'Level', 'Section', 'Amount Paid', 'Method', 'Reference', 'Date']);

        foreach ($rows as $r) {
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
        fclose($fp);

        GeneratedReport::create([
            'type' => 'Scheduled: '.$report->report_type.' ('.ucfirst($report->frequency).')',
            'file_url' => 'file://'.$filename,
            'created_by' => $report->created_by,
        ]);

        $this->info("Generated $filename");
    }
}
