<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class BookingController extends Controller
{
    // ═══════════════════════════════════════════════════════════════
    //  PUBLIC BOOKING FLOW (No authentication required)
    // ═══════════════════════════════════════════════════════════════

    /**
     * Show the public booking page.
     */
    public function showBookingPage()
    {
        $roomTypes = RoomType::all();

        return view('public.booking', compact('roomTypes'));
    }

    /**
     * Search for available rooms based on dates, guests, and room type.
     */
    public function searchAvailability(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'room_type' => 'nullable|uuid|exists:room_types,id',
        ]);

        $roomTypes = RoomType::all();
        $checkIn = $request->check_in;
        $checkOut = $request->check_out;
        $guests = $request->guests;
        $roomTypeId = $request->room_type;

        // Get available rooms for the selected dates using the new scope
        $availableRooms = Room::with(['roomType', 'floor'])
            ->availableForDates($checkIn, $checkOut)
            ->when($roomTypeId, function ($query) use ($roomTypeId) {
                $query->where('room_type_id', $roomTypeId);
            })
            ->get();

        return view('public.booking', compact('roomTypes', 'availableRooms', 'checkIn', 'checkOut', 'guests'));
    }

    /**
     * Show details for a selected room.
     */
    public function showRoom(Room $room, Request $request)
    {
        $roomTypes = RoomType::all();
        $selectedRoom = $room->load(['roomType', 'floor']);
        $checkIn = $request->check_in;
        $checkOut = $request->check_out;
        $guests = $request->guests;

        // Calculate total price
        $nights = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
        $totalPrice = $nights * ($room->roomType->base_rate ?? 150);

        return view('public.booking', compact('roomTypes', 'selectedRoom', 'checkIn', 'checkOut', 'guests', 'totalPrice'));
    }

    /**
     * Store a new public booking.
     * Creates both a Booking and a linked Reservation via the observer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|uuid|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:50',
            'guest_country' => 'nullable|string|max:100',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        $room = Room::with('roomType')->findOrFail($validated['room_id']);

        // Double-check room availability
        if (!Booking::isRoomAvailable($room->id, $validated['check_in'], $validated['check_out'])) {
            return back()
                ->withInput()
                ->with('error', 'Sorry, this room is no longer available for the selected dates. Please choose another room.');
        }

        // Calculate total amount
        $nights = Carbon::parse($validated['check_in'])->diffInDays(Carbon::parse($validated['check_out']));
        $totalAmount = $nights * ($room->roomType->base_rate ?? 150);

        // Try to find an existing guest by email
        $guest = Guest::where('email', $validated['guest_email'])->first();

        // Create the booking (observer will auto-create the reservation)
        $booking = Booking::create([
            'guest_id' => $guest?->id,
            'guest_name' => $validated['guest_name'],
            'guest_email' => $validated['guest_email'],
            'guest_phone' => $validated['guest_phone'],
            'guest_country' => $validated['guest_country'] ?? null,
            'room_id' => $room->id,
            'check_in_date' => $validated['check_in'],
            'check_out_date' => $validated['check_out'],
            'number_of_guests' => $validated['guests'],
            'total_amount' => $totalAmount,
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => 'pending',
            'source' => 'online',
        ]);

        return redirect()->route('booking.confirmation', $booking->id);
    }

    /**
     * Show booking confirmation page.
     */
    public function showConfirmation(Booking $booking)
    {
        $booking->load(['room.roomType', 'room.floor', 'reservation']);

        return view('public.booking-confirmation', compact('booking'));
    }

    // ═══════════════════════════════════════════════════════════════
    //  FRONTDESK BOOKING MANAGEMENT (Authentication required)
    // ═══════════════════════════════════════════════════════════════

    /**
     * List all bookings for the frontdesk.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['room.roomType', 'guest', 'creator', 'reservation'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by source
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('check_in_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('check_out_date', '<=', $request->date_to);
        }

        // Search by guest name, email, or booking number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhere('booking_number', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%");
            });
        }

        $bookings = $query->paginate(20);

        // Stats for dashboard cards
        $stats = [
            'total' => Booking::count(),
            'pending' => Booking::pending()->count(),
            'confirmed' => Booking::confirmed()->count(),
            'today_checkins' => Booking::today()->where('status', 'confirmed')->count(),
            'today_checkouts' => Booking::where('check_out_date', now()->toDateString())
                ->where('status', 'checked_in')->count(),
        ];

        return view('bookings.index', compact('bookings', 'stats'));
    }

    /**
     * Show create booking form for frontdesk.
     */
    public function create(Request $request)
    {
        $guests = Guest::orderBy('first_name')->get();
        $roomTypes = RoomType::all();

        // Pre-select guest if provided
        $selectedGuest = null;
        if ($request->has('guest_id')) {
            $selectedGuest = Guest::find($request->guest_id);
        }

        // Get available rooms if dates are provided
        $availableRooms = collect();
        if ($request->filled('check_in') && $request->filled('check_out')) {
            $availableRooms = Room::with(['roomType', 'floor.building'])
                ->availableForDates($request->check_in, $request->check_out)
                ->when($request->room_type, fn($q) => $q->where('room_type_id', $request->room_type))
                ->orderBy('room_number')
                ->get();
        }

        return view('bookings.create', compact('guests', 'roomTypes', 'selectedGuest', 'availableRooms'));
    }

    /**
     * Store a new frontdesk booking.
     */
    public function storeFrontdesk(Request $request)
    {
        // Determine if using existing guest or new guest info
        $guestId = $request->input('guest_id');
        $createNewGuest = $request->input('create_new_guest') === '1';

        if ($createNewGuest || !$guestId) {
            // Validate and create new guest
            $guestData = $request->validate([
                'guest_first_name' => 'required|string|max:255',
                'guest_last_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255|unique:guests,email',
                'guest_phone' => 'required|string|max:20',
                'guest_id_number' => 'required|string|max:50',
                'guest_nationality' => 'required|string|max:100',
            ]);

            $guest = Guest::create([
                'first_name' => $guestData['guest_first_name'],
                'last_name' => $guestData['guest_last_name'],
                'email' => $guestData['guest_email'],
                'phone_number' => $guestData['guest_phone'],
                'id_number' => $guestData['guest_id_number'],
                'nationality' => $guestData['guest_nationality'],
            ]);

            $guestId = $guest->id;
        }

        // Validate booking data
        $validated = $request->validate([
            'room_id' => 'required|uuid|exists:rooms,id',
            'check_in_date' => 'required|date|after_or_equal:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'special_requests' => 'nullable|string|max:1000',
            'source' => 'nullable|in:frontdesk,phone,walkin',
            'status' => 'nullable|in:pending,confirmed',
        ]);

        $room = Room::with('roomType')->findOrFail($validated['room_id']);

        // Check room availability
        if (!Booking::isRoomAvailable($room->id, $validated['check_in_date'], $validated['check_out_date'])) {
            return back()
                ->withInput()
                ->with('error', 'This room is not available for the selected dates.');
        }

        // Get guest data for legacy fields
        $guest = Guest::findOrFail($guestId);

        // Create booking (observer will auto-create reservation)
        $booking = Booking::create([
            'guest_id' => $guestId,
            'guest_name' => $guest->full_name,
            'guest_email' => $guest->email,
            'guest_phone' => $guest->phone_number,
            'guest_country' => $guest->nationality,
            'room_id' => $room->id,
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'number_of_guests' => $validated['number_of_guests'],
            'total_amount' => $validated['total_amount'],
            'special_requests' => $validated['special_requests'] ?? null,
            'status' => $validated['status'] ?? 'confirmed',
            'source' => $validated['source'] ?? 'frontdesk',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('bookings.index')
            ->with('success', 'Booking created successfully. Reservation #' . $booking->reservation?->reservation_number . ' has been generated.');
    }

    /**
     * Show a specific booking.
     */
    public function show(Booking $booking)
    {
        $booking->load(['room.roomType', 'room.floor.building', 'guest', 'creator', 'reservation']);

        return view('bookings.show', compact('booking'));
    }

    /**
     * Show edit form for a booking.
     */
    public function edit(Booking $booking)
    {
        if (!$booking->canBeEdited()) {
            return back()->with('error', 'This booking cannot be edited in its current status.');
        }

        $booking->load(['room.roomType', 'guest']);
        $guests = Guest::orderBy('first_name')->get();
        $roomTypes = RoomType::all();

        // Get available rooms (include currently assigned room)
        $availableRooms = Room::with(['roomType', 'floor.building'])
            ->where(function ($query) use ($booking) {
                $query->where(function ($q) use ($booking) {
                    $q->availableForDates(
                        $booking->check_in_date->toDateString(),
                        $booking->check_out_date->toDateString()
                    );
                })->orWhere('id', $booking->room_id);
            })
            ->orderBy('room_number')
            ->get();

        return view('bookings.edit', compact('booking', 'guests', 'roomTypes', 'availableRooms'));
    }

    /**
     * Update an existing booking.
     */
    public function update(Request $request, Booking $booking)
    {
        if (!$booking->canBeEdited()) {
            return back()->with('error', 'This booking cannot be edited in its current status.');
        }

        $validated = $request->validate([
            'room_id' => 'required|uuid|exists:rooms,id',
            'check_in_date' => 'required|date',
            'check_out_date' => 'required|date|after:check_in_date',
            'number_of_guests' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'special_requests' => 'nullable|string|max:1000',
            'guest_id' => 'nullable|uuid|exists:guests,id',
        ]);

        // Check room availability (excluding current booking)
        if (!Booking::isRoomAvailable($validated['room_id'], $validated['check_in_date'], $validated['check_out_date'], $booking->id)) {
            return back()
                ->withInput()
                ->with('error', 'The selected room is not available for these dates.');
        }

        // Update guest info if guest changed
        $updateData = [
            'room_id' => $validated['room_id'],
            'check_in_date' => $validated['check_in_date'],
            'check_out_date' => $validated['check_out_date'],
            'number_of_guests' => $validated['number_of_guests'],
            'total_amount' => $validated['total_amount'],
            'special_requests' => $validated['special_requests'] ?? null,
        ];

        if ($request->filled('guest_id')) {
            $guest = Guest::findOrFail($validated['guest_id']);
            $updateData['guest_id'] = $guest->id;
            $updateData['guest_name'] = $guest->full_name;
            $updateData['guest_email'] = $guest->email;
            $updateData['guest_phone'] = $guest->phone_number;
            $updateData['guest_country'] = $guest->nationality;
        }

        $booking->update($updateData);

        // Sync changes to linked reservation
        if ($booking->reservation) {
            $booking->reservation->update([
                'room_id' => $booking->room_id,
                'guest_id' => $booking->guest_id,
                'guest_name' => $booking->guest_name,
                'guest_email' => $booking->guest_email,
                'guest_phone' => $booking->guest_phone,
                'check_in_date' => $booking->check_in_date,
                'check_out_date' => $booking->check_out_date,
                'number_of_guests' => $booking->number_of_guests,
                'total_amount' => $booking->total_amount,
            ]);
        }

        return redirect()->route('bookings.index')
            ->with('success', 'Booking updated successfully.');
    }

    // ═══════════════════════════════════════════════════════════════
    //  STATUS MANAGEMENT ACTIONS
    // ═══════════════════════════════════════════════════════════════

    /**
     * Confirm a pending booking.
     */
    public function confirm(Booking $booking)
    {
        if (!$booking->canBeConfirmed()) {
            return back()->with('error', 'Only pending bookings can be confirmed.');
        }

        $booking->update(['status' => 'confirmed']);

        return back()->with('success', 'Booking confirmed successfully.');
    }

    /**
     * Check in a confirmed booking.
     */
    public function checkIn(Booking $booking)
    {
        if (!$booking->canBeCheckedIn()) {
            return back()->with('error', 'Only confirmed bookings can be checked in.');
        }

        if (!$booking->room_id) {
            return back()->with('error', 'Please assign a room before checking in.');
        }

        $booking->update(['status' => 'checked_in']);

        return back()->with('success', 'Guest checked in successfully.');
    }

    /**
     * Check out a checked-in booking.
     * Collects all unpaid booking charges and marks them as paid.
     */
    public function checkOut(Booking $booking)
    {
        if (!$booking->canBeCheckedOut()) {
            return back()->with('error', 'Only checked-in bookings can be checked out.');
        }

        // Check for pending/undelivered laundry orders
        $pendingLaundry = $booking->laundryOrders()
            ->whereIn('status', ['pending', 'in_progress', 'completed'])
            ->count();

        if ($pendingLaundry > 0) {
            return back()->with('error', 'Cannot check out: there are ' . $pendingLaundry . ' pending/undelivered laundry order(s). Please deliver all laundry first.');
        }

        // Collect all unpaid charges
        $unpaidCharges = $booking->bookingCharges()->unpaid()->sum('amount');
        $booking->bookingCharges()->unpaid()->update(['status' => 'paid']);

        $booking->update(['status' => 'checked_out']);

        $message = 'Guest checked out successfully.';
        if ($unpaidCharges > 0) {
            $message .= ' Total service charges collected: ' . number_format($unpaidCharges);
        }

        return back()->with('success', $message);
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, Booking $booking)
    {
        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'This booking cannot be cancelled in its current status.');
        }

        $reason = $request->input('cancellation_reason', 'Cancelled by staff');

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ]);

        return back()->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Mark a booking as no-show.
     */
    public function noShow(Booking $booking)
    {
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Only pending or confirmed bookings can be marked as no-show.');
        }

        $booking->update(['status' => 'no_show']);

        return back()->with('success', 'Booking marked as no-show.');
    }

    /**
     * Destroy a booking (soft-delete or hard-delete).
     */
    public function destroy(Booking $booking)
    {
        // Only allow deletion of cancelled or no-show bookings
        if (!in_array($booking->status, ['cancelled', 'no_show'])) {
            return back()->with('error', 'Only cancelled or no-show bookings can be deleted.');
        }

        $booking->delete();

        return redirect()->route('bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    // ═══════════════════════════════════════════════════════════════
    //  API / UTILITY METHODS
    // ═══════════════════════════════════════════════════════════════

    /**
     * Check room availability via AJAX.
     */
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_id' => 'required|uuid|exists:rooms,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'exclude_booking_id' => 'nullable|uuid',
        ]);

        $available = Booking::isRoomAvailable(
            $request->room_id,
            $request->check_in,
            $request->check_out,
            $request->exclude_booking_id
        );

        return response()->json([
            'available' => $available,
            'message' => $available
                ? 'Room is available for the selected dates.'
                : 'Room is not available for the selected dates.',
        ]);
    }

    /**
     * Get available rooms for dates via AJAX (used in frontdesk booking form).
     */
    public function getAvailableRooms(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'room_type_id' => 'nullable|uuid|exists:room_types,id',
        ]);

        $rooms = Room::with(['roomType', 'floor.building'])
            ->availableForDates($request->check_in, $request->check_out)
            ->when($request->room_type_id, fn($q) => $q->where('room_type_id', $request->room_type_id))
            ->orderBy('room_number')
            ->get();

        return response()->json([
            'rooms' => $rooms,
            'count' => $rooms->count(),
        ]);
    }
}
