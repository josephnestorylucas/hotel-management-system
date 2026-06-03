@extends('layouts.app')

@section('title', 'My Cleaning Tasks')
@section('page-title', 'My Cleaning Tasks')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">My Assigned Rooms</h2>
            <p class="text-sm text-gray-500 mt-1">Mark rooms as cleaned when done. Supervisor will confirm.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-xs text-gray-500 font-medium uppercase">Total Assigned</div>
            <div class="text-2xl font-extrabold text-secondary mt-1">{{ $assignedRooms->count() }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-xs text-gray-500 font-medium uppercase">Pending</div>
            <div class="text-2xl font-extrabold text-yellow-600 mt-1">{{ $assignedRooms->whereNull('cleaning_completed_at')->count() }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-xs text-gray-500 font-medium uppercase">Done</div>
            <div class="text-2xl font-extrabold text-green-600 mt-1">{{ $assignedRooms->whereNotNull('cleaning_completed_at')->count() }}</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        @if($assignedRooms->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gradient-to-r from-blue-50 to-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Room</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Floor</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Room Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Reason</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Assigned At</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Progress</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($assignedRooms as $room)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-secondary">{{ $room->room_number }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $room->roomType->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $room->floor->name ?? '—' }}</td>
                        <td class="px-6 py-4">@include('components.room-status-badge', ['status' => $room->status])</td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                            @if($room->status === 'out_of_order')
                                <span class="text-red-600">{{ $room->out_of_order_reason }}</span>
                                <div class="text-xs text-gray-400 mt-0.5">by {{ $room->outOfOrderBy?->name ?? '—' }} {{ $room->out_of_order_set_at?->diffForHumans() }}</div>
                            @elseif($room->status === 'occupied')
                                <span class="text-blue-600">Guest in room</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $room->cleaning_assigned_at?->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4">
                            @if($room->cleaning_completed_at)
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Done — Awaiting Confirmation</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if(!$room->cleaning_completed_at)
                                <form method="POST" action="{{ route('cleaning.mark-done', $room) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">Mark Done</button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">Awaiting supervisor</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-16 text-center">
            <div class="w-16 h-16 bg-gradient-to-br from-primary/10 to-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-secondary">No rooms assigned</h3>
            <p class="mt-2 text-sm text-gray-500">You have no rooms assigned for cleaning or maintenance right now.</p>
        </div>
        @endif
    </div>
</div>
@endsection
