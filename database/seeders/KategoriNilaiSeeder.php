<?php

namespace Database\Seeders;

use App\Models\KategoriNilai;
use Illuminate\Database\Seeder;

class KategoriNilaiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // Skor 4 (Sangat Baik) = 100%, skor 3 (Baik) = 75%, skor 2 (Cukup) = 50%, skor 1 (Kurang Baik) = 25%
            ['nama' => 'Sangat Baik', 'nilai_min' => 88, 'nilai_max' => 100, 'warna' => 'success', 'urutan' => 1],
            ['nama' => 'Baik',        'nilai_min' => 63, 'nilai_max' => 87,  'warna' => 'primary', 'urutan' => 2],
            ['nama' => 'Cukup',       'nilai_min' => 38, 'nilai_max' => 62,  'warna' => 'warning', 'urutan' => 3],
            ['nama' => 'Kurang Baik', 'nilai_min' => 0,  'nilai_max' => 37,  'warna' => 'danger',  'urutan' => 4],
        ];

        foreach ($data as $item) {
            KategoriNilai::firstOrCreate(['nama' => $item['nama']], $item);
        }
    }
}
