<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventSchedule;
use App\Models\Organization;
use Illuminate\Http\Request;

class EventScheduleController extends Controller
{
    public function index(Organization $organization, Event $event)
    {
        $schedules = $event->schedules()
            ->orderBy('start_datetime')
            ->get()
            ->groupBy(fn($s) => $s->start_datetime->format('Y-m-d'));

        return view('event-schedules.index', compact('organization', 'event', 'schedules'));
    }

    public function create(Organization $organization, Event $event)
    {
        $nextSessionNumber = $event->schedules()->max('session_number') + 1;
        return view('event-schedules.create', compact('organization', 'event', 'nextSessionNumber'));
    }

    public function store(Request $request, Organization $organization, Event $event)
    {
        $validated = $request->validate([
            'session_number' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'nullable|string|max:255',
            'speaker_name' => 'nullable|string|max:255',
            'speaker_email' => 'nullable|email|max:255',
            'session_type' => 'required|in:keynote,workshop,networking,break,panel,presentation,other',
            'max_capacity' => 'nullable|integer|min:1',
        ]);

        $event->schedules()->create($validated);

        return redirect()->route('organizations.events.schedules.index', [$organization, $event])
            ->with('success', 'Session added successfully.');
    }

    public function show(Organization $organization, Event $event, EventSchedule $schedule)
    {
        $schedule->load('checkIns.attendance');
        return view('event-schedules.show', compact('organization', 'event', 'schedule'));
    }

    public function edit(Organization $organization, Event $event, EventSchedule $schedule)
    {
        return view('event-schedules.edit', compact('organization', 'event', 'schedule'));
    }

    public function update(Request $request, Organization $organization, Event $event, EventSchedule $schedule)
    {
        $validated = $request->validate([
            'session_number' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'nullable|string|max:255',
            'speaker_name' => 'nullable|string|max:255',
            'speaker_email' => 'nullable|email|max:255',
            'session_type' => 'required|in:keynote,workshop,networking,break,panel,presentation,other',
            'max_capacity' => 'nullable|integer|min:1',
        ]);

        $schedule->update($validated);

        return redirect()->route('organizations.events.schedules.index', [$organization, $event])
            ->with('success', 'Session updated successfully.');
    }

    public function destroy(Organization $organization, Event $event, EventSchedule $schedule)
    {
        $schedule->delete();

        return back()->with('success', 'Session removed.');
    }
}
