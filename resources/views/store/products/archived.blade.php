@extends('store.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Deleted Products</h1>
        <a href="{{ route('store.products.index') }}" class="btn btn-secondary">
            &larr; Back to Products
        </a>
    </div>

    @if($products->isEmpty())
        <div class="alert alert-info">No deleted records found.</div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Cost Price</th>
                        <th>Selling Price</th>
                        <th>Deleted At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ $product->category ?? '—' }}</td>
                            <td>{{ $product->unit }}</td>
                            <td>{{ number_format($product->cost_price, 2) }}</td>
                            <td>{{ number_format($product->selling_price, 2) }}</td>
                            <td>{{ $product->deleted_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <form action="{{ route('store.products.restore', $product) }}" method="POST" class="d-inline">
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
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
