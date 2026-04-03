<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $checkout->receipt_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; padding: 20px; max-width: 700px; margin: auto; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        .hotel-name { font-size: 22px; font-weight: bold; text-align: center; margin-bottom: 4px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 12px; margin-bottom: 16px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 16px; }
        .meta-block p { margin-bottom: 3px; }
        .label { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #f5f5f5; border-bottom: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        td { padding: 5px 8px; border-bottom: 1px solid #f0f0f0; }
        .text-right { text-align: right; }
        .section-header { background: #eee; font-weight: bold; padding: 6px 8px; }
        .totals { margin-left: auto; width: 280px; }
        .totals td { padding: 4px 8px; }
        .grand-total { font-weight: bold; font-size: 14px; border-top: 2px solid #333; }
        .footer { text-align: center; margin-top: 24px; border-top: 1px solid #ddd; padding-top: 12px; color: #666; }
        .payment-badge { background: #e6f4ea; color: #2e7d32; padding: 3px 8px; border-radius: 12px; font-size: 11px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
@php
    use App\Helpers\CurrencyHelper;
@endphp

<div class="header">
    <div class="hotel-name">HOTEL NAME</div>
    <p>123 Hotel Street, Dar es Salaam, Tanzania</p>
    <p>Tel: +255 XXX XXX XXX | info@hotel.com</p>
    <h1 style="margin-top: 10px;">OFFICIAL RECEIPT</h1>
    <p style="font-size: 14px; font-weight: bold;">{{ $checkout->receipt_number }}</p>
</div>

<div class="meta">
    <div class="meta-block">
        <p><span class="label">Guest:</span> {{ $checkout->booking->guest_name ?? '—' }}</p>
        <p><span class="label">Room:</span> {{ $checkout->booking->room->room_number ?? '—' }}</p>
        <p><span class="label">Check-in:</span> {{ $checkout->booking->check_in_date?->format('d M Y') ?? '—' }}</p>
        <p><span class="label">Check-out:</span> {{ $checkout->completed_at?->format('d M Y') ?? now()->format('d M Y') }}</p>
    </div>
    <div class="meta-block" style="text-align: right;">
        <p><span class="label">Receipt No:</span> <strong>{{ $checkout->receipt_number }}</strong></p>
        <p><span class="label">Date:</span> {{ $checkout->completed_at?->format('d M Y H:i') }}</p>
        <p><span class="label">Cashier:</span> {{ $checkout->completer?->name ?? '—' }}</p>
        <p><span class="label">Rate:</span> 1 {{ CurrencyHelper::getCurrencySymbol('USD') }} = {{ number_format($exchangeRate, 0) }} {{ CurrencyHelper::getCurrencySymbol('TZS') }}</p>
    </div>
</div>

{{-- Charges grouped by type --}}
<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Description</th>
            <th class="text-right">{{ CurrencyHelper::getCurrencySymbol('USD') }}</th>
            <th class="text-right">{{ CurrencyHelper::getCurrencySymbol('TZS') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($chargesByType as $type => $items)
        <tr>
            <td colspan="4" class="section-header">
                {{ $items->first()->charge_type_label }}
            </td>
        </tr>
        @foreach($items as $charge)
        <tr>
            <td>{{ $charge->created_at->format('d M') }}</td>
            <td>{{ $charge->description }}</td>
            <td class="text-right">{{ CurrencyHelper::formatCurrency($charge->amount, 'USD', false) }}</td>
            <td class="text-right">{{ CurrencyHelper::formatCurrency($charge->amount * $exchangeRate, 'TZS', false) }}</td>
        </tr>
        @endforeach
        @endforeach
    </tbody>
</table>

{{-- Totals --}}
<table class="totals">
    <tr>
        <td class="label">Total Charges:</td>
        <td class="text-right">{{ CurrencyHelper::formatUSD($checkout->total_charges_usd) }}</td>
    </tr>
    @if($checkout->discount_usd > 0)
    <tr>
        <td class="label">Discount:</td>
        <td class="text-right" style="color: red;">- {{ CurrencyHelper::formatUSD($checkout->discount_usd) }}</td>
    </tr>
    @endif
    <tr class="grand-total">
        <td>GRAND TOTAL:</td>
        <td class="text-right">{{ CurrencyHelper::formatUSD($checkout->grand_total_usd) }}</td>
    </tr>
    <tr>
        <td class="label">In {{ CurrencyHelper::getCurrencySymbol('TZS') }}:</td>
        <td class="text-right"><strong>{{ CurrencyHelper::formatTZS($checkout->grand_total_tzs) }}</strong></td>
    </tr>
    <tr style="height: 8px;"></tr>
    <tr>
        <td class="label">Payment Method:</td>
        <td class="text-right">
            <span class="payment-badge">{{ ucwords(str_replace('_', ' ', $checkout->payment_method)) }}</span>
        </td>
    </tr>
    <tr>
        <td class="label">Amount Paid:</td>
        <td class="text-right">{{ CurrencyHelper::formatUSD($checkout->total_paid_usd) }}</td>
    </tr>
    @if($checkout->change_due_usd > 0)
    <tr>
        <td class="label">Change:</td>
        <td class="text-right">{{ CurrencyHelper::formatUSD($checkout->change_due_usd) }}</td>
    </tr>
    @endif
</table>

<div class="footer">
    <p><strong>Thank you for staying with us!</strong></p>
    <p style="margin-top: 4px;">We hope to see you again. Safe travels.</p>
    <p style="margin-top: 8px; font-size: 10px; color: #999;">
        This is an official receipt. For queries contact: info@hotel.com
    </p>
</div>

<div class="no-print" style="margin-top: 20px; text-align: center;">
    <button onclick="window.print()"
            style="background: #2563eb; color: white; padding: 10px 24px; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
        Print Receipt
    </button>
</div>

</body>
</html>
