@extends('layouts.app')

@section('title', 'Create Broadcast')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Broadcast</h1>
        <a href="{{ route('admin.broadcasts.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to Broadcasts</a>
    </div>

    <form action="{{ route('admin.broadcasts.store') }}" method="POST" class="bg-white rounded-lg shadow p-6 space-y-6">
        @csrf

        {{-- Title --}}
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                   placeholder="e.g., Weekend Special Offer">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Type --}}
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
            <select name="type" id="type" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="offer" {{ old('type') === 'offer' ? 'selected' : '' }}>Offer</option>
                <option value="event" {{ old('type') === 'event' ? 'selected' : '' }}>Event</option>
                <option value="announcement" {{ old('type') === 'announcement' ? 'selected' : '' }}>Announcement</option>
            </select>
        </div>

        {{-- Body (Email Content) --}}
        <div>
            <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Email Body *</label>
            <textarea name="body" id="body" rows="6" required
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Full content for the email...">{{ old('body') }}</textarea>
            @error('body') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- SMS Message --}}
        <div>
            <label for="sms_message" class="block text-sm font-medium text-gray-700 mb-1">SMS Message <span class="text-gray-400">(max 160 chars)</span></label>
            <textarea name="sms_message" id="sms_message" rows="2" maxlength="160"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500"
                      placeholder="Short version for SMS (max 160 chars)...">{{ old('sms_message') }}</textarea>
            <p class="text-xs text-gray-400 mt-1"><span id="sms-count">0</span>/160 characters</p>
            @error('sms_message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Target --}}
            <div>
                <label for="target" class="block text-sm font-medium text-gray-700 mb-1">Target Audience *</label>
                <select name="target" id="target" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="all" {{ old('target') === 'all' ? 'selected' : '' }}>All Guests</option>
                    <option value="Silver" {{ old('target') === 'Silver' ? 'selected' : '' }}>Silver &amp; Above</option>
                    <option value="Gold" {{ old('target') === 'Gold' ? 'selected' : '' }}>Gold &amp; Above</option>
                    <option value="Platinum" {{ old('target') === 'Platinum' ? 'selected' : '' }}>Platinum Only</option>
                    <option value="guests" {{ old('target') === 'guests' ? 'selected' : '' }}>Hotel Guests Only</option>
                    <option value="walkin" {{ old('target') === 'walkin' ? 'selected' : '' }}>Walk-in Customers</option>
                </select>
            </div>

            {{-- Channels --}}
            <div>
                <label for="channels" class="block text-sm font-medium text-gray-700 mb-1">Channels *</label>
                <select name="channels" id="channels" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="both" {{ old('channels') === 'both' ? 'selected' : '' }}>Email + SMS</option>
                    <option value="email" {{ old('channels') === 'email' ? 'selected' : '' }}>Email Only</option>
                    <option value="sms" {{ old('channels') === 'sms' ? 'selected' : '' }}>SMS Only</option>
                </select>
            </div>

            {{-- Schedule --}}
            <div>
                <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">Schedule <span class="text-gray-400">(optional)</span></label>
                <input type="datetime-local" name="scheduled_at" id="scheduled_at" value="{{ old('scheduled_at') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 pt-4 border-t">
            <button type="submit" name="action" value="save"
                    class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 text-sm font-medium">
                Save as Draft
            </button>
            <button type="submit" name="action" value="send"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium"
                    onclick="return confirm('Send this broadcast immediately?')">
                Save &amp; Send Now
            </button>
            <a href="{{ route('admin.broadcasts.index') }}" class="text-sm text-gray-500 hover:text-gray-700 ml-auto">Cancel</a>
        </div>
    </form>
</div>

<script>
    const smsInput = document.getElementById('sms_message');
    const smsCount = document.getElementById('sms-count');
    if (smsInput && smsCount) {
        smsInput.addEventListener('input', () => { smsCount.textContent = smsInput.value.length; });
        smsCount.textContent = smsInput.value.length;
    }
</script>
@endsection
