<?php
// app/Http/Controllers/ReservationController.php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;

/**
 * ReservationController — manages future room holds.
 *
 * Reservation = future intent. No billing.
 * Check-in converts a Reservation → Booking (active stay).
 */
class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['room.roomType', 'creator', 'guest'])
            ->orderBy('created_at', 'desc')
            ->paginate(min(request()->input('per_page', 20), 100));

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
                'guest_email' => 'required|email|max:255|unique:guests,email',
                'guest_phone' => 'required|string|max:20',
                'guest_id_type' => 'required|string|in:passport,driver_license,nida',
                'guest_id_number' => 'required|string|max:50',
                'guest_nationality' => 'required|string|max:100',
                'guest_id_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'guest_address' => 'required|string|max:500',
            ]);

            // Create new guest
            $guest = Guest::create([
                'first_name' => $guestData['guest_first_name'],
                'last_name' => $guestData['guest_last_name'],
                'email' => $guestData['guest_email'],
                'phone_number' => $guestData['guest_phone'],
                'id_number' => $guestData['guest_id_number'],
                'id_type' => $guestData['guest_id_type'],
                'nationality' => $guestData['guest_nationality'],
                'address' => $guestData['guest_address'],
            ]);

            // Handle ID photo upload using Spatie Media Library
            if ($request->hasFile('guest_id_photo')) {
                $guest->addMediaFromRequest('guest_id_photo')
                    ->toMediaCollection('id_documents');
            }

            $guestId = $guest->id;
        }

        // Validate reservation data
        $validated = $request->validate([
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'estimated_amount' => 'required|numeric|min:0',
            'room_id' => 'required|uuid|exists:rooms,id',
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
            'room_id' => $validated['room_id'],
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'number_of_guests' => $validated['number_of_guests'],
            'estimated_amount' => $validated['estimated_amount'],
            'status' => $validated['status'],
            'created_by' => auth()->id(),
        ]);

        // Load room relationship for notification
        $reservation->load('room.roomType');

        // Dispatch reservation confirmation notification (email + SMS)
        \App\Jobs\SendReservationConfirmationJob::dispatch([
            'reference'       => $reservation->reservation_number,
            'guest_name'      => $guest->full_name,
            'email'           => $guest->email,
            'phone'           => $guest->phone_number,
            'room_number'     => $reservation->room?->room_number ?? '',
            'room_type'       => $reservation->room?->roomType?->name ?? '',
            'check_in'        => $reservation->check_in_date->format('Y-m-d'),
            'check_out'       => $reservation->check_out_date->format('Y-m-d'),
            'nights'          => $reservation->nights,
            'guests'          => $reservation->number_of_guests,
            'estimated_total' => $reservation->estimated_amount,
        ])->onQueue('notifications');

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation created successfully.');
    }

    public function edit(Reservation $reservation)
    {
        if (!$reservation->canBeEdited()) {
            return back()->with('error', 'This reservation cannot be edited (already converted or cancelled).');
        }

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
        if (!$reservation->canBeEdited()) {
            return back()->with('error', 'This reservation cannot be edited.');
        }

        // Determine if using existing guest or creating new one
        $guestId = $request->input('guest_id');
        $createNewGuest = $request->input('create_new_guest') === '1';

        if ($createNewGuest) {
            // Validate new guest data
            $guestData = $request->validate([
                'guest_first_name' => 'required|string|max:255',
                'guest_last_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255|unique:guests,email',
                'guest_phone' => 'required|string|max:20',
                'guest_id_type' => 'required|string|in:passport,driver_license,nida',
                'guest_id_number' => 'required|string|max:50',
                'guest_nationality' => 'required|string|max:100',
                'guest_id_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'guest_address' => 'required|string|max:500',
            ]);

            // Create new guest
            $guest = Guest::create([
                'first_name' => $guestData['guest_first_name'],
                'last_name' => $guestData['guest_last_name'],
                'email' => $guestData['guest_email'],
                'phone_number' => $guestData['guest_phone'],
                'id_number' => $guestData['guest_id_number'],
                'id_type' => $guestData['guest_id_type'],
                'nationality' => $guestData['guest_nationality'],
                'address' => $guestData['guest_address'],
            ]);

            // Handle ID photo upload using Spatie Media Library
            if ($request->hasFile('guest_id_photo')) {
                $guest->addMediaFromRequest('guest_id_photo')
                    ->toMediaCollection('id_documents');
            }

            $guestId = $guest->id;
        }

        // Validate reservation data (only pending/confirmed/cancelled/no_show allowed)
        $validated = $request->validate([
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'estimated_amount' => 'required|numeric|min:0',
            'room_id' => 'required|uuid|exists:rooms,id',
            'status' => 'required|in:pending,confirmed,cancelled,no_show',
        ]);

        // Get guest for legacy field population
        $guest = $guestId ? Guest::find($guestId) : null;

        // Update reservation
        $updateData = [
            'guest_id' => $guestId,
            'room_id' => $validated['room_id'],
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'number_of_guests' => $validated['number_of_guests'],
            'estimated_amount' => $validated['estimated_amount'],
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

    /**
     * Confirm a pending reservation.
     */
    public function confirm(Reservation $reservation)
    {
        if (!$reservation->canBeConfirmed()) {
            return back()->with('error', 'Only pending reservations can be confirmed.');
        }

        $reservation->update(['status' => 'confirmed']);

        // Load relationships for notification
        $reservation->load(['guest', 'room.roomType']);

        // Dispatch confirmation notification (email + SMS)
        \App\Jobs\SendReservationConfirmationJob::dispatch([
            'reference'       => $reservation->reservation_number,
            'guest_name'      => $reservation->guest_display_name,
            'email'           => $reservation->guest_display_email,
            'phone'           => $reservation->guest_display_phone,
            'room_number'     => $reservation->room?->room_number ?? '',
            'room_type'       => $reservation->room?->roomType?->name ?? '',
            'check_in'        => $reservation->check_in_date->format('Y-m-d'),
            'check_out'       => $reservation->check_out_date->format('Y-m-d'),
            'nights'          => $reservation->nights,
            'guests'          => $reservation->number_of_guests,
            'estimated_total' => $reservation->estimated_amount,
        ])->onQueue('notifications');

        return back()->with('success', 'Reservation confirmed successfully.');
    }

    /**
     * Check in a confirmed reservation → creates a Booking (active stay).
     * The reservation status becomes "converted".
     */
    public function checkIn(Reservation $reservation)
    {
        if (!$reservation->canBeCheckedIn()) {
            return back()->with('error', 'Only confirmed reservations with a room assigned can be checked in.');
        }

        // Create Booking from this reservation (handles status + room via observer)
        $booking = Booking::createFromReservation($reservation, auth()->id());

        // Load relationships for notification
        $booking->load(['guest', 'room.roomType']);

        // Dispatch booking confirmation notification (email + SMS)
        \App\Jobs\SendBookingConfirmationJob::dispatch([
            'reference'   => $booking->booking_number,
            'guest_name'  => $booking->guest_display_name,
            'email'       => $booking->guest_display_email,
            'phone'       => $booking->guest_display_phone,
            'room_number' => $booking->room?->room_number ?? '',
            'room_type'   => $booking->room?->roomType?->name ?? '',
            'check_in'    => $booking->check_in_date->format('Y-m-d'),
            'check_out'   => $booking->check_out_date->format('Y-m-d'),
            'nights'      => $booking->nights,
            'rate'        => $booking->room?->roomType?->base_rate ?? 0,
            'total'       => $booking->total_amount ?? 0,
        ])->onQueue('notifications');

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Guest checked in successfully. Booking #' . $booking->booking_number . ' created.');
    }

    /**
     * Cancel a pending or confirmed reservation.
     */
    public function cancel(Reservation $reservation)
    {
        if (!$reservation->canBeCancelled()) {
            return back()->with('error', 'Only pending or confirmed reservations can be cancelled.');
        }

        $reservation->update(['status' => 'cancelled']);

        return back()->with('success', 'Reservation cancelled successfully.');
    }

    /**
     * Mark reservation as no-show.
     */
    public function noShow(Reservation $reservation)
    {
        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Only pending or confirmed reservations can be marked as no-show.');
        }

        $reservation->update(['status' => 'no_show']);

        return back()->with('success', 'Reservation marked as no-show.');
    }
}