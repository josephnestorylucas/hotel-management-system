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
        <div class="flex items-center gap-3">
            <a href="{{ route('organizations.events.check-in.dashboard', [$organization, $event]) }}" class="text-primary hover:text-blue-700 font-semibold">Dashboard</a>
        </div>
    </div>

    <!-- Scan Result -->
    <div id="scan-result" class="hidden rounded-2xl shadow-lg border-2 p-8 text-center">
        <div id="result-icon" class="w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center"></div>
        <h3 id="result-name" class="text-2xl font-extrabold text-secondary mb-2"></h3>
        <p id="result-message" class="text-sm text-gray-500 mb-2"></p>
        <p id="result-detail" class="text-xs text-gray-400"></p>
        <button onclick="document.getElementById('scan-result').classList.add('hidden')" class="mt-4 px-6 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200">Dismiss</button>
    </div>

    <!-- Camera Scanner -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-secondary">Camera Scanner</h3>
            <button id="toggle-camera" onclick="toggleCamera()" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700">Start Camera</button>
        </div>
        <div id="camera-container" class="hidden">
            <div class="relative bg-black rounded-xl overflow-hidden" style="max-height: 400px;">
                <video id="camera-video" class="w-full" autoplay playsinline></video>
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-56 h-56 border-4 border-white/60 rounded-2xl"></div>
                </div>
            </div>
            <canvas id="camera-canvas" class="hidden"></canvas>
            <p class="text-xs text-gray-400 text-center mt-2">Point the camera at a QR code. Scanning is automatic.</p>
        </div>
        <div id="camera-off" class="text-center py-8 text-gray-400">
            <svg class="w-16 h-16 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
            <p class="text-sm">Click "Start Camera" to begin scanning</p>
        </div>
    </div>

    <!-- Manual Code Entry -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">Manual Code Entry</h3>
        <form id="manual-form" class="flex gap-3">
            <input type="text" id="manual-code" maxlength="8" placeholder="Enter 8-char code" class="flex-1 px-4 py-3 text-lg font-mono text-center border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 uppercase tracking-widest">
            <button type="submit" class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700">Check In</button>
        </form>
    </div>

    <!-- Session Selection -->
    @if($schedules->count() > 0)
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">Check-in for Session</h3>
        <select id="session-select" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">General Check-in</option>
            @foreach($schedules as $schedule)
            <option value="{{ $schedule->id }}">{{ $schedule->name }} ({{ $schedule->start_datetime->format('M d, H:i') }})</option>
            @endforeach
        </select>
    </div>
    @endif

    <!-- Staff Override - Search -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-secondary mb-4">Staff Override</h3>
        <input type="text" id="override-search" placeholder="Search attendee by name or email..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-3">
        <div id="override-results" class="hidden space-y-2 max-h-60 overflow-y-auto"></div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;
let cameraRunning = false;
let processing = false;

function toggleCamera() {
    if (cameraRunning) {
        stopCamera();
    } else {
        startCamera();
    }
}

function startCamera() {
    const container = document.getElementById('camera-container');
    const video = document.getElementById('camera-video');
    const offMsg = document.getElementById('camera-off');
    const btn = document.getElementById('toggle-camera');

    html5QrCode = new Html5Qrcode("camera-video");

    Html5Qrcode.getCameras().then(cameras => {
        if (!cameras || cameras.length === 0) {
            alert('No camera found on this device.');
            return;
        }

        const cameraId = cameras[cameras.length - 1].id;

        html5QrCode.start(
            cameraId,
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onScanSuccess,
            () => {}
        ).then(() => {
            cameraRunning = true;
            container.classList.remove('hidden');
            offMsg.classList.add('hidden');
            btn.textContent = 'Stop Camera';
            btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            btn.classList.add('bg-red-600', 'hover:bg-red-700');
        }).catch(err => {
            alert('Could not start camera: ' + err);
        });
    }).catch(err => {
        alert('Could not access cameras: ' + err);
    });
}

function stopCamera() {
    if (html5QrCode && cameraRunning) {
        html5QrCode.stop().then(() => {
            cameraRunning = false;
            document.getElementById('camera-container').classList.add('hidden');
            document.getElementById('camera-off').classList.remove('hidden');
            const btn = document.getElementById('toggle-camera');
            btn.textContent = 'Start Camera';
            btn.classList.remove('bg-red-600', 'hover:bg-red-700');
            btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        });
    }
}

function onScanSuccess(decodedText) {
    if (processing) return;
    processing = true;

    const scheduleId = document.getElementById('session-select')?.value || null;

    fetch('{{ route("organizations.events.check-in.process", [$organization, $event]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ qr_token: decodedText, event_schedule_id: scheduleId }),
    })
    .then(r => r.json())
    .then(data => {
        showResult(data);
        if (data.success) {
            try { navigator.vibrate(200); } catch(e) {}
        }
    })
    .catch(() => showResult({ success: false, message: 'Network error.' }))
    .finally(() => {
        setTimeout(() => { processing = false; }, 3000);
    });
}

document.getElementById('manual-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const code = document.getElementById('manual-code').value.trim();
    if (!code) return;

    const scheduleId = document.getElementById('session-select')?.value || null;

    fetch('{{ route("organizations.events.check-in.manual", [$organization, $event]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ manual_code: code, event_schedule_id: scheduleId }),
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
        let detailParts = [];
        if (data.data.pass_type) detailParts.push(ucfirst(data.data.pass_type) + ' Pass');
        detailParts.push('Check-ins: ' + data.data.check_in_count);
        if (data.data.time) detailParts.push(data.data.time);
        detail.textContent = detailParts.join(' | ');
    } else {
        icon.className = 'w-20 h-20 mx-auto mb-4 rounded-full flex items-center justify-center bg-red-100';
        icon.innerHTML = '<svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
        result.className = 'bg-white rounded-2xl shadow-lg border-2 border-red-300 p-8 text-center';
        name.textContent = 'Error';
        msg.textContent = data.message;
        detail.textContent = '';
    }

    document.getElementById('manual-code').value = '';

    setTimeout(() => result.classList.add('hidden'), 8000);
}

function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Staff Override - Search attendees
let searchTimeout;
document.getElementById('override-search').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    const query = e.target.value.trim();
    const resultsDiv = document.getElementById('override-results');

    if (query.length < 2) {
        resultsDiv.classList.add('hidden');
        return;
    }

    searchTimeout = setTimeout(() => {
        fetch('{{ route("organizations.events.attendances.index", [$organization, $event]) }}?search=' + encodeURIComponent(query), {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            resultsDiv.classList.remove('hidden');
            if (data.data && data.data.length > 0) {
                resultsDiv.innerHTML = data.data.map(a => 
                    '<div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">' +
                    '<div><div class="text-sm font-semibold text-secondary">' + a.first_name + ' ' + a.last_name + '</div>' +
                    '<div class="text-xs text-gray-500">' + a.email + ' | ' + (a.manual_code || '') + '</div></div>' +
                    '<button onclick="staffOverride(\'' + a.id + '\')" class="px-3 py-1.5 bg-green-600 text-white text-xs font-semibold rounded-lg hover:bg-green-700">Check In</button></div>'
                ).join('');
            } else {
                resultsDiv.innerHTML = '<p class="text-sm text-gray-500 text-center py-2">No attendees found.</p>';
            }
        })
        .catch(() => {
            resultsDiv.classList.remove('hidden');
            resultsDiv.innerHTML = '<p class="text-sm text-red-500 text-center py-2">Search error. Try manual code instead.</p>';
        });
    }, 400);
});

function staffOverride(attendanceId) {
    const scheduleId = document.getElementById('session-select')?.value || null;

    fetch('{{ route("organizations.events.check-in.staff-override", [$organization, $event]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ attendance_id: attendanceId, event_schedule_id: scheduleId }),
    })
    .then(r => r.json())
    .then(data => showResult(data))
    .catch(() => showResult({ success: false, message: 'Network error.' }));
}

document.getElementById('manual-code').focus();
</script>
@endsection
