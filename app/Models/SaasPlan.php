<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaasPlan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'price_monthly' => 'decimal:2',
            'price_yearly' => 'decimal:2',
            'max_users' => 'integer',
            'max_branches' => 'integer',
            'max_storage_mb' => 'integer',
            'max_ai_tokens' => 'integer',
            'included_modules' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }
}
