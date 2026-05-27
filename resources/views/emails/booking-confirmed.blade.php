@extends('emails.layout')

@section('content')
<p>Dear <strong>{{ $booking['guest_name'] }}</strong>,</p>
<p>Your booking at <strong>Grand Hotel</strong> has been confirmed. Here are your booking details:</p>

<div class="highlight">
    <strong>Booking Reference: {{ $booking['reference'] }}</strong>
</div>

<table class="details">
    <tr><th>Room</th><td>{{ $booking['room_number'] }} &mdash; {{ $booking['room_type'] }}</td></tr>
    <tr><th>Check-in</th><td>{{ $booking['check_in'] }}</td></tr>
    <tr><th>Check-out</th><td>{{ $booking['check_out'] }}</td></tr>
    <tr><th>Nights</th><td>{{ $booking['nights'] }}</td></tr>
    <tr><th>Rate per Night</th><td>@currency($booking['rate'] ?? 0, 'TZS')</td></tr>
    <tr><th>Total</th><td><strong>@currency($booking['total'] ?? 0, 'TZS')</strong></td></tr>
    @if(!empty($booking['discount']))
    <tr><th>Discount Applied</th><td style="color: #dc2626;">- @currency($booking['discount'], 'TZS')</td></tr>
    @endif
</table>

<p>We look forward to welcoming you. If you need to make any changes, please contact our front desk.</p>
@endsection
