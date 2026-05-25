@extends('layouts.app')

@section('title', 'Bulk Upload')
@section('page-title', 'Events')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Bulk Upload Attendees</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <a href="{{ route('organizations.events.attendances.index', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Back to Attendees</a>
    </div>

    @if(session('bulk_results'))
    @php $results = session('bulk_results'); @endphp
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">Upload Results</h3>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="p-4 bg-green-50 rounded-xl">
                <div class="text-2xl font-extrabold text-green-600">{{ $results['success'] }}</div>
                <div class="text-sm text-green-700">Successful</div>
            </div>
            <div class="p-4 bg-red-50 rounded-xl">
                <div class="text-2xl font-extrabold text-red-600">{{ $results['failed'] }}</div>
                <div class="text-sm text-red-700">Failed</div>
            </div>
        </div>
        @if(!empty($results['errors']))
        <div class="p-4 bg-red-50 rounded-xl">
            <h4 class="text-sm font-bold text-red-700 mb-2">Errors:</h4>
            <ul class="text-sm text-red-600 space-y-1">
                @foreach($results['errors'] as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">Upload CSV File</h3>
        <p class="text-sm text-gray-500 mb-4">CSV must include columns: <code class="bg-gray-100 px-1 rounded">first_name</code>, <code class="bg-gray-100 px-1 rounded">last_name</code>, <code class="bg-gray-100 px-1 rounded">email</code>. Optional: <code class="bg-gray-100 px-1 rounded">phone</code>, <code class="bg-gray-100 px-1 rounded">company</code>, <code class="bg-gray-100 px-1 rounded">job_title</code>, <code class="bg-gray-100 px-1 rounded">dietary</code>, <code class="bg-gray-100 px-1 rounded">notes</code></p>

        <form method="POST" action="{{ route('organizations.events.attendances.process-bulk-upload', [$organization, $event]) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CSV File *</label>
                <input type="file" name="file" accept=".csv,.txt" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Assign Pass Type</label>
                <select name="event_pass_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">No pass</option>
                    @foreach($event->passes as $pass)
                    <option value="{{ $pass->id }}">{{ $pass->tier_name }} - @currency($pass->price)</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-lg hover:shadow-lg transition-all">Upload & Process</button>
        </form>
    </div>
</div>
@endsection
