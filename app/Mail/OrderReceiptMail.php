<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $order) {}

    public function build(): self
    {
        return $this
            ->subject("Order Receipt — Grand Hotel Restaurant")
            ->view('emails.order-receipt');
    }
}
