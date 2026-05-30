@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Deleted Menu Items</h1>
        <a href="{{ route('restaurant.menu.index') }}" class="btn btn-secondary">Back to Menu</a>
    </div>

    @if($records->isEmpty())
        <div class="alert alert-info">No deleted records found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Selling Price</th>
                        <th>Is Available</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($records as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->category->name ?? 'N/A' }}</td>
                            <td>{{ number_format($item->selling_price, 2) }}</td>
                            <td>{{ $item->is_available ? 'Yes' : 'No' }}</td>
                            <td>{{ $item->deleted_at }}</td>
                            <td>
                                <form action="{{ route('restaurant.menu.restore', $item) }}" method="POST">
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
