<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToBranch;
use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, BelongsToBranch;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'annual_revenue' => 'decimal:2',
            'social_links' => 'array',
            'custom_fields' => 'array',
            'tags' => 'array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function parentCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'parent_company_id');
    }

    public function childCompanies(): HasMany
    {
        return $this->hasMany(Company::class, 'parent_company_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
