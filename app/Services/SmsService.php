<?php

namespace App\Services;

use App\Support\PhoneNumber;
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
            Log::error('SMS not sent: provider not configured', [
                'phone_hash' => hash('sha256', $phone),
            ]);
            return false;
        }

        try {
            $phone = $this->normalizePhone($phone);

            $this->sms->send([
                'to'      => $phone,
                'message' => $message,
                'from'    => config('services.africastalking.sender_id'),
            ]);

            Log::info('SMS sent successfully', [
                'phone_hash' => hash('sha256', $phone),
            ]);
            return true;
        } catch (\Exception $e) {
            Log::error('SMS send failed', [
                'phone_hash' => hash('sha256', $phone),
                'error' => $e->getMessage(),
            ]);
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
            Log::error('Bulk SMS not sent: provider not configured', [
                'recipient_count' => count($phones),
            ]);
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
        return PhoneNumber::normalize($phone) ?? $phone;
    }
}
