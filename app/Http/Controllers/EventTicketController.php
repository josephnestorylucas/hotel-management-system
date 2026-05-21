<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventTicket;
use App\Models\Organization;
use Illuminate\Http\Request;

class EventTicketController extends Controller
{
    public function index(Organization $organization, Event $event)
    {
        $tickets = $event->tickets()
            ->withCount('attendances')
            ->orderBy('price')
            ->get();

        return view('event-tickets.index', compact('organization', 'event', 'tickets'));
    }

    public function create(Organization $organization, Event $event)
    {
        return view('event-tickets.create', compact('organization', 'event'));
    }

    public function store(Request $request, Organization $organization, Event $event)
    {
        $validated = $request->validate([
            'tier_name' => 'required|string|max:255',
            'tier_type' => 'required|in:standard,vip,exhibitor,speaker,student,corporate,media,press',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity_available' => 'nullable|integer|min:1',
            'early_bird_until' => 'nullable|date|after:today',
            'benefits' => 'nullable|array',
            'includes_guide' => 'boolean',
            'access_type' => 'required|in:single-session,all-sessions,day-pass,unlimited',
            'bulk_discount_percent' => 'nullable|numeric|min:0|max:100',
            'sale_start_date' => 'nullable|date',
            'sale_end_date' => 'nullable|date|after_or_equal:sale_start_date',
            'color' => 'nullable|string|max:7',
        ]);

        $validated['event_id'] = $event->id;
        $validated['status'] = 'draft';

        $event->tickets()->create($validated);

        return redirect()->route('organizations.events.tickets.index', [$organization, $event])
            ->with('success', 'Ticket tier created successfully.');
    }

    public function show(Organization $organization, Event $event, EventTicket $ticket)
    {
        $ticket->loadCount('attendances');
        return view('event-tickets.show', compact('organization', 'event', 'ticket'));
    }

    public function edit(Organization $organization, Event $event, EventTicket $ticket)
    {
        return view('event-tickets.edit', compact('organization', 'event', 'ticket'));
    }

    public function update(Request $request, Organization $organization, Event $event, EventTicket $ticket)
    {
        $validated = $request->validate([
            'tier_name' => 'required|string|max:255',
            'tier_type' => 'required|in:standard,vip,exhibitor,speaker,student,corporate,media,press',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity_available' => 'nullable|integer|min:1',
            'early_bird_until' => 'nullable|date',
            'benefits' => 'nullable|array',
            'includes_guide' => 'boolean',
            'access_type' => 'required|in:single-session,all-sessions,day-pass,unlimited',
            'bulk_discount_percent' => 'nullable|numeric|min:0|max:100',
            'sale_start_date' => 'nullable|date',
            'sale_end_date' => 'nullable|date',
            'status' => 'nullable|in:draft,on_sale,sold_out,archived',
            'color' => 'nullable|string|max:7',
        ]);

        $ticket->update($validated);

        return redirect()->route('organizations.events.tickets.index', [$organization, $event])
            ->with('success', 'Ticket tier updated successfully.');
    }

    public function destroy(Organization $organization, Event $event, EventTicket $ticket)
    {
        if ($ticket->attendances()->exists()) {
            return back()->with('error', 'Cannot delete ticket tier with existing registrations.');
        }

        $ticket->delete();

        return back()->with('success', 'Ticket tier deleted.');
    }

    public function putOnSale(Organization $organization, Event $event, EventTicket $ticket)
    {
        $ticket->update(['status' => 'on_sale']);
        return back()->with('success', 'Ticket tier is now on sale.');
    }

    public function archive(Organization $organization, Event $event, EventTicket $ticket)
    {
        $ticket->update(['status' => 'archived']);
        return back()->with('success', 'Ticket tier archived.');
    }
}
