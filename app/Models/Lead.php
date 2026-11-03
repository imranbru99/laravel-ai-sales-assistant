<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToBranch;
use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant, BelongsToBranch;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'budget' => 'decimal:2',
            'score' => 'integer',
            'score_explanation' => 'array',
            'last_scored_at' => 'datetime',
            'last_contacted_at' => 'datetime',
            'sla_due_at' => 'datetime',
            'custom_fields' => 'array',
            'tags' => 'array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function getGradeBadgeColor(): string
    {
        return match ($this->score_grade) {
            'A' => 'success', // Hot lead
            'B' => 'info',
            'C' => 'warning',
            default => 'danger',
        };
    }
}
