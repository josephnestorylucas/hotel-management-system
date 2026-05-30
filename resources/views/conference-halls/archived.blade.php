@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Archived Conference Halls</h1>
        <a href="{{ route('conference-halls.index') }}" class="btn btn-secondary">Back to Conference Halls</a>
    </div>

    @if($records->isEmpty())
        <div class="alert alert-info">No archived records found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Capacity</th>
                        <th>Location</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $hall)
                        <tr>
                            <td>{{ $hall->name }}</td>
                            <td>{{ $hall->capacity }}</td>
                            <td>{{ $hall->building->name ?? 'N/A' }}</td>
                            <td>{{ $hall->deleted_at }}</td>
                            <td>
                                <form action="{{ route('conference-halls.restore', $hall) }}" method="POST">
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
