<?php
namespace Database\Seeders;

use App\Models\Building;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder {
    public function run(): void {
        Building::updateOrCreate(['code' => 'MAIN'], [
            'name' => 'Main Building',
            'address' => '123 Hotel Street, City Center',
            'is_active' => true,
        ]);

        Building::updateOrCreate(['code' => 'WEST'], [
            'name' => 'West Wing',
            'address' => '125 Hotel Street, City Center',
            'is_active' => true,
        ]);
    }
}