<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organization;
use App\Models\ConferenceType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with(['organization', 'conferenceType'])
            ->withCount('attendances')
            ->orderBy('start_date', 'desc')
            ->paginate(20);

        return view('events.index', compact('events'));
    }

    public function create(Organization $organization)
    {
        $conferenceTypes = ConferenceType::orderBy('name')->get();
        return view('events.create', compact('organization', 'conferenceTypes'));
    }

    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'conference_type_id' => 'nullable|uuid|exists:conference_types,id',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'theme_color' => 'nullable|string|max:7',
            'visibility' => 'required|in:public,private,organization-only',
            'capacity' => 'nullable|integer|min:1',
            'expected_attendance' => 'nullable|integer|min:1',
        ]);

        $validated['organization_id'] = $organization->id;
        $validated['slug'] = Str::slug($validated['title']);
        $validated['status'] = 'draft';

        // Ensure unique slug per organization
        $baseSlug = $validated['slug'];
        $counter = 1;
        while (Event::where('organization_id', $organization->id)->where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $baseSlug . '-' . $counter;
            $counter++;
        }

        $event = Event::create($validated);

        return redirect()->route('organizations.events.show', [$organization, $event])
            ->with('success', 'Event created successfully. Now add schedules, tickets, and venues.');
    }

    public function show(Organization $organization, Event $event)
    {
        $event->load([
            'conferenceType',
            'schedules',
            'tickets',
            'venues.conferenceHall',
            'staff.user',
            'attendances' => function ($query) {
                $query->latest()->limit(10);
            },
        ]);
        $event->loadCount(['attendances', 'schedules', 'tickets']);

        $stats = [
            'total_attendances' => $event->attendances_count,
            'confirmed' => $event->attendances()->where('registration_status', 'confirmed')->count(),
            'checked_in' => $event->attendances()->where('total_check_ins', '>', 0)->count(),
            'no_shows' => $event->attendances()->where('registration_status', 'no_show')->count(),
            'revenue' => $event->total_revenue,
        ];

        return view('events.show', compact('organization', 'event', 'stats'));
    }

    public function edit(Organization $organization, Event $event)
    {
        $conferenceTypes = ConferenceType::orderBy('name')->get();
        return view('events.edit', compact('organization', 'event', 'conferenceTypes'));
    }

    public function update(Request $request, Organization $organization, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'conference_type_id' => 'nullable|uuid|exists:conference_types,id',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'theme_color' => 'nullable|string|max:7',
            'visibility' => 'required|in:public,private,organization-only',
            'capacity' => 'nullable|integer|min:1',
            'expected_attendance' => 'nullable|integer|min:1',
        ]);

        if ($event->isDraft()) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $event->update($validated);

        return redirect()->route('organizations.events.show', [$organization, $event])
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Organization $organization, Event $event)
    {
        if (!$event->isDraft()) {
            return back()->with('error', 'Only draft events can be deleted.');
        }

        $event->delete();

        return redirect()->route('organizations.events-list', $organization)
            ->with('success', 'Event deleted successfully.');
    }

    public function publish(Organization $organization, Event $event)
    {
        if ($event->publish()) {
            return back()->with('success', 'Event published and tickets are now on sale.');
        }
        return back()->with('error', 'Event cannot be published from its current state.');
    }

    public function start(Organization $organization, Event $event)
    {
        if ($event->start()) {
            return back()->with('success', 'Event started. Check-ins are now active.');
        }
        return back()->with('error', 'Event cannot be started from its current state.');
    }

    public function complete(Organization $organization, Event $event)
    {
        if ($event->complete()) {
            return back()->with('success', 'Event completed successfully.');
        }
        return back()->with('error', 'Event cannot be completed from its current state.');
    }

    public function cancel(Organization $organization, Event $event)
    {
        if ($event->cancel()) {
            return back()->with('success', 'Event cancelled.');
        }
        return back()->with('error', 'Event cannot be cancelled from its current state.');
    }

    public function duplicate(Organization $organization, Event $event)
    {
        $newEvent = $event->replicate();
        $newEvent->title = $event->title . ' (Copy)';
        $newEvent->slug = Str::slug($newEvent->title);
        $newEvent->status = 'draft';
        $newEvent->actual_attendance = 0;
        $newEvent->total_revenue = 0;
        $newEvent->save();

        return redirect()->route('organizations.events.show', [$organization, $newEvent])
            ->with('success', 'Event duplicated successfully.');
    }
}
