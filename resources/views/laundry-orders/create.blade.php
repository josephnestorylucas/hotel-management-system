{{-- resources/views/laundry-orders/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Laundry Order - MRK Hotel')
@section('page-title', 'Create Laundry Order')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('laundry.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Laundry Orders
        </a>
    </div>

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    <form action="{{ route('laundry.store') }}" method="POST" id="laundryOrderForm">
        @csrf

        <div class="space-y-6">
            {{-- Booking Selection --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Guest & Booking</h3>

                <div>
                    <label for="booking_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Select Checked-In Booking <span class="text-red-500">*</span>
                    </label>
                    <select name="booking_id" id="booking_id" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('booking_id') border-red-500 @enderror">
                        <option value="">-- Select a Booking --</option>
                        @foreach($bookings as $booking)
                        <option value="{{ $booking->id }}" {{ old('booking_id') == $booking->id ? 'selected' : '' }}>
                            {{ $booking->booking_number }} &mdash; {{ $booking->guest->first_name ?? '' }} {{ $booking->guest->last_name ?? '' }} &mdash; Room {{ $booking->room->room_number ?? 'N/A' }}
                        </option>
                        @endforeach
                    </select>
                    @error('booking_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                    @if($bookings->isEmpty())
                    <p class="text-sm text-amber-600 mt-2">
                        <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        No checked-in bookings available. Only checked-in guests can request laundry.
                    </p>
                    @endif
                </div>
            </div>

            {{-- Laundry Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Laundry Items</h3>
                    <button type="button" onclick="addItemRow()" class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-50 text-blue-600 text-sm font-medium rounded-lg hover:bg-blue-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Item
                    </button>
                </div>

                @error('items') <p class="text-red-500 text-xs mb-3">{{ $message }}</p> @enderror

                <div id="itemsContainer" class="space-y-3">
                    {{-- Item rows will be added here by JS --}}
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-600">Estimated Total:</span>
                    <span id="grandTotal" class="text-xl font-bold text-green-600">0</span>
                </div>
            </div>

            {{-- Notes --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Notes</h3>
                <textarea name="notes" rows="3" placeholder="Any special instructions (optional)..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">{{ old('notes') }}</textarea>
            </div>

            {{-- Submit --}}
            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Create Order
                </button>
                <a href="{{ route('laundry.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Laundry items data from server
    const laundryItems = @json($laundryItems);
    let rowIndex = 0;

    function addItemRow(itemId = '', quantity = 1) {
        const container = document.getElementById('itemsContainer');
        const row = document.createElement('div');
        row.className = 'flex items-start gap-3 p-3 bg-gray-50 rounded-lg item-row';
        row.id = 'item-row-' + rowIndex;

        let options = '<option value="">-- Select Item --</option>';
        laundryItems.forEach(item => {
            const selected = item.id === itemId ? 'selected' : '';
            options += `<option value="${item.id}" data-price="${item.price}" ${selected}>${item.name} (${Number(item.price).toLocaleString()}/pc)</option>`;
        });

        row.innerHTML = `
            <div class="flex-1">
                <select name="items[${rowIndex}][laundry_item_id]" required onchange="updateRowTotal(${rowIndex})"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    ${options}
                </select>
            </div>
            <div class="w-24">
                <input type="number" name="items[${rowIndex}][quantity]" value="${quantity}" min="1" required onchange="updateRowTotal(${rowIndex})" oninput="updateRowTotal(${rowIndex})"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm text-center"
                    placeholder="Qty">
            </div>
            <div class="w-28 flex items-center">
                <span id="row-total-${rowIndex}" class="text-sm font-bold text-green-600">0</span>
            </div>
            <button type="button" onclick="removeItemRow(${rowIndex})" class="p-2 text-red-400 hover:text-red-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        `;

        container.appendChild(row);
        updateRowTotal(rowIndex);
        rowIndex++;
    }

    function removeItemRow(index) {
        const row = document.getElementById('item-row-' + index);
        if (row) {
            row.remove();
            updateGrandTotal();
        }
    }

    function updateRowTotal(index) {
        const row = document.getElementById('item-row-' + index);
        if (!row) return;

        const select = row.querySelector('select');
        const quantityInput = row.querySelector('input[type="number"]');
        const totalSpan = document.getElementById('row-total-' + index);

        const selectedOption = select.options[select.selectedIndex];
        const price = selectedOption ? parseFloat(selectedOption.dataset.price || 0) : 0;
        const quantity = parseInt(quantityInput.value) || 0;
        const total = price * quantity;

        totalSpan.textContent = total.toLocaleString();
        updateGrandTotal();
    }

    function updateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach((row, i) => {
            const select = row.querySelector('select');
            const quantityInput = row.querySelector('input[type="number"]');
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption ? parseFloat(selectedOption.dataset.price || 0) : 0;
            const quantity = parseInt(quantityInput.value) || 0;
            total += price * quantity;
        });
        document.getElementById('grandTotal').textContent = total.toLocaleString();
    }

    // Initialize with one row
    document.addEventListener('DOMContentLoaded', function() {
        addItemRow();
    });
</script>
@endpush
@endsection
