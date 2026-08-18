<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Karyawan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'nip',
        'nama',
        'email',
        'no_hp',
        'divisi_id',
        'jabatan_id',
        'atasan_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ---- Relationships ----

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function divisi(): BelongsTo
    {
        return $this->belongsTo(Divisi::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function atasan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'atasan_id');
    }

    public function bawahans(): HasMany
    {
        return $this->hasMany(Karyawan::class, 'atasan_id');
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(Penilaian::class);
    }

    // ---- Scopes ----

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ---- Accessors ----

    public function getNamaLengkapAttribute(): string
    {
        return $this->nip . ' - ' . $this->nama;
    }
}
