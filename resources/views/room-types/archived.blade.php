@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Archived Room Types</h1>
        <a href="{{ route('room-types.index') }}" class="btn btn-secondary">Back to Room Types</a>
    </div>

    @if($records->isEmpty())
        <div class="alert alert-info">No archived records found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Base Rate</th>
                        <th>Max Occupancy</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $roomType)
                        <tr>
                            <td>{{ $roomType->name }}</td>
                            <td>{{ number_format($roomType->base_rate, 2) }}</td>
                            <td>{{ $roomType->max_occupancy }}</td>
                            <td>{{ $roomType->deleted_at }}</td>
                            <td>
                                <form action="{{ route('room-types.restore', $roomType) }}" method="POST">
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
