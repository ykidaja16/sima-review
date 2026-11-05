<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ketidaksesuaian extends Model
{
    protected $fillable = [
        'nomor_ftkp',
        'tanggal_laporan',
        'pelapor_id',
        'divisi_pelapor_id',
        'jenis_id',
        'jenis_lainnya',
        'penjelasan_temuan',
        'kategori_temuan',
        'divisi_tujuan_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_laporan' => 'date',
        ];
    }

    // ---- Relationships ----

    public function pelapor(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'pelapor_id');
    }

    public function divisiPelapor(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_pelapor_id');
    }

    public function jenis(): BelongsTo
    {
        return $this->belongsTo(JenisKetidaksesuaian::class, 'jenis_id');
    }

    public function divisiTujuan(): BelongsTo
    {
        return $this->belongsTo(Divisi::class, 'divisi_tujuan_id');
    }

    public function tindaklanjut(): HasOne
    {
        return $this->hasOne(KetidaksesuaianTindaklanjut::class);
    }

    public function verifikasis(): HasMany
    {
        return $this->hasMany(KetidaksesuaianVerifikasi::class);
    }

    public function verifikasiManager(): HasOne
    {
        return $this->hasOne(KetidaksesuaianVerifikasi::class)->where('level', 'manager');
    }

    public function verifikasiKacab(): HasOne
    {
        return $this->hasOne(KetidaksesuaianVerifikasi::class)->where('level', 'kacab');
    }

    // ---- Helpers ----

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'open'                => 'Open',
            'in_progress'         => 'Dalam Proses',
            'verifikasi_manager'  => 'Verifikasi Manager',
            'verifikasi_kacab'    => 'Verifikasi Kacab',
            'closed'              => 'Selesai',
            default               => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'open'                => 'danger',
            'in_progress'         => 'warning',
            'verifikasi_manager'  => 'info',
            'verifikasi_kacab'    => 'primary',
            'closed'              => 'success',
            default               => 'secondary',
        };
    }

    public function getKategoriLabelAttribute(): string
    {
        return match(strtolower((string) $this->kategori_temuan)) {
            'ok'        => 'OK',
            'observasi' => 'Observasi',
            'nc'        => 'NC (Ketidaksesuaian)',
            'nc minor'  => 'NC Minor',
            'nc mayor'  => 'NC Mayor',
            'ofi'       => 'OFI',
            default     => (string) $this->kategori_temuan,
        };
    }

    public function getKategoriColorAttribute(): string
    {
        $val = strtolower((string) $this->kategori_temuan);
        if ($val === 'ok' || $val === 'ofi') {
            return 'success';
        }
        if ($val === 'observasi' || str_contains($val, 'ami 1') || str_contains($val, 'minor')) {
            return 'warning';
        }
        if (str_contains($val, 'ami 2')) {
            return 'info';
        }
        if ($val === 'nc' || str_contains($val, 'mayor') || str_contains($val, 'ami 3')) {
            return 'danger';
        }
        return 'primary';
    }

    // ---- Auto-generate nomor FTKP ----

    public static function generateNomor(): string
    {
        $bulanRomawi = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        $bulan = $bulanRomawi[now()->month - 1];
        $tahun = now()->year;

        $count = self::whereYear('created_at', $tahun)->count() + 1;
        $nomor = str_pad($count, 3, '0', STR_PAD_LEFT);

        return "{$nomor}/{$bulan}/{$tahun}";
    }
}
