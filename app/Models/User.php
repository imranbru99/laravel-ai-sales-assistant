<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, BelongsToTenant;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'super-admin') {
            return $this->isSuperAdmin();
        }

        if (!$this->is_active) {
            return false;
        }

        if ($panel->getId() === 'app') {
            return $this->isSuperAdmin() || ($this->tenant_id !== null && !$this->hasRole(['Customer', 'Partner']));
        }

        if ($panel->getId() === 'portal') {
            return $this->hasRole('Customer') || $this->isSuperAdmin();
        }

        if ($panel->getId() === 'partner') {
            return $this->hasRole('Partner') || $this->isSuperAdmin();
        }

        if ($panel->getId() === 'me') {
            return $this->tenant_id !== null;
        }

        return true;
    }
}
