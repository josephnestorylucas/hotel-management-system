<?php

namespace App\Jobs;

use App\Mail\BookingConfirmedMail;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public array $booking) {}

    public function handle(SmsService $smsService): void
    {
        // Send email
        if (!empty($this->booking['email'])) {
            Mail::to($this->booking['email'])->send(new BookingConfirmedMail($this->booking));
        }

        // Send SMS
        if (!empty($this->booking['phone'])) {
            $msg = "Grand Hotel: Booking CONFIRMED. Ref: {$this->booking['reference']}. " .
                   "Room {$this->booking['room_number']}. Check-in: {$this->booking['check_in']}. " .
                   "Welcome!";
            $smsService->send($this->booking['phone'], $msg);
        }
    }
}
