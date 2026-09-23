<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'  => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ---- Relationships ----

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function karyawan(): HasOne
    {
        return $this->hasOne(Karyawan::class);
    }

    public function penilaians(): HasMany
    {
        return $this->hasMany(Penilaian::class, 'evaluator_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // ---- Role Helper Methods ----
    // Menggunakan relasi ke tabel roles, bukan enum langsung

    public function isSuperAdmin(): bool
    {
        return $this->role?->slug === 'super_admin';
    }

    public function isKacab(): bool
    {
        return $this->role?->slug === 'kacab';
    }

    public function isManager(): bool
    {
        return $this->role?->slug === 'manager';
    }

    public function isSupervisor(): bool
    {
        return $this->role?->slug === 'supervisor';
    }

    public function isPelaksana(): bool
    {
        return $this->role?->slug === 'pelaksana';
    }

    public function isMutu(): bool
    {
        return $this->role?->slug === 'mutu';
    }

    /**
     * Cek apakah user memiliki salah satu dari role yang diberikan (by slug).
     */
    public function hasRole(string|array $roles): bool
    {
        $slug = $this->role?->slug;
        if (is_string($roles)) {
            return $slug === $roles;
        }
        return in_array($slug, $roles);
    }

    public function getRoleLabelAttribute(): string
    {
        return $this->role?->nama ?? 'Unknown';
    }

    public function getRoleSlugAttribute(): string
    {
        return $this->role?->slug ?? '';
    }
}
