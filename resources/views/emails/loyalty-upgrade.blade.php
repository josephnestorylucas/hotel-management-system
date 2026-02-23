@extends('emails.layout')

@section('content')
<p>Dear <strong>{{ $data['guest_name'] }}</strong>,</p>

<div class="highlight">
    Congratulations! You have been upgraded to
    <strong>
        @if($data['tier'] === 'Silver') Silver
        @elseif($data['tier'] === 'Gold') Gold
        @else Platinum
        @endif
        Member
    </strong>
</div>

<p>
    You have earned <strong>{{ number_format($data['points']) }} points</strong>
    across your stays and purchases at Grand Hotel.
</p>

<table class="details">
    <tr><th>Your Tier</th><td><strong>{{ $data['tier'] }}</strong></td></tr>
    <tr><th>Total Points</th><td>{{ number_format($data['points']) }}</td></tr>
    <tr><th>Member Since</th><td>{{ $data['member_since'] }}</td></tr>
</table>

<p>As a <strong>{{ $data['tier'] }}</strong> member you now enjoy:</p>
<ul>
    @if($data['tier'] === 'Silver')
        <li>Priority check-in</li>
        <li>5% discount on room rate</li>
        <li>Free laundry &mdash; 3 items per stay</li>
    @elseif($data['tier'] === 'Gold')
        <li>Priority check-in and late checkout</li>
        <li>10% discount on room rate</li>
        <li>Free laundry &mdash; 5 items per stay</li>
        <li>Complimentary breakfast on arrival</li>
    @else
        <li>VIP check-in and guaranteed late checkout</li>
        <li>15% discount on room rate</li>
        <li>Unlimited complimentary laundry</li>
        <li>Daily complimentary breakfast</li>
        <li>Access to exclusive Platinum lounge</li>
    @endif
</ul>

<p>Thank you for choosing Grand Hotel. We look forward to your next visit.</p>
@endsection
