<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket — {{ $attendance->ticket_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f3f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .ticket { width: 380px; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.10); }
        .ticket-header { background: linear-gradient(135deg, {{ $event->theme_color ?? '#2563eb' }}, {{ $event->theme_color ?? '#2563eb' }}dd); color: #fff; padding: 24px 20px 20px; text-align: center; }
        .ticket-header h1 { font-size: 18px; font-weight: 800; margin-bottom: 4px; }
        .ticket-header p { font-size: 12px; opacity: 0.85; }
        .ticket-body { padding: 20px; text-align: center; }
        .ticket-number { background: #f9fafb; border: 1px dashed #d1d5db; border-radius: 8px; padding: 10px; margin-bottom: 16px; }
        .ticket-number small { display: block; font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing: 1px; }
        .ticket-number span { font-family: 'Courier New', monospace; font-size: 20px; font-weight: 700; color: #1e293b; }
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #f3f4f6; text-align: left; }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-size: 11px; color: #9ca3af; text-transform: uppercase; }
        .info-value { font-size: 13px; font-weight: 600; color: #1e293b; }
        .qr-section { margin: 20px 0 8px; text-align: center; }
        .qr-section img { display: block; margin: 0 auto; }
        .manual-code { font-family: 'Courier New', monospace; font-size: 22px; font-weight: 800; color: {{ $event->theme_color ?? '#2563eb' }}; margin-top: 8px; letter-spacing: 3px; }
        .ticket-footer { background: #f9fafb; padding: 12px 20px; text-align: center; font-size: 10px; color: #9ca3af; }
        .pass-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .pass-speaker { background: #f3e8ff; color: #7c3aed; }
        .pass-moderator { background: #dbeafe; color: #2563eb; }
        .pass-backdoor { background: #ffedd5; color: #ea580c; }
        .pass-attendee { background: #f3f4f6; color: #374151; }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .ticket { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="ticket-header">
            <h1>{{ $event->title }}</h1>
            <p>{{ $event->start_date->format('M d, Y') }} @if($event->end_date && $event->end_date->ne($event->start_date)) — {{ $event->end_date->format('M d, Y') }}@endif</p>
        </div>
        <div class="ticket-body">
            <div class="ticket-number">
                <small>Ticket Number</small>
                <span>{{ $attendance->ticket_number }}</span>
            </div>

            <div class="info-row">
                <div><div class="info-label">Name</div><div class="info-value">{{ $attendance->full_name }}</div></div>
                <div><div class="info-label">Pass</div><div class="info-value"><span class="pass-badge pass-{{ $attendance->pass_type ?? 'attendee' }}">{{ ucfirst($attendance->pass_type ?? 'attendee') }}</span></div></div>
            </div>
            <div class="info-row">
                <div><div class="info-label">Organization</div><div class="info-value">{{ $organization->name }}</div></div>
                <div><div class="info-label">Status</div><div class="info-value" style="color:#16a34a;">Confirmed</div></div>
            </div>

            <div class="qr-section">
                {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(160)->generate($attendance->qr_token) !!}
                <div class="manual-code">{{ $attendance->manual_code }}</div>
            </div>
        </div>
        <div class="ticket-footer">
            Present this ticket at the registration desk for check-in &middot; {{ config('app.name') }}
        </div>
    </div>

    <div class="no-print" style="position:fixed;top:16px;right:16px;display:flex;gap:8px;">
        <button onclick="window.print()" style="padding:8px 16px;background:#2563eb;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;">Print Ticket</button>
        <button onclick="window.close()" style="padding:8px 16px;background:#6b7280;color:#fff;border:none;border-radius:8px;cursor:pointer;font-weight:600;">Close</button>
    </div>
</body>
</html>
