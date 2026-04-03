<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * BookingController — manages active guest stays + billing.
 *
 * Booking = active stay. Created at check-in (from Reservation or walk-in).
 * All charges attach here. Ends at checkout.
 *
 * Public booking flow creates a Reservation (future intent), NOT a Booking.
 */
class BookingController extends Controller
{
    // ═══════════════════════════════════════════════════════════════
    //  PUBLIC BOOKING FLOW — creates a Reservation (future intent)
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
        $guests = (int) $request->guests;
        $roomTypeId = $request->room_type;

        // Get available rooms for the selected dates
        // Filter by room type and ensure max_occupancy can accommodate the guests
        $availableRooms = Room::with(['roomType', 'floor'])
            ->availableForDates($checkIn, $checkOut)
            ->when($roomTypeId, function ($query) use ($roomTypeId) {
                $query->where('room_type_id', $roomTypeId);
            })
            ->whereHas('roomType', function ($query) use ($guests) {
                $query->where('max_occupancy', '>=', $guests);
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

        // Calculate estimated price
        $nights = Carbon::parse($checkIn)->diffInDays(Carbon::parse($checkOut));
        $totalPrice = $nights * ($room->roomType->base_rate ?? 150);

        return view('public.booking', compact('roomTypes', 'selectedRoom', 'checkIn', 'checkOut', 'guests', 'totalPrice'));
    }

    /**
     * Store a public booking — creates a Reservation (future intent).
     * NOT a Booking (Booking = active stay, created at check-in).
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

        // Use transaction with lock to prevent race conditions (double-booking)
        try {
            $reservation = DB::transaction(function () use ($validated, $room) {
                // Double-check room availability with pessimistic lock
                if (!Booking::isRoomAvailable($room->id, $validated['check_in'], $validated['check_out'], null, true)) {
                    throw new \RuntimeException('Sorry, this room is no longer available for the selected dates. Please choose another room.');
                }

                // Calculate estimated amount
                $nights = Carbon::parse($validated['check_in'])->diffInDays(Carbon::parse($validated['check_out']));
                $estimatedAmount = $nights * ($room->roomType->base_rate ?? 150);

                // Try to find an existing guest by email
                $guest = Guest::where('email', $validated['guest_email'])->first();

                // Create a Reservation (future intent — NOT a Booking)
                return Reservation::create([
                    'guest_id' => $guest?->id,
                    'guest_name' => $validated['guest_name'],
                    'guest_email' => $validated['guest_email'],
                    'guest_phone' => $validated['guest_phone'],
                    'room_id' => $room->id,
                    'check_in_date' => $validated['check_in'],
                    'check_out_date' => $validated['check_out'],
                    'number_of_guests' => $validated['guests'],
                    'estimated_amount' => $estimatedAmount,
                    'status' => 'pending',
                    // created_by is null for public bookings (no auth user)
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        // Load room relationship for notification
        $reservation->load('room.roomType');

        // Dispatch reservation confirmation notification (email + SMS)
        \App\Jobs\SendReservationConfirmationJob::dispatch([
            'reference'       => $reservation->reservation_number,
            'guest_name'      => $reservation->guest_name,
            'email'           => $reservation->guest_email,
            'phone'           => $reservation->guest_phone,
            'room_number'     => $reservation->room?->room_number ?? '',
            'room_type'       => $reservation->room?->roomType?->name ?? '',
            'check_in'        => $reservation->check_in_date->format('Y-m-d'),
            'check_out'       => $reservation->check_out_date->format('Y-m-d'),
            'nights'          => $reservation->nights,
            'guests'          => $reservation->number_of_guests,
            'estimated_total' => $reservation->estimated_amount,
        ])->onQueue('notifications');

        return redirect()->route('booking.confirmation', $reservation->id);
    }

    /**
     * Show reservation confirmation page (after public booking).
     */
    public function showConfirmation(Reservation $reservation)
    {
        $reservation->load(['room.roomType', 'room.floor']);

        return view('public.booking-confirmation', compact('reservation'));
    }

    // ═══════════════════════════════════════════════════════════════
    //  FRONTDESK BOOKING MANAGEMENT — active stays
    // ═══════════════════════════════════════════════════════════════

    /**
     * List all bookings (active stays and past stays).
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
            $search = str_replace(['%', '_'], ['\%', '\_'], $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhere('booking_number', 'like', "%{$search}%")
                  ->orWhere('guest_phone', 'like', "%{$search}%");
            });
        }

        $bookings = $query->paginate(min($request->input('per_page', 20), 100));

        // Stats for dashboard cards
        $stats = [
            'total' => Booking::count(),
            'checked_in' => Booking::where('status', 'checked_in')->count(),
            'checked_out' => Booking::where('status', 'checked_out')->count(),
            'today_checkins' => Booking::whereDate('check_in_date', today())->where('status', 'checked_in')->count(),
            'today_checkouts' => Booking::where('check_out_date', now()->toDateString())
                ->where('status', 'checked_in')->count(),
        ];

        return view('bookings.index', compact('bookings', 'stats'));
    }

    /**
     * Show create booking form — for walk-in guests (immediate check-in).
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
     * Store a new walk-in booking — guest is checked in immediately.
     * Booking starts with status = checked_in.
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
        ]);

        $room = Room::with('roomType')->findOrFail($validated['room_id']);

        // Use transaction with lock to prevent double-booking race conditions
        try {
            $booking = DB::transaction(function () use ($validated, $room, $guestId) {
                // Check room availability with pessimistic lock
                if (!Booking::isRoomAvailable($room->id, $validated['check_in_date'], $validated['check_out_date'], null, true)) {
                    throw new \RuntimeException('This room is not available for the selected dates.');
                }

                // Get guest data for legacy fields
                $guest = Guest::findOrFail($guestId);

                // Create booking with checked_in status (walk-in = immediate check-in)
                return Booking::create([
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
                    'status' => 'checked_in', // Walk-in = immediately checked in
                    'source' => $validated['source'] ?? 'walkin',
                    'created_by' => auth()->id(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        // Dispatch booking confirmation (email + SMS)
        \App\Jobs\SendBookingConfirmationJob::dispatch([
            'reference'   => $booking->booking_number,
            'guest_name'  => $booking->guest?->full_name ?? $booking->guest_name,
            'email'       => $booking->guest?->email ?? $booking->guest_email,
            'phone'       => $booking->guest?->phone_number ?? $booking->guest_phone,
            'room_number' => $booking->room?->room_number ?? '',
            'room_type'   => $booking->room?->roomType?->name ?? '',
            'check_in'    => $booking->check_in_date,
            'check_out'   => $booking->check_out_date,
            'nights'      => $booking->check_in_date && $booking->check_out_date
                ? \Carbon\Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date) : 1,
            'rate'        => $booking->room?->roomType?->base_price ?? 0,
            'total'       => $booking->total_amount ?? 0,
        ])->onQueue('notifications');

        return redirect()->route('bookings.index')
            ->with('success', 'Walk-in guest checked in. Booking #' . $booking->booking_number . ' created.');
    }

    /**
     * Show a specific booking.
     */
    public function show(Booking $booking)
    {
        $booking->load(['room.roomType', 'room.floor.building', 'guest', 'creator', 'reservation', 'laundryOrders', 'bookingCharges']);

        return view('bookings.show', compact('booking'));
    }

    /**
     * Show edit form for a booking (only checked_in bookings).
     */
    public function edit(Booking $booking)
    {
        if (!$booking->canBeEdited()) {
            return back()->with('error', 'Only active (checked-in) bookings can be edited.');
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
            return back()->with('error', 'Only active (checked-in) bookings can be edited.');
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

        // Update data
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

        return redirect()->route('bookings.index')
            ->with('success', 'Booking updated successfully.');
    }

    // ═══════════════════════════════════════════════════════════════
    //  STATUS MANAGEMENT ACTIONS
    // ═══════════════════════════════════════════════════════════════

    /**
     * Check out a checked-in booking.
     * 
     * UNIFIED CHECKOUT FLOW:
     * - Redirect to Finance Checkout if there are unpaid charges
     * - Finance Checkout handles all payment processing
     * - Only allow direct checkout if no unpaid charges exist
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

        // Check for unsettled restaurant/bar orders (status = 'charged' but not 'settled')
        $pendingOrders = \App\Models\Order::where('booking_id', $booking->id)
            ->whereIn('status', ['open', 'sent', 'ready', 'served', 'charged'])
            ->count();

        if ($pendingOrders > 0) {
            return back()->with('error', 'Cannot check out: there are ' . $pendingOrders . ' pending restaurant/bar order(s). Please settle all orders first.');
        }

        // Check for unpaid charges
        $unpaidCharges = $booking->bookingCharges()->unpaid()->sum('amount');

        if ($unpaidCharges > 0) {
            // Redirect to Finance Checkout to process payment
            return redirect()
                ->route('finance.checkout.show', $booking->id)
                ->with('info', 'Please complete payment for all charges (Total: ' . number_format($unpaidCharges, 0) . ' TZS) before checking out.');
        }

        // No unpaid charges - proceed with checkout
        $booking->update(['status' => 'checked_out']);

        // Award loyalty points for the stay
        if ($booking->guest) {
            $nights = $booking->check_in_date && $booking->check_out_date
                ? max(1, \Carbon\Carbon::parse($booking->check_in_date)->diffInDays($booking->check_out_date))
                : 1;
            $booking->guest->addPoints(
                points: $nights * 100,
                source: 'booking',
                referenceId: $booking->id
            );
            $booking->guest->increment('total_stays');
            $booking->guest->increment('total_spent', $booking->total_amount ?? 0);
        }

        return back()->with('success', 'Guest checked out successfully.');
    }

    /**
     * Cancel an active booking.
     */
    public function cancel(Request $request, Booking $booking)
    {
        if (!$booking->canBeCancelled()) {
            return back()->with('error', 'Only active (checked-in) bookings can be cancelled.');
        }

        $reason = $request->input('cancellation_reason', 'Cancelled by staff');

        $booking->update([
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ]);

        // Dispatch cancellation notification (email + SMS)
        \App\Jobs\SendBookingCancellationJob::dispatch([
            'reference'           => $booking->booking_number,
            'guest_name'          => $booking->guest?->full_name ?? $booking->guest_name,
            'email'               => $booking->guest?->email ?? $booking->guest_email,
            'phone'               => $booking->guest?->phone_number ?? $booking->guest_phone,
            'room_number'         => $booking->room?->room_number ?? '',
            'check_in'            => $booking->check_in_date,
            'check_out'           => $booking->check_out_date,
            'cancellation_reason' => $reason,
        ])->onQueue('notifications');

        return back()->with('success', 'Booking cancelled successfully.');
    }

    /**
     * Destroy a booking (only checked-out or cancelled).
     */
    public function destroy(Booking $booking)
    {
        if (!in_array($booking->status, ['cancelled', 'checked_out'])) {
            return back()->with('error', 'Only checked-out or cancelled bookings can be deleted.');
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
     * Get available rooms for dates via AJAX.
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
