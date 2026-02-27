{{-- resources/views/components/grn-status-badge.blade.php --}}
@php
    $classes = match($status) {
        'draft' => 'bg-gray-100 text-gray-800',
        'pending_confirmation' => 'bg-yellow-100 text-yellow-800',
        'confirmed' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800',
    };
    
    $label = str_replace('_', ' ', ucfirst($status));
@endphp

<span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $classes }}">
    {{ $label }}
</span>