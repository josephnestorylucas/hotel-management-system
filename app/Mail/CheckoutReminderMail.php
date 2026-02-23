<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CheckoutReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $booking) {}

    public function build(): self
    {
        return $this
            ->subject("Check-out Reminder — Grand Hotel")
            ->view('emails.checkout-reminder');
    }
}
