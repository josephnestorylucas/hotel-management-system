<?php
// app/Http/Controllers/ConferenceHallController.php

namespace App\Http\Controllers;

use App\Models\ConferenceHall;
use Illuminate\Http\Request;

class ConferenceHallController extends Controller
{
    public function index()
    {
        $halls = ConferenceHall::withCount('conferenceBookings')
            ->orderBy('name')
            ->paginate(20);

        return view('conference-halls.index', compact('halls'));
    }

    public function create()
    {
        return view('conference-halls.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'required|in:available,maintenance',
            'amenities' => 'nullable|array',
            'description' => 'nullable|string',
        ]);

        ConferenceHall::create($validated);

        return redirect()->route('conference-halls.index')
            ->with('success', 'Conference hall created successfully.');
    }

    public function show(ConferenceHall $conferenceHall)
    {
        $conferenceHall->load(['conferenceBookings.guest', 'conferenceBookings.conference']);
        
        return view('conference-halls.show', compact('conferenceHall'));
    }

    public function edit(ConferenceHall $conferenceHall)
    {
        return view('conference-halls.edit', compact('conferenceHall'));
    }

    public function update(Request $request, ConferenceHall $conferenceHall)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'hourly_rate' => 'required|numeric|min:0',
            'status' => 'required|in:available,maintenance',
            'amenities' => 'nullable|array',
            'description' => 'nullable|string',
        ]);

        $conferenceHall->update($validated);

        return redirect()->route('conference-halls.index')
            ->with('success', 'Conference hall updated successfully.');
    }

    public function destroy(ConferenceHall $conferenceHall)
    {
        if ($conferenceHall->conferenceBookings()->whereIn('status', ['pending', 'confirmed'])->exists()) {
            return back()->with('error', 'Cannot delete hall with active bookings.');
        }

        $conferenceHall->delete();

        return redirect()->route('conference-halls.index')
            ->with('success', 'Conference hall deleted successfully.');
    }
}