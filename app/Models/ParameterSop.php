<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ParameterSop extends Model
{
    protected $fillable = [
        'kategori',
        'nama',
        'deskripsi',
        'urutan',
        'bobot',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'urutan'    => 'integer',
            'bobot'     => 'integer',
            'is_active' => 'boolean',
        ];
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
