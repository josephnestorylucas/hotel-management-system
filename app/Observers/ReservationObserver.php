<?php
namespace App\Observers;

use App\Models\Reservation;

/**
 * ReservationObserver — manages room status for future holds.
 *
 * Reservation = future intent. Room transitions:
 *   confirmed → room reserved
 *   cancelled / no_show → room available
 *   converted → no room change (BookingObserver handles it)
 */
class ReservationObserver {
    public function updating(Reservation $reservation): void {
        if ($reservation->isDirty('status')) {
            match ($reservation->status) {
                'confirmed' => $reservation->room?->update(['status' => 'reserved']),
                'cancelled', 'no_show' => $reservation->room?->update(['status' => 'available']),
                'converted' => null, // Room status handled by BookingObserver on Booking creation
                default => null,
            };
        }
    }
}