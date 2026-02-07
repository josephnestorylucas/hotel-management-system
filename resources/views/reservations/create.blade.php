{{-- resources/views/reservations/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Reservation')
@section('page-title', 'Reservations')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
            <h2 class="text-xl font-extrabold text-secondary">Create Reservation</h2>
            <p class="text-sm text-gray-500 mt-1">Add a new guest reservation</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('reservations.store') }}" class="p-6" x-data="reservationForm()">
            @csrf

            <div class="space-y-6">
                <!-- Guest Selection Section -->
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        Guest Information
                    </h3>

                    <!-- Guest Type Toggle -->
                    <div class="flex gap-4 mb-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="guest_type" value="existing" x-model="guestType" class="text-primary focus:ring-primary">
                            <span class="text-sm font-semibold text-secondary">Select Existing Guest</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="guest_type" value="new" x-model="guestType" class="text-primary focus:ring-primary">
                            <span class="text-sm font-semibold text-secondary">Create New Guest</span>
                        </label>
                    </div>

                    <!-- Existing Guest Selection -->
                    <div x-show="guestType === 'existing'" x-transition class="space-y-4">
                        <input type="hidden" name="create_new_guest" x-bind:value="guestType === 'new' ? '1' : '0'">
                        
                        <div>
                            <label for="guest_id" class="block text-sm font-semibold text-secondary mb-2">
                                Select Guest <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="guest_id" 
                                id="guest_id"
                                x-model="selectedGuestId"
                                @change="updateSelectedGuest()"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_id') border-red-500 @enderror">
                                <option value="">-- Select a guest --</option>
                                @foreach($guests as $guest)
                                <option value="{{ $guest->id }}" 
                                    data-name="{{ $guest->full_name }}"
                                    data-phone="{{ $guest->phone_number }}"
                                    data-email="{{ $guest->email }}"
                                    {{ (old('guest_id') == $guest->id || (isset($selectedGuest) && $selectedGuest && $selectedGuest->id == $guest->id)) ? 'selected' : '' }}>
                                    {{ $guest->full_name }} - {{ $guest->phone_number }}
                                </option>
                                @endforeach
                            </select>
                            @error('guest_id')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Selected Guest Preview -->
                        <div x-show="selectedGuestId" x-transition class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center text-white font-bold">
                                    <span x-text="getGuestInitials()"></span>
                                </div>
                                <div>
                                    <p class="font-bold text-secondary" x-text="selectedGuestName"></p>
                                    <p class="text-sm text-gray-600"><span x-text="selectedGuestPhone"></span> • <span x-text="selectedGuestEmail || 'No email'"></span></p>
                                </div>
                                <a :href="'/guests/' + selectedGuestId" target="_blank" class="ml-auto text-sm text-primary hover:underline font-medium">
                                    View Profile →
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <span>Can't find the guest?</span>
                            <button type="button" @click="guestType = 'new'" class="text-primary hover:underline font-semibold">
                                Create a new guest
                            </button>
                            <span>or</span>
                            <a href="{{ route('guests.create', ['return_to_reservation' => 1]) }}" class="text-primary hover:underline font-semibold">
                                Add full guest profile
                            </a>
                        </div>
                    </div>

                    <!-- New Guest Form -->
                    <div x-show="guestType === 'new'" x-transition class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div>
                                <label for="guest_first_name" class="block text-sm font-semibold text-secondary mb-2">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="guest_first_name" 
                                    id="guest_first_name"
                                    value="{{ old('guest_first_name') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_first_name') border-red-500 @enderror"
                                    placeholder="Enter first name"
                                    x-bind:required="guestType === 'new'">
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
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="guest_last_name" 
                                    id="guest_last_name"
                                    value="{{ old('guest_last_name') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_last_name') border-red-500 @enderror"
                                    placeholder="Enter last name"
                                    x-bind:required="guestType === 'new'">
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
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="guest_phone" 
                                    id="guest_phone"
                                    value="{{ old('guest_phone') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_phone') border-red-500 @enderror"
                                    placeholder="+1 (555) 123-4567"
                                    x-bind:required="guestType === 'new'">
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
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    name="guest_email" 
                                    id="guest_email"
                                    value="{{ old('guest_email') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_email') border-red-500 @enderror"
                                    placeholder="guest@example.com"
                                    x-bind:required="guestType === 'new'">
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
                                    ID/Passport Number <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="guest_id_number" 
                                    id="guest_id_number"
                                    value="{{ old('guest_id_number') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_id_number') border-red-500 @enderror"
                                    placeholder="Enter ID number"
                                    x-bind:required="guestType === 'new'">
                                @error('guest_id_number')
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Nationality -->
                            <div>
                                <label for="guest_nationality" class="block text-sm font-semibold text-secondary mb-2">
                                    Nationality <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="guest_nationality" 
                                    id="guest_nationality"
                                    value="{{ old('guest_nationality') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_nationality') border-red-500 @enderror"
                                    placeholder="e.g., American, British"
                                    x-bind:required="guestType === 'new'">
                                @error('guest_nationality')
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-yellow-50 to-amber-50 border border-yellow-200 rounded-xl p-4">
                            <div class="flex gap-3">
                                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-sm text-yellow-800">
                                    A new guest record will be created automatically. You can add more details (photo, ID document) by editing the guest profile later.
                                </p>
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
                        Reservation Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Check-In Date -->
                        <div>
                            <label for="check_in_date" class="block text-sm font-semibold text-secondary mb-2">
                                Check-In Date <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="check_in_date" 
                                id="check_in_date"
                                value="{{ old('check_in_date') }}" 
                                min="{{ date('Y-m-d') }}"
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
                                Check-Out Date <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="check_out_date" 
                                id="check_out_date"
                                value="{{ old('check_out_date') }}" 
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
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
                                Number of Guests <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="number_of_guests" 
                                id="number_of_guests"
                                value="{{ old('number_of_guests', 1) }}" 
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

                        <!-- Total Amount -->
                        <div>
                            <label for="total_amount" class="block text-sm font-semibold text-secondary mb-2">
                                Total Amount ($) <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="total_amount" 
                                id="total_amount"
                                value="{{ old('total_amount', 0) }}" 
                                step="0.01"
                                min="0"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('total_amount') border-red-500 @enderror"
                                placeholder="0.00"
                                required>
                            @error('total_amount')
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
                            Assign Room <span class="text-gray-400 text-xs font-normal">(Optional - can be assigned later)</span>
                        </label>
                        <select 
                            name="room_id" 
                            id="room_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('room_id') border-red-500 @enderror">
                            <option value="">Select a room (optional)</option>
                            @foreach($availableRooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
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
                            Reservation Status <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="status" 
                            id="status"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('status') border-red-500 @enderror"
                            required>
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
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

                <!-- Info Box -->
                <div class="bg-gradient-to-br from-primary/5 to-blue-50 border border-primary/20 rounded-xl p-4">
                    <div class="flex gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary/10 to-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-sm">
                            <p class="font-bold text-secondary mb-1">Reservation Tips:</p>
                            <ul class="list-disc list-inside space-y-1 text-gray-600">
                                <li>Select an existing guest or create a new one for this reservation</li>
                                <li>Room can be assigned now or later based on availability</li>
                                <li>Total amount can be calculated based on room rate and stay duration</li>
                                <li>A unique reservation number will be generated automatically</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('reservations.index') }}" 
                   class="px-6 py-2.5 text-sm font-semibold text-secondary bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                    Cancel
                </a>
                <button 
                    type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                    Create Reservation
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function reservationForm() {
    return {
        guestType: '{{ isset($selectedGuest) && $selectedGuest ? "existing" : (old("create_new_guest") == "1" ? "new" : "existing") }}',
        selectedGuestId: '{{ isset($selectedGuest) ? $selectedGuest->id : old("guest_id", "") }}',
        selectedGuestName: '{{ isset($selectedGuest) ? $selectedGuest->full_name : "" }}',
        selectedGuestPhone: '{{ isset($selectedGuest) ? $selectedGuest->phone_number : "" }}',
        selectedGuestEmail: '{{ isset($selectedGuest) ? $selectedGuest->email : "" }}',
        
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
