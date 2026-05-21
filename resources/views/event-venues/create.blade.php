@extends('layouts.app')

@section('title', 'Assign Venue')
@section('page-title', 'Events')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Assign Venue</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <a href="{{ route('organizations.events.venues.index', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Venues</a>
    </div>

    <form method="POST" action="{{ route('organizations.events.venues.store', [$organization, $event]) }}" class="space-y-6">
        @csrf
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conference Hall *</label>
                    <select name="conference_hall_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select hall...</option>
                        @foreach($halls as $hall)
                        <option value="{{ $hall->id }}" {{ old('conference_hall_id') == $hall->id ? 'selected' : '' }}>{{ $hall->name }} ({{ $hall->capacity }} people)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Link to Booking</label>
                    <select name="booking_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">None</option>
                        @foreach($bookings as $booking)
                        <option value="{{ $booking->id }}" {{ old('booking_id') == $booking->id ? 'selected' : '' }}>{{ $booking->booking_number }} - {{ $booking->conferenceHall->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Setup Type *</label>
                    <select name="setup_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="theater" {{ old('setup_type') === 'theater' ? 'selected' : '' }}>Theater</option>
                        <option value="classroom" {{ old('setup_type') === 'classroom' ? 'selected' : '' }}>Classroom</option>
                        <option value="banquet" {{ old('setup_type') === 'banquet' ? 'selected' : '' }}>Banquet</option>
                        <option value="boardroom" {{ old('setup_type') === 'boardroom' ? 'selected' : '' }}>Boardroom</option>
                        <option value="hollow_square" {{ old('setup_type') === 'hollow_square' ? 'selected' : '' }}>Hollow Square</option>
                        <option value="cocktail" {{ old('setup_type') === 'cocktail' ? 'selected' : '' }}>Cocktail</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Setup Start</label>
                    <input type="datetime-local" name="expected_setup_start" value="{{ old('expected_setup_start') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Room Layout Notes</label>
                    <textarea name="room_layout_notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('room_layout_notes') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Special Requests</label>
                    <textarea name="special_requests" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('special_requests') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('organizations.events.venues.index', [$organization, $event]) }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-lg hover:shadow-lg transition-all">Assign Venue</button>
        </div>
    </form>
</div>
@endsection
