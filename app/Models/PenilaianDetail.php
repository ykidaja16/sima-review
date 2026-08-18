<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianDetail extends Model
{
    protected $fillable = [
        'penilaian_id',
        'parameter_id',
        'nilai',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'float',
        ];
    }

    public function penilaian(): BelongsTo
    {
        return $this->belongsTo(Penilaian::class);
    }

    public function parameter(): BelongsTo
    {
        return $this->belongsTo(ParameterSop::class, 'parameter_id');
    }
}
