@extends('layouts.app')

@section('title', 'Edit Conference Hall')
@section('page-title', 'Conference Halls')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Edit Conference Hall</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $conferenceHall->name }}</p>
            </div>
            <a href="{{ route('conference-halls.show', $conferenceHall) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                View Details
            </a>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('conference-halls.update', $conferenceHall) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Basic Information
                    </h3>

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Hall Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name"
                            value="{{ old('name', $conferenceHall->name) }}"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-500 @enderror"
                            placeholder="e.g., Grand Ballroom">
                        @error('name')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Building -->
                    <div class="mb-4">
                        <label for="building_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Building
                        </label>
                        <select 
                            name="building_id" 
                            id="building_id"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('building_id') border-red-500 @enderror">
                            <option value="">-- Select Building (Optional) --</option>
                            @foreach($buildings as $building)
                            <option value="{{ $building->id }}" {{ old('building_id', $conferenceHall->building_id) == $building->id ? 'selected' : '' }}>
                                {{ $building->name }} {{ $building->code ? "({$building->code})" : '' }}
                            </option>
                            @endforeach
                        </select>
                        @error('building_id')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-500">Link this hall to a building for better organization</p>
                    </div>

                    <!-- Capacity & Rate -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                                Capacity <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="capacity" 
                                id="capacity"
                                value="{{ old('capacity', $conferenceHall->capacity) }}"
                                min="1"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('capacity') border-red-500 @enderror"
                                placeholder="100">
                            @error('capacity')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="hourly_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                Hourly Rate ({{ $currencySymbol }}) <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="hourly_rate" 
                                id="hourly_rate"
                                value="{{ old('hourly_rate', $conferenceHall->hourly_rate) }}"
                                step="{{ $systemCurrency === 'TZS' ? '1' : '0.01' }}"
                                min="0"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('hourly_rate') border-red-500 @enderror"
                                placeholder="{{ $systemCurrency === 'TZS' ? '0' : '0.00' }}">
                            <input type="hidden" name="currency" value="{{ old('currency', $conferenceHall->currency) }}">
                            @error('hourly_rate')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mt-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="status" 
                            id="status"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('status') border-red-500 @enderror">
                            <option value="available" {{ old('status', $conferenceHall->status) === 'available' ? 'selected' : '' }}>Available</option>
                            <option value="maintenance" {{ old('status', $conferenceHall->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                        @error('status')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Amenities -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Amenities
                    </h3>

                    <div class="grid grid-cols-2 gap-3">
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Projector" class="w-4 h-4 text-blue-600 rounded" {{ in_array('Projector', old('amenities', $conferenceHall->amenities ?? [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Projector</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Whiteboard" class="w-4 h-4 text-blue-600 rounded" {{ in_array('Whiteboard', old('amenities', $conferenceHall->amenities ?? [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Whiteboard</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Wi-Fi" class="w-4 h-4 text-blue-600 rounded" {{ in_array('Wi-Fi', old('amenities', $conferenceHall->amenities ?? [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Wi-Fi</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Air Conditioning" class="w-4 h-4 text-blue-600 rounded" {{ in_array('Air Conditioning', old('amenities', $conferenceHall->amenities ?? [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Air Conditioning</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Sound System" class="w-4 h-4 text-blue-600 rounded" {{ in_array('Sound System', old('amenities', $conferenceHall->amenities ?? [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Sound System</span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" name="amenities[]" value="Video Conference" class="w-4 h-4 text-blue-600 rounded" {{ in_array('Video Conference', old('amenities', $conferenceHall->amenities ?? [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm text-gray-700">Video Conference</span>
                        </label>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <textarea 
                        name="description" 
                        id="description"
                        rows="4"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('description') border-red-500 @enderror"
                        placeholder="Additional details about the hall...">{{ old('description', $conferenceHall->description) }}</textarea>
                    @error('description')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Conference Hall Guidelines:</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-700">
                                <li>Capacity should reflect maximum comfortable seating</li>
                                <li>Hourly rate will be used for booking calculations</li>
                                <li>30-minute cleanup buffer is automatically applied between bookings</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('conference-halls.index') }}" 
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                    Cancel
                </a>
                <button 
                    type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Update Hall
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
