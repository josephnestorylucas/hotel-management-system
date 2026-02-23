@extends('emails.layout')

@section('content')
<p>Dear <strong>{{ $booking['guest_name'] }}</strong>,</p>

<p>This is a friendly reminder that your <strong>check-out</strong> is scheduled for <strong>tomorrow</strong>.</p>

<div class="highlight">
    <strong>Check-out Date: {{ $booking['check_out'] }}</strong>
</div>

<table class="details">
    <tr><th>Room</th><td>{{ $booking['room_number'] ?? 'N/A' }}</td></tr>
    <tr><th>Check-in Date</th><td>{{ $booking['check_in'] }}</td></tr>
    <tr><th>Check-out Date</th><td>{{ $booking['check_out'] }}</td></tr>
    @if(isset($booking['balance']))
    <tr><th>Outstanding Balance</th><td><strong>TZS {{ number_format($booking['balance'], 2) }}</strong></td></tr>
    @endif
</table>

<p>Please ensure all outstanding charges are settled before check-out. Our front desk team is available to assist you.</p>

<p>Thank you for staying with us at Grand Hotel!</p>
@endsection
