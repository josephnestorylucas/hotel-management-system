<?php
// app/Http/Controllers/ConferenceParticipantController.php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\ConferenceParticipant;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ConferenceParticipantController extends Controller
{
    public function store(Request $request, Conference $conference)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:speaker,attendee',
            'guest_id' => 'nullable|uuid|exists:guests,id',
        ]);

        $participant = $conference->participants()->create([
            'guest_id' => $validated['guest_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'role' => $validated['role'],
            'rsvp_status' => 'pending',
        ]);

        return back()->with('success', 'Participant added successfully.');
    }

    public function update(Request $request, ConferenceParticipant $participant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|in:speaker,attendee',
            'rsvp_status' => 'required|in:pending,confirmed,declined',
        ]);

        $participant->update($validated);

        return back()->with('success', 'Participant updated successfully.');
    }

    public function destroy(ConferenceParticipant $participant)
    {
        $participant->delete();

        return back()->with('success', 'Participant removed successfully.');
    }

    public function printBadge(ConferenceParticipant $participant)
    {
        $participant->load('conference');
        
        return view('conference-participants.badge', compact('participant'));
    }

    public function printAllBadges(Conference $conference)
    {
        $participants = $conference->participants()
            ->where('rsvp_status', 'confirmed')
            ->get();

        return view('conference-participants.badges-all', compact('conference', 'participants'));
    }

    public function convertToGuest(Request $request, ConferenceParticipant $participant)
    {
        if ($participant->guest_id) {
            return back()->with('error', 'Participant is already linked to a guest.');
        }

        $validated = $request->validate([
            'guest_id' => 'required|uuid|exists:guests,id',
        ]);

        $participant->update(['guest_id' => $validated['guest_id']]);

        return back()->with('success', 'Participant linked to guest successfully.');
    }

    public function checkInDashboard(Conference $conference)
    {
        $conference->load(['participants' => function ($query) {
            $query->where('rsvp_status', 'confirmed')
                  ->orderBy('checked_in_count', 'desc')
                  ->orderBy('name');
        }]);

        return view('conferences.check-in', compact('conference'));
    }

    public function checkInByScan(Request $request)
    {
        $validated = $request->validate([
            'access_token' => 'required|string',
        ]);

        $participant = ConferenceParticipant::where('access_token', $validated['access_token'])->first();

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid access token.',
            ], 404);
        }

        return $this->processCheckIn($participant);
    }

    public function checkInByCode(Request $request)
    {
        $validated = $request->validate([
            'access_code' => 'required|string',
        ]);

        $participant = ConferenceParticipant::where('access_code', strtoupper($validated['access_code']))->first();

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid access code.',
            ], 404);
        }

        return $this->processCheckIn($participant);
    }

    private function processCheckIn(ConferenceParticipant $participant)
    {
        $participant->load('conference.conferenceBooking');

        // Validate conference status
        if (!$participant->conference->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Conference is not active.',
            ], 400);
        }

        // Validate RSVP
        if ($participant->rsvp_status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Participant has not confirmed attendance.',
            ], 400);
        }

        // Validate booking is active
        $booking = $participant->conference->conferenceBooking;
        if (!in_array($booking->status, ['confirmed', 'pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'Booking is not active.',
            ], 400);
        }

        // Check in
        $participant->checkIn();

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful!',
            'data' => [
                'participant' => $participant->name,
                'role' => $participant->role,
                'conference' => $participant->conference->title,
                'check_in_count' => $participant->checked_in_count,
                'time' => now()->format('h:i A'),
            ],
        ]);
    }
}