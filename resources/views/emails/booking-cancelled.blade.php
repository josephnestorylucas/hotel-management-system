@extends('emails.layout')

@section('content')
<p>Dear <strong>{{ $booking['guest_name'] }}</strong>,</p>

<p>We regret to inform you that your booking at <strong>Grand Hotel</strong> has been <strong style="color: #dc2626;">cancelled</strong>.</p>

<div class="highlight">
    <strong>Booking Reference: {{ $booking['reference'] }}</strong>
</div>

<table class="details">
    <tr><th>Room</th><td>{{ $booking['room_number'] ?? 'N/A' }}</td></tr>
    <tr><th>Check-in</th><td>{{ $booking['check_in'] }}</td></tr>
    <tr><th>Check-out</th><td>{{ $booking['check_out'] }}</td></tr>
    @if(!empty($booking['cancellation_reason']))
    <tr><th>Reason</th><td>{{ $booking['cancellation_reason'] }}</td></tr>
    @endif
</table>

<p>If you have any questions or would like to rebook, please don't hesitate to contact our front desk.</p>
<p>We hope to welcome you in the future.</p>
@endsection
