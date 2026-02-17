{{-- resources/views/components/conference-booking-status-badge.blade.php --}}
@php
    $classes = match($status) {
        'pending' => 'bg-yellow-100 text-yellow-800',
        'confirmed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'completed' => 'bg-gray-100 text-gray-800',
        default => 'bg-gray-100 text-gray-800',
    };
@endphp
<span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $classes }}">
    {{ ucfirst($status) }}
</span>