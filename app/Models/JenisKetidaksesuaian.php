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
        'kategori_temuan',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'kategori_temuan' => 'array',
            'is_active'       => 'boolean',
        ];
    }

    public function getKategoriTemuanListAttribute(): array
    {
        if (!empty($this->kategori_temuan) && is_array($this->kategori_temuan)) {
            return array_values(array_filter(array_map('trim', $this->kategori_temuan)));
        }

        $namaUpper = strtoupper($this->nama ?? '');
        if (str_contains($namaUpper, 'AMI')) {
            return ['AMI 1', 'AMI 2', 'AMI 3'];
        }
        if (str_contains($namaUpper, 'AME')) {
            return ['Observasi', 'NC Minor', 'NC Mayor', 'OFI'];
        }

        return ['Observasi', 'NC Minor', 'NC Mayor', 'OFI'];
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
