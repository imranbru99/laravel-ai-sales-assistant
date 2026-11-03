<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'dependencies' => 'array',
            'is_core' => 'boolean',
            'default_enabled' => 'boolean',
            'price_monthly' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function tenantModules(): HasMany
    {
        return $this->hasMany(TenantModule::class, 'module_key', 'key');
    }
}
