<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodePenilaian extends Model
{
    protected $fillable = [
        'nama',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai'   => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'periode_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function getIsAktifAttribute(): bool
    {
        return $this->status === 'aktif';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'   => 'Draft',
            'aktif'   => 'Aktif',
            'ditutup' => 'Ditutup',
            default   => ucfirst($this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'draft'   => 'secondary',
            'aktif'   => 'success',
            'ditutup' => 'danger',
            default   => 'secondary',
        };
    }
}
