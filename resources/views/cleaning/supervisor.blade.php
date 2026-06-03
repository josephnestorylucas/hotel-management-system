@extends('layouts.app')

@section('title', 'Room Cleaning & Maintenance')
@section('page-title', 'Room Cleaning & Maintenance')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Rooms Needing Attention</h2>
            <p class="text-sm text-gray-500 mt-1">Assign house help and confirm cleaning or maintenance completion</p>
        </div>
    </div>

    {{-- Unassigned Rooms --}}
    @php $unassigned = $roomsNeedingAttention->whereNull('cleaning_assigned_to'); @endphp
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-white border-b">
            <h3 class="font-bold text-gray-800">
                Unassigned
                <span class="text-yellow-600 text-sm font-normal ml-2">({{ $unassigned->count() }})</span>
            </h3>
        </div>
        @if($unassigned->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Floor</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Assign</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($unassigned as $room)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-secondary">{{ $room->room_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $room->roomType->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $room->floor->name ?? '—' }}</td>
                        <td class="px-6 py-4">@include('components.room-status-badge', ['status' => $room->status])</td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                            @if($room->status === 'out_of_order')
                                <span class="text-red-600">{{ $room->out_of_order_reason }}</span>
                                <div class="text-xs text-gray-400">by {{ $room->outOfOrderBy?->name ?? '—' }} {{ $room->out_of_order_set_at?->diffForHumans() }}</div>
                            @elseif($room->status === 'occupied')
                                <span class="text-blue-600">Guest in room — needs cleaning</span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('cleaning.assign', $room) }}" class="flex gap-2">
                                @csrf
                                <select name="house_help_id" required class="border border-gray-200 rounded-lg text-sm px-3 py-1.5">
                                    <option value="">Select house help...</option>
                                    @foreach($houseHelpers as $helper)
                                        <option value="{{ $helper->id }}">{{ $helper->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="px-3 py-1.5 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">Assign</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-8 text-center text-gray-400 text-sm">All rooms are assigned.</div>
        @endif
    </div>

    {{-- Assigned / Pending Confirmation --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-white border-b">
            <h3 class="font-bold text-gray-800">
                Assigned / Pending Confirmation
                <span class="text-blue-600 text-sm font-normal ml-2">({{ $roomsAssigned->count() }})</span>
            </h3>
        </div>
        @if($roomsAssigned->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Assigned To</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Assigned At</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Completed</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($roomsAssigned as $room)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-secondary">{{ $room->room_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $room->roomType->name ?? '—' }}</td>
                        <td class="px-6 py-4">@include('components.room-status-badge', ['status' => $room->status])</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $room->cleaningAssignee->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $room->cleaning_assigned_at?->format('d M Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($room->cleaning_completed_at)
                                <span class="text-green-600 font-semibold">{{ $room->cleaning_completed_at->format('d M Y H:i') }}</span>
                            @else
                                <span class="text-yellow-600">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($room->cleaning_completed_at)
                                <form method="POST" action="{{ route('cleaning.confirm', $room) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">Confirm</button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400">Awaiting house help</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-8 text-center text-gray-400 text-sm">No rooms assigned yet.</div>
        @endif
    </div>
</div>
@endsection
