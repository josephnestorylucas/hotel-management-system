@extends('layouts.app')

@section('title', 'Add Pass Type')
@section('page-title', 'Events')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Add Pass Type</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <a href="{{ route('organizations.events.passes.index', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Passes</a>
    </div>

    <form method="POST" action="{{ route('organizations.events.passes.store', [$organization, $event]) }}" class="space-y-6">
        @csrf
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Pass Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pass Type *</label>
                    <select name="tier_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="attendee" {{ old('tier_type') === 'attendee' ? 'selected' : '' }}>Attendee</option>
                        <option value="speaker" {{ old('tier_type') === 'speaker' ? 'selected' : '' }}>Speaker</option>
                        <option value="moderator" {{ old('tier_type') === 'moderator' ? 'selected' : '' }}>Moderator</option>
                        <option value="backdoor" {{ old('tier_type') === 'backdoor' ? 'selected' : '' }}>Backdoor</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Access Type *</label>
                    <select name="access_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all-sessions" {{ old('access_type') === 'all-sessions' ? 'selected' : '' }}>All Sessions</option>
                        @foreach($schedules as $schedule)
                        <option value="session-{{ $schedule->id }}" {{ old('access_type') === 'session-' . $schedule->id ? 'selected' : '' }}>{{ $schedule->name }} ({{ $schedule->start_datetime->format('M d, H:i') }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Badge Color</label>
                    <input type="color" name="color" value="{{ old('color', '#3B82F6') }}" class="w-full h-10 px-1 py-1 border border-gray-300 rounded-lg">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('organizations.events.passes.index', [$organization, $event]) }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-lg hover:shadow-lg transition-all">Create Pass Type</button>
        </div>
    </form>
</div>
@endsection
