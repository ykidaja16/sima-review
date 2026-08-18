<?php

namespace Database\Seeders;

use App\Models\Divisi;
use Illuminate\Database\Seeder;

class DivisiSeeder extends Seeder
{
    public function run(): void
    {
        $divisis = [
            ['nama' => 'Customer Service',    'kode' => 'CS',  'deskripsi' => 'Divisi pelayanan pelanggan'],
            ['nama' => 'Teller',              'kode' => 'TL',  'deskripsi' => 'Divisi teller dan transaksi'],
            ['nama' => 'Operasional',         'kode' => 'OPR', 'deskripsi' => 'Divisi operasional dan back office'],
            ['nama' => 'Pemasaran',           'kode' => 'MKT', 'deskripsi' => 'Divisi pemasaran dan pengembangan bisnis'],
            ['nama' => 'Keuangan & Akuntansi','kode' => 'KEU', 'deskripsi' => 'Divisi keuangan dan akuntansi'],
            ['nama' => 'SDM',                 'kode' => 'SDM', 'deskripsi' => 'Sumber Daya Manusia'],
        ];

        foreach ($divisis as $divisi) {
            Divisi::firstOrCreate(['kode' => $divisi['kode']], $divisi);
        }
    }
}
