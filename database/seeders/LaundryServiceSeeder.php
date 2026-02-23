<?php

namespace Database\Seeders;

use App\Models\LaundryService;
use App\Models\LaundryServiceItem;
use Illuminate\Database\Seeder;

class LaundryServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name'             => 'Wash Only',
                'description'      => 'Machine or hand wash, no ironing',
                'turnaround_hours' => 12,
                'items' => [
                    ['item_name' => 'Shirt',          'price' => 2000],
                    ['item_name' => 'T-Shirt',        'price' => 1500],
                    ['item_name' => 'Trouser',        'price' => 2500],
                    ['item_name' => 'Shorts',         'price' => 1500],
                    ['item_name' => 'Underwear',      'price' => 1000],
                    ['item_name' => 'Socks (pair)',   'price' => 1000],
                    ['item_name' => 'Dress',          'price' => 3000],
                    ['item_name' => 'Skirt',          'price' => 2000],
                    ['item_name' => 'Bedsheet',       'price' => 4000],
                    ['item_name' => 'Towel',          'price' => 2000],
                    ['item_name' => 'Pillowcase',     'price' => 1500],
                ],
            ],
            [
                'name'             => 'Wash & Iron',
                'description'      => 'Full wash and professional ironing',
                'turnaround_hours' => 24,
                'items' => [
                    ['item_name' => 'Shirt',          'price' => 3500],
                    ['item_name' => 'T-Shirt',        'price' => 2500],
                    ['item_name' => 'Trouser',        'price' => 4000],
                    ['item_name' => 'Shorts',         'price' => 2500],
                    ['item_name' => 'Dress',          'price' => 5000],
                    ['item_name' => 'Skirt',          'price' => 3500],
                    ['item_name' => 'Suit (full)',    'price' => 10000],
                    ['item_name' => 'Jacket',         'price' => 6000],
                    ['item_name' => 'Bedsheet',       'price' => 6000],
                    ['item_name' => 'Towel',          'price' => 3000],
                ],
            ],
            [
                'name'             => 'Iron Only',
                'description'      => 'Ironing of clean items only',
                'turnaround_hours' => 6,
                'items' => [
                    ['item_name' => 'Shirt',          'price' => 1500],
                    ['item_name' => 'T-Shirt',        'price' => 1000],
                    ['item_name' => 'Trouser',        'price' => 2000],
                    ['item_name' => 'Dress',          'price' => 2500],
                    ['item_name' => 'Suit (full)',    'price' => 5000],
                    ['item_name' => 'Jacket',         'price' => 3000],
                ],
            ],
            [
                'name'             => 'Dry Cleaning',
                'description'      => 'Professional dry cleaning for delicate items',
                'turnaround_hours' => 48,
                'items' => [
                    ['item_name' => 'Suit (full)',    'price' => 20000],
                    ['item_name' => 'Jacket',         'price' => 12000],
                    ['item_name' => 'Dress',          'price' => 15000],
                    ['item_name' => 'Coat',           'price' => 18000],
                    ['item_name' => 'Trouser',        'price' => 8000],
                    ['item_name' => 'Shirt',          'price' => 7000],
                    ['item_name' => 'Tie',            'price' => 5000],
                    ['item_name' => 'Skirt',          'price' => 10000],
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            $items = $serviceData['items'];
            unset($serviceData['items']);

            $service = LaundryService::updateOrCreate(
                ['name' => $serviceData['name']],
                $serviceData
            );

            foreach ($items as $item) {
                LaundryServiceItem::updateOrCreate(
                    [
                        'laundry_service_id' => $service->id,
                        'item_name'          => $item['item_name'],
                    ],
                    ['price' => $item['price']]
                );
            }
        }
    }
}
