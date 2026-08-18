<?php

namespace Database\Seeders;

use App\Models\Divisi;
use App\Models\Jabatan;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    public function run(): void
    {
        $divisiCS  = Divisi::where('kode', 'CS')->first();
        $divisiTL  = Divisi::where('kode', 'TL')->first();
        $divisiOPR = Divisi::where('kode', 'OPR')->first();

        $jabatanMgr   = Jabatan::where('nama', 'Manager Operasional')->first();
        $jabatanSpvCS = Jabatan::where('nama', 'Supervisor CS')->first();
        $jabatanCS    = Jabatan::where('nama', 'Customer Service')->first();
        $jabatanTL    = Jabatan::where('nama', 'Teller')->first();

        // Lookup user via username
        $userManager    = User::where('username', 'manager01')->first();
        $userSupervisor = User::where('username', 'supervisor01')->first();
        $userPelaksana  = User::where('username', 'pelaksana01')->first();

        // Manager
        $managerKaryawan = Karyawan::firstOrCreate(
            ['nip' => '19850001'],
            [
                'user_id'    => $userManager?->id,
                'nama'       => 'Manager Operasional',
                'email'      => 'manager@sima.local',
                'divisi_id'  => $divisiOPR->id,
                'jabatan_id' => $jabatanMgr->id,
                'is_active'  => true,
            ]
        );

        // Supervisor
        $supervisorKaryawan = Karyawan::firstOrCreate(
            ['nip' => '19900001'],
            [
                'user_id'    => $userSupervisor?->id,
                'nama'       => 'Supervisor CS',
                'email'      => 'supervisor@sima.local',
                'divisi_id'  => $divisiCS->id,
                'jabatan_id' => $jabatanSpvCS->id,
                'atasan_id'  => $managerKaryawan->id,
                'is_active'  => true,
            ]
        );

        // Pelaksana Demo
        Karyawan::firstOrCreate(
            ['nip' => '20000001'],
            [
                'user_id'    => $userPelaksana?->id,
                'nama'       => 'Pelaksana Demo',
                'email'      => 'pelaksana@sima.local',
                'divisi_id'  => $divisiCS->id,
                'jabatan_id' => $jabatanCS->id,
                'atasan_id'  => $supervisorKaryawan->id,
                'is_active'  => true,
            ]
        );

        // Karyawan dummy tambahan
        $dummyKaryawans = [
            ['nip' => '20010001', 'nama' => 'Budi Santoso',  'divisi_id' => $divisiCS->id, 'jabatan_id' => $jabatanCS->id, 'atasan_id' => $supervisorKaryawan->id],
            ['nip' => '20010002', 'nama' => 'Siti Rahayu',   'divisi_id' => $divisiCS->id, 'jabatan_id' => $jabatanCS->id, 'atasan_id' => $supervisorKaryawan->id],
            ['nip' => '20010003', 'nama' => 'Ahmad Fauzi',   'divisi_id' => $divisiCS->id, 'jabatan_id' => $jabatanCS->id, 'atasan_id' => $supervisorKaryawan->id],
            ['nip' => '20010004', 'nama' => 'Dewi Kusuma',   'divisi_id' => $divisiTL->id, 'jabatan_id' => $jabatanTL->id, 'atasan_id' => null],
            ['nip' => '20010005', 'nama' => 'Rendi Pratama', 'divisi_id' => $divisiTL->id, 'jabatan_id' => $jabatanTL->id, 'atasan_id' => null],
        ];

        foreach ($dummyKaryawans as $k) {
            Karyawan::firstOrCreate(
                ['nip' => $k['nip']],
                array_merge($k, ['is_active' => true])
            );
        }
    }
}
