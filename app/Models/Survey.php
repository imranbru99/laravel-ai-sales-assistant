<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Survey extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'questions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }
}
