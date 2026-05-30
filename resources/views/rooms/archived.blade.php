@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Deleted Rooms</h1>
        <a href="{{ route('rooms.index') }}" class="btn btn-secondary">
            &larr; Back to Rooms
        </a>
    </div>

    @if($rooms->isEmpty())
        <div class="alert alert-info">No deleted records found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Room Number</th>
                        <th>Floor</th>
                        <th>Room Type</th>
                        <th>Status</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rooms as $room)
                        <tr>
                            <td>{{ $room->room_number }}</td>
                            <td>{{ $room->floor?->building?->name ?? '' }} {{ $room->floor?->name ?? '' }}</td>
                            <td>{{ $room->roomType?->name ?? '—' }}</td>
                            <td>{{ $room->status }}</td>
                            <td>{{ $room->deleted_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <form action="{{ route('rooms.restore', $room) }}" method="POST" class="d-inline">
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

        <div class="d-flex justify-content-center">
            {{ $rooms->links() }}
        </div>
    @endif
</div>
@endsection
