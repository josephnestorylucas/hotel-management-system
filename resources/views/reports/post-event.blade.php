@extends('layouts.app')

@section('title', 'Post-Event Report')
@section('page-title', 'Events')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Post-Event Report</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('organizations.events.reports.export', [$organization, $event, 'type' => 'attendances', 'format' => 'csv']) }}" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700">Export Attendances</a>
            <a href="{{ route('organizations.events.reports.export', [$organization, $event, 'type' => 'checkins', 'format' => 'csv']) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">Export Check-ins</a>
            <a href="{{ route('organizations.events.show', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back</a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-3xl font-extrabold text-secondary">{{ $totalRegistered }}</div>
            <div class="text-sm text-gray-500">Total Registered</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-3xl font-extrabold text-green-600">{{ $checkedIn }}</div>
            <div class="text-sm text-gray-500">Attended</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-3xl font-extrabold text-blue-600">{{ $attendanceRate }}%</div>
            <div class="text-sm text-gray-500">Attendance Rate</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-3xl font-extrabold text-red-600">{{ $noShowRate }}%</div>
            <div class="text-sm text-gray-500">No-Show Rate</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 text-center">
            <div class="text-3xl font-extrabold text-purple-600">@currency($revenue)</div>
            <div class="text-xs text-gray-500">Revenue (Hall + Event Rate - Discount)</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Attendees by Pass Type -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Attendees by Pass Type</h3>
            <div class="space-y-3">
                @forelse($byPassTier as $tier)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <div class="text-sm font-medium text-secondary">{{ $tier->tier_name }}</div>
                    </div>
                    <div class="text-sm font-bold text-secondary">{{ $tier->count }} attendees</div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">No pass data.</p>
                @endforelse
            </div>
        </div>

        <!-- Top Companies -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Top Companies</h3>
            <div class="space-y-3">
                @forelse($byCompany as $company)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-sm font-medium text-secondary">{{ $company->company }}</span>
                    <span class="text-sm font-bold text-secondary">{{ $company->count }} attendees</span>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-4">No company data.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Session Breakdown -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">Session Attendance Breakdown</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Session</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Time</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Check-ins</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sessionBreakdown as $item)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-secondary">{{ $item['session']->name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst($item['session']->session_type) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $item['session']->start_datetime->format('M d, h:i A') }}</td>
                        <td class="px-4 py-3 text-sm font-bold text-secondary">{{ $item['check_ins'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-8 text-center text-gray-500">No sessions.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
