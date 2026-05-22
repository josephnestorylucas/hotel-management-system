{{-- resources/views/conference-participants/badges-all.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>All Passes — {{ $conference->title }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f3f4f6; padding: 20px; }
        .passes-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; max-width: 1100px; margin: 0 auto; }
        .pass-card {
            width: 100%; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            background: white; page-break-inside: avoid;
        }
        .pass-header { padding: 16px 20px; text-align: center; color: white; }
        .pass-header.attendee { background: linear-gradient(135deg, #2563eb, #1d4ed8); }
        .pass-header.speaker { background: linear-gradient(135deg, #7c3aed, #6d28d9); }
        .pass-header.organizer { background: linear-gradient(135deg, #059669, #047857); }
        .pass-type { font-size: 10px; text-transform: uppercase; letter-spacing: 2px; opacity: 0.9; }
        .pass-number { font-size: 28px; font-weight: 800; margin: 4px 0; }
        .pass-conference { font-size: 11px; opacity: 0.85; }
        .pass-body { padding: 16px 20px; text-align: center; }
        .pass-name { font-size: 18px; font-weight: 700; color: #1f2937; margin-bottom: 4px; }
        .pass-email { font-size: 11px; color: #6b7280; margin-bottom: 12px; }
        .qr-code img { width: 100px; height: 100px; border: 2px solid #e5e7eb; border-radius: 6px; margin-bottom: 8px; }
        .access-code { font-size: 18px; font-weight: 800; letter-spacing: 3px; color: #374151; font-family: 'Courier New', monospace; }
        .pass-footer { padding: 8px 20px; background: #f9fafb; border-top: 1px solid #e5e7eb; text-align: center; }
        .pass-footer p { font-size: 10px; color: #9ca3af; }
        .page-title { text-align: center; margin-bottom: 20px; }
        .page-title h1 { font-size: 20px; color: #1f2937; }
        .page-title p { font-size: 13px; color: #6b7280; }
        @media print { body { background: white; padding: 10px; } .pass-card { box-shadow: none; border: 1px solid #e5e7eb; } }
    </style>
</head>
<body>
    <div class="page-title">
        <h1>{{ $conference->title }}</h1>
        <p>{{ $participants->count() }} Confirmed Passes</p>
    </div>
    <div class="passes-grid">
        @foreach($participants as $participant)
        <div class="pass-card">
            <div class="pass-header {{ $participant->pass_type }}">
                <div class="pass-type">{{ $participant->pass_type_label }} Pass</div>
                <div class="pass-number">#{{ $participant->pass_number }}</div>
                <div class="pass-conference">{{ $conference->title }}</div>
            </div>
            <div class="pass-body">
                <div class="pass-name">{{ $participant->name }}</div>
                <div class="pass-email">{{ $participant->email }}</div>
                <div class="qr-code">
                    <img src="{{ $participant->qr_code_url }}" alt="QR">
                </div>
                <div class="access-code">{{ $participant->access_code }}</div>
            </div>
            <div class="pass-footer">
                <p>Scan QR or enter code at entry</p>
            </div>
        </div>
        @endforeach
    </div>
</body>
</html>
