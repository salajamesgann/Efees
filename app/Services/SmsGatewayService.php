<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SmsGatewayService
{
    protected $driver;

    public function __construct()
    {
        $this->driver = config('services.sms.driver', 'log');
    }

    /**
     * Send an SMS message.
     */
    public function send(string $to, string $message): array
    {
        Log::info("SMS Gateway: Sending via {$this->driver} to $to");

        try {
            switch ($this->driver) {
                case 'semaphore':
                    return $this->sendViaSemaphore($to, $message);
                case 'twilio':
                    return $this->sendViaTwilio($to, $message);
                case 'log':
                default:
                    return $this->sendViaLog($to, $message);
            }
        } catch (\Exception $e) {
            Log::error('SMS Gateway Error: '.$e->getMessage());

            return [
                'success' => false,
                'message_id' => null,
                'status' => 'failed',
                'response' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send via Semaphore (Philippines).
     */
    protected function sendViaSemaphore(string $to, string $message): array
    {
        $apiKey = config('services.semaphore.api_key');
        $senderName = config('services.semaphore.sender_name');

        if (empty($apiKey)) {
            throw new \Exception('Semaphore API Key is missing.');
        }

        $response = Http::retry(3, 500)->post('https://api.semaphore.co/api/v4/messages', [
            'apikey' => $apiKey,
            'number' => $to,
            'message' => $message,
            'sendername' => $senderName,
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'message_id' => $response->json()[0]['message_id'] ?? null,
                'status' => 'sent',
                'response' => $response->json(),
            ];
        }

        throw new \Exception('Semaphore Error: '.$response->body());
    }

    /**
     * Send via Twilio (International).
     */
    protected function sendViaTwilio(string $to, string $message): array
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.auth_token');
        $from = config('services.twilio.from');

        if (empty($sid) || empty($token) || empty($from)) {
            throw new \Exception('Twilio credentials are missing.');
        }

        // Format number to E.164 if not already (basic check)
        if (! Str::startsWith($to, '+')) {
            // Assume PH if starts with 09
            if (Str::startsWith($to, '09')) {
                $to = '+63'.substr($to, 1);
            }
        }

        $response = Http::retry(3, 500)
            ->withBasicAuth($sid, $token)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $from,
                'To' => $to,
                'Body' => $message,
            ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'message_id' => $response->json()['sid'],
                'status' => $response->json()['status'] ?? 'sent',
                'response' => $response->json(),
            ];
        }

        throw new \Exception('Twilio Error: '.$response->body());
    }

    /**
     * Simulation Mode (Log only).
     */
    protected function sendViaLog(string $to, string $message): array
    {
        $messageId = 'MSG-'.Str::upper(Str::random(12));

        Log::info("SMS SIMULATION to $to: $message", [
            'id' => $messageId,
        ]);

        return [
            'success' => true,
            'message_id' => $messageId,
            'status' => 'simulated',
            'response' => 'Message logged only',
        ];
    }

    /**
     * Get the current status of a message.
     */
    public function getStatus(string $messageId): string
    {
        // For now, we only implement this for Twilio if needed, or return generic
        // In a real app, you might query the API again using the message ID.
        return 'sent';
    }
}
