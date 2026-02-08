<?php

namespace Database\Seeders;

use App\Models\SmsTemplate;
use Illuminate\Database\Seeder;

class SmsTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Fee Reminder (Upcoming)',
                'content' => 'Dear [Guardian Name], this is a reminder that [Student Name] has a school fee of [Amount] due on [Due Date]. Please ensure payment is completed on time. Thank you, [School Name].',
            ],
            [
                'name' => 'Overdue Payment Alert',
                'content' => 'Dear [Guardian Name], [Student Name] has an overdue balance of [Amount] as of [Date]. Kindly settle the payment immediately to avoid penalties. - [School Name]',
            ],
            [
                'name' => 'Payment Confirmation',
                'content' => 'Dear [Guardian Name], we confirm receipt of [Amount Paid] from [Student Name] on [Payment Date]. Your current balance is [Remaining Balance]. Thank you, [School Name].',
            ],
            [
                'name' => 'Partial Payment Reminder',
                'content' => 'Dear [Guardian Name], [Student Name] has paid [Amount Paid]. Remaining balance is [Remaining Balance]. Please complete the payment by [Due Date]. - [School Name]',
            ],
            [
                'name' => 'System Test',
                'content' => 'This is a system test from [School Name] SMS service. If you received this message, your contact information is correctly registered.',
            ],
            // Provided templates
            [
                'name' => 'Enrollment Confirmation',
                'content' => '📚 {{school_name}}: Hi {{student_name}}, you are now officially enrolled for SY {{school_year}} – Grade {{grade_level}}. Please keep your contact details updated. Thank you!',
            ],
            [
                'name' => 'Upcoming Due Date Reminder',
                'content' => '🔔 {{school_name}} Reminder: Your payment of ₱{{amount}} for {{term_or_month}} is due on {{due_date}}. Please settle on or before the date to avoid penalties. Thank you.',
            ],
            [
                'name' => 'Installment Payment Reminder',
                'content' => '🔔 Reminder from {{school_name}}: Your installment payment of ₱{{amount}} is due on {{due_date}}. Kindly pay on time. Thank you.',
            ],
            [
                'name' => 'Overdue Balance Notice',
                'content' => '⚠️ {{school_name}} Notice: Your balance of ₱{{balance}} is overdue as of {{date}}. Please settle immediately to avoid further penalties. Thank you.',
            ],
            [
                'name' => 'Final Overdue Reminder',
                'content' => '⚠️ FINAL REMINDER: Your overdue balance of ₱{{balance}} at {{school_name}} must be paid immediately. Please contact the school for assistance.',
            ],
            [
                'name' => 'Payment Received',
                'content' => '✅ {{school_name}}: We received your payment of ₱{{amount}} on {{date}} for {{student_name}}. Thank you! OR/Ref No: {{or_number}}.',
            ],
            [
                'name' => 'Partial Payment Received',
                'content' => '📘 {{school_name}}: Partial payment of ₱{{amount}} received for {{student_name}}. Remaining balance: ₱{{balance}}. Thank you.',
            ],
            [
                'name' => 'Balance Update',
                'content' => '📊 {{school_name}} Update: {{student_name}} now has an outstanding balance of ₱{{balance}}. Due Date: {{due_date}}.',
            ],
            [
                'name' => 'Graduation Fee Reminder',
                'content' => '🎓 {{school_name}} Reminder: Graduation fee of ₱{{amount}} for {{student_name}} is due on {{due_date}}. Please settle to avoid delays in processing.',
            ],
            [
                'name' => 'Account Update Notice',
                'content' => 'ℹ️ {{school_name}}: Your account details have been updated. If you did not request this change, please contact the school immediately.',
            ],
            [
                'name' => 'Notification Preference',
                'content' => '🔔 {{school_name}}: You are subscribed to SMS reminders. Reply STOP to disable optional notifications.',
            ],
        ];

        foreach ($templates as $template) {
            SmsTemplate::updateOrCreate(
                ['name' => $template['name']],
                ['content' => $template['content']]
            );
        }
    }
}
