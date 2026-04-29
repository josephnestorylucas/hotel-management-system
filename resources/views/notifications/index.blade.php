@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Notifications</h1>
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to Dashboard</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @forelse($notifications as $notif)
        <form method="POST" action="{{ route('notifications.read', $notif) }}" class="block">
            @csrf
            <button type="submit"
                class="w-full text-left px-6 py-4 border-b hover:bg-gray-50 transition {{ $notif->is_read ? 'opacity-60' : 'bg-blue-50/30' }}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        @if(!$notif->is_read)
                        <span class="w-2 h-2 bg-blue-500 rounded-full flex-shrink-0"></span>
                        @endif
                        <span class="font-semibold text-gray-800 text-sm">{{ $notif->title }}</span>
                        @if($notif->type)
                        <span class="text-xs px-2 py-0.5 rounded-full
                            @if($notif->type === 'warning') bg-yellow-100 text-yellow-700
                            @elseif($notif->type === 'error') bg-red-100 text-red-700
                            @elseif($notif->type === 'success') bg-green-100 text-green-700
                            @else bg-blue-100 text-blue-700
                            @endif">
                            {{ ucfirst($notif->type) }}
                        </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ $notif->body }}</p>
                </div>
                <span class="text-xs text-gray-400 whitespace-nowrap ml-4">{{ $notif->created_at->diffForHumans() }}</span>
            </div>
            </button>
        </form>
        @empty
        <div class="px-6 py-12 text-center">
            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-gray-400">No notifications yet.</p>
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
