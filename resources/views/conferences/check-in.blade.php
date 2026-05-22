{{-- resources/views/conferences/check-in.blade.php --}}
@extends('layouts.app')

@section('title', 'Check-in — ' . $conference->title)
@section('page-title', 'Check-in Dashboard')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $conference->title }}</h2>
            <p class="text-sm text-gray-500">Check-in Dashboard</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('conferences.scan-portal', $conference) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700">
                Scan Portal
            </a>
            <a href="{{ route('conferences.show', $conference) }}" class="px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded-lg hover:bg-gray-700">
                Back to Conference
            </a>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
            <p class="text-3xl font-extrabold text-secondary">{{ $stats['total'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Participants</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
            <p class="text-3xl font-extrabold text-green-600">{{ $stats['confirmed'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Confirmed</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
            <p class="text-3xl font-extrabold text-blue-600">{{ $stats['checked_in'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Checked In</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
            <p class="text-3xl font-extrabold text-yellow-600">{{ $stats['pending'] }}</p>
            <p class="text-sm text-gray-500 mt-1">Pending</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">Participants</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Pass #</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">RSVP</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Check-ins</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($conference->participants as $p)
                    <tr class="{{ $p->hasCheckedIn() ? 'bg-green-50/50' : '' }}">
                        <td class="px-4 py-3 text-sm font-bold text-secondary">#{{ $p->pass_number ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-secondary">{{ $p->name }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $p->pass_type === 'organizer' ? 'bg-green-100 text-green-700' : ($p->pass_type === 'speaker' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ $p->pass_type_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3">@include('components.participant-rsvp-badge', ['status' => $p->rsvp_status])</td>
                        <td class="px-4 py-3 text-sm font-bold text-secondary">{{ $p->checked_in_count }}</td>
                        <td class="px-4 py-3">
                            @if($p->hasCheckedIn())
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">In</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Out</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
