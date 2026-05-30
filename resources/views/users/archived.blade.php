@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Deleted Users</h1>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">
            &larr; Back to Users
        </a>
    </div>

    @if($users->isEmpty())
        <div class="alert alert-info">No deleted records found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role?->name ?? '—' }}</td>
                            <td>{{ $user->deleted_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <form action="{{ route('users.restore', $user) }}" method="POST" class="d-inline">
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
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
