<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key'         => 'adjustment_approval_threshold',
                'value'       => '50',
                'description' => 'Adjustments above this unit count require STORE_MANAGER approval',
            ],
            [
                'key'         => 'low_stock_alert_enabled',
                'value'       => 'true',
                'description' => 'Toggle low stock notifications on/off',
            ],
            [
                'key'         => 'tzs_exchange_rate',
                'value'       => '2500',
                'description' => 'Current TZS per 1 USD — update daily',
            ],
            [
                'key'         => 'default_currency',
                'value'       => 'USD',
                'description' => 'System-wide default currency (USD or TZS)',
            ],
        ];

        $now = now();

        foreach ($settings as $s) {
            $exists = DB::table('system_settings')->where('key', $s['key'])->exists();

            if ($exists) {
                DB::table('system_settings')->where('key', $s['key'])->update([
                    'value'       => $s['value'],
                    'description' => $s['description'],
                    'updated_at'  => $now,
                ]);
            } else {
                DB::table('system_settings')->insert(array_merge($s, [
                    'id'         => Str::uuid()->toString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }
    }
}
