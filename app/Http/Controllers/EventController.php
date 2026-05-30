<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organization;
use App\Models\ConferenceHall;
use App\Models\ConferenceType;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
        $conferenceHalls = ConferenceHall::where('status', 'available')->orderBy('name')->get();
        return view('events.create', compact('organization', 'conferenceTypes', 'conferenceHalls'));
    }

    public function store(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'conference_type_id' => 'nullable|uuid|exists:conference_types,id',
            'conference_hall_id' => 'required|uuid|exists:conference_halls,id',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'theme_color' => 'nullable|string|max:7',
            'visibility' => 'required|in:public,private,organization-only',
            'capacity' => 'nullable|integer|min:1',
            'expected_attendance' => 'nullable|integer|min:1',
            'event_rate' => 'nullable|numeric|min:0',
        ]);

        $conferenceHallId = Arr::pull($validated, 'conference_hall_id');
        $eventRate = Arr::pull($validated, 'event_rate', 0);

        $hall = ConferenceHall::find($conferenceHallId);

        if (empty($validated['capacity']) && $hall && $hall->capacity) {
            $validated['capacity'] = $hall->capacity;
        }

        if (empty($eventRate) && $hall && $hall->hourly_rate) {
            $eventRate = (float) $hall->hourly_rate;
        }

        $validated['organization_id'] = $organization->id;
        $validated['slug'] = Str::slug($validated['title']);
        $validated['status'] = 'draft';

        if ($eventRate > 0) {
            $metadata = ['event_rate' => (float) $eventRate];
            $validated['metadata'] = $metadata;
            $validated['event_rate_total'] = (float) $eventRate;
        }

        $baseSlug = $validated['slug'];
        $counter = 1;
        while (Event::where('organization_id', $organization->id)->where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $baseSlug . '-' . $counter;
            $counter++;
        }

        $event = Event::create($validated);

        $event->venues()->create([
            'conference_hall_id' => $conferenceHallId,
            'setup_type' => 'theater',
            'status' => 'pending',
        ]);

        return redirect()->route('organizations.events.show', [$organization, $event])
            ->with('success', 'Event created successfully. Now add schedules, passes, and venues.');
    }

    public function show(Organization $organization, Event $event)
    {
        $event->load([
            'conferenceType',
            'schedules',
            'passes',
            'venues.conferenceHall',
            'venues.booking',
            'staff.user',
            'attendances' => function ($query) {
                $query->latest()->limit(10);
            },
        ]);
        $event->loadCount(['attendances', 'schedules', 'passes']);
        $event->loadCount('tickets as passes_count');

        $billing = $event->calculateBilling();

        $stats = [
            'total_attendances' => $event->attendances_count,
            'confirmed' => $event->attendances()->where('registration_status', 'confirmed')->count(),
            'checked_in' => $event->attendances()->where('total_check_ins', '>', 0)->count(),
            'no_shows' => $event->attendances()->where('registration_status', 'no_show')->count(),
            'revenue' => $billing['grand_total'],
        ];

        return view('events.show', compact('organization', 'event', 'stats', 'billing'));
    }

    public function edit(Organization $organization, Event $event)
    {
        $conferenceTypes = ConferenceType::orderBy('name')->get();
        $event->load('venues.conferenceHall');
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
            'event_rate' => 'nullable|numeric|min:0',
        ]);

        $eventRate = Arr::pull($validated, 'event_rate', 0);
        if ($eventRate > 0 || $event->event_rate_total > 0) {
            $metadata = $event->metadata ?? [];
            $metadata['event_rate'] = (float) $eventRate;
            $validated['metadata'] = $metadata;
            $validated['event_rate_total'] = (float) $eventRate;
        }

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

        $this->softDelete($event);

        return redirect()->route('organizations.events-list', $organization)
            ->with('success', 'Event archived successfully.');
    }

    public function publish(Organization $organization, Event $event)
    {
        if ($event->publish()) {
            return back()->with('success', 'Event published and passes are now active.');
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

    public function complete(Request $request, Organization $organization, Event $event)
    {
        $request->validate([
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_reason' => 'nullable|string|max:255',
        ]);

        if (!in_array($event->status, ['scheduled', 'ongoing'])) {
            return back()->with('error', 'Event cannot be completed from its current state.');
        }

        $discountPercent = (float) ($request->input('discount_percent', 0));
        $discountReason = $request->input('discount_reason');

        $event->load('venues.conferenceHall', 'venues.booking');
        $billing = $event->calculateBilling();

        $event->update([
            'status' => 'completed',
            'hall_rate_total' => $billing['hall_rate_total'],
            'event_rate_total' => $billing['event_rate_total'],
            'subtotal' => $billing['subtotal'],
        ]);

        if ($discountPercent > 0) {
            $event->applyDiscount($discountPercent, $discountReason);
        } else {
            $event->update(['grand_total' => $billing['subtotal']]);
        }

        return back()->with('success', 'Event completed successfully.');
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

    public function createStandalone()
    {
        $organizations = Organization::orderBy('name')->get();
        $conferenceTypes = ConferenceType::orderBy('name')->get();
        $conferenceHalls = ConferenceHall::where('status', 'available')->orderBy('name')->get();
        return view('events.create-standalone', compact('organizations', 'conferenceTypes', 'conferenceHalls'));
    }

    public function storeStandalone(Request $request)
    {
        $validated = $request->validate([
            'organization_id' => 'required|uuid|exists:organizations,id',
            'title' => 'required|string|max:255',
            'conference_type_id' => 'nullable|uuid|exists:conference_types,id',
            'conference_hall_id' => 'required|uuid|exists:conference_halls,id',
            'description' => 'nullable|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'theme_color' => 'nullable|string|max:7',
            'visibility' => 'required|in:public,private,organization-only',
            'capacity' => 'nullable|integer|min:1',
            'expected_attendance' => 'nullable|integer|min:1',
            'event_rate' => 'nullable|numeric|min:0',
        ]);

        $organization = Organization::findOrFail($validated['organization_id']);
        $conferenceHallId = Arr::pull($validated, 'conference_hall_id');
        $eventRate = Arr::pull($validated, 'event_rate', 0);

        $hall = ConferenceHall::find($conferenceHallId);

        if (empty($validated['capacity']) && $hall && $hall->capacity) {
            $validated['capacity'] = $hall->capacity;
        }

        if (empty($eventRate) && $hall && $hall->hourly_rate) {
            $eventRate = (float) $hall->hourly_rate;
        }

        $validated['slug'] = Str::slug($validated['title']);
        $validated['status'] = 'draft';

        if ($eventRate > 0) {
            $validated['metadata'] = ['event_rate' => (float) $eventRate];
            $validated['event_rate_total'] = (float) $eventRate;
        }

        $baseSlug = $validated['slug'];
        $counter = 1;
        while (Event::where('organization_id', $organization->id)->where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $baseSlug . '-' . $counter;
            $counter++;
        }

        $event = Event::create($validated);

        $event->venues()->create([
            'conference_hall_id' => $conferenceHallId,
            'setup_type' => 'theater',
            'status' => 'pending',
        ]);

        return redirect()->route('organizations.events.show', [$organization, $event])
            ->with('success', 'Event created successfully. Now add schedules, passes, and venues.');
    }

    public function showStandalone(Event $event)
    {
        $organization = $event->organization;
        $event->load([
            'conferenceType',
            'schedules',
            'passes',
            'venues.conferenceHall',
            'venues.booking',
            'staff.user',
            'attendances' => function ($query) {
                $query->latest()->limit(10);
            },
        ]);
        $event->loadCount(['attendances', 'schedules', 'passes']);

        $billing = $event->calculateBilling();

        $stats = [
            'total_attendances' => $event->attendances_count,
            'confirmed' => $event->attendances()->where('registration_status', 'confirmed')->count(),
            'checked_in' => $event->attendances()->where('total_check_ins', '>', 0)->count(),
            'no_shows' => $event->attendances()->where('registration_status', 'no_show')->count(),
            'revenue' => $billing['grand_total'],
        ];

        return view('events.show', compact('organization', 'event', 'stats', 'billing'));
    }
}
