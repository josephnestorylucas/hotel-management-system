<?php
namespace Database\Seeders;

use App\Models\Building;
use App\Models\Floor;
use Illuminate\Database\Seeder;

class FloorSeeder extends Seeder {
    public function run(): void {
        $mainBuilding = Building::where('code', 'MAIN')->first();
        $westWing = Building::where('code', 'WEST')->first();

        if (!$mainBuilding || !$westWing) return;

        $floors = [
            ['building_id' => $mainBuilding->id, 'name' => 'Ground Floor', 'floor_number' => 0, 'is_active' => true],
            ['building_id' => $mainBuilding->id, 'name' => 'First Floor', 'floor_number' => 1, 'is_active' => true],
            ['building_id' => $mainBuilding->id, 'name' => 'Second Floor', 'floor_number' => 2, 'is_active' => true],
            ['building_id' => $mainBuilding->id, 'name' => 'Third Floor', 'floor_number' => 3, 'is_active' => true],
            ['building_id' => $westWing->id, 'name' => 'Ground Floor', 'floor_number' => 0, 'is_active' => true],
            ['building_id' => $westWing->id, 'name' => 'First Floor', 'floor_number' => 1, 'is_active' => true],
            ['building_id' => $westWing->id, 'name' => 'Second Floor', 'floor_number' => 2, 'is_active' => true],
        ];

        foreach ($floors as $f) {
            Floor::updateOrCreate(
                ['building_id' => $f['building_id'], 'floor_number' => $f['floor_number']],
                $f
            );
        }
    }
}