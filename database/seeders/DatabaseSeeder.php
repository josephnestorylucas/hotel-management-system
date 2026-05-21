<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            BuildingSeeder::class,
            FloorSeeder::class,
            RoomTypeSeeder::class,
            RoomSeeder::class,
            LaundryServiceSeeder::class,
            StockLocationSeeder::class,
            SystemSettingsSeeder::class,
            MenuCategorySeeder::class,
            AccountSeeder::class,
            ConferenceTypeSeeder::class,
            OrganizationSeeder::class,
        ]);
    }
}