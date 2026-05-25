@extends('layouts.app')

@section('title', 'Passes - ' . $event->title)
@section('page-title', 'Events')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Pass Types</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('organizations.events.passes.create', [$organization, $event]) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add Pass Type
            </a>
            <a href="{{ route('organizations.events.show', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Event</a>
        </div>
    </div>

    @forelse($passes as $pass)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                @if($pass->color)
                <div class="w-4 h-12 rounded-full" style="background-color: {{ $pass->color }}"></div>
                @endif
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-bold text-secondary">{{ $pass->tier_name }}</h3>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                            @if($pass->status === 'on_sale') bg-green-100 text-green-700
                            @elseif($pass->status === 'sold_out') bg-red-100 text-red-700
                            @elseif($pass->status === 'archived') bg-gray-100 text-gray-500
                            @else bg-yellow-100 text-yellow-700 @endif">
                            {{ ucfirst(str_replace('_', ' ', $pass->status)) }}
                        </span>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                            @if($pass->tier_type === 'speaker') bg-purple-100 text-purple-700
                            @elseif($pass->tier_type === 'moderator') bg-blue-100 text-blue-700
                            @elseif($pass->tier_type === 'backdoor') bg-orange-100 text-orange-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ ucfirst($pass->tier_type) }}
                        </span>
                    </div>
                    @if($pass->description)
                    <p class="text-sm text-gray-500 mt-1">{{ $pass->description }}</p>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <div class="text-xs text-gray-500">
                    {{ $pass->attendances_count }} registered
                    @if($pass->quantity_available !== null) / {{ $pass->quantity_available }} available @else (unlimited) @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-100">
            <a href="{{ route('organizations.events.passes.show', [$organization, $event, $pass]) }}" class="text-primary hover:text-blue-700 font-semibold text-sm">View</a>
            <a href="{{ route('organizations.events.passes.edit', [$organization, $event, $pass]) }}" class="text-gray-600 hover:text-gray-800 font-semibold text-sm">Edit</a>
            @if($pass->status === 'draft')
            <form method="POST" action="{{ route('organizations.events.passes.activate', [$organization, $event, $pass]) }}" class="inline">@csrf
                <button type="submit" class="text-green-600 hover:text-green-700 font-semibold text-sm">Activate</button>
            </form>
            @endif
            @if($pass->attendances_count === 0)
            <form method="POST" action="{{ route('organizations.events.passes.destroy', [$organization, $event, $pass]) }}" class="inline">@csrf @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-700 font-semibold text-sm" onclick="return confirm('Delete this pass type?')">Delete</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-16 text-center">
        <h3 class="text-lg font-bold text-secondary">No pass types yet</h3>
        <p class="mt-2 text-sm text-gray-500">Create pass types (Speaker, Moderator, Backdoor, Attendee) to manage access control for your event.</p>
        <div class="mt-6">
            <a href="{{ route('organizations.events.passes.create', [$organization, $event]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Add First Pass Type
            </a>
        </div>
    </div>
    @endforelse
</div>
@endsection
