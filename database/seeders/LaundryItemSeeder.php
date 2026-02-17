<?php

namespace Database\Seeders;

use App\Models\LaundryItem;
use Illuminate\Database\Seeder;

class LaundryItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Shirt', 'price' => 3000, 'description' => 'Regular shirt (cotton, polyester)'],
            ['name' => 'T-Shirt', 'price' => 2500, 'description' => 'Casual t-shirt'],
            ['name' => 'Trousers', 'price' => 3500, 'description' => 'Regular trousers / pants'],
            ['name' => 'Jeans', 'price' => 4000, 'description' => 'Denim jeans'],
            ['name' => 'Suit (2-piece)', 'price' => 10000, 'description' => 'Full suit jacket and trousers'],
            ['name' => 'Suit (3-piece)', 'price' => 15000, 'description' => 'Suit jacket, trousers, and vest'],
            ['name' => 'Blazer / Jacket', 'price' => 7000, 'description' => 'Blazer or formal jacket'],
            ['name' => 'Dress', 'price' => 5000, 'description' => 'Regular dress'],
            ['name' => 'Evening Gown', 'price' => 12000, 'description' => 'Formal evening dress'],
            ['name' => 'Skirt', 'price' => 3000, 'description' => 'Regular skirt'],
            ['name' => 'Blouse', 'price' => 3000, 'description' => 'Women\'s blouse'],
            ['name' => 'Tie', 'price' => 2000, 'description' => 'Necktie'],
            ['name' => 'Scarf / Shawl', 'price' => 2500, 'description' => 'Scarf or shawl'],
            ['name' => 'Underwear', 'price' => 1500, 'description' => 'Per piece'],
            ['name' => 'Socks (pair)', 'price' => 1000, 'description' => 'One pair of socks'],
            ['name' => 'Bed Sheet (Single)', 'price' => 4000, 'description' => 'Single bed sheet'],
            ['name' => 'Bed Sheet (Double)', 'price' => 5000, 'description' => 'Double / queen bed sheet'],
            ['name' => 'Duvet Cover', 'price' => 7000, 'description' => 'Duvet or comforter cover'],
            ['name' => 'Pillow Case', 'price' => 2000, 'description' => 'Standard pillow case'],
            ['name' => 'Blanket', 'price' => 8000, 'description' => 'Regular blanket'],
            ['name' => 'Towel (Bath)', 'price' => 3000, 'description' => 'Bath towel'],
            ['name' => 'Towel (Hand)', 'price' => 1500, 'description' => 'Hand towel'],
            ['name' => 'Bathrobe', 'price' => 5000, 'description' => 'Guest bathrobe'],
            ['name' => 'Curtain (per panel)', 'price' => 6000, 'description' => 'Room curtain panel'],
            ['name' => 'Table Cloth', 'price' => 4000, 'description' => 'Restaurant / conference table cloth'],
            ['name' => 'Napkin', 'price' => 1000, 'description' => 'Cloth napkin'],
        ];

        foreach ($items as $item) {
            LaundryItem::updateOrCreate(
                ['name' => $item['name']],
                array_merge($item, ['is_active' => true])
            );
        }
    }
}
