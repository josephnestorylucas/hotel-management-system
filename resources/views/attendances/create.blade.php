@extends('layouts.app')

@section('title', 'Register Attendee')
@section('page-title', 'Events')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Register Attendee</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <a href="{{ route('organizations.events.attendances.index', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Attendees</a>
    </div>

    <form method="POST" action="{{ route('organizations.events.attendances.store', [$organization, $event]) }}" class="space-y-6">
        @csrf
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Attendee Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pass Type *</label>
                    <select name="event_pass_id" id="event_pass_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select a pass</option>
                        @foreach($passes as $pass)
                        @php
                            $accessLabel = 'All Sessions';
                            if (str_starts_with($pass->access_type, 'session-')) {
                                $sessionId = substr($pass->access_type, 8);
                                $session = $event->schedules->firstWhere('id', $sessionId);
                                $accessLabel = $session ? $session->name : 'Session';
                            }
                        @endphp
                        <option value="{{ $pass->id }}" data-tier="{{ $pass->tier_type }}" {{ old('event_pass_id') == $pass->id ? 'selected' : '' }}>{{ ucfirst($pass->tier_type) }} - {{ $accessLabel }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="pass_type" id="pass_type_input" value="{{ old('pass_type') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Registration Type</label>
                    <select name="registration_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="individual" {{ old('registration_type') === 'individual' ? 'selected' : '' }}>Individual</option>
                        <option value="walked_in" {{ old('registration_type') === 'walked_in' ? 'selected' : '' }}>Walked In</option>
                        <option value="complimentary" {{ old('registration_type') === 'complimentary' ? 'selected' : '' }}>Complimentary</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dietary Requirements</label>
                    <input type="text" name="dietary_requirements" value="{{ old('dietary_requirements') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('organizations.events.attendances.index', [$organization, $event]) }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-lg hover:shadow-lg transition-all">Register Attendee</button>
        </div>
    </form>
</div>

<script>
document.getElementById('event_pass_id').addEventListener('change', function() {
    var opt = this.options[this.selectedIndex];
    var tier = opt.getAttribute('data-tier');
    document.getElementById('pass_type_input').value = tier || 'attendee';
});
</script>
@endsection
