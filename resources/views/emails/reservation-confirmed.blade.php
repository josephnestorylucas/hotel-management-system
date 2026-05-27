@extends('emails.layout')

@section('content')
<p>Dear <strong>{{ $reservation['guest_name'] }}</strong>,</p>
<p>Thank you for your reservation at <strong>Grand Hotel</strong>. Your reservation has been confirmed. Here are your reservation details:</p>

<div class="highlight">
    <strong>Reservation Reference: {{ $reservation['reference'] }}</strong>
</div>

<table class="details">
    @if(!empty($reservation['room_number']))
    <tr><th>Room</th><td>{{ $reservation['room_number'] }} &mdash; {{ $reservation['room_type'] ?? 'Standard' }}</td></tr>
    @endif
    <tr><th>Check-in</th><td>{{ $reservation['check_in'] }}</td></tr>
    <tr><th>Check-out</th><td>{{ $reservation['check_out'] }}</td></tr>
    <tr><th>Nights</th><td>{{ $reservation['nights'] ?? 1 }}</td></tr>
    <tr><th>Guests</th><td>{{ $reservation['guests'] ?? 1 }}</td></tr>
    @if(!empty($reservation['estimated_total']))
    <tr><th>Estimated Total</th><td><strong>@currency($reservation['estimated_total'], 'TZS')</strong></td></tr>
    @endif
</table>

<p><strong>What's Next?</strong></p>
<ul>
    <li>Please arrive at the hotel on your check-in date with a valid ID</li>
    <li>Check-in time is from 2:00 PM onwards</li>
    <li>Check-out time is by 11:00 AM</li>
</ul>

<p>If you need to make any changes or have special requests, please contact our front desk.</p>

<p>We look forward to welcoming you!</p>
@endsection
