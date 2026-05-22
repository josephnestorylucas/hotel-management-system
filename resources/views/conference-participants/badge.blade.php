{{-- resources/views/conference-participants/badge.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pass #{{ $participant->pass_number }} — {{ $participant->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f3f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .pass-card {
            width: 340px; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            background: white; position: relative;
        }
        .pass-header {
            padding: 20px 24px; text-align: center; color: white;
        }
        .pass-header.attendee { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
        .pass-header.speaker { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
        .pass-header.organizer { background: linear-gradient(135deg, #059669, #047857); }
        .pass-type { font-size: 11px; text-transform: uppercase; letter-spacing: 2px; opacity: 0.9; }
        .pass-number { font-size: 36px; font-weight: 800; margin: 8px 0 4px; }
        .pass-conference { font-size: 13px; opacity: 0.85; margin-top: 4px; }
        .pass-body { padding: 24px; text-align: center; }
        .pass-name { font-size: 22px; font-weight: 700; color: #1f2937; margin-bottom: 4px; }
        .pass-email { font-size: 13px; color: #6b7280; margin-bottom: 16px; }
        .qr-code { margin: 0 auto 16px; }
        .qr-code img { width: 140px; height: 140px; border: 2px solid #e5e7eb; border-radius: 8px; }
        .access-code { font-size: 24px; font-weight: 800; letter-spacing: 4px; color: #374151; font-family: 'Courier New', monospace; }
        .access-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-top: 4px; }
        .pass-footer { padding: 12px 24px; background: #f9fafb; border-top: 1px solid #e5e7eb; text-align: center; }
        .pass-footer p { font-size: 11px; color: #9ca3af; }
        .pass-date { font-size: 12px; color: #6b7280; }
        .pass-venue { font-size: 12px; color: #9ca3af; margin-top: 2px; }
        @media print { body { background: white; } .pass-card { box-shadow: none; border: 2px solid #e5e7eb; } }
    </style>
</head>
<body>
    <div class="pass-card">
        <div class="pass-header {{ $participant->pass_type }}">
            <div class="pass-type">{{ $participant->pass_type_label }} Pass</div>
            <div class="pass-number">#{{ $participant->pass_number }}</div>
            <div class="pass-conference">{{ $participant->conference->title }}</div>
        </div>
        <div class="pass-body">
            <div class="pass-name">{{ $participant->name }}</div>
            <div class="pass-email">{{ $participant->email }}</div>
            <div class="qr-code">
                <img src="{{ $participant->qr_code_url }}" alt="QR Code">
            </div>
            <div class="access-code">{{ $participant->access_code }}</div>
            <div class="access-label">Access Code</div>
            <div style="margin-top: 16px;">
                <p class="pass-date">{{ $participant->conference->start_datetime->format('M d, Y') }}</p>
                <p class="pass-venue">{{ $participant->conference->display_venue }}</p>
            </div>
        </div>
        <div class="pass-footer">
            <p>Scan this QR code or enter access code at entry</p>
        </div>
    </div>
</body>
</html>
