<?php

namespace Database\Seeders;

use App\Models\KategoriNilai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriNilaiSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        KategoriNilai::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            [
                'nama'      => 'Sangat Kompeten',
                'nilai_min' => 90.00,
                'nilai_max' => 100.00,
                'warna'     => 'success',
                'urutan'    => 1,
            ],
            [
                'nama'      => 'Kompeten',
                'nilai_min' => 75.00,
                'nilai_max' => 89.99,
                'warna'     => 'primary',
                'urutan'    => 2,
            ],
            [
                'nama'      => 'Cukup Kompeten',
                'nilai_min' => 60.00,
                'nilai_max' => 74.99,
                'warna'     => 'warning',
                'urutan'    => 3,
            ],
            [
                'nama'      => 'Belum Kompeten',
                'nilai_min' => 0.00,
                'nilai_max' => 59.99,
                'warna'     => 'danger',
                'urutan'    => 4,
            ],
        ];

        foreach ($data as $item) {
            KategoriNilai::create($item);
        }
    }
}
