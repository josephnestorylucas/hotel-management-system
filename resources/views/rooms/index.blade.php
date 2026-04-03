{{-- resources/views/rooms/index.blade.php --}}
@extends('layouts.app')

@section('title', __('rooms.title'))
@section('page-title', __('rooms.title'))

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ __('rooms.rooms') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('rooms.manage_subtitle') }}</p>
        </div>
        <a href="{{ route('rooms.create') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('rooms.add_room') }}
        </a>
    </div>

    <!-- Filter Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $rooms->where('status', 'available')->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('rooms.status.available') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $rooms->where('status', 'occupied')->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('rooms.status.occupied') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $rooms->where('status', 'reserved')->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('rooms.status.reserved') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $rooms->where('status', 'dirty')->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('rooms.status.dirty') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $rooms->where('status', 'out_of_order')->count() }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('rooms.status.out_of_order') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rooms Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('rooms.table.room') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('rooms.table.location') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('rooms.table.type') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('rooms.table.rate') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('rooms.table.status') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('rooms.fields.active') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('rooms.table.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($rooms as $room)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center text-white font-bold shadow-lg">
                                {{ substr($room->room_number, -2) }}
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-semibold text-secondary">{{ $room->room_number }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-secondary">{{ $room->floor->building->name }}</div>
                        <div class="text-xs text-primary">{{ $room->floor->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-secondary">{{ $room->roomType->name }}</div>
                        <div class="text-xs text-primary">{{ $room->roomType->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm font-bold text-secondary">${{ number_format($room->roomType->base_rate, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @include('components.room-status-badge', ['status' => $room->status])
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $room->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $room->is_active ? __('rooms.fields.active') : __('rooms.fields.inactive') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex items-center gap-3">
                            <a href="{{ route('rooms.edit', $room) }}" class="text-primary hover:text-blue-700 font-semibold">{{ __('rooms.actions.edit') }}</a>
                            <form method="POST" action="{{ route('rooms.destroy', $room) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 font-semibold" onclick="return confirm(&quot;{{ __('rooms.actions.confirm_delete') }}&quot;)">{{ __('rooms.actions.delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-primary/10 to-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-secondary">{{ __('rooms.messages.no_rooms') }}</h3>
                        <p class="mt-2 text-sm text-gray-500">{{ __('rooms.messages.no_rooms_subtitle') }}</p>
                        <div class="mt-6">
                            <a href="{{ route('rooms.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                {{ __('rooms.add_room') }}
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($rooms->hasPages())
    <div class="mt-6">
        {{ $rooms->links() }}
    </div>
    @endif
</div>
@endsection