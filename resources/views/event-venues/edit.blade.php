@extends('layouts.app')

@section('title', 'Edit Venue')
@section('page-title', 'Events')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Edit Venue</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $venue->conferenceHall->name }}</p>
        </div>
        <a href="{{ route('organizations.events.venues.index', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Venues</a>
    </div>

    <form method="POST" action="{{ route('organizations.events.venues.update', [$organization, $event, $venue]) }}" class="space-y-6">
        @csrf @method('PUT')
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conference Hall *</label>
                    <select name="conference_hall_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($halls as $hall)
                        <option value="{{ $hall->id }}" {{ old('conference_hall_id', $venue->conference_hall_id) == $hall->id ? 'selected' : '' }}>{{ $hall->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Setup Type *</label>
                    <select name="setup_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach(['theater','classroom','banquet','boardroom','hollow_square','cocktail'] as $type)
                        <option value="{{ $type }}" {{ old('setup_type', $venue->setup_type) === $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach(['pending','confirmed','setup_in_progress','active','completed','cancelled'] as $status)
                        <option value="{{ $status }}" {{ old('status', $venue->status) === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Setup Start</label>
                    <input type="datetime-local" name="expected_setup_start" value="{{ old('expected_setup_start', $venue->expected_setup_start?->format('Y-m-d\TH:i')) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $venue->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('organizations.events.venues.index', [$organization, $event]) }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-lg hover:shadow-lg transition-all">Update Venue</button>
        </div>
    </form>
</div>
@endsection
