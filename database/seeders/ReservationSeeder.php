<?php
namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder {
    public function run(): void {
        $rooms = Room::take(5)->get();
        $frontDeskUser = User::whereHas('role', fn($q) => $q->where('name', 'front_desk'))->first();

        // Current/active reservation (this would be a confirmed reservation that hasn't been converted yet)
        // Note: In the new architecture, checked-in guests should have a Booking, not a Reservation
        Reservation::create([
            'room_id' => $rooms[0]->id,
            'guest_name' => 'John Smith',
            'guest_phone' => '+1-555-0101',
            'guest_email' => 'john.smith@email.com',
            'check_in_date' => now()->subDays(1),
            'check_out_date' => now()->addDays(2),
            'number_of_guests' => 2,
            'status' => Reservation::STATUS_CONFIRMED, // Changed from 'checked_in'
            'estimated_amount' => 360.00,
            'created_by' => $frontDeskUser->id,
        ]);
        $rooms[0]->update(['status' => 'occupied']);

        // Confirmed future reservation
        Reservation::create([
            'room_id' => $rooms[1]->id,
            'guest_name' => 'Sarah Johnson',
            'guest_phone' => '+1-555-0102',
            'guest_email' => 'sarah.j@email.com',
            'check_in_date' => now()->addDays(1),
            'check_out_date' => now()->addDays(4),
            'number_of_guests' => 1,
            'status' => Reservation::STATUS_CONFIRMED,
            'estimated_amount' => 240.00,
            'created_by' => $frontDeskUser->id,
        ]);
        $rooms[1]->update(['status' => 'reserved']);

        // Pending reservation
        Reservation::create([
            'room_id' => $rooms[2]->id,
            'guest_name' => 'Michael Brown',
            'guest_phone' => '+1-555-0103',
            'guest_email' => 'mbrown@email.com',
            'check_in_date' => now()->addDays(5),
            'check_out_date' => now()->addDays(7),
            'number_of_guests' => 2,
            'status' => Reservation::STATUS_PENDING,
            'estimated_amount' => 360.00,
            'created_by' => $frontDeskUser->id,
        ]);

        // Past reservation (this would be a no-show or converted reservation)
        // If the guest checked out, this should be 'converted' (meaning it became a Booking)
        Reservation::create([
            'room_id' => $rooms[3]->id,
            'guest_name' => 'Emily Davis',
            'guest_phone' => '+1-555-0104',
            'guest_email' => 'emily.davis@email.com',
            'check_in_date' => now()->subDays(5),
            'check_out_date' => now()->subDays(2),
            'number_of_guests' => 3,
            'status' => Reservation::STATUS_CONVERTED, // Changed from 'checked_out' - this reservation was converted to a booking
            'estimated_amount' => 660.00,
            'created_by' => $frontDeskUser->id,
        ]);

        // Cancelled reservation
        Reservation::create([
            'room_id' => null,
            'guest_name' => 'David Wilson',
            'guest_phone' => '+1-555-0105',
            'guest_email' => 'dwilson@email.com',
            'check_in_date' => now()->addDays(3),
            'check_out_date' => now()->addDays(6),
            'number_of_guests' => 2,
            'status' => Reservation::STATUS_CANCELLED,
            'estimated_amount' => 540.00,
            'created_by' => $frontDeskUser->id,
        ]);
    }
}