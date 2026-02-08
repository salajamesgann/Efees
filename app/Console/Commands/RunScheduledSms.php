<?php

namespace App\Console\Commands;

use App\Models\SmsSchedule;
use App\Services\SmsService;
use Illuminate\Console\Command;

class RunScheduledSms extends Command
{
    protected $signature = 'sms:run';

    protected $description = 'Send scheduled SMS reminders';

    public function handle(SmsService $smsService)
    {
        $pending = SmsSchedule::where('status', 'pending')
            ->where('schedule_time', '<=', now())
            ->with('student.parents')
            ->get();

        $this->info('Found '.$pending->count().' pending SMS to send.');

        foreach ($pending as $item) {
            $guardian = $item->student ? $item->student->parents->sortByDesc('pivot.is_primary')->first() : null;
            $contact = $guardian ? $guardian->phone : null;

            if ($contact) {
                // The service logs to SmsLog
                $smsService->send($contact, $item->message, $item->student_id);
                $item->update(['status' => 'sent']);
            } else {
                $item->update(['status' => 'failed']);
            }
        }
    }
}
