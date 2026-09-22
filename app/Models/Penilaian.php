<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penilaian extends Model
{

    protected $fillable = [
        'karyawan_id',
        'evaluator_id',
        'periode_id',
        'nilai_akhir',
        'catatan',
        'tanggal_penilaian',
    ];

    protected function casts(): array
    {
        return [
            'nilai_akhir'      => 'float',
            'tanggal_penilaian' => 'date',
        ];
    }

    // ---- Relationships ----

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodePenilaian::class, 'periode_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(PenilaianDetail::class);
    }

    // ---- Accessors ----

    public function getKategoriAttribute(): ?KategoriNilai
    {
        if ($this->nilai_akhir === null) return null;
        return KategoriNilai::getKategoriByNilai($this->nilai_akhir);
    }

    public function getNilaiAkhirFormattedAttribute(): string
    {
        return number_format($this->nilai_akhir ?? 0, 2);
    }
}
