<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $invoice) {}

    public function build(): self
    {
        $mail = $this
            ->subject("Invoice — {$this->invoice['reference']} — Grand Hotel")
            ->view('emails.invoice');

        // Attach PDF if dompdf is available
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emails.invoice-pdf', ['invoice' => $this->invoice]);
            $mail->attachData(
                $pdf->output(),
                "invoice-{$this->invoice['reference']}.pdf",
                ['mime' => 'application/pdf']
            );
        }

        return $mail;
    }
}
