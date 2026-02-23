@extends('emails.layout')

@section('content')
<p>Dear Guest,</p>

<p>Your laundry order is <strong style="color: #16a34a;">ready</strong>!</p>

<div class="highlight">
    <strong>Order Number: {{ $data['order_number'] }}</strong>
</div>

<table class="details">
    <tr><th>Order Number</th><td>{{ $data['order_number'] }}</td></tr>
    @if(!empty($data['room_number']))
    <tr><th>Room</th><td>{{ $data['room_number'] }}</td></tr>
    <tr><th>Delivery</th><td>Will be delivered to your room shortly</td></tr>
    @else
    <tr><th>Collection</th><td>Ready for pickup at our laundry counter</td></tr>
    @endif
    @if(!empty($data['total']))
    <tr><th>Total</th><td>TZS {{ number_format($data['total'], 2) }}</td></tr>
    @endif
</table>

<p>Thank you for using our laundry service!</p>
@endsection
