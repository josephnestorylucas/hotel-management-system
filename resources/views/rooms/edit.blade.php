{{-- resources/views/rooms/edit.blade.php --}}
@extends('layouts.app')

@section('title', __('rooms.edit_room'))
@section('page-title', __('rooms.title'))

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
            <h2 class="text-xl font-extrabold text-secondary">{{ __('rooms.edit_room') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('rooms.update_subtitle') }}</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('rooms.update', $room) }}" class="p-6">
            @csrf
            @method('PUT')

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
                        <option value="{{ $floor->id }}" {{ old('floor_id', $room->floor_id) == $floor->id ? 'selected' : '' }}>
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
                        <option value="{{ $roomType->id }}" {{ old('room_type_id', $room->room_type_id) == $roomType->id ? 'selected' : '' }}>
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
                        value="{{ old('room_number', $room->room_number) }}" 
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('room_number') border-red-500 @enderror"
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
                        <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>{{ __('rooms.status.available') }}</option>
                        <option value="occupied" {{ old('status', $room->status) == 'occupied' ? 'selected' : '' }}>{{ __('rooms.status.occupied') }}</option>
                        <option value="reserved" {{ old('status', $room->status) == 'reserved' ? 'selected' : '' }}>{{ __('rooms.status.reserved') }}</option>
                        <option value="dirty" {{ old('status', $room->status) == 'dirty' ? 'selected' : '' }}>{{ __('rooms.status.needs_cleaning') }}</option>
                        <option value="out_of_order" {{ old('status', $room->status) == 'out_of_order' ? 'selected' : '' }}>{{ __('rooms.status.out_of_order') }}</option>
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
                        {{ old('is_active', $room->is_active) ? 'checked' : '' }}
                        class="w-5 h-5 text-primary border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <label for="is_active" class="text-sm font-semibold text-secondary cursor-pointer">
                        {{ __('rooms.fields.active') }}
                    </label>
                </div>

                <!-- Room Info -->
                <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-4 border border-blue-100">
                    <h4 class="text-sm font-bold text-secondary mb-3">{{ __('rooms.info.room_information') }}</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">{{ __('rooms.fields.location') }}:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $room->floor->building->name }} - {{ $room->floor->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('rooms.fields.type') }}:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $room->roomType->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('rooms.fields.base_rate') }}:</span>
                            <span class="text-secondary font-semibold ml-2">${{ number_format($room->roomType->base_rate, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('rooms.fields.created') }}:</span>
                            <span class="text-secondary font-semibold ml-2">{{ $room->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Current Status Warning -->
                @if($room->status == 'occupied' || $room->status == 'reserved')
                <div class="bg-gradient-to-br from-yellow-50 to-amber-50 border border-yellow-200 rounded-xl p-4">
                    <div class="flex gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-yellow-100 to-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-sm">
                            <p class="font-bold text-yellow-800">{{ __('rooms.messages.room_currently') }} {{ __('rooms.status.' . $room->status) }}</p>
                            <p class="mt-1 text-yellow-700">{{ __('rooms.messages.status_warning') }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-100">
                <button 
                    type="button"
                    onclick="if(confirm(&quot;{{ __('rooms.actions.confirm_delete_room') }}&quot;)) { document.getElementById('delete-form').submit(); }"
                    class="px-6 py-2.5 text-sm font-semibold text-red-700 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-all">
                    {{ __('rooms.delete_room') }}
                </button>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('rooms.index') }}" 
                       class="px-6 py-2.5 text-sm font-semibold text-secondary bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                        {{ __('rooms.actions.cancel') }}
                    </a>
                    <button 
                        type="submit"
                        class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                        {{ __('rooms.update_room') }}
                    </button>
                </div>
            </div>
        </form>

        <!-- Hidden Delete Form -->
        <form id="delete-form" method="POST" action="{{ route('rooms.destroy', $room) }}" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection