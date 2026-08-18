<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roleSuperAdmin = Role::where('slug', 'super_admin')->first();
        $roleManager    = Role::where('slug', 'manager')->first();
        $roleSupervisor = Role::where('slug', 'supervisor')->first();
        $rolePelaksana  = Role::where('slug', 'pelaksana')->first();

        $users = [
            [
                'username'  => 'superadmin',
                'name'      => 'Super Administrator',
                'email'     => 'superadmin@sima.local',
                'password'  => Hash::make('Admin@123'),
                'role_id'   => $roleSuperAdmin->id,
                'is_active' => true,
            ],
            [
                'username'  => 'manager01',
                'name'      => 'Manager Operasional',
                'email'     => 'manager@sima.local',
                'password'  => Hash::make('Manager@123'),
                'role_id'   => $roleManager->id,
                'is_active' => true,
            ],
            [
                'username'  => 'supervisor01',
                'name'      => 'Supervisor CS',
                'email'     => 'supervisor@sima.local',
                'password'  => Hash::make('Spv@12345'),
                'role_id'   => $roleSupervisor->id,
                'is_active' => true,
            ],
            [
                'username'  => 'pelaksana01',
                'name'      => 'Pelaksana Demo',
                'email'     => 'pelaksana@sima.local',
                'password'  => Hash::make('Plks@1234'),
                'role_id'   => $rolePelaksana->id,
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['username' => $user['username']], $user);
        }
    }
}
