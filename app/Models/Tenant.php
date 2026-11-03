<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'branding' => 'array',
            'settings' => 'array',
            'trial_ends_at' => 'datetime',
        ];
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function tenantModules(): HasMany
    {
        return $this->hasMany(TenantModule::class);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function territories(): HasMany
    {
        return $this->hasMany(Territory::class);
    }

    public function settingsRecords(): HasMany
    {
        return $this->hasMany(TenantSetting::class);
    }

    public function isModuleEnabled(string $moduleKey): bool
    {
        return app(\App\Core\Modules\ModuleManager::class)->isModuleEnabled($moduleKey, $this);
    }

    public function getSetting(string $group, string $key, mixed $default = null): mixed
    {
        $record = $this->settingsRecords()->where('group', $group)->where('key', $key)->first();
        return $record ? $record->value : ($this->settings[$group][$key] ?? $default);
    }

    public function setSetting(string $group, string $key, mixed $value): void
    {
        $this->settingsRecords()->updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => $value]
        );
    }
}
