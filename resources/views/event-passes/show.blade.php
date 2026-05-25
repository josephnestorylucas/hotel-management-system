@extends('layouts.app')

@section('title', ucfirst($pass->tier_type) . ' Pass')
@section('page-title', 'Events')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ ucfirst($pass->tier_type) }} Pass</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <a href="{{ route('organizations.events.passes.index', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Passes</a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pass Type</label>
                <p class="text-sm font-semibold text-secondary mt-1">
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                        @if($pass->tier_type === 'speaker') bg-purple-100 text-purple-700
                        @elseif($pass->tier_type === 'moderator') bg-blue-100 text-blue-700
                        @elseif($pass->tier_type === 'backdoor') bg-orange-100 text-orange-700
                        @else bg-gray-100 text-gray-700 @endif">
                        {{ ucfirst($pass->tier_type) }}
                    </span>
                </p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</label>
                <p class="text-2xl font-extrabold text-secondary mt-1">{{ $pass->attendances_count }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</label>
                <p class="text-2xl font-extrabold text-secondary mt-1">{{ $pass->quantity_remaining ?? 'Unlimited' }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Access</label>
                @php
                    $accessLabel = ucfirst(str_replace('-', ' ', $pass->access_type));
                    if (str_starts_with($pass->access_type, 'session-')) {
                        $sessionId = substr($pass->access_type, 8);
                        $session = $event->schedules->firstWhere('id', $sessionId);
                        $accessLabel = $session ? $session->name : 'Session';
                    }
                @endphp
                <p class="text-sm font-semibold text-secondary mt-1">{{ $accessLabel }}</p>
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Status</label>
                <p class="mt-1">
                    @if($pass->status === 'on_sale') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Active</span>
                    @elseif($pass->status === 'sold_out') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Sold Out</span>
                    @elseif($pass->status === 'archived') <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-500">Archived</span>
                    @else <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Draft</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
