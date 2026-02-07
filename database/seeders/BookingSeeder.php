<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::where('status', 'available')->where('is_active', true)->get();
        $guests = Guest::all();
        $frontdeskUser = User::whereHas('role', fn($q) => $q->where('name', 'front_desk'))->first();

        if ($rooms->isEmpty() || $guests->isEmpty()) {
            $this->command->info('Skipping BookingSeeder: No rooms or guests available.');
            return;
        }

        $sources = ['online', 'frontdesk', 'phone', 'walkin'];
        $statuses = ['pending', 'confirmed'];

        foreach ($rooms->take(5) as $index => $room) {
            $guest = $guests->random();
            $checkIn = now()->addDays(rand(1, 30));
            $checkOut = $checkIn->copy()->addDays(rand(1, 7));
            $nights = $checkIn->diffInDays($checkOut);
            $rate = $room->roomType->base_rate ?? 150;

            Booking::create([
                'guest_id' => $guest->id,
                'guest_name' => $guest->full_name,
                'guest_email' => $guest->email,
                'guest_phone' => $guest->phone_number,
                'guest_country' => $guest->nationality,
                'room_id' => $room->id,
                'check_in_date' => $checkIn,
                'check_out_date' => $checkOut,
                'number_of_guests' => rand(1, $room->roomType->max_occupancy ?? 2),
                'total_amount' => $nights * $rate,
                'status' => $statuses[array_rand($statuses)],
                'source' => $sources[array_rand($sources)],
                'created_by' => $frontdeskUser?->id,
            ]);
        }

        $this->command->info('BookingSeeder: Created ' . min(5, $rooms->count()) . ' sample bookings with linked reservations.');
    }
}
