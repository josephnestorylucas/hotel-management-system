{{-- resources/views/conferences/scan-portal.blade.php --}}
@extends('layouts.app')

@section('title', 'Scan Portal — ' . $conference->title)
@section('page-title', 'Pass Scanning Portal')

@section('content')
<div class="max-w-2xl mx-auto" x-data="scanPortal()">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-secondary">{{ $conference->title }}</h2>
        <p class="text-sm text-gray-500 mt-1">Pass Scanning Portal</p>
        <p class="text-xs text-gray-400 mt-1">{{ $conference->participants_count }} participants registered</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 mb-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-secondary">Scan or Enter Pass Code</h3>
            <p class="text-sm text-gray-500 mt-1">Enter the access code from the participant's pass</p>
        </div>

        <form @submit.prevent="verifyCode" class="mb-6">
            <div class="flex gap-3">
                <input type="text" x-model="code" placeholder="Enter access code (e.g., AB12CD34)"
                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg text-center text-lg font-mono font-bold tracking-wider focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    maxlength="64" autofocus>
                <button type="submit" :disabled="!code || loading"
                    class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading">Verify</span>
                    <span x-show="loading">...</span>
                </button>
            </div>
        </form>

        <template x-if="result">
            <div :class="{
                'bg-green-50 border-green-200': result.valid,
                'bg-red-50 border-red-200': !result.valid && result.status === 'invalid',
                'bg-yellow-50 border-yellow-200': !result.valid && result.status !== 'invalid',
                'bg-blue-50 border-blue-200': result.valid && result.status === 'already_checked_in'
            }" class="border rounded-xl p-6 text-center">
                <div class="mb-3">
                    <template x-if="result.valid">
                        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto"
                            :class="result.status === 'already_checked_in' ? 'bg-blue-100' : 'bg-green-100'">
                            <svg class="w-8 h-8" :class="result.status === 'already_checked_in' ? 'text-blue-600' : 'text-green-600'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </template>
                    <template x-if="!result.valid">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </template>
                </div>

                <p class="text-lg font-bold" :class="{
                    'text-green-800': result.valid && result.status !== 'already_checked_in',
                    'text-blue-800': result.valid && result.status === 'already_checked_in',
                    'text-red-800': !result.valid
                }" x-text="result.message"></p>

                <template x-if="result.data">
                    <div class="mt-4 space-y-1">
                        <p class="text-sm text-gray-700"><span class="font-semibold" x-text="result.data.name"></span></p>
                        <p class="text-xs text-gray-500">
                            Pass #<span x-text="result.data.pass_number"></span> &middot;
                            <span x-text="result.data.pass_type"></span>
                        </p>
                        <template x-if="result.data.check_ins !== undefined">
                            <p class="text-xs text-gray-500">Check-ins: <span x-text="result.data.check_ins"></span></p>
                        </template>
                    </div>
                </template>

                <template x-if="result.valid">
                    <button @click="checkIn()" :disabled="checkingIn"
                        class="mt-4 px-8 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 disabled:opacity-50 text-lg">
                        <span x-show="!checkingIn">Check In Now</span>
                        <span x-show="checkingIn">Processing...</span>
                    </button>
                </template>
            </div>
        </template>

        <template x-if="checkInResult">
            <div class="mt-4 p-4 rounded-lg text-center" :class="checkInResult.success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                <p class="font-bold" x-text="checkInResult.message"></p>
                <template x-if="checkInResult.data">
                    <p class="text-sm mt-1" x-text="'Check-in #' + checkInResult.data.check_in_count + ' at ' + checkInResult.data.time"></p>
                </template>
            </div>
        </template>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-4">
        <h4 class="text-sm font-semibold text-gray-700 mb-3">Access Rules</h4>
        <div class="grid grid-cols-3 gap-3 text-center">
            <div class="p-3 bg-blue-50 rounded-lg">
                <p class="text-xs font-bold text-blue-700">Attendee</p>
                <p class="text-xs text-blue-600">General access</p>
            </div>
            <div class="p-3 bg-purple-50 rounded-lg">
                <p class="text-xs font-bold text-purple-700">Speaker</p>
                <p class="text-xs text-purple-600">Speaker area + general</p>
            </div>
            <div class="p-3 bg-green-50 rounded-lg">
                <p class="text-xs font-bold text-green-700">Organizer</p>
                <p class="text-xs text-green-600">Full access</p>
            </div>
        </div>
    </div>
</div>

<script>
function scanPortal() {
    return {
        code: '',
        loading: false,
        result: null,
        checkingIn: false,
        checkInResult: null,

        async verifyCode() {
            if (!this.code.trim()) return;
            this.loading = true;
            this.result = null;
            this.checkInResult = null;

            try {
                const response = await fetch('{{ route("conferences.verify-pass", $conference) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ code: this.code.trim() }),
                });
                this.result = await response.json();
            } catch (e) {
                this.result = { valid: false, status: 'error', message: 'Network error. Please try again.' };
            }
            this.loading = false;
        },

        async checkIn() {
            if (!this.code.trim()) return;
            this.checkingIn = true;
            this.checkInResult = null;

            try {
                const response = await fetch('{{ route("conferences.check-in-pass", $conference) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ code: this.code.trim() }),
                });
                this.checkInResult = await response.json();
                if (this.checkInResult.success) {
                    this.result = null;
                    this.code = '';
                }
            } catch (e) {
                this.checkInResult = { success: false, message: 'Network error.' };
            }
            this.checkingIn = false;
        },
    }
}
</script>
@endsection
