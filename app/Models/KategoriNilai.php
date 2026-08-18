<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriNilai extends Model
{
    protected $fillable = [
        'nama',
        'nilai_min',
        'nilai_max',
        'warna',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'nilai_min' => 'float',
            'nilai_max' => 'float',
            'urutan'    => 'integer',
        ];
    }

    /**
     * Ambil kategori nilai berdasarkan skor tertentu
     */
    public static function getKategoriByNilai(float $nilai): ?self
    {
        return self::where('nilai_min', '<=', $nilai)
            ->where('nilai_max', '>=', $nilai)
            ->first();
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('nilai_min');
    }
}
