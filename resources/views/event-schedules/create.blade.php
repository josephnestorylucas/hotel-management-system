@extends('layouts.app')

@section('title', 'Add Session')
@section('page-title', 'Events')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Add Session</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <a href="{{ route('organizations.events.schedules.index', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Sessions</a>
    </div>

    <form method="POST" action="{{ route('organizations.events.schedules.store', [$organization, $event]) }}" class="space-y-6">
        @csrf
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Session Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Session Number *</label>
                    <input type="number" name="session_number" value="{{ old('session_number', $nextSessionNumber) }}" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Session Type *</label>
                    <select name="session_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="presentation" {{ old('session_type') === 'presentation' ? 'selected' : '' }}>Presentation</option>
                        <option value="keynote" {{ old('session_type') === 'keynote' ? 'selected' : '' }}>Keynote</option>
                        <option value="workshop" {{ old('session_type') === 'workshop' ? 'selected' : '' }}>Workshop</option>
                        <option value="panel" {{ old('session_type') === 'panel' ? 'selected' : '' }}>Panel</option>
                        <option value="networking" {{ old('session_type') === 'networking' ? 'selected' : '' }}>Networking</option>
                        <option value="break" {{ old('session_type') === 'break' ? 'selected' : '' }}>Break</option>
                        <option value="other" {{ old('session_type') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Session Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Opening Keynote, Workshop A, Networking Lunch">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date & Time *</label>
                    <input type="datetime-local" name="start_datetime" value="{{ old('start_datetime') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date & Time *</label>
                    <input type="datetime-local" name="end_datetime" value="{{ old('end_datetime') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                @if($hallName)
                <input type="hidden" name="location" value="{{ $hallName }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" value="{{ $hallName }}" disabled class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-600">
                </div>
                @endif
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('organizations.events.schedules.index', [$organization, $event]) }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-lg hover:shadow-lg transition-all">Add Session</button>
        </div>
    </form>
</div>
@endsection
