<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function index()
    {
        $institutions = Institution::withCount('conferenceBookings')
            ->orderBy('name')
            ->paginate(20);

        return view('institutions.index', compact('institutions'));
    }

    public function create()
    {
        return view('institutions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone'          => 'required|string|max:30',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:500',
            'notes'          => 'nullable|string',
        ]);

        $institution = Institution::create($validated);

        return redirect()->route('institutions.show', $institution)
            ->with('success', 'Institution created successfully.');
    }

    public function show(Institution $institution)
    {
        $institution->load(['conferenceBookings.conferenceHall']);

        return view('institutions.show', compact('institution'));
    }

    public function edit(Institution $institution)
    {
        return view('institutions.edit', compact('institution'));
    }

    public function update(Request $request, Institution $institution)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone'          => 'required|string|max:30',
            'email'          => 'nullable|email|max:255',
            'address'        => 'nullable|string|max:500',
            'notes'          => 'nullable|string',
        ]);

        $institution->update($validated);

        return redirect()->route('institutions.show', $institution)
            ->with('success', 'Institution updated successfully.');
    }

    public function destroy(Institution $institution)
    {
        $activeBookingsCount = $institution->conferenceBookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($activeBookingsCount > 0) {
            return redirect()->route('institutions.show', $institution)
                ->with('error', "Cannot delete institution: it has {$activeBookingsCount} active booking(s). Cancel or complete all bookings first.");
        }

        $this->softDelete($institution);

        return redirect()->route('institutions.index')
            ->with('success', 'Institution deleted successfully.');
    }

    public function archived()
    {
        $records = Institution::onlyDeleted()->latest('deleted_at')->paginate(20);
        return view('institutions.archived', compact('records'));
    }

    public function restore(Institution $institution)
    {
        $this->restoreModel($institution);
        return redirect()->route('institutions.index')->with('success', 'Institution restored successfully.');
    }
}
