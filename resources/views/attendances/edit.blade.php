@extends('layouts.app')

@section('title', 'Edit Attendee')
@section('page-title', 'Events')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Edit Attendee</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $attendance->full_name }}</p>
        </div>
        <a href="{{ route('organizations.events.attendances.show', [$organization, $event, $attendance]) }}" class="text-primary hover:text-blue-700 font-semibold">Back</a>
    </div>

    <form method="POST" action="{{ route('organizations.events.attendances.update', [$organization, $event, $attendance]) }}" class="space-y-6">
        @csrf @method('PUT')
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $attendance->first_name) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $attendance->last_name) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $attendance->email) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $attendance->phone) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pass Type</label>
                    <select name="event_pass_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">No pass</option>
                        @foreach($passes as $pass)
                        <option value="{{ $pass->id }}" {{ old('event_pass_id', $attendance->event_pass_id) == $pass->id ? 'selected' : '' }}>{{ $pass->tier_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pass Category</label>
                    <select name="pass_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="attendee" {{ old('pass_type', $attendance->pass_type) === 'attendee' ? 'selected' : '' }}>Attendee</option>
                        <option value="speaker" {{ old('pass_type', $attendance->pass_type) === 'speaker' ? 'selected' : '' }}>Speaker</option>
                        <option value="moderator" {{ old('pass_type', $attendance->pass_type) === 'moderator' ? 'selected' : '' }}>Moderator</option>
                        <option value="backdoor" {{ old('pass_type', $attendance->pass_type) === 'backdoor' ? 'selected' : '' }}>Backdoor</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="registration_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach(['pending','confirmed','cancelled','no_show'] as $status)
                        <option value="{{ $status }}" {{ old('registration_status', $attendance->registration_status) === $status ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('organizations.events.attendances.show', [$organization, $event, $attendance]) }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-lg hover:shadow-lg transition-all">Update Attendee</button>
        </div>
    </form>
</div>
@endsection
