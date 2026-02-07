{{-- resources/views/bookings/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Booking')
@section('page-title', 'Bookings')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-secondary">Edit Booking</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $booking->booking_number }}</p>
                </div>
                @include('components.reservation-status-badge', ['status' => $booking->status])
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('bookings.update', $booking) }}" class="p-6" x-data="editBookingForm()">
            @csrf
            @method('PUT')

            <div class="space-y-8">
                {{-- ── Guest ── --}}
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        Guest Information
                    </h3>

                    @if($booking->guest)
                        <div class="bg-blue-50/50 border border-blue-100 rounded-xl p-4 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($booking->guest->first_name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-secondary">{{ $booking->guest->full_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $booking->guest->email }} &bull; {{ $booking->guest->phone_number }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-semibold text-secondary mb-2">Change Guest (Optional)</label>
                        <select name="guest_id" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                            <option value="">-- Keep Current Guest --</option>
                            @foreach($guests as $guest)
                                <option value="{{ $guest->id }}" {{ old('guest_id', $booking->guest_id) == $guest->id ? 'selected' : '' }}>
                                    {{ $guest->full_name }} — {{ $guest->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- ── Stay Details ── --}}
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-50 to-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        Stay Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-secondary mb-2">Check-in Date <span class="text-red-500">*</span></label>
                            <input type="date" name="check_in_date" x-model="checkIn"
                                   value="{{ old('check_in_date', $booking->check_in_date->format('Y-m-d')) }}"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                            @error('check_in_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-secondary mb-2">Check-out Date <span class="text-red-500">*</span></label>
                            <input type="date" name="check_out_date" x-model="checkOut"
                                   value="{{ old('check_out_date', $booking->check_out_date->format('Y-m-d')) }}"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                            @error('check_out_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-secondary mb-2">Number of Guests <span class="text-red-500">*</span></label>
                            <input type="number" name="number_of_guests" value="{{ old('number_of_guests', $booking->number_of_guests) }}" min="1" max="10"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                            @error('number_of_guests') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Room Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-secondary mb-2">Room <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-80 overflow-y-auto p-1">
                            @foreach($availableRooms as $room)
                                <label class="relative flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-primary/50 hover:bg-blue-50/30 transition-all"
                                       :class="{ 'border-primary bg-blue-50/50 ring-2 ring-primary/20': selectedRoom === '{{ $room->id }}' }">
                                    <input type="radio" name="room_id" value="{{ $room->id }}" 
                                           x-model="selectedRoom"
                                           @change="updateTotal({{ $room->roomType->base_rate ?? 0 }})"
                                           class="text-primary focus:ring-primary"
                                           {{ old('room_id', $booking->room_id) == $room->id ? 'checked' : '' }}>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold text-secondary">Room {{ $room->room_number }}</span>
                                            <span class="text-sm font-bold text-primary">${{ number_format($room->roomType->base_rate ?? 0, 2) }}/night</span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $room->roomType->name ?? 'N/A' }} &bull; Floor {{ $room->floor->number ?? 'N/A' }}
                                            @if($room->id == $booking->room_id)
                                                <span class="text-primary font-semibold">(Current)</span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- ── Pricing & Notes ── --}}
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        Pricing & Notes
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-secondary mb-2">Total Amount <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500">$</span>
                                <input type="number" name="total_amount" x-model="totalAmount" step="0.01" min="0"
                                       value="{{ old('total_amount', $booking->total_amount) }}"
                                       class="w-full pl-8 pr-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                            </div>
                            <p class="text-xs text-gray-500 mt-1" x-show="nights > 0">
                                <span x-text="nights"></span> night(s) × $<span x-text="pricePerNight.toFixed(2)"></span> = $<span x-text="(nights * pricePerNight).toFixed(2)"></span>
                            </p>
                            @error('total_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-secondary mb-2">Special Requests</label>
                        <textarea name="special_requests" rows="3"
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                                  placeholder="Any special requests or notes...">{{ old('special_requests', $booking->special_requests) }}</textarea>
                    </div>
                </div>

                {{-- ── Actions ── --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                    <a href="{{ route('bookings.show', $booking) }}" 
                       class="px-6 py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-all">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-primary to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                        Update Booking
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function editBookingForm() {
        return {
            checkIn: '{{ old("check_in_date", $booking->check_in_date->format("Y-m-d")) }}',
            checkOut: '{{ old("check_out_date", $booking->check_out_date->format("Y-m-d")) }}',
            selectedRoom: '{{ old("room_id", $booking->room_id) }}',
            pricePerNight: {{ $booking->room?->roomType?->base_rate ?? 0 }},
            totalAmount: {{ old('total_amount', $booking->total_amount) }},

            get nights() {
                if (this.checkIn && this.checkOut) {
                    const diff = new Date(this.checkOut) - new Date(this.checkIn);
                    return Math.max(0, Math.ceil(diff / (1000 * 60 * 60 * 24)));
                }
                return 0;
            },

            updateTotal(rate) {
                this.pricePerNight = rate;
                if (this.nights > 0) {
                    this.totalAmount = (this.nights * rate).toFixed(2);
                }
            }
        }
    }
</script>
@endpush
@endsection
