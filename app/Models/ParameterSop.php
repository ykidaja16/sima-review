<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParameterSop extends Model
{
    public const METODE_OPTIONS = [
        'Interview',
        'Observasi',
        'Tes Tulis',
    ];

    protected $fillable = [
        'kategori',
        'nama',
        'deskripsi',
        'metode_penilaian',
        'urutan',
        'bobot',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'metode_penilaian' => 'array',
            'urutan'           => 'integer',
            'bobot'            => 'integer',
            'is_active'        => 'boolean',
        ];
    }

    public function getMetodePenilaianLabelAttribute(): ?string
    {
        if (empty($this->metode_penilaian) || !is_array($this->metode_penilaian)) {
            return null;
        }
        $filtered = array_values(array_filter(array_map('trim', $this->metode_penilaian)));
        return !empty($filtered) ? implode(', ', $filtered) : null;
    }

    public function penilaianDetails(): HasMany
    {
        return $this->hasMany(PenilaianDetail::class, 'parameter_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }
}
