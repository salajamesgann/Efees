<?php

namespace App\Console\Commands;

use App\Models\ParentContact;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendParentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'efees:send-parent-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send due date and overdue reminders to parents';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting fee reminder process...');

        $this->sendDueDateReminders();
        $this->sendOverdueReminders();

        $this->info('Fee reminder process completed.');
    }

    private function sendDueDateReminders()
    {
        // Find fees due in 3 days
        $targetDate = now()->addDays(3)->format('Y-m-d');

        // Find parents with students who have fee records due on the target date with positive balance
        $parents = ParentContact::whereHas('students', function ($query) use ($targetDate) {
            $query->whereHas('feeRecords', function ($q) use ($targetDate) {
                $q->whereDate('payment_date', $targetDate)
                    ->where('balance', '>', 0);
            });
        })->with(['students' => function ($query) use ($targetDate) {
            $query->whereHas('feeRecords', function ($q) use ($targetDate) {
                $q->whereDate('payment_date', $targetDate)
                    ->where('balance', '>', 0);
            });
        }])->get();

        foreach ($parents as $parent) {
            $user = \App\Models\User::where('roleable_type', \App\Models\ParentContact::class)
                ->where('roleable_id', $parent->id)
                ->first();

            if (! $user) {
                continue;
            }

            $prefs = DB::table('user_preferences')->where('user_id', $user->user_id)->first();
            $smsEnabled = $prefs->sms_reminders ?? false;
            $emailEnabled = $prefs->email_notifications ?? false;

            foreach ($parent->students as $student) {
                // Get the specific records due
                $dueRecords = $student->feeRecords()
                    ->whereDate('payment_date', $targetDate)
                    ->where('balance', '>', 0)
                    ->get();

                if ($dueRecords->isEmpty()) {
                    continue;
                }

                $totalDue = $dueRecords->sum('balance');

                // In-App Notification
                DB::table('notifications')->insert([
                    'user_id' => $user->user_id,
                    'title' => 'Upcoming Payment Due',
                    'body' => 'Reminder: A payment of ₱'.number_format($totalDue, 2)." for {$student->first_name} is due on {$targetDate}.",
                    'created_at' => now(),
                ]);

                // Send SMS if enabled
                if ($smsEnabled && $parent->phone) {
                    Log::info("SMS sent to {$parent->phone}: Payment of ₱{$totalDue} for {$student->first_name} is due on {$targetDate}.");
                }

                // Send Email if enabled
                if ($emailEnabled && $user->email) {
                    Log::info("Email sent to {$user->email}: Payment Due Reminder for {$student->first_name}");
                }
            }
        }
    }

    private function sendOverdueReminders()
    {
        // Find parents with active students who have outstanding balance
        // and have opted in for SMS reminders.

        $parents = ParentContact::whereHas('students', function ($query) {
            $query->whereHas('feeRecords', function ($q) {
                $q->where('balance', '>', 0);
            });
        })->with(['students' => function ($query) {
            $query->whereHas('feeRecords', function ($q) {
                $q->where('balance', '>', 0);
            });
        }])->get();

        foreach ($parents as $parent) {
            // Check preferences
            $user = \App\Models\User::where('roleable_type', \App\Models\ParentContact::class)
                ->where('roleable_id', $parent->id)
                ->first();

            if (! $user) {
                continue;
            }

            $prefs = DB::table('user_preferences')->where('user_id', $user->user_id)->first();
            $smsEnabled = $prefs->sms_reminders ?? false;
            $emailEnabled = $prefs->email_notifications ?? false;

            foreach ($parent->students as $student) {
                $balance = $student->feeRecords()->sum('balance');

                if ($balance > 0) {
                    // Create In-App Notification
                    DB::table('notifications')->insert([
                        'user_id' => $user->user_id,
                        'title' => 'Outstanding Balance Reminder',
                        'body' => "Reminder: {$student->first_name} has an outstanding balance of ₱".number_format($balance, 2).'. Please settle at your earliest convenience.',
                        'created_at' => now(),
                    ]);

                    // Send SMS if enabled
                    if ($smsEnabled && $parent->phone) {
                        // Mock SMS sending - in production this would call an SMS gateway
                        Log::info("SMS sent to {$parent->phone}: Reminder for {$student->first_name}'s balance of {$balance}");
                    }

                    // Send Email if enabled
                    if ($emailEnabled && $user->email) {
                        Log::info("Email sent to {$user->email}: Overdue Balance Reminder for {$student->first_name}");
                    }
                }
            }
        }
    }
}
