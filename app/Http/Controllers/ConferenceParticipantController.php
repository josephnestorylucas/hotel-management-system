<?php
// app/Http/Controllers/ConferenceParticipantController.php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\ConferenceParticipant;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConferenceParticipantController extends Controller
{
    public function store(Request $request, Conference $conference)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:speaker,attendee,organizer',
            'guest_id' => 'nullable|uuid|exists:guests,id',
        ]);

        $passNumber = $conference->getNextPassNumber();

        $conference->participants()->create([
            'guest_id'    => $validated['guest_id'] ?? null,
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'phone'       => $validated['phone'],
            'role'        => $validated['role'],
            'pass_type'   => $validated['role'],
            'pass_number' => $passNumber,
            'rsvp_status' => 'pending',
        ]);

        return back()->with('success', "Participant added. Pass #{$passNumber} assigned.");
    }

    public function update(Request $request, ConferenceParticipant $participant)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'nullable|string|max:20',
            'role'        => 'required|in:speaker,attendee,organizer',
            'rsvp_status' => 'required|in:pending,confirmed,declined',
        ]);

        $validated['pass_type'] = $validated['role'];
        $participant->update($validated);

        return back()->with('success', 'Participant updated.');
    }

    public function destroy(ConferenceParticipant $participant)
    {
        $participant->delete();

        return back()->with('success', 'Participant removed.');
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
            ->orderBy('pass_number')
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
            $query->orderBy('pass_number');
        }]);

        $stats = [
            'total'      => $conference->participants->count(),
            'confirmed'  => $conference->participants->where('rsvp_status', 'confirmed')->count(),
            'checked_in' => $conference->participants->where('checked_in_count', '>', 0)->count(),
            'pending'    => $conference->participants->where('rsvp_status', 'pending')->count(),
        ];

        return view('conferences.check-in', compact('conference', 'stats'));
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
                'message' => 'Invalid pass. Token not found.',
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
        $participant->load('conference');

        if (!$participant->conference->isActive()) {
            return response()->json([
                'success' => false,
                'message' => 'Conference is not active.',
            ], 400);
        }

        if ($participant->rsvp_status !== 'confirmed') {
            return response()->json([
                'success' => false,
                'message' => 'Participant has not confirmed attendance.',
                'data' => [
                    'name'   => $participant->name,
                    'status' => $participant->rsvp_status,
                ],
            ], 400);
        }

        $participant->checkIn();

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful!',
            'data' => [
                'participant'   => $participant->name,
                'role'          => $participant->pass_type_label,
                'pass_number'   => $participant->pass_number,
                'conference'    => $participant->conference->title,
                'check_in_count' => $participant->checked_in_count,
                'time'          => now()->format('h:i A'),
            ],
        ]);
    }

    public function scanningPortal(Conference $conference)
    {
        $conference->loadCount('participants');

        return view('conferences.scan-portal', compact('conference'));
    }

    public function verifyPass(Request $request, Conference $conference)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:64',
        ]);

        $code = trim($validated['code']);

        $participant = $conference->participants()
            ->where('access_code', strtoupper($code))
            ->orWhere('access_token', $code)
            ->first();

        if (!$participant) {
            return response()->json([
                'valid'   => false,
                'status'  => 'invalid',
                'message' => 'Pass not found. Invalid code.',
            ]);
        }

        if ($participant->rsvp_status === 'declined') {
            return response()->json([
                'valid'   => false,
                'status'  => 'declined',
                'message' => 'This pass has been declined.',
                'data'    => [
                    'name'       => $participant->name,
                    'pass_number' => $participant->pass_number,
                    'pass_type'  => $participant->pass_type_label,
                ],
            ]);
        }

        if ($participant->rsvp_status === 'pending') {
            return response()->json([
                'valid'   => false,
                'status'  => 'pending',
                'message' => 'Attendance not confirmed yet.',
                'data'    => [
                    'name'       => $participant->name,
                    'pass_number' => $participant->pass_number,
                    'pass_type'  => $participant->pass_type_label,
                ],
            ]);
        }

        if (!$participant->conference->isActive()) {
            return response()->json([
                'valid'   => false,
                'status'  => 'inactive',
                'message' => 'Conference is not active.',
            ]);
        }

        $alreadyCheckedIn = $participant->hasCheckedIn();

        return response()->json([
            'valid'   => true,
            'status'  => $alreadyCheckedIn ? 'already_checked_in' : 'valid',
            'message' => $alreadyCheckedIn
                ? 'Pass already scanned. Re-entry allowed.'
                : 'Pass is valid. Ready for check-in.',
            'data' => [
                'name'       => $participant->name,
                'email'      => $participant->email,
                'pass_number' => $participant->pass_number,
                'pass_type'  => $participant->pass_type_label,
                'check_ins'  => $participant->checked_in_count,
                'access_code' => $participant->access_code,
            ],
        ]);
    }

    public function checkInFromPortal(Request $request, Conference $conference)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:64',
        ]);

        $code = trim($validated['code']);

        $participant = $conference->participants()
            ->where('access_code', strtoupper($code))
            ->orWhere('access_token', $code)
            ->first();

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Pass not found.',
            ], 404);
        }

        return $this->processCheckIn($participant);
    }
}
