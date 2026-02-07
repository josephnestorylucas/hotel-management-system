<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;
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
            'email' => 'required|email|max:255|unique:guests,email',
            'phone_number' => 'required|string|max:20',
            'id_number' => 'required|string|max:50',
            'address' => 'nullable|string|max:500',
            'nationality' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date|before:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'id_documents' => 'nullable|array',
            'id_documents.*' => 'file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        // Remove file fields from validated data (handled separately via Media Library)
        unset($validated['photo'], $validated['id_documents']);

        // Create the guest
        $guest = Guest::create($validated);

        // Handle photo upload using Spatie Media Library
        if ($request->hasFile('photo')) {
            $guest->addMediaFromRequest('photo')
                ->toMediaCollection('guest_photo');
        }

        // Handle multiple ID document uploads using Spatie Media Library
        if ($request->hasFile('id_documents')) {
            foreach ($request->file('id_documents') as $document) {
                $guest->addMedia($document)
                    ->toMediaCollection('id_documents');
            }
        }

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
        $guest->load(['reservations.room.roomType', 'media']);
        return view('guests.show', compact('guest'));
    }

    /**
     * Show the form for editing the specified guest.
     */
    public function edit(Guest $guest)
    {
        $guest->load('media');
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
                'required',
                'email',
                'max:255',
                Rule::unique('guests', 'email')->ignore($guest->id),
            ],
            'phone_number' => 'required|string|max:20',
            'id_number' => 'required|string|max:50',
            'address' => 'nullable|string|max:500',
            'nationality' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date|before:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'id_documents' => 'nullable|array',
            'id_documents.*' => 'file|mimes:jpeg,png,jpg,pdf|max:5120',
            'remove_photo' => 'nullable|boolean',
            'remove_documents' => 'nullable|array',
            'remove_documents.*' => 'integer',
        ]);

        // Remove file and removal fields from validated data
        unset($validated['photo'], $validated['id_documents'], $validated['remove_photo'], $validated['remove_documents']);

        // Update guest data
        $guest->update($validated);

        // Handle photo removal
        if ($request->boolean('remove_photo')) {
            $guest->clearMediaCollection('guest_photo');
        }

        // Handle photo upload/replacement using Spatie Media Library
        // Note: singleFile() in registerMediaCollections() handles automatic replacement
        if ($request->hasFile('photo')) {
            $guest->addMediaFromRequest('photo')
                ->toMediaCollection('guest_photo');
        }

        // Handle document removal
        if ($request->has('remove_documents') && is_array($request->remove_documents)) {
            foreach ($request->remove_documents as $mediaId) {
                $media = $guest->media()->find($mediaId);
                if ($media) {
                    $media->delete();
                }
            }
        }

        // Handle new ID document uploads
        if ($request->hasFile('id_documents')) {
            foreach ($request->file('id_documents') as $document) {
                $guest->addMedia($document)
                    ->toMediaCollection('id_documents');
            }
        }

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

        // Media files are automatically deleted by Spatie Media Library
        $guest->delete();

        return redirect()->route('guests.index')
            ->with('success', 'Guest deleted successfully.');
    }

    /**
     * Remove a specific media item from a guest.
     */
    public function removeMedia(Guest $guest, $mediaId)
    {
        $media = $guest->media()->find($mediaId);
        
        if (!$media) {
            return back()->with('error', 'Media not found.');
        }

        $media->delete();

        return back()->with('success', 'Document removed successfully.');
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
