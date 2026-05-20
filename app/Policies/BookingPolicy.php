<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy
{
    public function view(User $user, Booking $booking): bool
    {
        if ($user->isAdmin() || $user->isGeneralManager()) {
            return true;
        }

        return (string) $user->id === (string) $booking->created_by;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Booking $booking): bool
    {
        return $this->view($user, $booking);
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $user->isGeneralManager();
    }
}