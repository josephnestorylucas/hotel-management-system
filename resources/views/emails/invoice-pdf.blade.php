<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 20px; }
        .invoice-header { text-align: center; border-bottom: 2px solid #1e3a5f; padding-bottom: 20px; margin-bottom: 20px; }
        .invoice-header h1 { color: #1e3a5f; margin: 0; font-size: 24px; }
        .invoice-header p { margin: 4px 0; font-size: 12px; color: #666; }
        .details-grid { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .details-grid div { font-size: 12px; }
        .details-grid strong { display: block; font-size: 11px; color: #999; text-transform: uppercase; margin-bottom: 4px; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th { background: #1e3a5f; color: #fff; text-align: left; padding: 8px 12px; font-size: 12px; }
        td { padding: 8px 12px; font-size: 12px; border-bottom: 1px solid #eee; }
        .total-row td { font-weight: bold; border-top: 2px solid #1e3a5f; }
        .footer { text-align: center; margin-top: 30px; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 15px; }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h1>Grand Hotel</h1>
        <p>P.O. Box 000, Dar es Salaam, Tanzania</p>
        <p>Phone: +255 xxx xxx xxx | Email: info@grandhotel.co.tz</p>
        <p style="margin-top: 10px; font-size: 16px; font-weight: bold;">INVOICE</p>
    </div>

    <table style="margin-bottom: 20px;">
        <tr>
            <td style="border: none; width: 50%;">
                <strong style="font-size: 11px; color: #999;">BILL TO</strong><br>
                {{ $invoice['guest_name'] }}<br>
                @if(!empty($invoice['guest_email'])){{ $invoice['guest_email'] }}<br>@endif
                @if(!empty($invoice['guest_phone'])){{ $invoice['guest_phone'] }}@endif
            </td>
            <td style="border: none; width: 50%; text-align: right;">
                <strong style="font-size: 11px; color: #999;">INVOICE DETAILS</strong><br>
                Reference: {{ $invoice['reference'] }}<br>
                Date: {{ $invoice['date'] ?? now()->format('d M Y') }}<br>
                Room: {{ $invoice['room_number'] ?? 'N/A' }}
            </td>
        </tr>
    </table>

    @if(!empty($invoice['charges']))
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th style="text-align: right;">Amount (TZS)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice['charges'] as $i => $charge)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $charge['description'] }}</td>
                <td style="text-align: right;">{{ number_format($charge['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2">Total</td>
                <td style="text-align: right;">TZS {{ number_format($invoice['total'] ?? 0, 2) }}</td>
            </tr>
            @if(isset($invoice['paid']))
            <tr>
                <td colspan="2">Amount Paid</td>
                <td style="text-align: right;">TZS {{ number_format($invoice['paid'], 2) }}</td>
            </tr>
            @endif
            @if(isset($invoice['balance']))
            <tr class="total-row">
                <td colspan="2">Balance Due</td>
                <td style="text-align: right;">TZS {{ number_format($invoice['balance'], 2) }}</td>
            </tr>
            @endif
        </tfoot>
    </table>
    @endif

    <div class="footer">
        <p>Thank you for choosing Grand Hotel!</p>
        <p>This is a computer-generated invoice. No signature required.</p>
    </div>
</body>
</html>
