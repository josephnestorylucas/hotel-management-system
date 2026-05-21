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
            [
                'key'         => 'sms_provider_key',
                'value'       => '',
                'description' => 'SMS provider key',
            ],
            [
                'key'         => 'sms_sender_id',
                'value'       => '',
                'description' => 'SMS sender ID',
            ],
            [
                'key'         => 'sms_api_key',
                'value'       => '',
                'description' => 'SMS provider API key',
            ],
            [
                'key'         => 'sms_base_url',
                'value'       => '',
                'description' => 'SMS provider base URL',
            ],
            [
                'key'         => 'sms_is_enabled',
                'value'       => 'false',
                'description' => 'SMS provider enabled flag',
            ],
            [
                'key'         => 'mail_driver',
                'value'       => 'smtp',
                'description' => 'Mail driver',
            ],
            [
                'key'         => 'mail_host',
                'value'       => '',
                'description' => 'Mail host',
            ],
            [
                'key'         => 'mail_port',
                'value'       => '587',
                'description' => 'Mail port',
            ],
            [
                'key'         => 'mail_username',
                'value'       => '',
                'description' => 'Mail username',
            ],
            [
                'key'         => 'mail_password',
                'value'       => '',
                'description' => 'Mail password',
            ],
            [
                'key'         => 'mail_encryption',
                'value'       => 'tls',
                'description' => 'Mail encryption',
            ],
            [
                'key'         => 'mail_from_address',
                'value'       => '',
                'description' => 'Mail from address',
            ],
            [
                'key'         => 'mail_from_name',
                'value'       => '',
                'description' => 'Mail from name',
            ],
            [
                'key'         => 'mail_is_enabled',
                'value'       => 'false',
                'description' => 'Mail enabled flag',
            ],
            [
                'key'         => 'azampesa_base_url',
                'value'       => '',
                'description' => 'AzamPesa base URL',
            ],
            [
                'key'         => 'azampesa_auth_url',
                'value'       => '',
                'description' => 'AzamPesa auth URL',
            ],
            [
                'key'         => 'azampesa_app_name',
                'value'       => '',
                'description' => 'AzamPesa app name',
            ],
            [
                'key'         => 'azampesa_client_id',
                'value'       => '',
                'description' => 'AzamPesa client ID',
            ],
            [
                'key'         => 'azampesa_client_secret',
                'value'       => '',
                'description' => 'AzamPesa client secret',
            ],
            [
                'key'         => 'azampesa_is_enabled',
                'value'       => 'false',
                'description' => 'AzamPesa payment enabled flag',
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
