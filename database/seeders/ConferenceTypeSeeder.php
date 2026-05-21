<?php

namespace Database\Seeders;

use App\Models\ConferenceType;
use Illuminate\Database\Seeder;

class ConferenceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Conference', 'description' => 'Large-scale professional conference with multiple sessions', 'icon' => 'conference', 'features' => ['networking', 'sessions', 'catering', 'exhibitions']],
            ['name' => 'Seminar', 'description' => 'Educational or informational session', 'icon' => 'seminar', 'features' => ['sessions', 'materials']],
            ['name' => 'Workshop', 'description' => 'Hands-on interactive learning session', 'icon' => 'workshop', 'features' => ['hands_on', 'materials', 'certificates']],
            ['name' => 'Webinar', 'description' => 'Online web-based seminar', 'icon' => 'webinar', 'features' => ['live_stream', 'recording', 'chat']],
            ['name' => 'Training', 'description' => 'Professional development and skill building', 'icon' => 'training', 'features' => ['certificates', 'materials', 'assessments']],
            ['name' => 'Symposium', 'description' => 'Academic or formal discussion forum', 'icon' => 'symposium', 'features' => ['panel_discussions', 'networking']],
            ['name' => 'Summit', 'description' => 'High-level leadership meeting', 'icon' => 'summit', 'features' => ['vip', 'networking', 'catering']],
            ['name' => 'Exhibition', 'description' => 'Trade show or exhibition event', 'icon' => 'exhibition', 'features' => ['booths', 'exhibitors', 'networking']],
        ];

        foreach ($types as $type) {
            ConferenceType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
