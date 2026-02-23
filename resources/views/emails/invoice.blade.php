@extends('emails.layout')

@section('content')
<p>Dear <strong>{{ $invoice['guest_name'] }}</strong>,</p>

<p>Thank you for staying with us at <strong>Grand Hotel</strong>. Please find your invoice details below.</p>

<div class="highlight">
    <strong>Invoice Reference: {{ $invoice['reference'] }}</strong>
</div>

<table class="details">
    <tr><th>Guest</th><td>{{ $invoice['guest_name'] }}</td></tr>
    <tr><th>Room</th><td>{{ $invoice['room_number'] ?? 'N/A' }}</td></tr>
    <tr><th>Check-in</th><td>{{ $invoice['check_in'] ?? 'N/A' }}</td></tr>
    <tr><th>Check-out</th><td>{{ $invoice['check_out'] ?? 'N/A' }}</td></tr>
</table>

@if(!empty($invoice['charges']))
<h3 style="margin-top: 24px; font-size: 15px;">Charges Breakdown</h3>
<table class="details">
    <thead>
        <tr>
            <th>Description</th>
            <th style="text-align: right;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice['charges'] as $charge)
        <tr>
            <td>{{ $charge['description'] }}</td>
            <td style="text-align: right;">TZS {{ number_format($charge['amount'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <td style="text-align: right;"><strong>TZS {{ number_format($invoice['total'] ?? 0, 2) }}</strong></td>
        </tr>
        @if(isset($invoice['paid']))
        <tr>
            <th>Paid</th>
            <td style="text-align: right;">TZS {{ number_format($invoice['paid'], 2) }}</td>
        </tr>
        @endif
        @if(isset($invoice['balance']))
        <tr>
            <th>Balance Due</th>
            <td style="text-align: right;"><strong style="color: {{ $invoice['balance'] > 0 ? '#dc2626' : '#16a34a' }};">TZS {{ number_format($invoice['balance'], 2) }}</strong></td>
        </tr>
        @endif
    </tfoot>
</table>
@endif

<p>A PDF copy of this invoice is attached to this email for your records.</p>
<p>Thank you for choosing Grand Hotel. We hope to see you again!</p>
@endsection
