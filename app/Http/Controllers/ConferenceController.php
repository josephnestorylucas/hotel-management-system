<?php
// app/Http/Controllers/ConferenceController.php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Models\ConferenceBooking;
use Illuminate\Http\Request;

class ConferenceController extends Controller
{
    public function index()
    {
        $conferences = Conference::with(['conferenceBooking.conferenceHall', 'organizer'])
            ->withCount('participants')
            ->orderBy('start_datetime', 'desc')
            ->paginate(20);

        return view('conferences.index', compact('conferences'));
    }

    public function create()
    {
        $bookings = ConferenceBooking::with(['conferenceHall', 'guest'])
            ->whereIn('status', ['confirmed'])
            ->whereDoesntHave('conference')
            ->orderBy('booking_date')
            ->get();

        return view('conferences.create', compact('bookings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'conference_booking_id' => 'required|uuid|exists:conference_bookings,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
        ]);

        $booking = ConferenceBooking::findOrFail($validated['conference_booking_id']);

        $conference = Conference::create([
            'conference_booking_id' => $validated['conference_booking_id'],
            'guest_id' => $booking->guest_id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'start_datetime' => $validated['start_datetime'],
            'end_datetime' => $validated['end_datetime'],
            'status' => 'draft',
        ]);

        return redirect()->route('conferences.show', $conference)
            ->with('success', 'Conference created successfully. Now add participants.');
    }

    public function show(Conference $conference)
    {
        $conference->load([
            'conferenceBooking.conferenceHall',
            'conferenceBooking.guest',
            'organizer',
            'participants.guest'
        ]);

        return view('conferences.show', compact('conference'));
    }

    public function edit(Conference $conference)
    {
        return view('conferences.edit', compact('conference'));
    }

    public function update(Request $request, Conference $conference)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'status' => 'required|in:draft,scheduled,ongoing,completed,cancelled',
        ]);

        $conference->update($validated);

        return redirect()->route('conferences.show', $conference)
            ->with('success', 'Conference updated successfully.');
    }

    public function destroy(Conference $conference)
    {
        $conference->delete();

        return redirect()->route('conferences.index')
            ->with('success', 'Conference deleted successfully.');
    }
}