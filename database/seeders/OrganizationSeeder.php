<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = [
            [
                'name' => 'TechConf Tanzania Ltd',
                'type' => 'company',
                'registration_number' => 'BRN-2023-45678',
                'email' => 'contact@techconf.tz',
                'phone' => '+255-789-123456',
                'address' => '123 Innovation Drive',
                'city' => 'Dar es Salaam',
                'country' => 'Tanzania',
                'contact_person_name' => 'Jane Mwale',
                'contact_person_email' => 'jane@techconf.tz',
                'contact_person_phone' => '+255-789-123457',
                'status' => 'active',
                'verified_at' => now(),
            ],
            [
                'name' => 'East Africa Business Forum',
                'type' => 'ngo',
                'email' => 'info@eabf.org',
                'phone' => '+255-765-987654',
                'address' => '45 Business Avenue',
                'city' => 'Nairobi',
                'country' => 'Kenya',
                'contact_person_name' => 'John Kamau',
                'contact_person_email' => 'john@eabf.org',
                'status' => 'active',
                'verified_at' => now(),
            ],
            [
                'name' => 'University of Dar es Salaam',
                'type' => 'university',
                'registration_number' => 'UDSM-1961',
                'email' => 'events@udsm.ac.tz',
                'phone' => '+255-22-2410000',
                'address' => 'Mwalimu Nyerere Mlimani Campus',
                'city' => 'Dar es Salaam',
                'country' => 'Tanzania',
                'contact_person_name' => 'Dr. Sarah Kimaro',
                'contact_person_email' => 'sarah@udsm.ac.tz',
                'status' => 'active',
                'verified_at' => now(),
            ],
        ];

        foreach ($organizations as $org) {
            Organization::firstOrCreate(['email' => $org['email']], $org);
        }
    }
}
