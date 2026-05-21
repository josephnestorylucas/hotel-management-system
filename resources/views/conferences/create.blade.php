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
            <p class="text-sm text-gray-500 mt-1">Select a conference hall and schedule your event</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('conferences.store') }}" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- Hall Selection -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Select Conference Hall
                    </h3>

                    <div>
                        <label for="conference_hall_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Conference Hall <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="conference_hall_id" 
                            id="conference_hall_id"
                            required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('conference_hall_id') border-red-500 @enderror">
                            <option value="">-- Select Hall --</option>
                            @foreach($halls as $hall)
                            <option value="{{ $hall->id }}" 
                                    data-rate="{{ $hall->hourly_rate }}"
                                    data-capacity="{{ $hall->capacity }}"
                                    data-currency="{{ $hall->currency ?? 'USD' }}"
                                    {{ old('conference_hall_id') == $hall->id ? 'selected' : '' }}>
                                {{ $hall->name }} (Capacity: {{ $hall->capacity }}) - {{ $hall->formatted_rate }}/hr
                            </option>
                            @endforeach
                        </select>
                        @error('conference_hall_id')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-gray-500">Only available halls are shown</p>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Schedule -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Schedule
                    </h3>

                    <div class="space-y-4">
                        <!-- Booking Date -->
                        <div>
                            <label for="booking_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Date <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="booking_date" 
                                id="booking_date"
                                value="{{ old('booking_date') }}"
                                required
                                min="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('booking_date') border-red-500 @enderror">
                            @error('booking_date')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Time Slots -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Start Time <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="time" 
                                    name="start_time" 
                                    id="start_time"
                                    value="{{ old('start_time') }}"
                                    required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('start_time') border-red-500 @enderror">
                                @error('start_time')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    End Time <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="time" 
                                    name="end_time" 
                                    id="end_time"
                                    value="{{ old('end_time') }}"
                                    required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('end_time') border-red-500 @enderror">
                                @error('end_time')
                                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Cost Estimate -->
                        <div id="cost-estimate" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">Estimated Cost:</span>
                                <span id="estimated-cost" class="text-lg font-semibold text-gray-900">-</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Includes 30-minute cleanup buffer between bookings</p>
                        </div>
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
                    Create Conference & Book Hall
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hallSelect = document.getElementById('conference_hall_id');
    const startTime = document.getElementById('start_time');
    const endTime = document.getElementById('end_time');
    const costEstimate = document.getElementById('cost-estimate');
    const estimatedCost = document.getElementById('estimated-cost');

    function calculateCost() {
        const selectedHall = hallSelect.options[hallSelect.selectedIndex];
        if (!selectedHall.value || !startTime.value || !endTime.value) {
            costEstimate.classList.add('hidden');
            return;
        }

        const rate = parseFloat(selectedHall.dataset.rate);
        const currency = selectedHall.dataset.currency || 'USD';
        
        const start = new Date(`2000-01-01T${startTime.value}`);
        const end = new Date(`2000-01-01T${endTime.value}`);
        
        if (end <= start) {
            costEstimate.classList.add('hidden');
            return;
        }

        const hours = (end - start) / (1000 * 60 * 60);
        const cost = (rate * hours).toFixed(2);
        
        estimatedCost.textContent = `${currency} ${cost}`;
        costEstimate.classList.remove('hidden');
    }

    hallSelect.addEventListener('change', calculateCost);
    startTime.addEventListener('change', calculateCost);
    endTime.addEventListener('change', calculateCost);
});
</script>
@endsection
