@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Deleted Guests</h1>
        <a href="{{ route('guests.index') }}" class="btn btn-secondary">
            &larr; Back to Guests
        </a>
    </div>

    @if($guests->isEmpty())
        <div class="alert alert-info">No deleted records found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Nationality</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guests as $guest)
                        <tr>
                            <td>{{ $guest->first_name }}</td>
                            <td>{{ $guest->last_name }}</td>
                            <td>{{ $guest->email }}</td>
                            <td>{{ $guest->phone_number }}</td>
                            <td>{{ $guest->nationality ?? '—' }}</td>
                            <td>{{ $guest->deleted_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <form action="{{ route('guests.restore', $guest) }}" method="POST" class="d-inline">
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
            {{ $guests->links() }}
        </div>
    @endif
</div>
@endsection
