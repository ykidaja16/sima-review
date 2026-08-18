<?php

namespace Database\Seeders;

use App\Models\PeriodePenilaian;
use Illuminate\Database\Seeder;

class PeriodePenilaianSeeder extends Seeder
{
    public function run(): void
    {
        $periodes = [
            [
                'nama'            => 'Periode I 2026 (1-14 Jan)',
                'tanggal_mulai'   => '2026-01-01',
                'tanggal_selesai' => '2026-01-14',
                'status'          => 'ditutup',
                'keterangan'      => 'Periode evaluasi dua mingguan pertama tahun 2026',
            ],
            [
                'nama'            => 'Periode II 2026 (15-31 Jan)',
                'tanggal_mulai'   => '2026-01-15',
                'tanggal_selesai' => '2026-01-31',
                'status'          => 'ditutup',
                'keterangan'      => 'Periode evaluasi dua mingguan kedua tahun 2026',
            ],
            [
                'nama'            => 'Periode III 2026 (1-14 Feb)',
                'tanggal_mulai'   => '2026-02-01',
                'tanggal_selesai' => '2026-02-14',
                'status'          => 'aktif',
                'keterangan'      => 'Periode evaluasi aktif saat ini',
            ],
        ];

        foreach ($periodes as $p) {
            PeriodePenilaian::firstOrCreate(
                ['nama' => $p['nama']],
                $p
            );
        }
    }
}
