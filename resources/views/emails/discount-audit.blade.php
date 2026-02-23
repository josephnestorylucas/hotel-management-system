@extends('emails.layout')

@section('content')
<p>Dear Manager,</p>

<p>A discount has been applied to a booking. Please review the details below:</p>

<div class="highlight">
    <strong>Discount Audit Notice</strong>
</div>

<table class="details">
    <tr><th>Authorized By</th><td>{{ $data['authorized_by'] }}</td></tr>
    <tr><th>Booking ID</th><td style="font-family: monospace;">{{ $data['booking_id'] }}</td></tr>
    <tr><th>Discount Amount</th><td><strong style="color: #dc2626;">TZS {{ number_format($data['discount_amount'], 2) }}</strong></td></tr>
    <tr><th>Valid Days</th><td>{{ $data['valid_days'] }} days</td></tr>
    <tr><th>Reason</th><td>{{ $data['reason'] }}</td></tr>
    <tr><th>Authorized At</th><td>{{ $data['authorized_at'] }}</td></tr>
</table>

<p>If this discount was not authorized by you, please contact the supervisor immediately.</p>
@endsection
