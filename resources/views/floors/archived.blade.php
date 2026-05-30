@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Deleted Floors</h1>
        <a href="{{ route('floors.index') }}" class="btn btn-secondary">Back to Floors</a>
    </div>

    @if($records->isEmpty())
        <div class="alert alert-info">No deleted records found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Building</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $floor)
                        <tr>
                            <td>{{ $floor->name }}</td>
                            <td>{{ $floor->building->name ?? 'N/A' }}</td>
                            <td>{{ $floor->deleted_at }}</td>
                            <td>
                                <form action="{{ route('floors.restore', $floor) }}" method="POST">
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
