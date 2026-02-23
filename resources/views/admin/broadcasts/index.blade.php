@extends('layouts.app')

@section('title', 'Broadcasts')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Broadcasts &amp; Offers</h1>
        <a href="{{ route('admin.broadcasts.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">
            + New Broadcast
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Title</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Type</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Target</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-medium">Channels</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-medium">Recipients</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-medium">Status</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Created By</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-medium">Date</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($broadcasts as $bc)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-800">{{ Str::limit($bc->title, 40) }}</td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            @if($bc->type === 'offer') bg-green-100 text-green-700
                            @elseif($bc->type === 'event') bg-purple-100 text-purple-700
                            @else bg-blue-100 text-blue-700
                            @endif">
                            {{ ucfirst($bc->type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ ucfirst($bc->target) }}</td>
                    <td class="px-4 py-3 text-center text-gray-500">{{ ucfirst($bc->channels) }}</td>
                    <td class="px-4 py-3 text-center font-mono">{{ $bc->recipients_count }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                            @if($bc->status === 'sent') bg-green-100 text-green-700
                            @elseif($bc->status === 'sending') bg-yellow-100 text-yellow-700
                            @elseif($bc->status === 'draft') bg-gray-100 text-gray-600
                            @elseif($bc->status === 'scheduled') bg-blue-100 text-blue-700
                            @else bg-red-100 text-red-700
                            @endif">
                            {{ ucfirst($bc->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $bc->creator->name ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $bc->created_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3 text-center">
                        @if(in_array($bc->status, ['draft', 'scheduled']))
                        <form action="{{ route('admin.broadcasts.send', $bc) }}" method="POST" class="inline"
                              onsubmit="return confirm('Send this broadcast to all targeted guests?')">
                            @csrf
                            <button type="submit"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium">
                                Send Now
                            </button>
                        </form>
                        @elseif($bc->status === 'sent')
                        <span class="text-xs text-gray-400">Sent {{ $bc->sent_at?->diffForHumans() }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-12 text-center text-gray-400">No broadcasts yet. Create your first one!</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($broadcasts->hasPages())
        <div class="px-4 py-3 border-t">{{ $broadcasts->links() }}</div>
        @endif
    </div>
</div>
@endsection
