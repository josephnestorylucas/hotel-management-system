<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $booking) {}

    public function build(): self
    {
        return $this
            ->subject("Booking Confirmed — {$this->booking['reference']}")
            ->view('emails.booking-confirmed');
    }
}
