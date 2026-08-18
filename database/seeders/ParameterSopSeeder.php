<?php

namespace Database\Seeders;

use App\Models\ParameterSop;
use Illuminate\Database\Seeder;

class ParameterSopSeeder extends Seeder
{
    /**
     * Parameter sesuai formulir penilaian praktek diklat Service Excellent.
     * Skor: 4 = Sangat Baik, 3 = Baik, 2 = Cukup, 1 = Kurang Baik.
     * Semua parameter berbobot 1 (setara) kecuali disebutkan lain.
     */
    public function run(): void
    {
        // Hapus data lama secara aman (FK constraint aware)
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ParameterSop::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $parameters = [
            // ===================================================
            // I. ATTITUDE
            // ===================================================

            // 1. Komponen Pengetahuan
            [
                'kategori' => 'I. Attitude — Komponen Pengetahuan',
                'nama'     => 'Peserta memahami prinsip dasar Service Excellent',
                'deskripsi'=> 'Wawancara: pemahaman prinsip dasar service excellent',
                'bobot'    => 2, 'urutan' => 101,
            ],
            [
                'kategori' => 'I. Attitude — Komponen Pengetahuan',
                'nama'     => 'Dapat menyebutkan langkah-langkah pelayanan prima',
                'deskripsi'=> 'Wawancara: kemampuan menyebutkan langkah pelayanan',
                'bobot'    => 2, 'urutan' => 102,
            ],
            [
                'kategori' => 'I. Attitude — Komponen Pengetahuan',
                'nama'     => 'Mengetahui pentingnya empati dan komunikasi dalam pelayanan',
                'deskripsi'=> 'Wawancara: pemahaman tentang empati dan komunikasi',
                'bobot'    => 2, 'urutan' => 103,
            ],
            [
                'kategori' => 'I. Attitude — Komponen Pengetahuan',
                'nama'     => 'Memahami standar pelayanan organisasi (SOP/aturan layanan)',
                'deskripsi'=> 'Wawancara: pemahaman SOP dan aturan layanan',
                'bobot'    => 2, 'urutan' => 104,
            ],

            // 2. Komponen Afektif
            [
                'kategori' => 'I. Attitude — Komponen Afektif',
                'nama'     => 'Semangat Melayani',
                'bobot'    => 3, 'urutan' => 201,
            ],
            [
                'kategori' => 'I. Attitude — Komponen Afektif',
                'nama'     => 'Bisa mengontrol emosi saat keadaan "under pressure"',
                'bobot'    => 3, 'urutan' => 202,
            ],
            [
                'kategori' => 'I. Attitude — Komponen Afektif',
                'nama'     => 'Senang memberikan pelayanan yang berorientasi kepuasan pelanggan',
                'bobot'    => 3, 'urutan' => 203,
            ],
            [
                'kategori' => 'I. Attitude — Komponen Afektif',
                'nama'     => 'Empati terhadap Pelanggan',
                'bobot'    => 3, 'urutan' => 204,
            ],
            [
                'kategori' => 'I. Attitude — Komponen Afektif',
                'nama'     => 'Bertanggung jawab terhadap kualitas pelayanan dan hasil pekerjaannya',
                'bobot'    => 3, 'urutan' => 205,
            ],

            // 3. Perilaku
            [
                'kategori' => 'I. Attitude — Perilaku',
                'nama'     => 'Selalu bersemangat saat memberikan pelayanan',
                'bobot'    => 2, 'urutan' => 301,
            ],
            [
                'kategori' => 'I. Attitude — Perilaku',
                'nama'     => 'Selalu tersenyum ramah',
                'bobot'    => 2, 'urutan' => 302,
            ],
            [
                'kategori' => 'I. Attitude — Perilaku',
                'nama'     => 'Selalu berkomunikasi dengan efektif, sopan dan santun',
                'bobot'    => 2, 'urutan' => 303,
            ],
            [
                'kategori' => 'I. Attitude — Perilaku',
                'nama'     => 'Selalu menjaga kualitas pekerjaan',
                'bobot'    => 2, 'urutan' => 304,
            ],
            [
                'kategori' => 'I. Attitude — Perilaku',
                'nama'     => 'Responsif menanggapi pasien terhadap pasien kurang paham / keluhan',
                'bobot'    => 3, 'urutan' => 305,
            ],
            [
                'kategori' => 'I. Attitude — Perilaku',
                'nama'     => 'Pasca melayani selalu mengucapkan: Terima Kasih, Ada yang bisa dibantu?, Semoga sehat selalu',
                'bobot'    => 2, 'urutan' => 306,
            ],

            // ===================================================
            // II. ABILITY (KEMAMPUAN KOMUNIKASI)
            // ===================================================

            // 1. Kesan Pertama (P-S-B-S)
            [
                'kategori' => 'II. Ability — Kesan Pertama Yang Baik (P-S-B-S)',
                'nama'     => 'Penampilan: Pastikan penampilan Anda rapi dan profesional',
                'bobot'    => 2, 'urutan' => 401,
            ],
            [
                'kategori' => 'II. Ability — Kesan Pertama Yang Baik (P-S-B-S)',
                'nama'     => 'Senyum: Berikan senyuman ramah saat bertemu pelanggan',
                'bobot'    => 2, 'urutan' => 402,
            ],
            [
                'kategori' => 'II. Ability — Kesan Pertama Yang Baik (P-S-B-S)',
                'nama'     => 'Bahasa Tubuh: Gunakan bahasa tubuh yang sopan dan terbuka',
                'bobot'    => 2, 'urutan' => 403,
            ],
            [
                'kategori' => 'II. Ability — Kesan Pertama Yang Baik (P-S-B-S)',
                'nama'     => 'Sapaan: Sapa pelanggan dengan sapaan yang hangat dan ramah',
                'bobot'    => 2, 'urutan' => 404,
            ],

            // 2-6. Kemampuan Komunikasi
            [
                'kategori' => 'II. Ability — Kemampuan Komunikasi',
                'nama'     => 'Dengarkan dengan Aktif',
                'bobot'    => 3, 'urutan' => 501,
            ],
            [
                'kategori' => 'II. Ability — Kemampuan Komunikasi',
                'nama'     => 'Jelas dan Ringkas',
                'bobot'    => 3, 'urutan' => 502,
            ],
            [
                'kategori' => 'II. Ability — Kemampuan Komunikasi',
                'nama'     => 'Personalisasi',
                'bobot'    => 2, 'urutan' => 503,
            ],
            [
                'kategori' => 'II. Ability — Kemampuan Komunikasi',
                'nama'     => 'Respons Tepat Waktu',
                'bobot'    => 3, 'urutan' => 504,
            ],
            [
                'kategori' => 'II. Ability — Kemampuan Komunikasi',
                'nama'     => 'Empati',
                'bobot'    => 3, 'urutan' => 505,
            ],

            // 7. Komunikasi Non Verbal
            [
                'kategori' => 'II. Ability — Komunikasi Non Verbal',
                'nama'     => 'Ekspresi Wajah',
                'bobot'    => 2, 'urutan' => 601,
            ],
            [
                'kategori' => 'II. Ability — Komunikasi Non Verbal',
                'nama'     => 'Gestur dan Bahasa Tubuh',
                'bobot'    => 2, 'urutan' => 602,
            ],
            [
                'kategori' => 'II. Ability — Komunikasi Non Verbal',
                'nama'     => 'Parabahasa (Kecepatan Bicara, Intonasi, Volume Suara)',
                'bobot'    => 2, 'urutan' => 603,
            ],
        ];

        foreach ($parameters as $param) {
            ParameterSop::create(array_merge($param, [
                'is_active' => true,
                'deskripsi' => $param['deskripsi'] ?? null,
            ]));
        }
    }
}
