<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToBranch;
use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, BelongsToBranch;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'gdpr_consent' => 'boolean',
            'gdpr_consent_at' => 'datetime',
            'social_profiles' => 'array',
            'custom_fields' => 'array',
            'tags' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function ($contact) {
            $contact->full_name = trim("{$contact->first_name} {$contact->last_name}");
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }
}
