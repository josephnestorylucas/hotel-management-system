<?php
// app/Http/Controllers/ReservationController.php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['room.roomType', 'creator', 'guest'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reservations.index', compact('reservations'));
    }

    public function create(Request $request)
    {
        // Get all available rooms
        $availableRooms = Room::where('status', 'available')
            ->where('is_active', true)
            ->with(['roomType', 'floor.building'])
            ->orderBy('room_number')
            ->get();

        // Get all guests for selection
        $guests = Guest::orderBy('first_name')->get();

        // Pre-select guest if provided
        $selectedGuest = null;
        if ($request->has('guest_id')) {
            $selectedGuest = Guest::find($request->guest_id);
        }

        return view('reservations.create', compact('availableRooms', 'guests', 'selectedGuest'));
    }

    public function store(Request $request)
    {
        // Determine if using existing guest or creating new one
        $guestId = $request->input('guest_id');
        $createNewGuest = $request->input('create_new_guest') === '1';

        if ($createNewGuest || !$guestId) {
            // Validate new guest data
            $guestData = $request->validate([
                'guest_first_name' => 'required|string|max:255',
                'guest_last_name' => 'required|string|max:255',
                'guest_email' => 'nullable|email|max:255|unique:guests,email',
                'guest_phone' => 'required|string|max:20',
                'guest_id_number' => 'nullable|string|max:50',
                'guest_nationality' => 'nullable|string|max:100',
            ]);

            // Create new guest
            $guest = Guest::create([
                'first_name' => $guestData['guest_first_name'],
                'last_name' => $guestData['guest_last_name'],
                'email' => $guestData['guest_email'] ?? null,
                'phone_number' => $guestData['guest_phone'],
                'id_number' => $guestData['guest_id_number'] ?? null,
                'nationality' => $guestData['guest_nationality'] ?? null,
            ]);

            $guestId = $guest->id;
        }

        // Validate reservation data
        $validated = $request->validate([
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'room_id' => 'nullable|uuid|exists:rooms,id',
            'status' => 'required|in:pending,confirmed',
        ]);

        // Get guest for legacy field population
        $guest = Guest::find($guestId);

        // Create reservation
        $reservation = Reservation::create([
            'guest_id' => $guestId,
            'guest_name' => $guest->full_name,
            'guest_phone' => $guest->phone_number,
            'guest_email' => $guest->email,
            'room_id' => $validated['room_id'] ?? null,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'number_of_guests' => $validated['number_of_guests'],
            'total_amount' => $validated['total_amount'],
            'status' => $validated['status'],
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation created successfully.');
    }

    public function edit(Reservation $reservation)
    {
        // Get available rooms plus the currently assigned room
        $availableRooms = Room::where(function($query) use ($reservation) {
                $query->where('status', 'available')
                      ->where('is_active', true);
                
                // Include the currently assigned room even if it's not available
                if ($reservation->room_id) {
                    $query->orWhere('id', $reservation->room_id);
                }
            })
            ->with(['roomType', 'floor.building'])
            ->orderBy('room_number')
            ->get();

        // Get all guests
        $guests = Guest::orderBy('first_name')->get();

        // Load guest relationship
        $reservation->load('guest');

        return view('reservations.edit', compact('reservation', 'availableRooms', 'guests'));
    }

    public function update(Request $request, Reservation $reservation)
    {
        // Determine if using existing guest or creating new one
        $guestId = $request->input('guest_id');
        $createNewGuest = $request->input('create_new_guest') === '1';

        if ($createNewGuest) {
            // Validate new guest data
            $guestData = $request->validate([
                'guest_first_name' => 'required|string|max:255',
                'guest_last_name' => 'required|string|max:255',
                'guest_email' => 'nullable|email|max:255|unique:guests,email',
                'guest_phone' => 'required|string|max:20',
                'guest_id_number' => 'nullable|string|max:50',
                'guest_nationality' => 'nullable|string|max:100',
            ]);

            // Create new guest
            $guest = Guest::create([
                'first_name' => $guestData['guest_first_name'],
                'last_name' => $guestData['guest_last_name'],
                'email' => $guestData['guest_email'] ?? null,
                'phone_number' => $guestData['guest_phone'],
                'id_number' => $guestData['guest_id_number'] ?? null,
                'nationality' => $guestData['guest_nationality'] ?? null,
            ]);

            $guestId = $guest->id;
        }

        // Validate reservation data
        $validated = $request->validate([
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'room_id' => 'nullable|uuid|exists:rooms,id',
            'status' => 'required|in:pending,confirmed,checked_in,checked_out,cancelled,no_show',
        ]);

        // Get guest for legacy field population
        $guest = $guestId ? Guest::find($guestId) : null;

        // Update reservation
        $updateData = [
            'guest_id' => $guestId,
            'room_id' => $validated['room_id'] ?? null,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'number_of_guests' => $validated['number_of_guests'],
            'total_amount' => $validated['total_amount'],
            'status' => $validated['status'],
        ];

        // Update legacy fields if guest exists
        if ($guest) {
            $updateData['guest_name'] = $guest->full_name;
            $updateData['guest_phone'] = $guest->phone_number;
            $updateData['guest_email'] = $guest->email;
        }

        $reservation->update($updateData);

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation updated successfully.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation deleted successfully.');
    }

    public function checkIn(Reservation $reservation)
    {
        if ($reservation->status !== 'confirmed') {
            return back()->with('error', 'Only confirmed reservations can be checked in.');
        }

        if (!$reservation->room_id) {
            return back()->with('error', 'Please assign a room before checking in.');
        }

        $reservation->update(['status' => 'checked_in']);

        return back()->with('success', 'Guest checked in successfully.');
    }

    public function checkOut(Reservation $reservation)
    {
        if ($reservation->status !== 'checked_in') {
            return back()->with('error', 'Only checked-in reservations can be checked out.');
        }

        $reservation->update(['status' => 'checked_out']);

        return back()->with('success', 'Guest checked out successfully.');
    }

    public function cancel(Reservation $reservation)
    {
        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Only pending or confirmed reservations can be cancelled.');
        }

        $reservation->update(['status' => 'cancelled']);

        return back()->with('success', 'Reservation cancelled successfully.');
    }
}