<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisKetidaksesuaian extends Model
{
    protected $table = 'jenis_ketidaksesuaians';

    protected $fillable = [
        'nama',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function ketidaksesuaians(): HasMany
    {
        return $this->hasMany(Ketidaksesuaian::class, 'jenis_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
