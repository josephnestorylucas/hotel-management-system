<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $reservation) {}

    public function build(): self
    {
        return $this
            ->subject("Reservation Confirmed — {$this->reservation['reference']}")
            ->view('emails.reservation-confirmed')
            ->with(['email' => $this->reservation['email'] ?? null]);
    }
}
