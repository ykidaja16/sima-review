<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KetidaksesuaianVerifikasi extends Model
{
    protected $table = 'ketidaksesuaian_verifikasis';

    protected $fillable = [
        'ketidaksesuaian_id',
        'verifikator_id',
        'level',
        'tindakan_efektif',
        'alasan',
        'ftkp_baru_no',
    ];

    protected function casts(): array
    {
        return [
            'tindakan_efektif' => 'boolean',
        ];
    }

    public function ketidaksesuaian(): BelongsTo
    {
        return $this->belongsTo(Ketidaksesuaian::class);
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }
}
