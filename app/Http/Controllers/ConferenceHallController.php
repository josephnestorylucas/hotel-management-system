<?php
// app/Http/Controllers/ConferenceHallController.php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\ConferenceHall;
use Illuminate\Http\Request;

class ConferenceHallController extends Controller
{
    public function index()
    {
        $halls = ConferenceHall::with(['building'])
            ->withCount('conferenceBookings')
            ->orderBy('name')
            ->paginate(20);

        return view('conference-halls.index', compact('halls'));
    }

    public function create()
    {
        $buildings = Building::where('is_active', true)->orderBy('name')->get();
        
        return view('conference-halls.create', compact('buildings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'building_id' => 'nullable|uuid|exists:buildings,id',
            'capacity' => 'required|integer|min:1',
            'hourly_rate' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,TZS',
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
        $conferenceHall->load('building');

        return view('conference-halls.show', compact('conferenceHall'));
    }

    public function edit(ConferenceHall $conferenceHall)
    {
        $buildings = Building::where('is_active', true)->orderBy('name')->get();
        
        return view('conference-halls.edit', compact('conferenceHall', 'buildings'));
    }

    public function update(Request $request, ConferenceHall $conferenceHall)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'building_id' => 'nullable|uuid|exists:buildings,id',
            'capacity' => 'required|integer|min:1',
            'hourly_rate' => 'required|numeric|min:0',
            'currency' => 'required|in:USD,TZS',
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

        $this->softDelete($conferenceHall);

        return redirect()->route('conference-halls.index')
            ->with('success', 'Conference hall archived successfully.');
    }

    public function archived()
    {
        $records = ConferenceHall::onlyDeleted()->latest('deleted_at')->paginate(20);
        return view('conference-halls.archived', compact('records'));
    }

    public function restore(ConferenceHall $conferenceHall)
    {
        $this->restoreModel($conferenceHall);
        return redirect()->route('conference-halls.index')->with('success', 'Conference hall restored successfully.');
    }
}