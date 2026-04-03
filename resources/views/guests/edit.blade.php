{{-- resources/views/guests/edit.blade.php --}}
@extends('layouts.app')

@section('title', __('guests.edit_guest'))
@section('page-title', __('guests.title'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white text-lg font-bold shadow-lg overflow-hidden
                    {{ $guest->hasPhoto() ? '' : 'bg-gradient-to-br from-primary to-blue-600' }}">
                    @if($guest->hasPhoto())
                        <img src="{{ $guest->photo_medium_url ?? $guest->photo_url }}" alt="{{ $guest->full_name }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($guest->first_name, 0, 1) . substr($guest->last_name, 0, 1)) }}
                    @endif
                </div>
                <div>
                    <h2 class="text-xl font-extrabold text-secondary">{{ __('guests.edit_guest') }}</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $guest->full_name }}</p>
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
                        {{ __('guests.sections.personal_information') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('guests.fields.first_name') }} <span class="text-red-500">*</span>
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
                                {{ __('guests.fields.last_name') }} <span class="text-red-500">*</span>
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
                                {{ __('guests.fields.date_of_birth') }} <span class="text-gray-400 text-xs font-normal">({{ __('guests.forms.optional') }})</span>
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
                                {{ __('guests.fields.nationality') }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="nationality" 
                                id="nationality"
                                value="{{ old('nationality', $guest->nationality) }}" 
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('nationality') border-red-500 @enderror"
                                placeholder="{{ __('guests.forms.nationality_placeholder') }}"
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
                        {{ __('guests.sections.contact_information') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Phone Number -->
                        <div>
                            <label for="phone_number" class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('guests.fields.phone') }} <span class="text-red-500">*</span>
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
                                {{ __('guests.fields.email_address') }} <span class="text-red-500">*</span>
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
                            {{ __('guests.fields.address') }} <span class="text-gray-400 text-xs font-normal">({{ __('guests.forms.optional') }})</span>
                        </label>
                        <textarea 
                            name="address" 
                            id="address"
                            rows="3"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('address') border-red-500 @enderror"
                            placeholder="{{ __('guests.forms.address_placeholder') }}">{{ old('address', $guest->address) }}</textarea>
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
                        {{ __('guests.sections.identification') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- ID Number -->
                        <div>
                            <label for="id_number" class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('guests.fields.id_passport_number') }} <span class="text-red-500">*</span>
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
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                <!-- Photo Section -->
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        {{ __('guests.sections.guest_photo') }}
                    </h3>

                    <!-- Current Photo -->
                    @if($guest->hasPhoto())
                    <div class="mb-4 p-4 bg-gray-50 rounded-xl">
                        <label class="block text-sm font-semibold text-secondary mb-2">{{ __('guests.forms.current_photo') }}</label>
                        <div class="flex items-center gap-4">
                            <img src="{{ $guest->photo_medium_url ?? $guest->photo_url }}" alt="{{ $guest->full_name }}" class="w-24 h-24 object-cover rounded-xl shadow">
                            <div class="flex items-center gap-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="remove_photo" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span class="text-sm text-red-600 font-medium">{{ __('guests.forms.remove_photo') }}</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Upload New Photo -->
                    <div>
                        <label for="photo" class="block text-sm font-semibold text-secondary mb-2">
                            {{ $guest->hasPhoto() ? __('guests.forms.replace_photo') : __('guests.forms.upload_photo') }} <span class="text-gray-400 text-xs font-normal">({{ __('guests.forms.photo_replace_placeholder') }})</span>
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

                <div class="border-t border-gray-100"></div>

                <!-- ID Documents Section -->
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        {{ __('guests.sections.id_documents') }}
                    </h3>

                    <!-- Current Documents -->
                    @if($guest->hasIdDocuments())
                    <div class="mb-4 p-4 bg-gray-50 rounded-xl">
                        <label class="block text-sm font-semibold text-secondary mb-3">{{ __('guests.forms.current_documents') }} ({{ $guest->id_documents_count }})</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($guest->id_documents as $document)
                            <div class="flex items-center justify-between bg-white border border-gray-200 rounded-xl p-3">
                                <div class="flex items-center gap-3">
                                    @if(str_contains($document->mime_type, 'image'))
                                        <img src="{{ $document->getUrl('thumb') }}" alt="Document" class="w-12 h-12 object-cover rounded-lg">
                                    @else
                                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="overflow-hidden">
                                        <p class="text-sm font-medium text-secondary truncate max-w-[150px]">{{ $document->file_name }}</p>
                                        <p class="text-xs text-gray-500">{{ number_format($document->size / 1024, 1) }} KB</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ $document->getUrl() }}" target="_blank" class="text-xs text-primary hover:underline font-medium">{{ __('guests.actions.view') }}</a>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="remove_documents[]" value="{{ $document->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                        <span class="ml-1 text-xs text-red-600">{{ __('guests.remove') }}</span>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Upload New Documents -->
                    <div>
                        <label for="id_documents" class="block text-sm font-semibold text-secondary mb-2">
                            {{ __('guests.forms.add_documents') }} <span class="text-gray-400 text-xs font-normal">({{ __('guests.forms.documents_add_placeholder') }})</span>
                        </label>
                        <input 
                            type="file" 
                            name="id_documents[]" 
                            id="id_documents"
                            accept=".jpg,.jpeg,.png,.pdf"
                            multiple
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 @error('id_documents.*') border-red-500 @enderror">
                        @error('id_documents.*')
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
                    <h4 class="text-sm font-bold text-secondary mb-3">{{ __('guests.sections.guest_info') }}</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">{{ __('guests.info.registered') }}:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $guest->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('guests.info.last_updated') }}:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $guest->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('guests.stats.total_reservations') }}:</span>
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
                    data-confirm-message="{{ __('guests.messages.confirm_delete') }}"
                    onclick="if(confirm(this.dataset.confirmMessage)) { document.getElementById('delete-form').submit(); }"
                    class="px-6 py-2.5 text-sm font-semibold text-red-700 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all">
                    {{ __('guests.delete_guest') }}
                </button>
                @else
                <div></div>
                @endif
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('guests.index') }}" 
                       class="px-6 py-2.5 text-sm font-semibold text-secondary bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                        {{ __('guests.actions.cancel') }}
                    </a>
                    <button 
                        type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                        {{ __('guests.update_guest') }}
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
