<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToBranch;
use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory, BelongsToTenant, BelongsToBranch;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'sla_due_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }
}
