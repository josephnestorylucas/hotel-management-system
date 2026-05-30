@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Archived Conference Bookings</h1>
        <a href="{{ route('conference-bookings.index') }}" class="btn btn-secondary">Back to Conference Bookings</a>
    </div>

    @if($records->isEmpty())
        <div class="alert alert-info">No archived records found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Hall</th>
                        <th>Guest Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $booking)
                        <tr>
                            <td>{{ $booking->conferenceHall->name ?? 'N/A' }}</td>
                            <td>{{ $booking->guest->full_name ?? $booking->guest->name ?? 'N/A' }}</td>
                            <td>{{ $booking->booking_date }} {{ $booking->start_time }}</td>
                            <td>{{ $booking->booking_date }} {{ $booking->end_time }}</td>
                            <td>{{ ucfirst($booking->status) }}</td>
                            <td>{{ $booking->deleted_at }}</td>
                            <td>
                                <form action="{{ route('conference-bookings.restore', $booking) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success">Restore</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $records->links() }}
    @endif
</div>
@endsection
