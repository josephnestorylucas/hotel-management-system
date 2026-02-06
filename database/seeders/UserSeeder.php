<?php
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

        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@hotel.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Front Desk Officer',
            'email' => 'frontdesk@hotel.com',
            'password' => Hash::make('password'),
            'role_id' => $frontDeskRole->id,
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Operations Supervisor',
            'email' => 'supervisor@hotel.com',
            'password' => Hash::make('password'),
            'role_id' => $supervisorRole->id,
            'is_active' => true,
        ]);
    }
}