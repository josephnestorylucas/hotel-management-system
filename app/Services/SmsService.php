<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    private $sms;
    private bool $enabled;

    public function __construct()
    {
        $this->enabled = !empty(config('services.africastalking.username'))
            && !empty(config('services.africastalking.api_key'));

        if ($this->enabled && class_exists(\AfricasTalking\SDK\AfricasTalking::class)) {
            try {
                $at = new \AfricasTalking\SDK\AfricasTalking(
                    config('services.africastalking.username'),
                    config('services.africastalking.api_key')
                );
                $this->sms = $at->sms();
            } catch (\Exception $e) {
                Log::error("SMS Service initialization failed: " . $e->getMessage());
                $this->enabled = false;
            }
        } else {
            $this->enabled = false;
        }
    }

    /**
     * Send a single SMS.
     *
     * @param string $phone  e.g. +255712345678 or 0712345678
     * @param string $message
     */
    public function send(string $phone, string $message): bool
    {
        if (!$this->enabled) {
            Log::info("SMS (disabled/sandbox): To={$phone} | Message={$message}");
            return true; // Simulate success when SMS provider not configured
        }

        try {
            $phone = $this->normalizePhone($phone);

            $this->sms->send([
                'to'      => $phone,
                'message' => $message,
                'from'    => config('services.africastalking.sender_id'),
            ]);

            Log::info("SMS sent successfully to {$phone}");
            return true;
        } catch (\Exception $e) {
            Log::error("SMS send failed to {$phone}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send same message to multiple numbers.
     *
     * @param array  $phones  ['+255712345678', '+255723456789']
     * @param string $message
     */
    public function sendBulk(array $phones, string $message): void
    {
        if (!$this->enabled) {
            Log::info("SMS Bulk (disabled/sandbox): To=" . implode(',', $phones) . " | Message={$message}");
            return;
        }

        try {
            $normalized = array_map([$this, 'normalizePhone'], $phones);

            $this->sms->send([
                'to'      => implode(',', $normalized),
                'message' => $message,
                'from'    => config('services.africastalking.sender_id'),
            ]);

            Log::info("Bulk SMS sent to " . count($phones) . " recipients");
        } catch (\Exception $e) {
            Log::error("Bulk SMS send failed: " . $e->getMessage());
        }
    }

    /**
     * Normalize TZ numbers to international format.
     */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '+255' . substr($phone, 1);
        }
        if (str_starts_with($phone, '255')) {
            return '+' . $phone;
        }
        if (!str_starts_with($phone, '+')) {
            return '+' . $phone;
        }

        return $phone;
    }
}
