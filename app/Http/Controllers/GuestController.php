<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class GuestController extends Controller
{
    /**
     * Display a listing of guests.
     */
    public function index(Request $request)
    {
        $query = Guest::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        $guests = $query->withCount('reservations')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('guests.index', compact('guests'));
    }

    /**
     * Show the form for creating a new guest.
     */
    public function create()
    {
        return view('guests.create');
    }

    /**
     * Store a newly created guest.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:guests,email',
            'phone_number' => 'required|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'nationality' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date|before:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'id_document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('guests/photos', 'public');
        }

        // Handle ID document upload
        if ($request->hasFile('id_document')) {
            $validated['id_document'] = $request->file('id_document')->store('guests/documents', 'public');
        }

        $guest = Guest::create($validated);

        // Check if this was called from reservation create page
        if ($request->has('return_to_reservation')) {
            return redirect()->route('reservations.create', ['guest_id' => $guest->id])
                ->with('success', 'Guest created successfully.');
        }

        return redirect()->route('guests.index')
            ->with('success', 'Guest created successfully.');
    }

    /**
     * Display the specified guest.
     */
    public function show(Guest $guest)
    {
        $guest->load(['reservations.room.roomType']);
        return view('guests.show', compact('guest'));
    }

    /**
     * Show the form for editing the specified guest.
     */
    public function edit(Guest $guest)
    {
        return view('guests.edit', compact('guest'));
    }

    /**
     * Update the specified guest.
     */
    public function update(Request $request, Guest $guest)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('guests', 'email')->ignore($guest->id),
            ],
            'phone_number' => 'required|string|max:20',
            'id_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'nationality' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date|before:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'id_document' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($guest->photo) {
                Storage::disk('public')->delete($guest->photo);
            }
            $validated['photo'] = $request->file('photo')->store('guests/photos', 'public');
        }

        // Handle ID document upload
        if ($request->hasFile('id_document')) {
            // Delete old document
            if ($guest->id_document) {
                Storage::disk('public')->delete($guest->id_document);
            }
            $validated['id_document'] = $request->file('id_document')->store('guests/documents', 'public');
        }

        $guest->update($validated);

        return redirect()->route('guests.index')
            ->with('success', 'Guest updated successfully.');
    }

    /**
     * Remove the specified guest.
     */
    public function destroy(Guest $guest)
    {
        // Check if guest has reservations
        if ($guest->reservations()->count() > 0) {
            return back()->with('error', 'Cannot delete guest with existing reservations.');
        }

        // Delete files
        if ($guest->photo) {
            Storage::disk('public')->delete($guest->photo);
        }
        if ($guest->id_document) {
            Storage::disk('public')->delete($guest->id_document);
        }

        $guest->delete();

        return redirect()->route('guests.index')
            ->with('success', 'Guest deleted successfully.');
    }

    /**
     * Search guests for AJAX autocomplete.
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        
        $guests = Guest::where(function ($query) use ($search) {
            $query->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
        })
        ->limit(10)
        ->get(['id', 'first_name', 'last_name', 'email', 'phone_number']);

        return response()->json($guests->map(function ($guest) {
            return [
                'id' => $guest->id,
                'name' => $guest->full_name,
                'email' => $guest->email,
                'phone' => $guest->phone_number,
            ];
        }));
    }
}
