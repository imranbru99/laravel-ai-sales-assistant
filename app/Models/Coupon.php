<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_spend' => 'decimal:2',
            'max_uses' => 'integer',
            'uses_count' => 'integer',
            'expires_at' => 'date',
        ];
    }
}
