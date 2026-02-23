@extends('emails.layout')

@section('content')
<p>Dear <strong>{{ $data['guest_name'] ?? 'Valued Guest' }}</strong>,</p>

@if($data['type'] === 'offer')
<div class="highlight">
    <strong>Special Offer</strong>
</div>
@elseif($data['type'] === 'event')
<div class="highlight">
    <strong>Upcoming Event</strong>
</div>
@else
<div class="highlight">
    <strong>Announcement</strong>
</div>
@endif

<h2 style="color: #1e3a5f; margin-top: 16px;">{{ $data['title'] }}</h2>

<div style="font-size: 14px; line-height: 1.7; margin: 16px 0;">
    {!! nl2br(e($data['body'])) !!}
</div>

<p>For more information or to make a reservation, please contact our front desk.</p>
<p>We look forward to seeing you!</p>
@endsection
