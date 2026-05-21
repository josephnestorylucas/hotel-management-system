<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventVenue;
use App\Models\ConferenceHall;
use App\Models\ConferenceBooking;
use App\Models\Organization;
use Illuminate\Http\Request;

class EventVenueController extends Controller
{
    public function index(Organization $organization, Event $event)
    {
        $venues = $event->venues()
            ->with(['conferenceHall', 'booking'])
            ->get();

        return view('event-venues.index', compact('organization', 'event', 'venues'));
    }

    public function create(Organization $organization, Event $event)
    {
        $halls = ConferenceHall::where('status', 'available')->orderBy('name')->get();
        $bookings = ConferenceBooking::whereIn('status', ['confirmed', 'pending'])
            ->with('conferenceHall')
            ->orderBy('booking_date')
            ->get();

        return view('event-venues.create', compact('organization', 'event', 'halls', 'bookings'));
    }

    public function store(Request $request, Organization $organization, Event $event)
    {
        $validated = $request->validate([
            'conference_hall_id' => 'required|uuid|exists:conference_halls,id',
            'booking_id' => 'nullable|uuid|exists:conference_bookings,id',
            'setup_type' => 'required|in:theater,classroom,banquet,boardroom,hollow_square,cocktail',
            'room_layout_notes' => 'nullable|string',
            'special_requests' => 'nullable|string',
            'expected_setup_start' => 'nullable|date',
            'expected_setup_end' => 'nullable|date|after:expected_setup_start',
            'notes' => 'nullable|string',
        ]);

        $validated['event_id'] = $event->id;
        $validated['status'] = 'pending';

        $event->venues()->create($validated);

        return redirect()->route('organizations.events.venues.index', [$organization, $event])
            ->with('success', 'Venue assigned to event successfully.');
    }

    public function show(Organization $organization, Event $event, EventVenue $venue)
    {
        $venue->load(['conferenceHall', 'booking']);
        return view('event-venues.show', compact('organization', 'event', 'venue'));
    }

    public function edit(Organization $organization, Event $event, EventVenue $venue)
    {
        $halls = ConferenceHall::where('status', 'available')->orderBy('name')->get();
        return view('event-venues.edit', compact('organization', 'event', 'venue', 'halls'));
    }

    public function update(Request $request, Organization $organization, Event $event, EventVenue $venue)
    {
        $validated = $request->validate([
            'conference_hall_id' => 'required|uuid|exists:conference_halls,id',
            'setup_type' => 'required|in:theater,classroom,banquet,boardroom,hollow_square,cocktail',
            'room_layout_notes' => 'nullable|string',
            'special_requests' => 'nullable|string',
            'expected_setup_start' => 'nullable|date',
            'expected_setup_end' => 'nullable|date',
            'status' => 'nullable|in:pending,confirmed,setup_in_progress,active,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $venue->update($validated);

        return redirect()->route('organizations.events.venues.index', [$organization, $event])
            ->with('success', 'Venue updated successfully.');
    }

    public function destroy(Organization $organization, Event $event, EventVenue $venue)
    {
        $venue->delete();

        return back()->with('success', 'Venue removed from event.');
    }
}
