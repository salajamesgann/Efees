<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayMongoService
{
    protected $baseUrl = 'https://api.paymongo.com/v1';

    protected $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.paymongo.secret_key');
    }

    /**
     * Create a Checkout Session
     *
     * @return array
     */
    public function createCheckoutSession(array $attributes)
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->post("{$this->baseUrl}/checkout_sessions", [
                'data' => [
                    'attributes' => $attributes,
                ],
            ]);

        if ($response->failed()) {
            Log::error('PayMongo Create Session Failed: '.$response->body());
            throw new \Exception('Failed to create payment session.');
        }

        return $response->json();
    }

    /**
     * Retrieve a Checkout Session
     *
     * @return array
     */
    public function retrieveCheckoutSession(string $sessionId)
    {
        $response = Http::withBasicAuth($this->secretKey, '')
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])
            ->get("{$this->baseUrl}/checkout_sessions/{$sessionId}");

        if ($response->failed()) {
            Log::error('PayMongo Retrieve Session Failed: '.$response->body());
            throw new \Exception('Failed to retrieve payment session.');
        }

        return $response->json();
    }
}
