<?php

namespace App\Jobs;

use App\Mail\ReservationConfirmedMail;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendReservationConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(public array $reservation) {}

    public function handle(SmsService $smsService): void
    {
        $sent = ['email' => false, 'sms' => false];

        // Send email if email exists
        if (!empty($this->reservation['email'])) {
            try {
                Mail::to($this->reservation['email'])->send(new ReservationConfirmedMail($this->reservation));
                $sent['email'] = true;
                Log::info("Reservation confirmation email sent", [
                    'reference' => $this->reservation['reference'],
                    'email' => $this->reservation['email'],
                ]);
            } catch (\Exception $e) {
                Log::error("Reservation confirmation email failed", [
                    'reference' => $this->reservation['reference'],
                    'email' => $this->reservation['email'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Send SMS if phone exists
        if (!empty($this->reservation['phone'])) {
            try {
                $msg = "Hello {$this->reservation['guest_name']}, your reservation #{$this->reservation['reference']} is confirmed from {$this->reservation['check_in']} to {$this->reservation['check_out']}. Welcome to Grand Hotel!";
                $result = $smsService->send($this->reservation['phone'], $msg);
                $sent['sms'] = $result;
                
                if ($result) {
                    Log::info("Reservation confirmation SMS sent", [
                        'reference' => $this->reservation['reference'],
                        'phone' => $this->reservation['phone'],
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Reservation confirmation SMS failed", [
                    'reference' => $this->reservation['reference'],
                    'phone' => $this->reservation['phone'],
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Log overall result
        Log::info("Reservation confirmation notification completed", [
            'reference' => $this->reservation['reference'],
            'email_sent' => $sent['email'],
            'sms_sent' => $sent['sms'],
        ]);
    }
}
