<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'version' => 'integer',
            'is_active' => 'boolean',
        ];
    }
}
