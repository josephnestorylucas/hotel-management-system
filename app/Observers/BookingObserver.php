<?php

namespace App\Observers;

use App\Models\Booking;

class BookingObserver
{
    /**
     * Handle the Booking "updating" event.
     * Syncs booking status changes to the linked reservation and room.
     */
    public function updating(Booking $booking): void
    {
        if ($booking->isDirty('status')) {
            // Sync status to linked reservation
            if ($booking->reservation) {
                $booking->reservation->update(['status' => $booking->status]);
            }

            // Update timestamps based on status
            match ($booking->status) {
                'confirmed' => $booking->confirmed_at = $booking->confirmed_at ?? now(),
                'cancelled' => $booking->cancelled_at = $booking->cancelled_at ?? now(),
                default => null,
            };
        }
    }

    /**
     * Handle the Booking "created" event.
     * Automatically creates a linked reservation.
     */
    public function created(Booking $booking): void
    {
        // Auto-create a linked reservation when a booking is created
        if (!$booking->reservation_id) {
            $booking->createReservation();
        }
    }
}
