<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'nama'      => 'Super Admin',
                'slug'      => 'super_admin',
                'deskripsi' => 'Akses penuh ke seluruh sistem termasuk master data dan konfigurasi',
                'level'     => 4,
            ],
            [
                'nama'      => 'Manager',
                'slug'      => 'manager',
                'deskripsi' => 'Mengatur periode penilaian dan memantau divisi yang dibawahinya',
                'level'     => 3,
            ],
            [
                'nama'      => 'Supervisor',
                'slug'      => 'supervisor',
                'deskripsi' => 'Melakukan penilaian terhadap anggota tim yang menjadi bawahannya',
                'level'     => 2,
            ],
            [
                'nama'      => 'Pelaksana',
                'slug'      => 'pelaksana',
                'deskripsi' => 'Karyawan yang dinilai, hanya dapat melihat data penilaian miliknya sendiri',
                'level'     => 1,
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
