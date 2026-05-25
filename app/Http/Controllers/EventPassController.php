<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventPass;
use App\Models\Organization;
use Illuminate\Http\Request;

class EventPassController extends Controller
{
    public function index(Organization $organization, Event $event)
    {
        $passes = $event->passes()
            ->withCount('attendances')
            ->orderBy('tier_type')
            ->get();

        return view('event-passes.index', compact('organization', 'event', 'passes'));
    }

    public function create(Organization $organization, Event $event)
    {
        return view('event-passes.create', compact('organization', 'event'));
    }

    public function store(Request $request, Organization $organization, Event $event)
    {
        $validated = $request->validate([
            'tier_name' => 'required|string|max:255',
            'tier_type' => 'required|in:speaker,moderator,backdoor,attendee',
            'description' => 'nullable|string',
            'quantity_available' => 'nullable|integer|min:1',
            'access_type' => 'required|in:single-session,all-sessions,day-pass,unlimited',
            'benefits' => 'nullable|array',
            'color' => 'nullable|string|max:7',
        ]);

        $validated['event_id'] = $event->id;
        $validated['status'] = 'draft';

        $event->passes()->create($validated);

        return redirect()->route('organizations.events.passes.index', [$organization, $event])
            ->with('success', 'Pass type created successfully.');
    }

    public function show(Organization $organization, Event $event, EventPass $pass)
    {
        $pass->loadCount('attendances');
        return view('event-passes.show', compact('organization', 'event', 'pass'));
    }

    public function edit(Organization $organization, Event $event, EventPass $pass)
    {
        return view('event-passes.edit', compact('organization', 'event', 'pass'));
    }

    public function update(Request $request, Organization $organization, Event $event, EventPass $pass)
    {
        $validated = $request->validate([
            'tier_name' => 'required|string|max:255',
            'tier_type' => 'required|in:speaker,moderator,backdoor,attendee',
            'description' => 'nullable|string',
            'quantity_available' => 'nullable|integer|min:1',
            'access_type' => 'required|in:single-session,all-sessions,day-pass,unlimited',
            'benefits' => 'nullable|array',
            'status' => 'nullable|in:draft,on_sale,sold_out,archived',
            'color' => 'nullable|string|max:7',
        ]);

        $pass->update($validated);

        return redirect()->route('organizations.events.passes.index', [$organization, $event])
            ->with('success', 'Pass type updated successfully.');
    }

    public function destroy(Organization $organization, Event $event, EventPass $pass)
    {
        if ($pass->attendances()->exists()) {
            return back()->with('error', 'Cannot delete pass type with existing registrations.');
        }

        $pass->delete();

        return back()->with('success', 'Pass type deleted.');
    }

    public function activate(Organization $organization, Event $event, EventPass $pass)
    {
        $pass->update(['status' => 'on_sale']);
        return back()->with('success', 'Pass type is now active.');
    }

    public function archive(Organization $organization, Event $event, EventPass $pass)
    {
        $pass->update(['status' => 'archived']);
        return back()->with('success', 'Pass type archived.');
    }
}
