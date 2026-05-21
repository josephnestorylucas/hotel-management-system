<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::withCount('events')
            ->orderBy('name')
            ->paginate(20);

        return view('organizations.index', compact('organizations'));
    }

    public function create()
    {
        return view('organizations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:organizations,name',
            'type' => 'required|in:company,ngo,institution,church,government,university,other',
            'registration_number' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:organizations,email',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_email' => 'nullable|email|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
        ]);

        $organization = Organization::create($validated);

        return redirect()->route('organizations.show', $organization)
            ->with('success', 'Organization created successfully.');
    }

    public function show(Organization $organization)
    {
        $organization->load(['events' => function ($query) {
            $query->orderBy('start_date', 'desc')->limit(10);
        }]);
        $organization->loadCount('events');

        return view('organizations.show', compact('organization'));
    }

    public function edit(Organization $organization)
    {
        return view('organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:organizations,name,' . $organization->id,
            'type' => 'required|in:company,ngo,institution,church,government,university,other',
            'registration_number' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:organizations,email,' . $organization->id,
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'contact_person_name' => 'nullable|string|max:255',
            'contact_person_email' => 'nullable|email|max:255',
            'contact_person_phone' => 'nullable|string|max:20',
            'status' => 'nullable|in:active,inactive,suspended',
        ]);

        $organization->update($validated);

        return redirect()->route('organizations.show', $organization)
            ->with('success', 'Organization updated successfully.');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return redirect()->route('organizations.index')
            ->with('success', 'Organization deleted successfully.');
    }

    public function verify(Organization $organization)
    {
        $organization->verify();

        return back()->with('success', 'Organization verified successfully.');
    }

    public function events(Organization $organization)
    {
        $events = $organization->events()
            ->withCount('attendances')
            ->orderBy('start_date', 'desc')
            ->paginate(20);

        return view('organizations.events', compact('organization', 'events'));
    }
}
