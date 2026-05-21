{{-- resources/views/conferences/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Conference Details')
@section('page-title', 'Conferences')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $conference->title }}</h2>
            <p class="text-sm text-gray-500 mt-1">Conference Event Management</p>
        </div>
        <div class="flex items-center gap-3">
            @if($conference->status === 'scheduled' || $conference->status === 'ongoing')
            <a href="{{ route('conferences.check-in', $conference) }}" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                Check-in Dashboard
            </a>
            @endif
            @if($conference->participants->where('rsvp_status', 'confirmed')->count() > 0)
            <a href="{{ route('conferences.badges', $conference) }}" class="px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition-colors">
                Print All Badges
            </a>
            @endif
            <a href="{{ route('conferences.edit', $conference) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                Edit
            </a>
        </div>
    </div>

    <!-- Conference Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-secondary">Conference Information</h3>
                @include('components.conference-status-badge', ['status' => $conference->status])
            </div>
            
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Title</label>
                    <p class="text-sm font-semibold text-secondary mt-1">{{ $conference->title }}</p>
                </div>
                
                @if($conference->description)
                <div>
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Description</label>
                    <p class="text-sm text-gray-700 mt-1">{{ $conference->description }}</p>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Start</label>
                        <p class="text-sm font-semibold text-secondary mt-1">{{ $conference->start_datetime->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">End</label>
                        <p class="text-sm font-semibold text-secondary mt-1">{{ $conference->end_datetime->format('M d, Y h:i A') }}</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Organising Institution</label>
                    <div class="mt-2 flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($conference->institution->name ?? 'N', 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-secondary">{{ $conference->institution->name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">{{ $conference->institution->contact_person ?? '' }}</p>
                            <p class="text-xs text-gray-400">{{ $conference->institution->phone ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hall Details -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Hall Details</h3>
            
            <div class="space-y-4">
                <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
                    <p class="text-xs text-gray-600 mb-1">Conference Hall</p>
                    <p class="text-lg font-bold text-secondary">{{ $conference->conferenceBooking->conferenceHall->name }}</p>
                    @if($conference->conferenceBooking->conferenceHall->building)
                    <p class="text-xs text-primary mt-1">{{ $conference->conferenceBooking->conferenceHall->building->name }}</p>
                    @endif
                </div>

                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-xs text-gray-500">Capacity</span>
                    <span class="text-sm font-semibold text-secondary">{{ $conference->conferenceBooking->conferenceHall->capacity }} people</span>
                </div>

                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                    <span class="text-xs text-gray-500">Booking #</span>
                    <a href="{{ route('conference-bookings.show', $conference->conferenceBooking) }}" class="text-sm font-semibold text-primary hover:text-blue-700">
                        {{ $conference->conferenceBooking->booking_number }}
                    </a>
                </div>

                <div class="flex items-center justify-between py-2">
                    <span class="text-xs text-gray-500">Total Cost</span>
                    <span class="text-sm font-bold text-secondary">@currency($conference->conferenceBooking->total_cost)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Participants Section -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-secondary">Participants ({{ $conference->participants->count() }})</h3>
            <button 
                type="button"
                onclick="document.getElementById('add-participant-form').classList.toggle('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Participant
            </button>
        </div>

        <!-- Add Participant Form (Hidden by default) -->
        <div id="add-participant-form" class="hidden mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <form method="POST" action="{{ route('conference-participants.store', $conference) }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="name" class="block text-xs font-medium text-gray-700 mb-1">Name *</label>
                        <input type="text" name="name" id="name" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" id="email" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="phone" class="block text-xs font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone" id="phone" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-3">
                    <div>
                        <label for="role" class="block text-xs font-medium text-gray-700 mb-1">Role *</label>
                        <select name="role" id="role" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="attendee">Attendee</option>
                            <option value="speaker">Speaker</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                            Add Participant
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Participants Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gradient-to-r from-blue-50 to-white">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Role</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">RSVP</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Check-ins</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Access Code</th>
                        <th class="px-4 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($conference->participants as $participant)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-secondary">{{ $participant->name }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm text-gray-700">{{ $participant->email }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $participant->role === 'speaker' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($participant->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @include('components.participant-rsvp-badge', ['status' => $participant->rsvp_status])
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-sm font-bold text-secondary">{{ $participant->checked_in_count }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded font-mono">{{ $participant->access_code }}</code>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                @if($participant->rsvp_status === 'confirmed')
                                <a href="{{ route('conference-participants.badge', $participant) }}" target="_blank" class="text-purple-600 hover:text-purple-700 font-semibold">Badge</a>
                                @endif
                                <form method="POST" action="{{ route('conference-participants.destroy', $participant) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-semibold" onclick="return confirm('Remove this participant?')">Remove</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            No participants added yet. Click "Add Participant" to get started.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('conferences.index') }}" class="text-primary hover:text-blue-700 font-semibold">
            ← Back to Conferences
        </a>
    </div>
</div>
@endsection