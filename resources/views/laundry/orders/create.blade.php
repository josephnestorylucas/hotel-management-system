{{-- resources/views/laundry/orders/create.blade.php --}}
@extends('layouts.app')

@section('title', __('laundry.new_laundry_order'))
@section('page-title', __('laundry.title'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
            <h2 class="text-xl font-extrabold text-secondary">{{ __('laundry.new_laundry_order') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('laundry.create_subtitle') }}</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('laundry.orders.store') }}" class="p-6" x-data="laundryForm()">
            @csrf

            <div class="space-y-8">
                {{-- ── Step 1: Customer Details ── --}}
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        {{ __('laundry.sections.customer_details') }}
                    </h3>

                    <!-- Customer Type Toggle -->
                    <div class="flex gap-4 mb-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="customer_type" value="guest"
                                   {{ old('customer_type', 'guest') === 'guest' ? 'checked' : '' }}
                                   x-model="customerType" class="text-primary focus:ring-primary">
                            <span class="text-sm font-semibold text-secondary">{{ __('laundry.customer_type.guest') }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="customer_type" value="walkin"
                                   {{ old('customer_type') === 'walkin' ? 'checked' : '' }}
                                   x-model="customerType" class="text-primary focus:ring-primary">
                            <span class="text-sm font-semibold text-secondary">{{ __('laundry.customer_type.walkin') }}</span>
                        </label>
                    </div>

                    {{-- Guest fields --}}
                    <div x-show="customerType === 'guest'" x-transition class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('laundry.fields.booking') }} <span class="text-red-500">*</span></label>
                                <select name="booking_id" id="booking-select" x-model="selectedBooking"
                                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                                    <option value="">{{ __('laundry.placeholders.select_booking') }}</option>
                                    @foreach($bookings as $b)
                                    <option value="{{ $b->id }}"
                                            data-room="{{ $b->room->room_number ?? '' }}"
                                            {{ old('booking_id') == $b->id ? 'selected' : '' }}>
                                        {{ $b->guest->full_name ?? 'Guest' }} — Room {{ $b->room->room_number ?? 'N/A' }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('booking_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('laundry.fields.room_number') }}</label>
                                <input type="text" name="room_number" id="room-number-input"
                                       x-model="roomNumber"
                                       placeholder="{{ __('laundry.placeholders.auto_filled') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-sm" readonly>
                            </div>
                        </div>
                    </div>

                    {{-- Walk-in fields --}}
                    <div x-show="customerType === 'walkin'" x-transition class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('laundry.fields.customer_name') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                                       placeholder="{{ __('laundry.placeholders.customer_name') }}">
                                @error('customer_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('laundry.fields.customer_phone') }}</label>
                                <input type="tel" name="customer_phone" value="{{ old('customer_phone') }}"
                                       placeholder="{{ __('laundry.placeholders.phone') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-semibold text-secondary mb-2">{{ __('laundry.fields.special_instructions') }}</label>
                        <textarea name="special_instructions" rows="2"
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                                  placeholder="{{ __('laundry.placeholders.instructions') }}">{{ old('special_instructions') }}</textarea>
                    </div>
                </div>

                {{-- ── Step 2: Items ── --}}
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-50 to-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        {{ __('laundry.sections.laundry_items') }}
                    </h3>

                    <div class="flex justify-end mb-4">
                        <button type="button" onclick="addRow()"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-600 text-sm font-semibold rounded-xl hover:bg-green-100 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            {{ __('laundry.actions.add_item') }}
                        </button>
                    </div>

                    <div id="items-container" class="space-y-4"></div>

                    <div class="mt-4 pt-4 border-t border-gray-100 flex justify-end">
                        <div class="bg-blue-50 border border-blue-200 rounded-xl px-6 py-3">
                            <p class="text-sm text-gray-600">
                                {{ __('laundry.info.estimated_total') }}:
                                <span id="order-total" class="text-2xl font-extrabold text-primary ml-2">0</span>
                                <span class="text-sm text-gray-400 ml-1">TZS</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ── Actions ── --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                    <a href="{{ route('laundry.orders.index') }}" 
                       class="px-6 py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-all">
                        {{ __('laundry.actions.cancel') }}
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-primary to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                        {{ __('laundry.actions.create_order') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const services = @json($services);
let rowIndex   = 0;

// Translation strings for JavaScript
const translations = {
    serviceItem: @json(__('laundry.fields.service_item')),
    quantity: @json(__('laundry.fields.quantity')),
    notes: @json(__('laundry.fields.notes')),
    selectService: @json(__('laundry.placeholders.select_service')),
    itemNotes: @json(__('laundry.placeholders.item_notes'))
};

function laundryForm() {
    return {
        customerType: '{{ old("customer_type", "guest") }}',
        selectedBooking: '{{ old("booking_id", "") }}',
        roomNumber: '',
        
        init() {
            this.$watch('selectedBooking', (val) => {
                const sel = document.getElementById('booking-select');
                const opt = sel?.selectedOptions[0];
                this.roomNumber = opt?.dataset?.room || '';
            });
            // Auto-fill on page load
            setTimeout(() => {
                const sel = document.getElementById('booking-select');
                const opt = sel?.selectedOptions[0];
                if (opt?.dataset?.room) {
                    this.roomNumber = opt.dataset.room;
                }
            }, 100);
        }
    }
}

function addRow() {
    const container = document.getElementById('items-container');
    const row       = document.createElement('div');
    row.id          = `row-${rowIndex}`;
    row.className   = 'grid grid-cols-12 gap-3 items-end p-4 border border-gray-200 rounded-xl bg-gray-50';

    let options = `<option value="">${translations.selectService}</option>`;
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
        <div class="col-span-5">
            <label class="block text-sm font-semibold text-secondary mb-2">${translations.serviceItem}</label>
            <select name="items[${rowIndex}][service_item_id]"
                    onchange="recalculate()"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all" required>
                ${options}
            </select>
        </div>
        <div class="col-span-2">
            <label class="block text-sm font-semibold text-secondary mb-2">${translations.quantity}</label>
            <input type="number" name="items[${rowIndex}][quantity]"
                   value="1" min="1" onchange="recalculate()" oninput="recalculate()"
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all" required>
        </div>
        <div class="col-span-4">
            <label class="block text-sm font-semibold text-secondary mb-2">${translations.notes}</label>
            <input type="text" name="items[${rowIndex}][notes]"
                   placeholder="${translations.itemNotes}"
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all">
        </div>
        <div class="col-span-1 flex justify-end pb-1">
            <button type="button" onclick="removeRow('row-${rowIndex}')"
                    class="w-8 h-8 flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
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
</script>
@endpush
@endsection
