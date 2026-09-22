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
                'level'     => 5,
            ],
            [
                'nama'      => 'Kepala Cabang',
                'slug'      => 'kacab',
                'deskripsi' => 'Kepala Cabang: memantau seluruh aktivitas cabang, verifikasi akhir ketidaksesuaian, dan mengelola periode penilaian',
                'level'     => 4,
            ],
            [
                'nama'      => 'Manager',
                'slug'      => 'manager',
                'deskripsi' => 'Mengatur dan memantau divisi yang dibawahinya, verifikasi tindak lanjut ketidaksesuaian',
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
