<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KetidaksesuaianTindaklanjut extends Model
{
    protected $table = 'ketidaksesuaian_tindaklanjuts';

    protected $fillable = [
        'ketidaksesuaian_id',
        'user_id',
        'akar_masalah',
        'tindakan_korektif',
        'tindakan_pencegahan',
        'perkiraan_tanggal_selesai',
    ];

    protected function casts(): array
    {
        return [
            'perkiraan_tanggal_selesai' => 'date',
        ];
    }

    public function ketidaksesuaian(): BelongsTo
    {
        return $this->belongsTo(Ketidaksesuaian::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
