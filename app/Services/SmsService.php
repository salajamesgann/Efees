<?php

namespace App\Services;

use App\Models\SmsLog;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $gateway;

    public function __construct(SmsGatewayService $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Send an SMS message via the configured gateway.
     *
     * @param  string  $to  Mobile number
     * @param  string  $message  Message content
     * @param  string|null  $studentId  Optional student ID for logging association
     */
    public function send(string $to, string $message, ?string $studentId = null): bool
    {
        $notificationsEnabled = SystemSetting::where('key', 'notifications_enabled')->value('value');

        if ($notificationsEnabled !== '1') {
            SmsLog::create([
                'student_id' => $studentId,
                'mobile_number' => $to,
                'message' => $message,
                'status' => 'disabled',
                'sent_at' => now(),
            ]);

            return false;
        }

        try {
            $result = $this->gateway->send($to, $message);
            $success = $result['success'];
            $status = $result['status'];
        } catch (\Exception $e) {
            Log::error('SmsService Error: '.$e->getMessage());
            $success = false;
            $status = 'failed';
        }

        // Create Database Log
        SmsLog::create([
            'student_id' => $studentId,
            'mobile_number' => $to,
            'message' => $message,
            'status' => $status,
            'sent_at' => now(),
        ]);

        return $success;
    }
}
