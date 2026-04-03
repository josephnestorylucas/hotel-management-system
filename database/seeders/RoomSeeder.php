<?php
namespace Database\Seeders;

use App\Models\Room;
use App\Models\Floor;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder {
    public function run(): void {
        $floors = Floor::all();
        $roomTypes = RoomType::all();

        foreach ($floors as $floor) {
            $building = $floor->building;
            $floorPrefix = $floor->floor_number;

            for ($i = 1; $i <= 10; $i++) {
                $roomNumber = $floorPrefix . str_pad($i, 2, '0', STR_PAD_LEFT);
                
                $roomType = match (true) {
                    $i <= 3 => $roomTypes->where('code', 'STD-SGL')->first(),
                    $i <= 6 => $roomTypes->where('code', 'STD-DBL')->first(),
                    $i <= 8 => $roomTypes->where('code', 'DLX')->first(),
                    $i == 9 => $roomTypes->where('code', 'FAM')->first(),
                    default => $roomTypes->where('code', 'STE')->first(),
                };

                Room::updateOrCreate(
                    ['room_number' => $building->code . '-' . $roomNumber],
                    [
                        'floor_id' => $floor->id,
                        'room_type_id' => $roomType?->id,
                        'status' => 'available',
                        'is_active' => true,
                    ]
                );
            }
        }
    }
}