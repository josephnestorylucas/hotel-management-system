<?php

namespace App\Mail;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttendeeTicketMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Attendance $attendance) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Event Ticket — ' . $this->attendance->event->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.attendee-ticket',
        );
    }
}
