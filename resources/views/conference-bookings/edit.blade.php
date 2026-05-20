{{-- resources/views/conference-bookings/edit.blade.php --}}
@php use App\Helpers\CurrencyHelper; @endphp
@extends('layouts.app')

@section('title', 'Edit Hall Booking')
@section('page-title', 'Conference Hall Bookings')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Edit Hall Booking</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $conferenceBooking->booking_number }}</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('conference-bookings.update', $conferenceBooking) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Institution Selection -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Institution Information
                    </h3>

                    <div>
                        <label for="institution_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Select Institution <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="institution_id"
                            id="institution_id"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('institution_id') border-red-500 @enderror">
                            <option value="">Select an institution</option>
                            @foreach($institutions as $institution)
                            <option value="{{ $institution->id }}"
                                    data-contact="{{ $institution->contact_person }}"
                                    data-phone="{{ $institution->phone }}"
                                    {{ old('institution_id', $conferenceBooking->institution_id) == $institution->id ? 'selected' : '' }}>
                                {{ $institution->name }} — {{ $institution->contact_person }}
                            </option>
                            @endforeach
                        </select>
                        @error('institution_id')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Hall & Schedule -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Booking Details
                    </h3>

                    <!-- Hall Selection -->
                    <div class="mb-4">
                        <label for="conference_hall_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Conference Hall <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="conference_hall_id"
                            id="conference_hall_id"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('conference_hall_id') border-red-500 @enderror">
                            <option value="">Select a hall</option>
                            @foreach($halls as $hall)
                            <option value="{{ $hall->id }}"
                                    data-rate="{{ $hall->hourly_rate }}"
                                    {{ old('conference_hall_id', $conferenceBooking->conference_hall_id) == $hall->id ? 'selected' : '' }}>
                                {{ $hall->name }} - {{ $hall->location }} (Capacity: {{ $hall->capacity }}, {{ $hall->formatted_rate }}/hr)
                            </option>
                            @endforeach
                        </select>
                        @error('conference_hall_id')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div class="mb-4">
                        <label for="booking_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Booking Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            name="booking_date"
                            id="booking_date"
                            value="{{ old('booking_date', $conferenceBooking->booking_date->format('Y-m-d')) }}"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('booking_date') border-red-500 @enderror">
                        @error('booking_date')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Time Range -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Start Time <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="time"
                                name="start_time"
                                id="start_time"
                                value="{{ old('start_time', \Carbon\Carbon::parse($conferenceBooking->start_time)->format('H:i')) }}"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('start_time') border-red-500 @enderror">
                            @error('start_time')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                End Time <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="time"
                                name="end_time"
                                id="end_time"
                                value="{{ old('end_time', \Carbon\Carbon::parse($conferenceBooking->end_time)->format('H:i')) }}"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('end_time') border-red-500 @enderror">
                            @error('end_time')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="status"
                            id="status"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('status') border-red-500 @enderror">
                            @foreach(['pending', 'confirmed', 'cancelled', 'completed'] as $statusOption)
                            <option value="{{ $statusOption }}" {{ old('status', $conferenceBooking->status) === $statusOption ? 'selected' : '' }}>
                                {{ ucfirst($statusOption) }}
                            </option>
                            @endforeach
                        </select>
                        @error('status')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Cost Preview -->
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Estimated Cost:</span>
                            <span id="cost-preview" class="text-lg font-bold text-primary">{{ CurrencyHelper::formatCurrency($conferenceBooking->total_cost) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Based on hourly rate and duration</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('conference-bookings.show', $conferenceBooking) }}"
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                    Cancel
                </a>
                <button
                    type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hallSelect = document.getElementById('conference_hall_id');
    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');
    const costPreview = document.getElementById('cost-preview');

    const currencySymbol = '{{ CurrencyHelper::getCurrencySymbol() }}';
    const currencyPosition = '{{ CurrencyHelper::CURRENCIES[CurrencyHelper::getDefaultCurrency()]["position"] ?? "before" }}';

    function formatMoney(amount) {
        const formatted = amount.toFixed(2);
        if (currencyPosition === 'before') {
            return currencySymbol + formatted;
        }
        return formatted + ' ' + currencySymbol;
    }

    function calculateCost() {
        const selectedHall = hallSelect.options[hallSelect.selectedIndex];
        const rate = parseFloat(selectedHall.dataset.rate || 0);

        if (startTime.value && endTime.value) {
            const start = new Date(`2000-01-01T${startTime.value}`);
            const end = new Date(`2000-01-01T${endTime.value}`);
            const hours = (end - start) / (1000 * 60 * 60);

            if (hours > 0) {
                costPreview.textContent = formatMoney(hours * rate);
            } else {
                costPreview.textContent = formatMoney(0);
            }
        }
    }

    hallSelect.addEventListener('change', calculateCost);
    startTime.addEventListener('change', calculateCost);
    endTime.addEventListener('change', calculateCost);
});
</script>
@endsection
