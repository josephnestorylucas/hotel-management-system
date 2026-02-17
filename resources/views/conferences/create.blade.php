{{-- resources/views/conferences/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Conference')
@section('page-title', 'Conferences')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Create Conference</h2>
            <p class="text-sm text-gray-500 mt-1">Create a conference event from an existing hall booking</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('conferences.store') }}" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- Booking Selection -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Select Hall Booking
                    </h3>

                    <div>
                        <label for="conference_booking_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Conference Booking <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="conference_booking_id" 
                            id="conference_booking_id"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('conference_booking_id') border-red-500 @enderror">
                            <option value="">Select a confirmed booking</option>
                            @foreach($bookings as $booking)
                            <option value="{{ $booking->id }}" 
                                    data-date="{{ $booking->booking_date->format('Y-m-d') }}"
                                    data-start="{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}"
                                    data-end="{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}"
                                    {{ old('conference_booking_id') == $booking->id ? 'selected' : '' }}>
                                {{ $booking->conferenceHall->name }} - {{ $booking->booking_date->format('M d, Y') }} 
                                ({{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}) 
                                - {{ $booking->guest->full_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('conference_booking_id')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-500">Only confirmed bookings without conferences are shown</p>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Conference Details -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Event Information
                    </h3>

                    <!-- Title -->
                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Conference Title <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="title" 
                            id="title"
                            value="{{ old('title') }}"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('title') border-red-500 @enderror"
                            placeholder="e.g., Annual Tech Summit 2026">
                        @error('title')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-gray-400 text-xs">(Optional)</span>
                        </label>
                        <textarea 
                            name="description" 
                            id="description"
                            rows="4"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('description') border-red-500 @enderror"
                            placeholder="Brief overview of the conference...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Schedule -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-2">
                                Start Date & Time <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="datetime-local" 
                                name="start_datetime" 
                                id="start_datetime"
                                value="{{ old('start_datetime') }}"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('start_datetime') border-red-500 @enderror">
                            @error('start_datetime')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_datetime" class="block text-sm font-medium text-gray-700 mb-2">
                                End Date & Time <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="datetime-local" 
                                name="end_datetime" 
                                id="end_datetime"
                                value="{{ old('end_datetime') }}"
                                required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('end_datetime') border-red-500 @enderror">
                            @error('end_datetime')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Next Steps:</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-700">
                                <li>After creating, add participants to the conference</li>
                                <li>Print badges for confirmed participants</li>
                                <li>Use the check-in dashboard on event day</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('conferences.index') }}" 
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                    Cancel
                </a>
                <button 
                    type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Create Conference
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingSelect = document.getElementById('conference_booking_id');
    const startDatetime = document.getElementById('start_datetime');
    const endDatetime = document.getElementById('end_datetime');

    bookingSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            const date = selected.dataset.date;
            const start = selected.dataset.start;
            const end = selected.dataset.end;
            
            startDatetime.value = `${date}T${start}`;
            endDatetime.value = `${date}T${end}`;
        }
    });
});
</script>
@endsection 