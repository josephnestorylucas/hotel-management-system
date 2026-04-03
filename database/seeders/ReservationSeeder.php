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

        if ($rooms->count() < 4 || !$frontDeskUser) {
            $this->command->warn('Not enough rooms or front desk user. Run RoomSeeder and UserSeeder first.');
            return;
        }

        $reservations = [
            [
                'room_id' => $rooms[0]->id,
                'guest_name' => 'John Smith',
                'guest_phone' => '+1-555-0101',
                'guest_email' => 'john.smith@email.com',
                'check_in_date' => now()->subDays(1),
                'check_out_date' => now()->addDays(2),
                'number_of_guests' => 2,
                'status' => Reservation::STATUS_CONFIRMED,
                'estimated_amount' => 360.00,
                'created_by' => $frontDeskUser->id,
            ],
            [
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
            ],
            [
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
            ],
            [
                'room_id' => $rooms[3]->id,
                'guest_name' => 'Emily Davis',
                'guest_phone' => '+1-555-0104',
                'guest_email' => 'emily.davis@email.com',
                'check_in_date' => now()->subDays(5),
                'check_out_date' => now()->subDays(2),
                'number_of_guests' => 3,
                'status' => Reservation::STATUS_CONVERTED,
                'estimated_amount' => 660.00,
                'created_by' => $frontDeskUser->id,
            ],
            [
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
            ],
        ];

        foreach ($reservations as $res) {
            $existing = Reservation::where('guest_email', $res['guest_email'])->first();
            if (!$existing) {
                Reservation::create($res);
            }
        }

        // Update room statuses
        if ($rooms[0]->status !== 'occupied') $rooms[0]->update(['status' => 'occupied']);
        if ($rooms[1]->status !== 'reserved') $rooms[1]->update(['status' => 'reserved']);
    }
}