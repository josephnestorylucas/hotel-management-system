<?php

namespace App\Observers;

use App\Models\Booking;

/**
 * BookingObserver — manages room status transitions for active stays.
 *
 * Booking = active guest stay. Room status is controlled here:
 *   checked_in  → room occupied
 *   checked_out → room dirty (needs cleaning)
 *   cancelled   → room available
 */
class BookingObserver
{
    /**
     * When a Booking is created (check-in), mark the room as occupied.
     */
    public function created(Booking $booking): void
    {
        if ($booking->status === 'checked_in') {
            $booking->room?->update(['status' => 'occupied']);
        }
    }

    /**
     * When a Booking status changes, update room status accordingly.
     */
    public function updating(Booking $booking): void
    {
        if ($booking->isDirty('status')) {
            match ($booking->status) {
                'checked_in'  => $booking->room?->update(['status' => 'occupied']),
                'checked_out' => $booking->room?->update(['status' => 'dirty']),
                'cancelled'   => $booking->room?->update(['status' => 'available']),
                default => null,
            };

            // Update timestamps
            match ($booking->status) {
                'cancelled' => $booking->cancelled_at = $booking->cancelled_at ?? now(),
                default => null,
            };
        }
    }
}
