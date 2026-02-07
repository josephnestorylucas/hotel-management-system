{{-- resources/views/guests/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Guest Profile')
@section('page-title', 'Guests')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Guest Profile Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-5 bg-gradient-to-r from-blue-50 to-white border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white text-xl font-bold shadow-lg overflow-hidden
                        {{ $guest->hasPhoto() ? '' : 'bg-gradient-to-br from-primary to-blue-600' }}">
                        @if($guest->hasPhoto())
                            <img src="{{ $guest->photo_medium_url ?? $guest->photo_url }}" alt="{{ $guest->full_name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($guest->first_name, 0, 1) . substr($guest->last_name, 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <h2 class="text-xl font-extrabold text-secondary">{{ $guest->full_name }}</h2>
                        <p class="text-sm text-gray-500 mt-1">Guest since {{ $guest->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('reservations.create', ['guest_id' => $guest->id]) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Booking
                    </a>
                    <a href="{{ route('guests.edit', $guest) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                </div>
            </div>
        </div>

        <!-- Guest Details -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Contact Information -->
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        Contact Information
                    </h3>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span class="text-sm text-secondary font-medium">{{ $guest->phone_number }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm text-secondary font-medium">{{ $guest->email ?? 'No email provided' }}</span>
                        </div>
                        @if($guest->address)
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="text-sm text-secondary font-medium">{{ $guest->address }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Personal Information -->
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        Personal Details
                    </h3>
                    <div class="space-y-3">
                        @if($guest->date_of_birth)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Date of Birth</span>
                            <span class="text-sm text-secondary font-medium">{{ $guest->date_of_birth->format('M d, Y') }}</span>
                        </div>
                        @endif
                        @if($guest->nationality)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Nationality</span>
                            <span class="text-sm text-secondary font-medium">{{ $guest->nationality }}</span>
                        </div>
                        @endif
                        @if($guest->id_number)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">ID/Passport</span>
                            <span class="text-sm text-secondary font-medium">{{ $guest->id_number }}</span>
                        </div>
                        @endif
                        @if($guest->hasIdDocuments())
                        <div class="mt-4">
                            <span class="text-sm text-gray-500 block mb-2">ID Documents ({{ $guest->id_documents_count }})</span>
                            <div class="space-y-2">
                                @foreach($guest->id_documents as $document)
                                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-2">
                                    <div class="flex items-center gap-2">
                                        @if(str_contains($document->mime_type, 'image'))
                                            <img src="{{ $document->getUrl('thumb') }}" alt="Document" class="w-10 h-10 object-cover rounded">
                                        @else
                                            <div class="w-10 h-10 bg-red-100 rounded flex items-center justify-center">
                                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <span class="text-xs text-gray-600">{{ $document->file_name }}</span>
                                    </div>
                                    <a href="{{ $document->getUrl() }}" target="_blank" class="text-xs text-primary hover:underline font-medium">
                                        View
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservation History -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white">
            <h3 class="text-lg font-extrabold text-secondary">Reservation History</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $guest->reservations->count() }} total reservations</p>
        </div>

        @if($guest->reservations->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Reservation</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Check-In</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Check-Out</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-primary uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($guest->reservations->sortByDesc('created_at') as $reservation)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('reservations.edit', $reservation) }}" class="text-sm font-semibold text-primary hover:underline">
                                {{ $reservation->reservation_number }}
                            </a>
                            <div class="text-xs text-gray-500">{{ $reservation->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($reservation->room)
                                <div class="text-sm font-medium text-secondary">{{ $reservation->room->room_number }}</div>
                                <div class="text-xs text-primary">{{ $reservation->room->roomType->name }}</div>
                            @else
                                <span class="text-xs text-red-600 font-semibold">Not Assigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-secondary">{{ $reservation->check_in_date->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-secondary">{{ $reservation->check_out_date->format('M d, Y') }}</div>
                            <div class="text-xs text-primary">{{ $reservation->check_in_date->diffInDays($reservation->check_out_date) }} nights</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-secondary">${{ number_format($reservation->total_amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @include('components.reservation-status-badge', ['status' => $reservation->status])
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <div class="flex flex-col items-center">
                <div class="w-16 h-16 bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">No reservations yet</p>
                <p class="text-gray-400 text-sm mt-1">Create a new booking for this guest</p>
                <a href="{{ route('reservations.create', ['guest_id' => $guest->id]) }}" 
                   class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Booking
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Back Button -->
    <div class="flex justify-start">
        <a href="{{ route('guests.index') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-secondary bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:shadow-md transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Guests
        </a>
    </div>
</div>
@endsection
