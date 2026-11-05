<?php

namespace Database\Seeders;

use App\Models\ParameterSop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParameterSopSeeder extends Seeder
{
    /**
     * Instrumen Penilaian Pelayanan Service Excellence.
     * Skor: 4 = Sangat Baik, 3 = Baik, 2 = Cukup, 1 = Kurang Baik.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ParameterSop::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $parameters = [
            // ===================================================
            // I. DIMENSI ATTITUDE & EMOTIONAL INTELLIGENCE (SIKAP)
            // ===================================================

            // 1. Cognitive Component (Aspek Pengetahuan Standar Layanan)
            [
                'kategori' => 'I. ATTITUDE & EMOTIONAL INTELLIGENCE — 1. Cognitive Component',
                'nama'     => 'a. Menunjukkan pemahaman regulasi pelayanan melalui Nilai Post-Test.',
                'deskripsi'=> 'Aspek Pengetahuan Standar Layanan',
                'bobot'    => 1, 'urutan' => 101, 'is_active' => true,
            ],
            [
                'kategori' => 'I. ATTITUDE & EMOTIONAL INTELLIGENCE — 1. Cognitive Component',
                'nama'     => 'b. Mampu menjelaskan esensi Service Excellence bagi reputasi organisasi.',
                'deskripsi'=> 'Aspek Pengetahuan Standar Layanan',
                'bobot'    => 1, 'urutan' => 102, 'is_active' => true,
            ],
            [
                'kategori' => 'I. ATTITUDE & EMOTIONAL INTELLIGENCE — 1. Cognitive Component',
                'nama'     => 'c. Memahami dan mampu menguraikan alur SOP pelayanan secara runtut.',
                'deskripsi'=> 'Aspek Pengetahuan Standar Layanan',
                'bobot'    => 1, 'urutan' => 103, 'is_active' => true,
            ],

            // 2. Affective Component (Orientasi Pelayanan & Regulasi Emosi)
            [
                'kategori' => 'I. ATTITUDE & EMOTIONAL INTELLIGENCE — 2. Affective Component',
                'nama'     => 'a. Emotional Labor Management: Mampu mengontrol emosi, tetap tenang, dan profesional saat menghadapi situasi tertekan (under pressure).',
                'deskripsi'=> 'Orientasi Pelayanan & Regulasi Emosi',
                'bobot'    => 1, 'urutan' => 104, 'is_active' => true,
            ],
            [
                'kategori' => 'I. ATTITUDE & EMOTIONAL INTELLIGENCE — 2. Affective Component',
                'nama'     => 'b. Customer Centric Mindset: Menunjukkan motivasi intrinsik dan antusiasme tinggi untuk membantu menyelesaikan masalah pelanggan.',
                'deskripsi'=> 'Orientasi Pelayanan & Regulasi Emosi',
                'bobot'    => 1, 'urutan' => 105, 'is_active' => true,
            ],
            [
                'kategori' => 'I. ATTITUDE & EMOTIONAL INTELLIGENCE — 2. Affective Component',
                'nama'     => 'c. Accountability: Bertanggung jawab penuh terhadap kualitas hasil kerjanya tanpa melempar kesalahan kepada pihak lain.',
                'deskripsi'=> 'Orientasi Pelayanan & Regulasi Emosi',
                'bobot'    => 1, 'urutan' => 106, 'is_active' => true,
            ],

            // 3. Behavioral Component (Tindakan Nyata di Lapangan)
            [
                'kategori' => 'I. ATTITUDE & EMOTIONAL INTELLIGENCE — 3. Behavioral Component',
                'nama'     => 'a. Secara konsisten menampilkan ekspresi ramah, senyum tulus, dan kontak mata yang membangun kenyamanan sejak awal interaksi.',
                'deskripsi'=> 'Tindakan Nyata di Lapangan',
                'bobot'    => 1, 'urutan' => 107, 'is_active' => true,
            ],
            [
                'kategori' => 'I. ATTITUDE & EMOTIONAL INTELLIGENCE — 3. Behavioral Component',
                'nama'     => 'b. Service Recovery Readiness: Responsif, cekatan, dan solutif saat menanggapi komplain atau pasien yang kebingungan.',
                'deskripsi'=> 'Tindakan Nyata di Lapangan',
                'bobot'    => 1, 'urutan' => 108, 'is_active' => true,
            ],
            [
                'kategori' => 'I. ATTITUDE & EMOTIONAL INTELLIGENCE — 3. Behavioral Component',
                'nama'     => 'c. Mempraktikkan closing statement bermakna pasca-pelayanan (mengucapkan terima kasih, menawarkan bantuan tambahan, dan mendoakan kesehatan).',
                'deskripsi'=> 'Tindakan Nyata di Lapangan',
                'bobot'    => 1, 'urutan' => 109, 'is_active' => true,
            ],

            // ===================================================
            // II. DIMENSI ABILITY & INTERPERSONAL COMMUNICATION (KEMAMPUAN)
            // ===================================================

            // 1. Kesan Pertama & Atribut Fisik (Tangibles)
            [
                'kategori' => 'II. ABILITY & INTERPERSONAL COMMUNICATION — 1. Kesan Pertama & Atribut Fisik (Tangibles)',
                'nama'     => 'a. Memenuhi standar Grooming: Penampilan rapi, bersih, wangi, sesuai dengan aturan seragam instansi.',
                'deskripsi'=> 'Kesan Pertama & Atribut Fisik',
                'bobot'    => 1, 'urutan' => 201, 'is_active' => true,
            ],
            [
                'kategori' => 'II. ABILITY & INTERPERSONAL COMMUNICATION — 1. Kesan Pertama & Atribut Fisik (Tangibles)',
                'nama'     => 'b. Menerapkan formula P-S-B-S (Penampilan, Senyum, Bahasa Tubuh, Sapaan) sebagai standar penyambutan awal secara natural.',
                'deskripsi'=> 'Kesan Pertama & Atribut Fisik',
                'bobot'    => 1, 'urutan' => 202, 'is_active' => true,
            ],

            // 2. Komunikasi Verbal & Keterampilan Interpersonal (Assurance)
            [
                'kategori' => 'II. ABILITY & INTERPERSONAL COMMUNICATION — 2. Komunikasi Verbal & Keterampilan Interpersonal (Assurance)',
                'nama'     => 'a. Active Listening: Mendengarkan dengan penuh perhatian tanpa memotong pembicaraan, melakukan verifikasi (konfirmasi ulang) maksud pelanggan.',
                'deskripsi'=> 'Komunikasi Verbal & Keterampilan Interpersonal',
                'bobot'    => 1, 'urutan' => 203, 'is_active' => true,
            ],
            [
                'kategori' => 'II. ABILITY & INTERPERSONAL COMMUNICATION — 2. Komunikasi Verbal & Keterampilan Interpersonal (Assurance)',
                'nama'     => 'b. Clarity & Brevity: Memberikan informasi atau edukasi dengan kalimat yang jelas, ringkas, mudah dipahami (menghindari jargon medis/birokrasi yang rumit).',
                'deskripsi'=> 'Komunikasi Verbal & Keterampilan Interpersonal',
                'bobot'    => 1, 'urutan' => 204, 'is_active' => true,
            ],
            [
                'kategori' => 'II. ABILITY & INTERPERSONAL COMMUNICATION — 2. Komunikasi Verbal & Keterampilan Interpersonal (Assurance)',
                'nama'     => 'c. Personalization: Melakukan pendekatan personal secara sopan (menyebut nama pelanggan, menyesuaikan gaya bicara dengan usia/kondisi pelanggan).',
                'deskripsi'=> 'Komunikasi Verbal & Keterampilan Interpersonal',
                'bobot'    => 1, 'urutan' => 205, 'is_active' => true,
            ],

            // 3. Ketanggapan Pelayanan (Responsiveness)
            [
                'kategori' => 'II. ABILITY & INTERPERSONAL COMMUNICATION — 3. Ketanggapan Pelayanan (Responsiveness)',
                'nama'     => 'a. Menunjukkan waktu respons yang cepat dan efisien (tidak membiarkan pelanggan menunggu tanpa kejelasan).',
                'deskripsi'=> 'Ketanggapan Pelayanan',
                'bobot'    => 1, 'urutan' => 206, 'is_active' => true,
            ],

            // 4. Kedekatan Emosional (Empathy)
            [
                'kategori' => 'II. ABILITY & INTERPERSONAL COMMUNICATION — 4. Kedekatan Emosional (Empathy)',
                'nama'     => 'a. Menunjukkan rasa peduli melalui kalimat validasi emosi (contoh: "Saya memahami kondisi Ibu, mari saya bantu...").',
                'deskripsi'=> 'Kedekatan Emosional',
                'bobot'    => 1, 'urutan' => 207, 'is_active' => true,
            ],

            // 5. Komunikasi Non-Verbal & Kinesik
            [
                'kategori' => 'II. ABILITY & INTERPERSONAL COMMUNICATION — 5. Komunikasi Non-Verbal & Kinesik',
                'nama'     => 'a. Membaca dan merespons isyarat non-verbal (bahasa tubuh, ekspresi wajah) dari pelanggan secara tepat.',
                'deskripsi'=> 'Komunikasi Non-Verbal & Kinesik',
                'bobot'    => 1, 'urutan' => 208, 'is_active' => true,
            ],
            [
                'kategori' => 'II. ABILITY & INTERPERSONAL COMMUNICATION — 5. Komunikasi Non-Verbal & Kinesik',
                'nama'     => 'b. Paralinguistic Control: Mengontrol kualitas vokal secara ideal (intonasi ramah, volume suara cukup terdengar tetapi tidak membentak, tempo bicara stabil).',
                'deskripsi'=> 'Komunikasi Non-Verbal & Kinesik',
                'bobot'    => 1, 'urutan' => 209, 'is_active' => true,
            ],
            [
                'kategori' => 'II. ABILITY & INTERPERSONAL COMMUNICATION — 5. Komunikasi Non-Verbal & Kinesik',
                'nama'     => 'c. Menjaga postur tubuh yang tegap namun terbuka (open body language) dan condong ke arah pelanggan saat berinteraksi.',
                'deskripsi'=> 'Komunikasi Non-Verbal & Kinesik',
                'bobot'    => 1, 'urutan' => 210, 'is_active' => true,
            ],
        ];

        foreach ($parameters as $param) {
            ParameterSop::create($param);
        }
    }
}
