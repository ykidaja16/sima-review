<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $jabatans = [
            ['nama' => 'Kepala Kantor',        'level' => 5, 'deskripsi' => 'Pimpinan tertinggi kantor'],
            ['nama' => 'Manager Operasional',  'level' => 4, 'deskripsi' => 'Manager bidang operasional'],
            ['nama' => 'Manager Pemasaran',    'level' => 4, 'deskripsi' => 'Manager bidang pemasaran'],
            ['nama' => 'Supervisor CS',        'level' => 3, 'deskripsi' => 'Supervisor Customer Service'],
            ['nama' => 'Supervisor Teller',    'level' => 3, 'deskripsi' => 'Supervisor Teller'],
            ['nama' => 'Customer Service',     'level' => 2, 'deskripsi' => 'Staf CS pelayanan nasabah'],
            ['nama' => 'Teller',               'level' => 2, 'deskripsi' => 'Staf Teller transaksi'],
            ['nama' => 'Staf Operasional',     'level' => 2, 'deskripsi' => 'Staf back office operasional'],
            ['nama' => 'Security',             'level' => 1, 'deskripsi' => 'Petugas keamanan'],
            ['nama' => 'Office Boy',           'level' => 1, 'deskripsi' => 'Petugas kebersihan/umum'],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::firstOrCreate(['nama' => $jabatan['nama']], $jabatan);
        }
    }
}
