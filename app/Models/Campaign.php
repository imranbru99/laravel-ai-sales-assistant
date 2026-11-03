<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory, BelongsToTenant;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'sent_count' => 'integer',
            'open_count' => 'integer',
            'click_count' => 'integer',
            'scheduled_at' => 'datetime',
        ];
    }
}
