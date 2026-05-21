@extends('layouts.app')

@section('title', 'QR Scanner')
@section('page-title', 'Events')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Check-in Scanner</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $event->title }}</p>
        </div>
        <a href="{{ route('organizations.events.check-in.dashboard', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Dashboard</a>
    </div>

    <!-- Scanner Result -->
    <div id="scan-result" class="hidden bg-white rounded-2xl shadow-lg border-2 p-8 text-center">
        <div id="result-icon" class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center"></div>
        <h3 id="result-name" class="text-2xl font-extrabold text-secondary mb-2"></h3>
        <p id="result-message" class="text-sm text-gray-500 mb-2"></p>
        <p id="result-detail" class="text-xs text-gray-400"></p>
    </div>

    <!-- Manual Code Entry -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">Manual Code Entry</h3>
        <form id="manual-form" class="flex gap-3">
            <input type="text" id="manual-code" maxlength="8" placeholder="Enter 8-char code" class="flex-1 px-4 py-3 text-lg font-mono text-center border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase">
            <button type="submit" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700">Check In</button>
        </form>
    </div>

    <!-- QR Token Entry -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">QR Token Entry</h3>
        <form id="qr-form" class="flex gap-3">
            <input type="text" id="qr-token" placeholder="Paste QR token here" class="flex-1 px-4 py-3 text-sm font-mono border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700">Check In</button>
        </form>
    </div>

    <!-- Staff Override -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">Staff Override</h3>
        <form id="override-form" class="space-y-3">
            <input type="text" id="override-search" placeholder="Search attendee by name or email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <div id="override-results" class="hidden space-y-2"></div>
        </form>
    </div>
</div>

<script>
document.getElementById('qr-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const token = document.getElementById('qr-token').value.trim();
    if (!token) return;

    fetch('{{ route("organizations.events.check-in.process", [$organization, $event]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ qr_token: token }),
    })
    .then(r => r.json())
    .then(data => showResult(data))
    .catch(() => showResult({ success: false, message: 'Network error.' }));
});

document.getElementById('manual-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const code = document.getElementById('manual-code').value.trim();
    if (!code) return;

    fetch('{{ route("organizations.events.check-in.manual", [$organization, $event]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ manual_code: code }),
    })
    .then(r => r.json())
    .then(data => showResult(data))
    .catch(() => showResult({ success: false, message: 'Network error.' }));
});

function showResult(data) {
    const result = document.getElementById('scan-result');
    const icon = document.getElementById('result-icon');
    const name = document.getElementById('result-name');
    const msg = document.getElementById('result-message');
    const detail = document.getElementById('result-detail');

    result.classList.remove('hidden');

    if (data.success) {
        icon.className = 'w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center bg-green-100';
        icon.innerHTML = '<svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        result.className = 'bg-white rounded-2xl shadow-lg border-2 border-green-300 p-8 text-center';
        name.textContent = data.data.name;
        msg.textContent = data.message;
        detail.textContent = data.data.company ? data.data.company + ' | Check-ins: ' + data.data.check_in_count : 'Check-ins: ' + data.data.check_in_count;
    } else {
        icon.className = 'w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center bg-red-100';
        icon.innerHTML = '<svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
        result.className = 'bg-white rounded-2xl shadow-lg border-2 border-red-300 p-8 text-center';
        name.textContent = 'Error';
        msg.textContent = data.message;
        detail.textContent = '';
    }

    // Clear inputs
    document.getElementById('qr-token').value = '';
    document.getElementById('manual-code').value = '';

    // Auto-hide after 5 seconds
    setTimeout(() => result.classList.add('hidden'), 5000);
}

// Focus on manual code input
document.getElementById('manual-code').focus();
</script>
@endsection
