<?php

namespace App\Models;

use App\Core\Tenancy\BelongsToBranch;
use App\Core\Tenancy\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUp extends Model
{
    use HasFactory, BelongsToTenant, BelongsToBranch;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'approved_at' => 'datetime',
            'replied_at' => 'datetime',
            'open_count' => 'integer',
            'click_count' => 'integer',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isPendingApproval(): bool
    {
        return $this->status === 'draft' || $this->status === 'scheduled';
    }
}
