{{-- resources/views/conferences/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Conference')
@section('page-title', 'Conferences')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Create Conference</h2>
            <p class="text-sm text-gray-500 mt-1">Set up a new conference event</p>
        </div>

        <form method="POST" action="{{ route('conferences.store') }}" class="p-6">
            @csrf
            <div class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Conference Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                        placeholder="e.g., Annual Tech Summit 2026">
                    @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                </div>

                <div class="border-t border-gray-200"></div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="conference_fee" class="block text-sm font-medium text-gray-700 mb-2">Conference Fee (TZS) <span class="text-red-500">*</span></label>
                        <input type="number" name="conference_fee" id="conference_fee" value="{{ old('conference_fee', 0) }}" min="0" step="0.01" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('conference_fee') border-red-500 @enderror">
                        @error('conference_fee')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-gray-500">This charge lives on the conference, not on attendees.</p>
                    </div>
                    <div>
                        <label for="institution_id" class="block text-sm font-medium text-gray-700 mb-2">Organizing Institution</label>
                        <select name="institution_id" id="institution_id"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">-- Select --</option>
                            @foreach(\App\Models\Institution::orderBy('name')->get() as $inst)
                            <option value="{{ $inst->id }}" {{ old('institution_id') == $inst->id ? 'selected' : '' }}>{{ $inst->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Venue</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="hall_name" class="block text-sm font-medium text-gray-700 mb-2">Venue Name</label>
                            <input type="text" name="hall_name" id="hall_name" value="{{ old('hall_name') }}"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="e.g., Hall A, Ballroom">
                        </div>
                        <div>
                            <label for="conference_hall_id" class="block text-sm font-medium text-gray-700 mb-2">Link Hall (optional)</label>
                            <select name="conference_hall_id" id="conference_hall_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- None --</option>
                                @foreach($halls as $hall)
                                <option value="{{ $hall->id }}" data-rate="{{ $hall->hourly_rate }}">
                                    {{ $hall->name }} ({{ $hall->capacity }} people)
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div id="booking-fields" class="hidden bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Hall Booking Details</h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="booking_date" class="block text-xs font-medium text-gray-700 mb-1">Date</label>
                            <input type="date" name="booking_date" id="booking_date" value="{{ old('booking_date') }}" min="{{ date('Y-m-d') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label for="start_time" class="block text-xs font-medium text-gray-700 mb-1">Start Time</label>
                            <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label for="end_time" class="block text-xs font-medium text-gray-700 mb-1">End Time</label>
                            <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-2">Conference Start <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="start_datetime" id="start_datetime" value="{{ old('start_datetime') }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="end_datetime" class="block text-sm font-medium text-gray-700 mb-2">Conference End <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="end_datetime" id="end_datetime" value="{{ old('end_datetime') }}" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">After creating:</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-700">
                                <li>Add participants and assign passes</li>
                                <li>Print passes for confirmed attendees</li>
                                <li>Use the scanning portal on event day</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('conferences.index') }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Create Conference</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hallSelect = document.getElementById('conference_hall_id');
    const bookingFields = document.getElementById('booking-fields');

    hallSelect.addEventListener('change', function() {
        if (this.value) {
            bookingFields.classList.remove('hidden');
        } else {
            bookingFields.classList.add('hidden');
        }
    });
});
</script>
@endsection
