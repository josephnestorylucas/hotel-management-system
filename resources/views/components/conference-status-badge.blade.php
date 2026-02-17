{{-- resources/views/components/conference-status-badge.blade.php --}}
@php
    $classes = match($status) {
        'draft' => 'bg-gray-100 text-gray-800',
        'scheduled' => 'bg-blue-100 text-blue-800',
        'ongoing' => 'bg-green-100 text-green-800',
        'completed' => 'bg-purple-100 text-purple-800',
        'cancelled' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800',
    };
@endphp
<span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $classes }}">
    {{ ucfirst($status) }}
</span>