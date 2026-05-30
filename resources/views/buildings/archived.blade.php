@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Archived Buildings</h1>
        <a href="{{ route('buildings.index') }}" class="btn btn-secondary">Back to Buildings</a>
    </div>

    @if($records->isEmpty())
        <div class="alert alert-info">No archived records found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $building)
                        <tr>
                            <td>{{ $building->name }}</td>
                            <td>{{ $building->address ?? 'N/A' }}</td>
                            <td>{{ $building->deleted_at }}</td>
                            <td>
                                <form action="{{ route('buildings.restore', $building) }}" method="POST">
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
