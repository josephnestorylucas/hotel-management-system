@extends('emails.layout')

@section('content')
<p>Dear <strong>{{ $order['guest_name'] ?? 'Guest' }}</strong>,</p>

<p>Thank you for dining at <strong>Grand Hotel</strong>. Here is your order receipt:</p>

<div class="highlight">
    <strong>Order #{{ $order['order_number'] }}</strong>
</div>

<table class="details">
    <tr><th>Date</th><td>{{ $order['date'] ?? now()->format('d M Y H:i') }}</td></tr>
    @if(!empty($order['table']))
    <tr><th>Table</th><td>{{ $order['table'] }}</td></tr>
    @endif
    <tr><th>Order Type</th><td>{{ ucfirst($order['type'] ?? 'dine-in') }}</td></tr>
</table>

@if(!empty($order['items']))
<h3 style="margin-top: 24px; font-size: 15px;">Items Ordered</h3>
<table class="details">
    <thead>
        <tr>
            <th>Item</th>
            <th style="text-align: center;">Qty</th>
            <th style="text-align: right;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($order['items'] as $item)
        <tr>
            <td>{{ $item['name'] }}</td>
            <td style="text-align: center;">{{ $item['quantity'] }}</td>
            <td style="text-align: right;">TZS {{ number_format($item['total'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">Total</th>
            <td style="text-align: right;"><strong>TZS {{ number_format($order['total'] ?? 0, 2) }}</strong></td>
        </tr>
    </tfoot>
</table>
@endif

<p>Thank you for dining with us. We hope you enjoyed your meal!</p>
@endsection
