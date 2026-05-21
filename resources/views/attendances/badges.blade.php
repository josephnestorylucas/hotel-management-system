@extends('layouts.app')

@section('title', 'Print Badges')
@section('page-title', 'Events')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Print Badges</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }} &middot; {{ $attendances->count() }} badges</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700">Print</button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print:grid-cols-2">
        @foreach($attendances as $a)
        <div class="bg-white border-2 border-gray-200 rounded-xl p-6 break-inside-avoid" style="min-height: 200px;">
            <div class="text-center">
                <div class="text-xs text-gray-500 uppercase tracking-wider mb-2">{{ $event->title }}</div>
                <div class="text-xl font-extrabold text-secondary mb-1">{{ $a->full_name }}</div>
                @if($a->company)
                <div class="text-sm text-gray-600 mb-1">{{ $a->company }}</div>
                @endif
                @if($a->job_title)
                <div class="text-xs text-gray-500 mb-2">{{ $a->job_title }}</div>
                @endif
                @if($a->eventTicket)
                <div class="inline-block px-3 py-1 rounded-full text-xs font-semibold mb-3" style="background-color: {{ $a->eventTicket->color ?? '#3B82F6' }}20; color: {{ $a->eventTicket->color ?? '#3B82F6' }}">
                    {{ $a->eventTicket->tier_name }}
                </div>
                @endif
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <div class="text-xs font-mono text-gray-500">{{ $a->ticket_number }}</div>
                    <div class="text-xs font-mono font-bold text-primary mt-1">{{ $a->manual_code }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white; }
}
</style>
@endsection
