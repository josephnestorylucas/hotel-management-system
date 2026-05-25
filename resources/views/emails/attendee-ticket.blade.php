@extends('emails.layout')

@section('content')
<p>Dear <strong>{{ $attendance->full_name }}</strong>,</p>

<p>Your registration for <strong>{{ $attendance->event->title }}</strong> has been confirmed. Below is your event ticket:</p>

<div style="background:#f9fafb;border:2px solid #e5e7eb;border-radius:12px;padding:24px;margin:20px 0;text-align:center;">
    <h2 style="margin:0 0 4px;color:#1e293b;">{{ $attendance->event->title }}</h2>
    <p style="margin:0 0 12px;color:#6b7280;font-size:14px;">{{ $attendance->event->start_date->format('M d, Y') }} @if($attendance->event->end_date) — {{ $attendance->event->end_date->format('M d, Y') }}@endif</p>

    <div style="background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;padding:16px;margin:16px auto;max-width:300px;">
        <p style="margin:0;font-size:12px;color:#6b7280;">TICKET NUMBER</p>
        <p style="margin:4px 0;font-size:18px;font-weight:bold;font-family:monospace;color:#1e293b;">{{ $attendance->ticket_number }}</p>
    </div>

    <table style="width:100%;max-width:400px;margin:0 auto;text-align:left;" cellpadding="8">
        <tr><td style="color:#6b7280;font-size:13px;">Name</td><td style="font-weight:600;color:#1e293b;">{{ $attendance->full_name }}</td></tr>
        <tr><td style="color:#6b7280;font-size:13px;">Pass</td><td style="font-weight:600;color:#1e293b;">{{ ucfirst($attendance->pass_type ?? 'Attendee') }}</td></tr>
        <tr><td style="color:#6b7280;font-size:13px;">Manual Code</td><td style="font-weight:600;font-family:monospace;color:#2563eb;">{{ $attendance->manual_code }}</td></tr>
    </table>

    <div style="margin-top:16px;">
        {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(180)->generate($attendance->qr_token) !!}
    </div>

    <p style="margin:8px 0 0;font-size:11px;color:#9ca3af;">Present this QR code at the registration desk for check-in</p>
</div>

<p style="font-size:13px;color:#6b7280;">If you have any questions, please contact the event organizer.</p>
@endsection
