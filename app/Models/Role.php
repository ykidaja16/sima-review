<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'nama',
        'slug',
        'deskripsi',
        'level',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    // ---- Static helpers ----

    public static function getBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }

    public static function superAdmin(): ?self { return static::getBySlug('super_admin'); }
    public static function kacab(): ?self      { return static::getBySlug('kacab'); }
    public static function manager(): ?self    { return static::getBySlug('manager'); }
    public static function supervisor(): ?self { return static::getBySlug('supervisor'); }
    public static function pelaksana(): ?self  { return static::getBySlug('pelaksana'); }
}
