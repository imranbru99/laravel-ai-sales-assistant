<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sequence extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'total_enrolled' => 'integer',
            'total_completed' => 'integer',
        ];
    }

    public function steps(): HasMany
    {
        return $this->hasMany(SequenceStep::class)->orderBy('step_number');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(SequenceEnrollment::class);
    }
}
