@extends('layouts.app')

@section('title', 'Bug Reports')
@section('page-title', 'Bug Reports')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-xl font-extrabold text-secondary">Bug Reports</h2>
                <p class="text-sm text-gray-500">Track issues reported by testers.</p>
            </div>
            <a href="{{ route('bug-reports.index', array_merge(request()->query(), ['export' => 'csv'])) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-3-3m3 3l3-3M5 20h14" />
                </svg>
                Export CSV
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-100">
                <div class="text-xs text-blue-600 font-semibold">Total</div>
                <div class="text-2xl font-bold text-secondary">{{ $stats['total'] }}</div>
            </div>
            <div class="p-4 rounded-xl bg-yellow-50 border border-yellow-100">
                <div class="text-xs text-yellow-700 font-semibold">Open</div>
                <div class="text-2xl font-bold text-secondary">{{ $stats['open'] }}</div>
            </div>
            <div class="p-4 rounded-xl bg-green-50 border border-green-100">
                <div class="text-xs text-green-700 font-semibold">Fixed</div>
                <div class="text-2xl font-bold text-secondary">{{ $stats['fixed'] }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Module</label>
                <select name="module" class="w-full px-3 py-2 border border-gray-200 rounded-xl">
                    <option value="">All</option>
                    @foreach(['Front Desk','Rooms','Billing','Guests','Housekeeping','Reports','Other'] as $module)
                        <option value="{{ $module }}" @selected(request('module') === $module)>{{ $module }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Severity</label>
                <select name="severity" class="w-full px-3 py-2 border border-gray-200 rounded-xl">
                    <option value="">All</option>
                    @foreach(['low','medium','high','critical'] as $level)
                        <option value="{{ $level }}" @selected(request('severity') === $level)>{{ ucfirst($level) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-xl">
                    <option value="">All</option>
                    @foreach(['open','acknowledged','fixed'] as $state)
                        <option value="{{ $state }}" @selected(request('status') === $state)>{{ ucfirst($state) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-xl text-sm font-semibold">Filter</button>
                <a href="{{ route('bug-reports.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Module</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Severity</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Page</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Reported By</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($bugReports as $report)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-600">#{{ $report->id }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-secondary">{{ $report->title }}</div>
                            <div class="text-xs text-gray-500">{{ $report->details }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $report->module }}</td>
                        <td class="px-6 py-4">
                            @php
                                $severityClasses = [
                                    'low' => 'bg-green-100 text-green-700',
                                    'medium' => 'bg-yellow-100 text-yellow-700',
                                    'high' => 'bg-orange-100 text-orange-700',
                                    'critical' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $severityClasses[$report->severity] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($report->severity) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($report->page_url)
                                <a href="{{ $report->page_url }}" target="_blank" class="text-primary hover:underline">Open</a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $report->reported_by ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $report->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('bug-reports.update', $report) }}">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="px-2 py-1 border border-gray-200 rounded-lg text-xs">
                                    @foreach(['open','acknowledged','fixed'] as $state)
                                        <option value="{{ $state }}" @selected($report->status === $state)>{{ ucfirst($state) }}</option>
                                    @endforeach
                                </select>
                            </form>
                        </td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('bug-reports.destroy', $report) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-semibold">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-10 text-center text-gray-500">No bug reports found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $bugReports->links() }}
        </div>
    </div>
</div>
@endsection
