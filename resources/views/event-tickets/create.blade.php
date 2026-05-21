@extends('layouts.app')

@section('title', 'Add Ticket Tier')
@section('page-title', 'Events')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Add Ticket Tier</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <a href="{{ route('organizations.events.tickets.index', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Tickets</a>
    </div>

    <form method="POST" action="{{ route('organizations.events.tickets.store', [$organization, $event]) }}" class="space-y-6">
        @csrf
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Ticket Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tier Name *</label>
                    <input type="text" name="tier_name" value="{{ old('tier_name') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Early Bird, Regular, VIP">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tier Type *</label>
                    <select name="tier_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="standard" {{ old('tier_type') === 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="vip" {{ old('tier_type') === 'vip' ? 'selected' : '' }}>VIP</option>
                        <option value="exhibitor" {{ old('tier_type') === 'exhibitor' ? 'selected' : '' }}>Exhibitor</option>
                        <option value="speaker" {{ old('tier_type') === 'speaker' ? 'selected' : '' }}>Speaker</option>
                        <option value="student" {{ old('tier_type') === 'student' ? 'selected' : '' }}>Student</option>
                        <option value="corporate" {{ old('tier_type') === 'corporate' ? 'selected' : '' }}>Corporate</option>
                        <option value="media" {{ old('tier_type') === 'media' ? 'selected' : '' }}>Media</option>
                        <option value="press" {{ old('tier_type') === 'press' ? 'selected' : '' }}>Press</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                    <input type="number" name="price" value="{{ old('price', '0') }}" min="0" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Available</label>
                    <input type="number" name="quantity_available" value="{{ old('quantity_available') }}" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Leave empty for unlimited">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Access Type *</label>
                    <select name="access_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="all-sessions" {{ old('access_type') === 'all-sessions' ? 'selected' : '' }}>All Sessions</option>
                        <option value="single-session" {{ old('access_type') === 'single-session' ? 'selected' : '' }}>Single Session</option>
                        <option value="day-pass" {{ old('access_type') === 'day-pass' ? 'selected' : '' }}>Day Pass</option>
                        <option value="unlimited" {{ old('access_type') === 'unlimited' ? 'selected' : '' }}>Unlimited</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Early Bird Deadline</label>
                    <input type="date" name="early_bird_until" value="{{ old('early_bird_until') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sale Start Date</label>
                    <input type="date" name="sale_start_date" value="{{ old('sale_start_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sale End Date</label>
                    <input type="date" name="sale_end_date" value="{{ old('sale_end_date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bulk Discount %</label>
                    <input type="number" name="bulk_discount_percent" value="{{ old('bulk_discount_percent') }}" min="0" max="100" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Badge Color</label>
                    <input type="color" name="color" value="{{ old('color', '#3B82F6') }}" class="w-full h-10 px-1 py-1 border border-gray-300 rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="includes_guide" value="1" {{ old('includes_guide') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-gray-700">Includes attendee guide/manual</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('organizations.events.tickets.index', [$organization, $event]) }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-lg hover:shadow-lg transition-all">Create Ticket Tier</button>
        </div>
    </form>
</div>
@endsection
