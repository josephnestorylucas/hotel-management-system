@extends('layouts.app')

@section('title', 'Pass')
@section('page-title', 'Events')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white border-2 border-gray-200 rounded-2xl p-8 text-center">
        <div class="text-xs text-gray-500 uppercase tracking-wider mb-2">{{ $event->title }}</div>
        <div class="text-xs text-gray-400 mb-4">{{ $event->start_date->format('M d, Y') }}</div>

        <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-xl flex items-center justify-center">
            <span class="text-3xl font-extrabold text-secondary">{{ strtoupper(substr($attendance->first_name, 0, 1) . substr($attendance->last_name, 0, 1)) }}</span>
        </div>

        <h2 class="text-2xl font-extrabold text-secondary mb-1">{{ $attendance->full_name }}</h2>
        @if($attendance->company)
        <p class="text-sm text-gray-600">{{ $attendance->company }}</p>
        @endif
        @if($attendance->eventPass)
        <div class="mt-3">
            <span class="px-4 py-1.5 rounded-full text-sm font-semibold" style="background-color: {{ $attendance->eventPass->color ?? '#3B82F6' }}20; color: {{ $attendance->eventPass->color ?? '#3B82F6' }}">
                {{ $attendance->eventPass->tier_name }}
            </span>
        </div>
        @endif

        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="text-sm font-mono text-gray-600 mb-2">{{ $attendance->ticket_number }}</div>
            <div class="text-2xl font-mono font-extrabold text-primary tracking-widest mb-4">{{ $attendance->manual_code }}</div>
            <div class="text-xs text-gray-400">Manual Code for Check-in</div>
        </div>
    </div>
</div>
@endsection
