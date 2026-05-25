@extends('layouts.app')

@section('title', 'Create Event')
@section('page-title', 'Organizations')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Create Event</h2>
            <p class="text-sm text-gray-500 mt-1">For {{ $organization->name }}</p>
        </div>
        <a href="{{ route('organizations.events-list', $organization) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Events</a>
    </div>

    <form method="POST" action="{{ route('organizations.events.store', $organization) }}" class="space-y-6">
        @csrf
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Event Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conference Type</label>
                    <select name="conference_type_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select type...</option>
                        @foreach($conferenceTypes as $type)
                        <option value="{{ $type->id }}" {{ old('conference_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conference Hall *</label>
                    <select name="conference_hall_id" id="conference_hall_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select hall...</option>
                        @foreach($conferenceHalls as $hall)
                        @php
                            $hallRate = (float) $hall->hourly_rate;
                            $hallCurrency = $hall->currency ?? 'USD';
                            $systemCurrency = \App\Helpers\CurrencyHelper::getDefaultCurrency();
                            $convertedRate = $hallCurrency !== $systemCurrency ? \App\Helpers\CurrencyHelper::convert($hallRate, $hallCurrency, $systemCurrency) : $hallRate;
                        @endphp
                        <option value="{{ $hall->id }}" data-capacity="{{ $hall->capacity }}" data-rate="{{ $convertedRate }}" data-rate-display="{{ \App\Helpers\CurrencyHelper::formatCurrency($convertedRate, $systemCurrency) }}/hr" {{ old('conference_hall_id') == $hall->id ? 'selected' : '' }}>{{ $hall->name }} {{ $hall->capacity ? '(' . $hall->capacity . ' pax)' : '' }}</option>
                        @endforeach
                    </select>
                    @error('conference_hall_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Visibility *</label>
                    <select name="visibility" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="public" {{ old('visibility') === 'public' ? 'selected' : '' }}>Public</option>
                        <option value="private" {{ old('visibility') === 'private' ? 'selected' : '' }}>Private</option>
                        <option value="organization-only" {{ old('visibility') === 'organization-only' ? 'selected' : '' }}>Organization Only</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacity <span id="capacity-hint" class="text-xs text-gray-400 font-normal"></span></label>
                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity') }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Auto-filled from hall">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expected Attendance</label>
                    <input type="number" name="expected_attendance" value="{{ old('expected_attendance') }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Event Rate <span id="rate-hint" class="text-xs text-gray-400 font-normal"></span></label>
                    <div class="relative">
                        <input type="number" name="event_rate" id="event_rate" value="{{ old('event_rate') }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-blue-50" placeholder="Auto-filled from hall rate" readonly>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Theme Color</label>
                    <input type="color" name="theme_color" value="{{ old('theme_color', '#0066FF') }}" class="w-full h-10 px-1 py-1 border border-gray-300 rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('organizations.events-list', $organization) }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-lg hover:shadow-lg transition-all">Create Event</button>
        </div>
    </form>
</div>

<script>
document.getElementById('conference_hall_id').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    const cap = opt.dataset.capacity;
    const rate = opt.dataset.rate;
    const capacityInput = document.getElementById('capacity');
    const eventRateInput = document.getElementById('event_rate');
    const capacityHint = document.getElementById('capacity-hint');
    const rateHint = document.getElementById('rate-hint');

    if (cap) {
        capacityInput.value = cap;
        capacityHint.textContent = '(from hall)';
    } else {
        capacityInput.value = '';
        capacityHint.textContent = '';
    }

    if (rate) {
        eventRateInput.value = rate;
        rateHint.textContent = '(hall rate: ' + opt.dataset.rateDisplay + ')';
    } else {
        eventRateInput.value = '';
        rateHint.textContent = '';
    }
});
</script>
@endsection
