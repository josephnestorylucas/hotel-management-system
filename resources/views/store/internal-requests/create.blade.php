@extends('layouts.app')

@section('title', 'New Internal Request')
@section('page-title', 'New Internal Request')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">New Internal Usage Request</h1>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('store.internal-requests.store') }}">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                    <select name="department" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <option value="">Select department...</option>
                        @foreach(['Housekeeping', 'Laundry', 'Front Office', 'Maintenance', 'Kitchen', 'Bar', 'Other'] as $dept)
                        <option value="{{ $dept }}" {{ old('department') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                    @error('department')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                    <select name="product_id" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <option value="">Select product...</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') === $product->id ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->unit }})
                        </option>
                        @endforeach
                    </select>
                    @error('product_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                    <input type="number" name="quantity" value="{{ old('quantity') }}" step="0.001" min="0.001" required
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('quantity') border-red-400 @enderror">
                    @error('quantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                    <textarea name="reason" rows="3"
                              placeholder="Why do you need this item?"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">{{ old('reason') }}</textarea>
                </div>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mt-4 text-sm text-blue-700">
                Your request will be reviewed by a Supervisor. Once approved, the Store Keeper will fulfill it.
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="bg-primary text-white px-5 py-2 rounded-xl hover:bg-blue-700 text-sm font-medium">
                    Submit Request
                </button>
                <a href="{{ route('store.internal-requests.index') }}"
                   class="bg-gray-200 text-gray-700 px-5 py-2 rounded-xl hover:bg-gray-300 text-sm font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
