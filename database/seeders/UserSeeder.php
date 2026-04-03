<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run(): void {
        $adminRole = Role::where('name', Role::ADMIN)->first();
        $frontDeskRole = Role::where('name', Role::FRONT_DESK)->first();
        $supervisorRole = Role::where('name', Role::SUPERVISOR)->first();
        $houseHelpRole = Role::where('name', Role::HOUSE_HELP)->first();

        User::updateOrCreate(
            ['email' => 'admin@hotel.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'frontdesk@hotel.com'],
            [
                'name' => 'Front Desk Officer',
                'password' => Hash::make('password'),
                'role_id' => $frontDeskRole->id,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'supervisor@hotel.com'],
            [
                'name' => 'Operations Supervisor',
                'password' => Hash::make('password'),
                'role_id' => $supervisorRole->id,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'househelp@hotel.com'],
            [
                'name' => 'House Help Staff',
                'password' => Hash::make('password'),
                'role_id' => $houseHelpRole->id,
                'is_active' => true,
            ]
        );
    }
}