<?php

namespace App\Jobs;

use App\Mail\BookingConfirmedMail;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public array $booking) {}

    public function handle(SmsService $smsService): void
    {
        $sent = ['email' => false, 'sms' => false];

        // Send email if email exists
        if (!empty($this->booking['email'])) {
            try {
                Mail::to($this->booking['email'])->send(new BookingConfirmedMail($this->booking));
                $sent['email'] = true;
                Log::info("Booking confirmation email sent", [
                    'reference' => $this->booking['reference'],
                    'email' => $this->booking['email'],
                ]);
            } catch (\Exception $e) {
                Log::error("Booking confirmation email failed", [
                    'reference' => $this->booking['reference'],
                    'email' => $this->booking['email'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Send SMS if phone exists
        if (!empty($this->booking['phone'])) {
            try {
                $msg = "Grand Hotel: Booking CONFIRMED. Ref: {$this->booking['reference']}. " .
                       "Room {$this->booking['room_number']}. Check-in: {$this->booking['check_in']}. " .
                       "Welcome!";
                $result = $smsService->send($this->booking['phone'], $msg);
                $sent['sms'] = $result;
                
                if ($result) {
                    Log::info("Booking confirmation SMS sent", [
                        'reference' => $this->booking['reference'],
                        'phone' => $this->booking['phone'],
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Booking confirmation SMS failed", [
                    'reference' => $this->booking['reference'],
                    'phone' => $this->booking['phone'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Log overall result
        Log::info("Booking confirmation notification completed", [
            'reference' => $this->booking['reference'],
            'email_sent' => $sent['email'],
            'sms_sent' => $sent['sms'],
        ]);
    }
}
