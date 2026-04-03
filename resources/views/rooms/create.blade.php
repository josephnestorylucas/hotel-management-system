{{-- resources/views/rooms/create.blade.php --}}
@extends('layouts.app')

@section('title', __('rooms.create_room'))
@section('page-title', __('rooms.title'))

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
            <h2 class="text-xl font-extrabold text-secondary">{{ __('rooms.create_room') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('rooms.add_new_subtitle') }}</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('rooms.store') }}" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- Floor -->
                <div>
                    <label for="floor_id" class="block text-sm font-semibold text-secondary mb-2">
                        {{ __('rooms.fields.floor') }} <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="floor_id" 
                        id="floor_id"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('floor_id') border-red-500 @enderror"
                        required>
                        <option value="">{{ __('rooms.filters.select_floor') }}</option>
                        @foreach($floors as $floor)
                        <option value="{{ $floor->id }}" {{ old('floor_id') == $floor->id ? 'selected' : '' }}>
                            {{ $floor->building->name }} - {{ $floor->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('floor_id')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Room Type -->
                <div>
                    <label for="room_type_id" class="block text-sm font-semibold text-secondary mb-2">
                        {{ __('rooms.fields.room_type') }} <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="room_type_id" 
                        id="room_type_id"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('room_type_id') border-red-500 @enderror"
                        required>
                        <option value="">{{ __('rooms.filters.select_room_type') }}</option>
                        @foreach($roomTypes as $roomType)
                        <option value="{{ $roomType->id }}" {{ old('room_type_id') == $roomType->id ? 'selected' : '' }}>
                            {{ $roomType->name }} ({{ $roomType->code }}) - ${{ number_format($roomType->base_rate, 2) }}
                        </option>
                        @endforeach
                    </select>
                    @error('room_type_id')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Room Number -->
                <div>
                    <label for="room_number" class="block text-sm font-semibold text-secondary mb-2">
                        {{ __('rooms.fields.room_number') }} <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="room_number" 
                        id="room_number"
                        value="{{ old('room_number') }}" 
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('room_number') border-red-500 @enderror"
                        placeholder="{{ __('rooms.filters.room_number_placeholder') }}"
                        required>
                    @error('room_number')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-semibold text-secondary mb-2">
                        {{ __('rooms.fields.status') }} <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="status" 
                        id="status"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('status') border-red-500 @enderror"
                        required>
                        <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>{{ __('rooms.status.available') }}</option>
                        <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>{{ __('rooms.status.occupied') }}</option>
                        <option value="reserved" {{ old('status') == 'reserved' ? 'selected' : '' }}>{{ __('rooms.status.reserved') }}</option>
                        <option value="dirty" {{ old('status') == 'dirty' ? 'selected' : '' }}>{{ __('rooms.status.needs_cleaning') }}</option>
                        <option value="out_of_order" {{ old('status') == 'out_of_order' ? 'selected' : '' }}>{{ __('rooms.status.out_of_order') }}</option>
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

                <!-- Active Status -->
                <div class="flex items-center gap-3">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        id="is_active"
                        value="1" 
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-5 h-5 text-primary border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <label for="is_active" class="text-sm font-semibold text-secondary cursor-pointer">
                        {{ __('rooms.fields.active') }}
                    </label>
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
                            <p class="font-bold text-secondary mb-1">{{ __('rooms.info.room_numbering_tips') }}</p>
                            <ul class="list-disc list-inside space-y-1 text-gray-600">
                                <li>{{ __('rooms.info.floor_prefix') }}</li>
                                <li>{{ __('rooms.info.consistent_numbering') }}</li>
                                <li>{{ __('rooms.info.building_codes') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('rooms.index') }}" 
                   class="px-6 py-2.5 text-sm font-semibold text-secondary bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                    {{ __('rooms.actions.cancel') }}
                </a>
                <button 
                    type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                    {{ __('rooms.create_room') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection