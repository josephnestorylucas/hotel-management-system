@extends('layouts.app')

@section('title', 'Pre-Event Report')
@section('page-title', 'Events')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Pre-Event Report</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('organizations.events.reports.export', [$organization, $event, 'type' => 'attendances', 'format' => 'csv']) }}" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700">Export CSV</a>
            <a href="{{ route('organizations.events.show', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Event</a>
        </div>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-3xl font-extrabold text-secondary">{{ $totalRegistrations }}</div>
            <div class="text-sm text-gray-500">Total Registrations</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-3xl font-extrabold text-green-600">{{ $byStatus['confirmed'] ?? 0 }}</div>
            <div class="text-sm text-gray-500">Confirmed</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-3xl font-extrabold text-yellow-600">{{ $byStatus['pending'] ?? 0 }}</div>
            <div class="text-sm text-gray-500">Pending</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-3xl font-extrabold text-purple-600">@currency($revenue)</div>
            <div class="text-sm text-gray-500">Expected Revenue</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- By Pass Tier -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">By Pass Tier</h3>
            <div class="space-y-3">
                @forelse($byPassTier as $tier => $count)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-secondary">{{ $tier }}</span>
                    <span class="text-sm font-bold text-secondary">{{ $count }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">No pass data.</p>
                @endforelse
            </div>
        </div>

        <!-- By Company -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Top Companies</h3>
            <div class="space-y-3">
                @forelse($byCompany as $company => $count)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-secondary">{{ $company }}</span>
                    <span class="text-sm font-bold text-secondary">{{ $count }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">No company data.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Registration Trend -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">Registration Trend</h3>
        @if($registrationTrend->count() > 0)
        <div class="flex items-end gap-1 h-32">
            @php $max = $registrationTrend->max('count'); @endphp
            @foreach($registrationTrend as $point)
            <div class="flex-1 bg-blue-500 rounded-t" style="height: {{ $max > 0 ? ($point->count / $max) * 100 : 0 }}%" title="{{ $point->date }}: {{ $point->count }}"></div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-500 text-center py-4">No registration data yet.</p>
        @endif
    </div>
</div>
@endsection
