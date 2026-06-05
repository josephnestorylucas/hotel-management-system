@props([
    'title' => 'No data found',
    'message' => 'There is nothing to display right now.',
    'table' => false,
    'colspan' => null,
])

@if($table)
    <tr>
        <td colspan="{{ $colspan ?? 1 }}" class="px-4 py-12">
            <div class="flex flex-col items-center justify-center gap-3 text-center text-gray-500">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                    <svg class="h-9 w-9" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="7" y="10" width="50" height="12" rx="4"></rect>
                        <rect x="7" y="26" width="50" height="12" rx="4"></rect>
                        <rect x="7" y="42" width="50" height="12" rx="4"></rect>
                        <circle cx="49" cy="16" r="2.5"></circle>
                        <circle cx="55" cy="16" r="2.5"></circle>
                        <circle cx="49" cy="32" r="2.5"></circle>
                        <circle cx="55" cy="32" r="2.5"></circle>
                        <circle cx="49" cy="48" r="2.5"></circle>
                        <circle cx="55" cy="48" r="2.5"></circle>
                        <circle cx="28" cy="31" r="11"></circle>
                        <path d="M36 39l11 11"></path>
                        <path d="M23 31a5 5 0 0 1 5-5"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-700">{{ $title }}</p>
                    <p class="mt-1 text-sm text-gray-400">{{ $message }}</p>
                </div>
            </div>
        </td>
    </tr>
@else
    <div class="flex flex-col items-center justify-center gap-3 rounded-2xl border border-dashed border-gray-200 bg-white px-6 py-12 text-center text-gray-500 shadow-sm">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-gray-400">
            <svg class="h-9 w-9" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="7" y="10" width="50" height="12" rx="4"></rect>
                <rect x="7" y="26" width="50" height="12" rx="4"></rect>
                <rect x="7" y="42" width="50" height="12" rx="4"></rect>
                <circle cx="49" cy="16" r="2.5"></circle>
                <circle cx="55" cy="16" r="2.5"></circle>
                <circle cx="49" cy="32" r="2.5"></circle>
                <circle cx="55" cy="32" r="2.5"></circle>
                <circle cx="49" cy="48" r="2.5"></circle>
                <circle cx="55" cy="48" r="2.5"></circle>
                <circle cx="28" cy="31" r="11"></circle>
                <path d="M36 39l11 11"></path>
                <path d="M23 31a5 5 0 0 1 5-5"></path>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-700">{{ $title }}</p>
            <p class="mt-1 text-sm text-gray-400">{{ $message }}</p>
        </div>
        {{ $slot }}
    </div>
@endif