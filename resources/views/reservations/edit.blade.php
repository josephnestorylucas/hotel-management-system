{{-- resources/views/reservations/edit.blade.php --}}
@extends('layouts.app')

@section('title', __('reservations.edit_reservation'))
@section('page-title', __('reservations.reservations'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-secondary">{{ __('reservations.edit_reservation') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $reservation->reservation_number }}</p>
                </div>
                @include('components.reservation-status-badge', ['status' => $reservation->status])
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('reservations.update', $reservation) }}" enctype="multipart/form-data" class="p-6" x-data="reservationForm()">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Guest Information Section -->
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        {{ __('reservations.sections.guest_information') }}
                    </h3>

                    <!-- Current Guest Display -->
                    @if($reservation->guest)
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 mb-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold shadow-lg overflow-hidden
                                {{ $reservation->guest->photo ? '' : 'bg-gradient-to-br from-primary to-blue-600' }}">
                                @if($reservation->guest->photo)
                                    <img src="{{ $reservation->guest->photo_url }}" alt="{{ $reservation->guest->full_name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($reservation->guest->first_name, 0, 1) . substr($reservation->guest->last_name, 0, 1)) }}
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-secondary">{{ $reservation->guest->full_name }}</p>
                                <p class="text-sm text-gray-600">{{ $reservation->guest->phone_number }} • {{ $reservation->guest->email ?? __('reservations.labels.no_email') }}</p>
                            </div>
                            <a href="{{ route('guests.show', $reservation->guest) }}" target="_blank" class="text-sm text-primary hover:underline font-medium">
                                {{ __('reservations.labels.view_profile') }} →
                            </a>
                        </div>
                    </div>
                    @else
                    <div class="bg-gradient-to-br from-yellow-50 to-amber-50 border border-yellow-200 rounded-xl p-4 mb-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-bold text-yellow-800">{{ __('reservations.labels.legacy_reservation') }}</p>
                                <p class="text-sm text-yellow-700">{{ __('reservations.labels.guest') }}: {{ $reservation->guest_name }} • {{ $reservation->guest_phone }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Change Guest Option -->
                    <div class="flex gap-4 mb-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="change_guest" value="keep" x-model="changeGuest" class="text-primary focus:ring-primary">
                            <span class="text-sm font-semibold text-secondary">{{ __('reservations.change_guest.keep') }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="change_guest" value="select" x-model="changeGuest" class="text-primary focus:ring-primary">
                            <span class="text-sm font-semibold text-secondary">{{ __('reservations.change_guest.select') }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="change_guest" value="new" x-model="changeGuest" class="text-primary focus:ring-primary">
                            <span class="text-sm font-semibold text-secondary">{{ __('reservations.change_guest.new') }}</span>
                        </label>
                    </div>

                    <!-- Keep Current Guest -->
                    <div x-show="changeGuest === 'keep'" x-transition>
                        <input type="hidden" name="guest_id" value="{{ $reservation->guest_id }}">
                        <input type="hidden" name="create_new_guest" value="0">
                    </div>

                    <!-- Select Different Guest -->
                    <div x-show="changeGuest === 'select'" x-transition class="space-y-4">
                        <input type="hidden" name="create_new_guest" value="0">
                        
                        <div>
                            <label for="guest_id" class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('reservations.fields.guest_id') }} <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="guest_id" 
                                id="guest_id"
                                x-model="selectedGuestId"
                                @change="updateSelectedGuest()"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_id') border-red-500 @enderror">
                                <option value="">{{ __('reservations.placeholders.select_guest') }}</option>
                                @foreach($guests as $guest)
                                <option value="{{ $guest->id }}" 
                                    data-name="{{ $guest->full_name }}"
                                    data-phone="{{ $guest->phone_number }}"
                                    data-email="{{ $guest->email }}"
                                    {{ $reservation->guest_id == $guest->id ? 'selected' : '' }}>
                                    {{ $guest->full_name }} - {{ $guest->phone_number }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Selected Guest Preview -->
                        <div x-show="selectedGuestId && changeGuest === 'select'" x-transition class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center text-white font-bold">
                                    <span x-text="getGuestInitials()"></span>
                                </div>
                                <div>
                                    <p class="font-bold text-secondary" x-text="selectedGuestName"></p>
                                    <p class="text-sm text-gray-600"><span x-text="selectedGuestPhone"></span> • <span x-text="selectedGuestEmail || '{{ __('reservations.labels.no_email') }}'"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Create New Guest -->
                    <div x-show="changeGuest === 'new'" x-transition class="space-y-4">
                        <input type="hidden" name="create_new_guest" value="1">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div>
                                <label for="guest_first_name" class="block text-sm font-semibold text-secondary mb-2">
                                    {{ __('reservations.fields.guest_first_name') }} <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="guest_first_name" 
                                    id="guest_first_name"
                                    value="{{ old('guest_first_name') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_first_name') border-red-500 @enderror"
                                    placeholder="{{ __('reservations.placeholders.enter_first_name') }}"
                                    x-bind:required="changeGuest === 'new'">
                                @error('guest_first_name')
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="guest_last_name" class="block text-sm font-semibold text-secondary mb-2">
                                    {{ __('reservations.fields.guest_last_name') }} <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="guest_last_name" 
                                    id="guest_last_name"
                                    value="{{ old('guest_last_name') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_last_name') border-red-500 @enderror"
                                    placeholder="{{ __('reservations.placeholders.enter_last_name') }}"
                                    x-bind:required="changeGuest === 'new'">
                                @error('guest_last_name')
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="guest_phone" class="block text-sm font-semibold text-secondary mb-2">
                                    {{ __('reservations.fields.phone_number') }} <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="guest_phone" 
                                    id="guest_phone"
                                    value="{{ old('guest_phone') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_phone') border-red-500 @enderror"
                                    placeholder="{{ __('reservations.placeholders.phone') }}"
                                    x-bind:required="changeGuest === 'new'">
                                @error('guest_phone')
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="guest_email" class="block text-sm font-semibold text-secondary mb-2">
                                    {{ __('reservations.fields.email') }} <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    name="guest_email" 
                                    id="guest_email"
                                    value="{{ old('guest_email') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_email') border-red-500 @enderror"
                                    placeholder="{{ __('reservations.placeholders.email') }}"
                                    x-bind:required="changeGuest === 'new'">
                                @error('guest_email')
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- ID Number -->
                            <div>
                                <label for="guest_id_number" class="block text-sm font-semibold text-secondary mb-2">
                                    {{ __('reservations.fields.id_number') }} <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="guest_id_number" 
                                    id="guest_id_number"
                                    value="{{ old('guest_id_number') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_id_number') border-red-500 @enderror"
                                    placeholder="{{ __('reservations.placeholders.enter_id_number') }}"
                                    x-bind:required="changeGuest === 'new'">
                            </div>

                            <!-- Nationality -->
                            <div>
                                <label for="guest_nationality" class="block text-sm font-semibold text-secondary mb-2">
                                    {{ __('reservations.fields.nationality') }} <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="guest_nationality" 
                                    id="guest_nationality"
                                    value="{{ old('guest_nationality') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_nationality') border-red-500 @enderror"
                                    placeholder="{{ __('reservations.placeholders.nationality') }}"
                                    x-bind:required="changeGuest === 'new'">
                            </div>

                            <!-- Guest Photo Upload -->
                            <div class="md:col-span-2">
                                <label for="guest_photo" class="block text-sm font-semibold text-secondary mb-2">
                                    {{ __('reservations.fields.guest_photo') }} <span class="text-gray-400 text-xs font-normal">({{ __('reservations.labels.photo_optional') }})</span>
                                </label>
                                <input 
                                    type="file" 
                                    name="guest_photo" 
                                    id="guest_photo"
                                    accept=".jpg,.jpeg,.png"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 @error('guest_photo') border-red-500 @enderror">
                                @error('guest_photo')
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                <!-- Reservation Details Section -->
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        {{ __('reservations.sections.reservation_details') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Check-In Date -->
                        <div>
                            <label for="check_in_date" class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('reservations.fields.check_in_date') }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="check_in_date" 
                                id="check_in_date"
                                value="{{ old('check_in_date', $reservation->check_in_date->format('Y-m-d')) }}" 
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('check_in_date') border-red-500 @enderror"
                                required>
                            @error('check_in_date')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Check-Out Date -->
                        <div>
                            <label for="check_out_date" class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('reservations.fields.check_out_date') }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="check_out_date" 
                                id="check_out_date"
                                value="{{ old('check_out_date', $reservation->check_out_date->format('Y-m-d')) }}" 
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('check_out_date') border-red-500 @enderror"
                                required>
                            @error('check_out_date')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Number of Guests -->
                        <div>
                            <label for="number_of_guests" class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('reservations.fields.number_of_guests') }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="number_of_guests" 
                                id="number_of_guests"
                                value="{{ old('number_of_guests', $reservation->number_of_guests) }}" 
                                min="1"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('number_of_guests') border-red-500 @enderror"
                                required>
                            @error('number_of_guests')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Estimated Amount -->
                        <div>
                            <label for="estimated_amount" class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('reservations.fields.estimated_amount') }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="estimated_amount" 
                                id="estimated_amount"
                                value="{{ old('estimated_amount', $reservation->estimated_amount) }}" 
                                step="0.01"
                                min="0"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('estimated_amount') border-red-500 @enderror"
                                required>
                            @error('estimated_amount')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Room Assignment -->
                    <div class="mt-6">
                        <label for="room_id" class="block text-sm font-semibold text-secondary mb-2">
                            {{ __('reservations.fields.assigned_room') }}
                        </label>
                        <select 
                            name="room_id" 
                            id="room_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('room_id') border-red-500 @enderror">
                            <option value="">{{ __('reservations.placeholders.not_assigned') }}</option>
                            @foreach($availableRooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id', $reservation->room_id) == $room->id ? 'selected' : '' }}>
                                {{ $room->room_number }} - {{ $room->roomType->name }} (${{ number_format($room->roomType->base_rate, 2) }}/night)
                            </option>
                            @endforeach
                        </select>
                        @error('room_id')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mt-6">
                        <label for="status" class="block text-sm font-semibold text-secondary mb-2">
                            {{ __('reservations.fields.status') }} <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="status" 
                            id="status"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('status') border-red-500 @enderror"
                            required>
                            <option value="pending" {{ old('status', $reservation->status) == 'pending' ? 'selected' : '' }}>{{ __('reservations.status.pending') }}</option>
                            <option value="confirmed" {{ old('status', $reservation->status) == 'confirmed' ? 'selected' : '' }}>{{ __('reservations.status.confirmed') }}</option>
                            <option value="cancelled" {{ old('status', $reservation->status) == 'cancelled' ? 'selected' : '' }}>{{ __('reservations.status.cancelled') }}</option>
                            <option value="no_show" {{ old('status', $reservation->status) == 'no_show' ? 'selected' : '' }}>{{ __('reservations.status.no_show') }}</option>
                        </select>
                        @error('status')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Reservation Info -->
                <div class="bg-gradient-to-br from-primary/5 to-blue-50 rounded-xl p-4 border border-primary/20">
                    <h4 class="text-sm font-bold text-secondary mb-3">{{ __('reservations.sections.reservation_information') }}</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">{{ __('reservations.info.created_by') }}:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $reservation->creator->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('reservations.info.created') }}:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $reservation->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('reservations.info.last_updated') }}:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $reservation->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('reservations.info.stay_duration') }}:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $reservation->check_in_date->diffInDays($reservation->check_out_date) }} {{ __('reservations.labels.nights') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                <button 
                    type="button"
                    onclick="if(confirm('{{ __('reservations.messages.confirm_delete') }}')) { document.getElementById('delete-form').submit(); }"
                    class="px-6 py-2.5 text-sm font-semibold text-red-700 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all">
                    {{ __('reservations.actions.delete_reservation') }}
                </button>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('reservations.index') }}" 
                       class="px-6 py-2.5 text-sm font-semibold text-secondary bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                        {{ __('reservations.actions.cancel') }}
                    </a>
                    <button 
                        type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                        {{ __('reservations.actions.update') }}
                    </button>
                </div>
            </div>
        </form>

        <!-- Hidden Delete Form -->
        <form id="delete-form" method="POST" action="{{ route('reservations.destroy', $reservation) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

<script>
function reservationForm() {
    return {
        changeGuest: 'keep',
        selectedGuestId: '{{ $reservation->guest_id ?? "" }}',
        selectedGuestName: '{{ $reservation->guest ? $reservation->guest->full_name : "" }}',
        selectedGuestPhone: '{{ $reservation->guest ? $reservation->guest->phone_number : "" }}',
        selectedGuestEmail: '{{ $reservation->guest ? $reservation->guest->email : "" }}',
        
        updateSelectedGuest() {
            const select = document.getElementById('guest_id');
            const option = select.options[select.selectedIndex];
            if (option && option.value) {
                this.selectedGuestName = option.getAttribute('data-name') || '';
                this.selectedGuestPhone = option.getAttribute('data-phone') || '';
                this.selectedGuestEmail = option.getAttribute('data-email') || '';
            } else {
                this.selectedGuestName = '';
                this.selectedGuestPhone = '';
                this.selectedGuestEmail = '';
            }
        },
        
        getGuestInitials() {
            if (!this.selectedGuestName) return '';
            const names = this.selectedGuestName.split(' ');
            return names.map(n => n.charAt(0).toUpperCase()).slice(0, 2).join('');
        }
    }
}
</script>
@endsection
