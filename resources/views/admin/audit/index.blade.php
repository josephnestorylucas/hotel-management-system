@extends('layouts.app')

@section('title', 'Discount Audit Log')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Discount Audit Log</h1>
        <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to Dashboard</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Date &amp; Time</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Booking</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Authorized By</th>
                    <th class="px-4 py-3 text-right text-gray-600 font-medium">Discount</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-medium">Valid Days</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Valid Period</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Reason</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($audits as $audit)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-xs text-gray-400 font-mono">
                        {{ $audit->authorized_at->format('d M Y H:i:s') }}
                    </td>
                    <td class="px-4 py-3 font-mono text-xs">
                        {{ Str::limit($audit->booking_id, 8, '...') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800">{{ $audit->authorizer->name ?? 'N/A' }}</div>
                        <div class="text-xs text-gray-400">{{ $audit->authorizer->role->name ?? '' }}</div>
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-red-600">
                        - TZS {{ number_format($audit->discount_amount, 2) }}
                    </td>
                    <td class="px-4 py-3 text-center">{{ $audit->valid_days }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        {{ $audit->valid_from->format('d M') }} &mdash; {{ $audit->valid_until->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600 max-w-xs truncate" title="{{ $audit->reason }}">
                        {{ Str::limit($audit->reason, 50) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">No discount records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($audits->hasPages())
        <div class="px-4 py-3 border-t">{{ $audits->links() }}</div>
        @endif
    </div>
</div>
@endsection
