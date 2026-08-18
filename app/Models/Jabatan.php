<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    protected $fillable = [
        'nama',
        'level',
        'deskripsi',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'level'     => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function karyawans(): HasMany
    {
        return $this->hasMany(Karyawan::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
