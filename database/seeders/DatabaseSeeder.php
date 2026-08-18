<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,           // PERTAMA: roles harus ada sebelum users
            KategoriNilaiSeeder::class,
            DivisiSeeder::class,
            JabatanSeeder::class,
            UserSeeder::class,           // Setelah roles & divisi/jabatan
            KaryawanSeeder::class,
            ParameterSopSeeder::class,
            PeriodePenilaianSeeder::class,
        ]);
    }
}
