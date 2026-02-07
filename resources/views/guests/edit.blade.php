{{-- resources/views/guests/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Guest')
@section('page-title', 'Guests')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-extrabold text-secondary">Edit Guest</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $guest->full_name }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-lg font-bold shadow-lg overflow-hidden
                    {{ $guest->photo ? '' : 'bg-gradient-to-br from-primary to-blue-600' }}">
                    @if($guest->photo)
                        <img src="{{ $guest->photo_url }}" alt="{{ $guest->full_name }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($guest->first_name, 0, 1) . substr($guest->last_name, 0, 1)) }}
                    @endif
                </div>
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('guests.update', $guest) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Personal Information Section -->
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        Personal Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-semibold text-secondary mb-2">
                                First Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="first_name" 
                                id="first_name"
                                value="{{ old('first_name', $guest->first_name) }}" 
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('first_name') border-red-500 @enderror"
                                required>
                            @error('first_name')
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
                            <label for="last_name" class="block text-sm font-semibold text-secondary mb-2">
                                Last Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="last_name" 
                                id="last_name"
                                value="{{ old('last_name', $guest->last_name) }}" 
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('last_name') border-red-500 @enderror"
                                required>
                            @error('last_name')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label for="date_of_birth" class="block text-sm font-semibold text-secondary mb-2">
                                Date of Birth <span class="text-gray-400 text-xs font-normal">(Optional)</span>
                            </label>
                            <input 
                                type="date" 
                                name="date_of_birth" 
                                id="date_of_birth"
                                value="{{ old('date_of_birth', $guest->date_of_birth?->format('Y-m-d')) }}" 
                                max="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('date_of_birth') border-red-500 @enderror">
                            @error('date_of_birth')
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
                            <label for="nationality" class="block text-sm font-semibold text-secondary mb-2">
                                Nationality <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="nationality" 
                                id="nationality"
                                value="{{ old('nationality', $guest->nationality) }}" 
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('nationality') border-red-500 @enderror"
                                placeholder="e.g., American, British"
                                required>
                            @error('nationality')
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

                <div class="border-t border-gray-100"></div>

                <!-- Contact Information Section -->
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        Contact Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Phone Number -->
                        <div>
                            <label for="phone_number" class="block text-sm font-semibold text-secondary mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="phone_number" 
                                id="phone_number"
                                value="{{ old('phone_number', $guest->phone_number) }}" 
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('phone_number') border-red-500 @enderror"
                                required>
                            @error('phone_number')
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
                            <label for="email" class="block text-sm font-semibold text-secondary mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email"
                                value="{{ old('email', $guest->email) }}" 
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('email') border-red-500 @enderror"
                                required>
                            @error('email')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="mt-6">
                        <label for="address" class="block text-sm font-semibold text-secondary mb-2">
                            Address <span class="text-gray-400 text-xs font-normal">(Optional)</span>
                        </label>
                        <textarea 
                            name="address" 
                            id="address"
                            rows="3"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('address') border-red-500 @enderror">{{ old('address', $guest->address) }}</textarea>
                        @error('address')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                <!-- Identification Section -->
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                        </div>
                        Identification
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- ID Number -->
                        <div>
                            <label for="id_number" class="block text-sm font-semibold text-secondary mb-2">
                                ID/Passport Number <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="id_number" 
                                id="id_number"
                                value="{{ old('id_number', $guest->id_number) }}" 
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('id_number') border-red-500 @enderror"
                                required>
                            @error('id_number')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Current ID Document -->
                        <div>
                            <label class="block text-sm font-semibold text-secondary mb-2">
                                Current ID Document
                            </label>
                            @if($guest->id_document)
                                <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <a href="{{ $guest->id_document_url }}" target="_blank" class="text-sm text-primary hover:underline font-medium">
                                        View Document
                                    </a>
                                </div>
                            @else
                                <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <span class="text-sm text-gray-500">No document uploaded</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Upload New ID Document -->
                    <div class="mt-6">
                        <label for="id_document" class="block text-sm font-semibold text-secondary mb-2">
                            Upload New ID Document <span class="text-gray-400 text-xs font-normal">(Optional, max 5MB)</span>
                        </label>
                        <input 
                            type="file" 
                            name="id_document" 
                            id="id_document"
                            accept=".jpg,.jpeg,.png,.pdf"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 @error('id_document') border-red-500 @enderror">
                        @error('id_document')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Upload New Photo -->
                    <div class="mt-6">
                        <label for="photo" class="block text-sm font-semibold text-secondary mb-2">
                            Upload New Photo <span class="text-gray-400 text-xs font-normal">(Optional, max 2MB)</span>
                        </label>
                        <input 
                            type="file" 
                            name="photo" 
                            id="photo"
                            accept=".jpg,.jpeg,.png"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 @error('photo') border-red-500 @enderror">
                        @error('photo')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Guest Information -->
                <div class="bg-gradient-to-br from-primary/5 to-blue-50 rounded-xl p-4 border border-primary/20">
                    <h4 class="text-sm font-bold text-secondary mb-3">Guest Information</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Registered:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $guest->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Last Updated:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $guest->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Total Reservations:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $guest->reservations()->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                @if($guest->reservations()->count() === 0)
                <button 
                    type="button"
                    onclick="if(confirm('Are you sure you want to delete this guest?')) { document.getElementById('delete-form').submit(); }"
                    class="px-6 py-2.5 text-sm font-semibold text-red-700 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all">
                    Delete Guest
                </button>
                @else
                <span class="text-sm text-gray-500">Cannot delete: guest has reservations</span>
                @endif
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('guests.index') }}" 
                       class="px-6 py-2.5 text-sm font-semibold text-secondary bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                        Cancel
                    </a>
                    <button 
                        type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                        Update Guest
                    </button>
                </div>
            </div>
        </form>

        <!-- Hidden Delete Form -->
        <form id="delete-form" method="POST" action="{{ route('guests.destroy', $guest) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
