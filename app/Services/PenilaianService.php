<?php

namespace App\Services;

use App\Models\KategoriNilai;
use App\Models\Penilaian;
use App\Models\PenilaianDetail;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\DB;

class PenilaianService
{
    /**
     * Skor 1-4 per parameter. Formula nilai_akhir (skala 0-100):
     *   nilai_akhir = (Σ skor_i × bobot_i) / (Σ 4 × bobot_i) × 100
     *
     * @param  array  $details  [['parameter_id', 'nilai' (1-4), 'bobot'], ...]
     */
    public function hitungNilaiAkhir(array $details): float
    {
        $totalBobotMax     = 0;  // Σ (4 × bobot) — skor maksimal mungkin
        $totalNilaiTerbobot = 0;  // Σ (skor × bobot)

        foreach ($details as $detail) {
            $bobot = $detail['bobot'] ?? 1;
            $totalBobotMax     += 4 * $bobot;
            $totalNilaiTerbobot += $detail['nilai'] * $bobot;
        }

        if ($totalBobotMax === 0) return 0;

        return round(($totalNilaiTerbobot / $totalBobotMax) * 100, 2);
    }

    /**
     * Ambil kategori nilai berdasarkan skor akhir (0-100).
     */
    public function getKategori(float $nilai): ?KategoriNilai
    {
        return KategoriNilai::getKategoriByNilai($nilai);
    }

    /**
     * Simpan penilaian baru beserta detail per parameter.
     */
    public function simpanPenilaian(array $data, array $details): Penilaian
    {
        return DB::transaction(function () use ($data, $details) {
            $nilaiAkhir = $this->hitungNilaiAkhir($details);

            $penilaian = Penilaian::create([
                'karyawan_id'       => $data['karyawan_id'],
                'evaluator_id'      => $data['evaluator_id'],
                'periode_id'        => $data['periode_id'],
                'nilai_akhir'       => $nilaiAkhir,
                'catatan'           => $data['catatan'] ?? null,
                'tanggal_penilaian' => $data['tanggal_penilaian'],
            ]);

            foreach ($details as $detail) {
                PenilaianDetail::create([
                    'penilaian_id' => $penilaian->id,
                    'parameter_id' => $detail['parameter_id'],
                    'nilai'        => $detail['nilai'],
                    'catatan'      => $detail['catatan'] ?? null,
                ]);
            }

            AuditLogService::log(
                'CREATE_PENILAIAN', 'Penilaian', $penilaian->id,
                null, $penilaian->toArray()
            );

            return $penilaian;
        });
    }

    /**
     * Update penilaian yang sudah ada.
     */
    public function updatePenilaian(Penilaian $penilaian, array $data, array $details): Penilaian
    {
        return DB::transaction(function () use ($penilaian, $data, $details) {
            $dataLama   = $penilaian->toArray();
            $nilaiAkhir = $this->hitungNilaiAkhir($details);

            $penilaian->update([
                'catatan'           => $data['catatan'] ?? null,
                'tanggal_penilaian' => $data['tanggal_penilaian'],
                'nilai_akhir'       => $nilaiAkhir,
            ]);

            $penilaian->details()->delete();
            foreach ($details as $detail) {
                PenilaianDetail::create([
                    'penilaian_id' => $penilaian->id,
                    'parameter_id' => $detail['parameter_id'],
                    'nilai'        => $detail['nilai'],
                    'catatan'      => $detail['catatan'] ?? null,
                ]);
            }

            AuditLogService::log(
                'UPDATE_PENILAIAN', 'Penilaian', $penilaian->id,
                $dataLama, $penilaian->fresh()->toArray()
            );

            return $penilaian->fresh();
        });
    }
}
