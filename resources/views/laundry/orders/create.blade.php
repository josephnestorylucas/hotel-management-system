@extends('laundry.layout')

@section('title', 'New Laundry Order')

@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">New Laundry Order</h1>

    <form method="POST" action="{{ route('laundry.orders.store') }}">
        @csrf

        {{-- Customer details --}}
        <div class="bg-white rounded shadow p-6 mb-4">
            <h2 class="font-semibold text-gray-700 mb-4">Customer Details</h2>

            <div class="flex gap-4 mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="customer_type" value="guest"
                           {{ old('customer_type', 'guest') === 'guest' ? 'checked' : '' }}
                           onchange="toggleCustomerType('guest')">
                    <span class="text-sm font-medium text-gray-700">Hotel Guest</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="customer_type" value="walkin"
                           {{ old('customer_type') === 'walkin' ? 'checked' : '' }}
                           onchange="toggleCustomerType('walkin')">
                    <span class="text-sm font-medium text-gray-700">Walk-in Customer</span>
                </label>
            </div>

            {{-- Guest fields --}}
            <div id="guest-fields" class="{{ old('customer_type') === 'walkin' ? 'hidden' : '' }}">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Booking *</label>
                        <select name="booking_id"
                                id="booking-select"
                                onchange="updateRoomNumber()"
                                class="w-full border rounded px-3 py-2 text-sm @error('booking_id') border-red-400 @enderror">
                            <option value="">-- Select checked-in guest --</option>
                            @foreach($bookings as $b)
                            <option value="{{ $b->id }}"
                                    data-room="{{ $b->room->room_number ?? '' }}"
                                    {{ old('booking_id') === $b->id ? 'selected' : '' }}>
                                {{ $b->guest->full_name ?? 'Guest' }} — Room {{ $b->room->room_number ?? 'N/A' }}
                            </option>
                            @endforeach
                        </select>
                        @error('booking_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Room Number</label>
                        <input type="text" name="room_number" id="room-number-input"
                               value="{{ old('room_number') }}"
                               placeholder="Auto-filled from booking"
                               class="w-full border rounded px-3 py-2 text-sm bg-gray-50" readonly>
                    </div>
                </div>
            </div>

            {{-- Walk-in fields --}}
            <div id="walkin-fields" class="{{ old('customer_type') !== 'walkin' ? 'hidden' : '' }}">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name *</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                               class="w-full border rounded px-3 py-2 text-sm @error('customer_name') border-red-400 @enderror">
                        @error('customer_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}"
                               placeholder="e.g. 0712 345 678"
                               class="w-full border rounded px-3 py-2 text-sm">
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Special Instructions</label>
                <textarea name="special_instructions" rows="2"
                          placeholder="Stains, delicate items, handle with care..."
                          class="w-full border rounded px-3 py-2 text-sm">{{ old('special_instructions') }}</textarea>
            </div>
        </div>

        {{-- Items --}}
        <div class="bg-white rounded shadow p-6 mb-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="font-semibold text-gray-700">Items</h2>
                <button type="button" onclick="addRow()"
                        class="text-sm bg-blue-50 text-blue-600 px-3 py-1.5 rounded hover:bg-blue-100">
                    + Add Item
                </button>
            </div>

            <div id="items-container" class="space-y-3"></div>

            <div class="mt-4 pt-4 border-t flex justify-end">
                <p class="text-sm text-gray-600 font-semibold">
                    Estimated Total:
                    <span id="order-total" class="text-blue-600 text-lg ml-2">0</span>
                    <span class="text-gray-400 text-xs ml-1">TZS</span>
                </p>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 text-sm font-medium">
                Create Order
            </button>
            <a href="{{ route('laundry.orders.index') }}"
               class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300 text-sm">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
const services = @json($services);
let rowIndex   = 0;

function toggleCustomerType(type) {
    document.getElementById('guest-fields').classList.toggle('hidden', type === 'walkin');
    document.getElementById('walkin-fields').classList.toggle('hidden', type === 'guest');
}

function updateRoomNumber() {
    const sel = document.getElementById('booking-select');
    const opt = sel.selectedOptions[0];
    document.getElementById('room-number-input').value = opt?.dataset?.room || '';
}

function addRow() {
    const container = document.getElementById('items-container');
    const row       = document.createElement('div');
    row.id          = `row-${rowIndex}`;
    row.className   = 'grid grid-cols-12 gap-2 items-end p-3 border rounded bg-gray-50';

    let options = '<option value="">Select service & item...</option>';
    services.forEach(service => {
        options += `<optgroup label="${service.name} (${service.turnaround_hours}h)">`;
        service.service_items.forEach(item => {
            options += `<option value="${item.id}" data-price="${item.price}">
                ${item.item_name} — ${parseFloat(item.price).toLocaleString()} TZS
            </option>`;
        });
        options += '</optgroup>';
    });

    row.innerHTML = `
        <div class="col-span-6">
            <label class="block text-xs text-gray-500 mb-1">Service & Item</label>
            <select name="items[${rowIndex}][service_item_id]"
                    onchange="recalculate()"
                    class="w-full border rounded px-2 py-1.5 text-sm" required>
                ${options}
            </select>
        </div>
        <div class="col-span-2">
            <label class="block text-xs text-gray-500 mb-1">Quantity</label>
            <input type="number" name="items[${rowIndex}][quantity]"
                   value="1" min="1" onchange="recalculate()" oninput="recalculate()"
                   class="w-full border rounded px-2 py-1.5 text-sm" required>
        </div>
        <div class="col-span-3">
            <label class="block text-xs text-gray-500 mb-1">Notes</label>
            <input type="text" name="items[${rowIndex}][notes]"
                   placeholder="Stain, delicate..."
                   class="w-full border rounded px-2 py-1.5 text-sm">
        </div>
        <div class="col-span-1 flex justify-end">
            <button type="button" onclick="removeRow('row-${rowIndex}')"
                    class="text-red-400 hover:text-red-600 text-xl font-bold leading-none">×</button>
        </div>
    `;

    container.appendChild(row);
    rowIndex++;
    recalculate();
}

function removeRow(id) {
    document.getElementById(id)?.remove();
    recalculate();
}

function recalculate() {
    let total = 0;
    document.querySelectorAll('#items-container > div').forEach(row => {
        const select = row.querySelector('select');
        const qty    = row.querySelector('input[type=number]');
        if (select?.value && qty) {
            const price = parseFloat(select.selectedOptions[0]?.dataset?.price || 0);
            total += price * parseInt(qty.value || 1);
        }
    });
    document.getElementById('order-total').textContent =
        total.toLocaleString('en-US', { minimumFractionDigits: 0 });
}

// Start with one row
addRow();

// Auto-fill room on page load if booking pre-selected
updateRoomNumber();
</script>
@endsection
