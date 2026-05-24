{{-- resources/views/bookings/create.blade.php --}}
@extends('layouts.app')

@section('title', __('bookings.walk_in_checkin'))
@section('page-title', __('bookings.title'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
            <h2 class="text-xl font-extrabold text-secondary">{{ __('bookings.walk_in_checkin') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('bookings.walk_in_description') }}</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('bookings.store') }}" class="p-6" x-data="bookingForm()">
            @csrf

            <div class="space-y-8">
                {{-- ── Step 1: Guest Selection ── --}}
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        {{ __('bookings.sections.guest_info') }}
                    </h3>

                    <!-- Guest Type Toggle -->
                    <div class="flex gap-4 mb-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="guest_type" value="existing" x-model="guestType" class="text-primary focus:ring-primary">
                            <span class="text-sm font-semibold text-secondary">{{ __('bookings.form.select_existing_guest') }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="guest_type" value="new" x-model="guestType" class="text-primary focus:ring-primary">
                            <span class="text-sm font-semibold text-secondary">{{ __('bookings.form.create_new_guest') }}</span>
                        </label>
                    </div>

                    <input type="hidden" name="create_new_guest" x-bind:value="guestType === 'new' ? '1' : '0'">

                    <!-- Existing Guest Selection -->
                    <div x-show="guestType === 'existing'" x-transition class="space-y-4">
                        <div>
                            <label for="guest_id" class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('bookings.fields.guest') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="guest_id" id="guest_id"
                                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                                <option value="">{{ __('bookings.form.choose_guest') }}</option>
                                @foreach($guests as $guest)
                                    <option value="{{ $guest->id }}" {{ (old('guest_id', $selectedGuest?->id) == $guest->id) ? 'selected' : '' }}>
                                        {{ $guest->full_name }} — {{ $guest->email }} — {{ $guest->phone_number }}
                                    </option>
                                @endforeach
                            </select>
                            @error('guest_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- New Guest Form -->
                    <div x-show="guestType === 'new'" x-transition class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('bookings.form.first_name') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="guest_first_name" value="{{ old('guest_first_name') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                                       placeholder="{{ __('bookings.placeholders.first_name') }}">
                                @error('guest_first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('bookings.form.last_name') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="guest_last_name" value="{{ old('guest_last_name') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                                       placeholder="{{ __('bookings.placeholders.last_name') }}">
                                @error('guest_last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('bookings.form.email') }} <span class="text-red-500">*</span></label>
                                <input type="email" name="guest_email" value="{{ old('guest_email') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                                       placeholder="{{ __('bookings.placeholders.email') }}">
                                @error('guest_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('bookings.form.phone') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="guest_phone" value="{{ old('guest_phone') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                                       placeholder="{{ __('bookings.placeholders.phone') }}">
                                @error('guest_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('bookings.form.id_number') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="guest_id_number" value="{{ old('guest_id_number') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                                       placeholder="{{ __('bookings.placeholders.id_number') }}">
                                @error('guest_id_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-secondary mb-2">{{ __('bookings.form.nationality') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="guest_nationality" value="{{ old('guest_nationality') }}"
                                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                                       placeholder="{{ __('bookings.placeholders.nationality') }}">
                                @error('guest_nationality') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Step 2: Dates & Room ── --}}
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-50 to-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        {{ __('bookings.sections.booking_details') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-secondary mb-2">{{ __('bookings.fields.check_in_date') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="check_in_date" x-model="checkIn"
                                   value="{{ old('check_in_date', request('check_in')) }}"
                                   min="{{ date('Y-m-d') }}"
                                   @input="searchRooms()"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                            @error('check_in_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-secondary mb-2">{{ __('bookings.fields.check_out_date') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="check_out_date" x-model="checkOut"
                                   value="{{ old('check_out_date', request('check_out')) }}"
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   @input="searchRooms()"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                            @error('check_out_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-secondary mb-2">{{ __('bookings.form.number_of_guests') }} <span class="text-red-500">*</span></label>
                            <input type="number" name="number_of_guests" value="{{ old('number_of_guests', 1) }}" min="1" max="10"
                                   class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                            @error('number_of_guests') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-secondary mb-2">{{ __('bookings.fields.source') }}</label>
                            <select name="source" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                                <option value="frontdesk" {{ old('source') === 'frontdesk' ? 'selected' : '' }}>{{ __('bookings.sources.frontdesk') }}</option>
                                <option value="phone" {{ old('source') === 'phone' ? 'selected' : '' }}>{{ __('bookings.sources.phone') }}</option>
                                <option value="walkin" {{ old('source') === 'walkin' ? 'selected' : '' }}>{{ __('bookings.sources.walkin') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Room Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-secondary mb-2">
                            {{ __('bookings.fields.room') }} <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-500 ml-2" x-show="!checkIn || !checkOut">{{ __('bookings.form.select_dates_hint') }}</span>
                        </label>

                        <template x-if="!checkIn || !checkOut">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
                                <p class="text-sm text-yellow-800">{{ __('bookings.search_rooms_hint') }}</p>
                            </div>
                        </template>

                        <template x-if="checkIn && checkOut">
                            <div>
                                <div x-show="roomsLoading" class="flex items-center justify-center py-8">
                                    <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="ml-2 text-sm text-gray-600">Searching for available rooms...</span>
                                </div>
                                <div x-show="!roomsLoading && availableRooms.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-80 overflow-y-auto p-1">
                                    <template x-for="room in availableRooms" :key="room.id">
                                        <label class="relative flex items-center gap-3 p-4 border border-gray-200 rounded-xl cursor-pointer hover:border-primary/50 hover:bg-blue-50/30 transition-all"
                                               :class="{ 'border-primary bg-blue-50/50 ring-2 ring-primary/20': selectedRoom === String(room.id) }">
                                            <input type="radio" name="room_id" :value="room.id"
                                                   x-model="selectedRoom"
                                                   @change="updateTotalFromRoom(room)"
                                                   class="text-primary focus:ring-primary">
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-semibold text-secondary" x-text="'Room ' + room.room_number"></span>
                                                    <span class="text-sm font-bold text-primary" x-text="(room.room_type?.formatted_rate || '') + '{{ __("bookings.per_night") }}'"></span>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1" x-text="(room.room_type?.name || 'N/A') + ' &bull; Floor ' + (room.floor?.floor_number || 'N/A')"></div>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                                <div x-show="!roomsLoading && availableRooms.length === 0 && checkIn && checkOut" class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                                    <p class="text-sm text-red-700">{{ __('bookings.messages.no_available_rooms') }}</p>
                                </div>
                            </div>
                        </template>
                        @error('room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- ── Step 3: Pricing & Notes ── --}}
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        {{ __('bookings.sections.pricing_notes') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-secondary mb-2">{{ __('bookings.fields.total_amount') }} <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-500">$</span>
                                <input type="number" name="total_amount" x-model="totalAmount" step="0.01" min="0"
                                       value="{{ old('total_amount', '0.00') }}"
                                       class="w-full pl-8 pr-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all">
                            </div>
                            <p class="text-xs text-gray-500 mt-1" x-show="nights > 0">
                                <span x-text="nights"></span> night(s) × $<span x-text="pricePerNight.toFixed(2)"></span> = $<span x-text="(nights * pricePerNight).toFixed(2)"></span>
                            </p>
                            @error('total_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-center">
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 w-full">
                                <p class="text-sm font-semibold text-blue-800">{{ __('bookings.status_checked_in') }}</p>
                                <p class="text-xs text-blue-600 mt-1">{{ __('bookings.walkin_checked_in_immediately') }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-secondary mb-2">{{ __('bookings.fields.special_requests') }}</label>
                        <textarea name="special_requests" rows="3"
                                  class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary transition-all"
                                  placeholder="{{ __('bookings.form.special_requests_placeholder') }}">{{ old('special_requests') }}</textarea>
                    </div>
                </div>

                {{-- ── Actions ── --}}
                <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                    <a href="{{ route('bookings.index') }}" 
                       class="px-6 py-3 border border-gray-200 text-gray-600 font-semibold rounded-xl hover:bg-gray-50 transition-all">
                        {{ __('bookings.cancel') }}
                    </a>
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-primary to-blue-600 text-white font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                        {{ __('bookings.check_in_guest') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function bookingForm() {
        return {
            guestType: '{{ old("guest_type", $selectedGuest ? "existing" : "existing") }}',
            checkIn: '{{ old("check_in_date", request("check_in", "")) }}',
            checkOut: '{{ old("check_out_date", request("check_out", "")) }}',
            selectedRoom: '{{ old("room_id", "") }}',
            pricePerNight: 0,
            totalAmount: {{ old('total_amount', 0) }},
            availableRooms: [],
            roomsLoading: false,
            searchDebounce: null,

            get nights() {
                if (this.checkIn && this.checkOut) {
                    const diff = new Date(this.checkOut) - new Date(this.checkIn);
                    return Math.max(0, Math.ceil(diff / (1000 * 60 * 60 * 24)));
                }
                return 0;
            },

            init() {
                if (this.checkIn && this.checkOut) {
                    this.searchRooms();
                }
            },

            searchRooms() {
                if (this.searchDebounce) clearTimeout(this.searchDebounce);
                this.searchDebounce = setTimeout(() => {
                    if (!this.checkIn || !this.checkOut) {
                        this.availableRooms = [];
                        this.selectedRoom = '';
                        this.pricePerNight = 0;
                        return;
                    }
                    if (new Date(this.checkOut) <= new Date(this.checkIn)) {
                        this.availableRooms = [];
                        this.selectedRoom = '';
                        this.pricePerNight = 0;
                        return;
                    }
                    this.roomsLoading = true;
                    this.selectedRoom = '';
                    this.pricePerNight = 0;

                    const url = '{{ route("bookings.available-rooms") }}' 
                        + '?check_in=' + encodeURIComponent(this.checkIn) 
                        + '&check_out=' + encodeURIComponent(this.checkOut);

                    fetch(url, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        this.availableRooms = data.rooms || [];
                        this.roomsLoading = false;
                    })
                    .catch(error => {
                        console.error('Error fetching rooms:', error);
                        this.availableRooms = [];
                        this.roomsLoading = false;
                    });
                }, 300);
            },

            updateTotalFromRoom(room) {
                this.pricePerNight = parseFloat(room.room_type?.price_per_night || room.room_type?.base_rate || 0);
                if (this.nights > 0) {
                    this.totalAmount = (this.nights * this.pricePerNight).toFixed(2);
                }
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
