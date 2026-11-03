<?php

namespace App\Core\Ai;

use App\Models\Deal;
use App\Models\FollowUp;
use App\Models\Lead;

class FollowUpPlanner
{
    protected AiGateway $aiGateway;

    public function __construct(AiGateway $aiGateway)
    {
        $this->aiGateway = $aiGateway;
    }

    public function createDraft(Lead|Deal $record, array $options = []): FollowUp
    {
        $draftData = $this->aiGateway->draftFollowUp($record, $options);

        $tenantId = $record->tenant_id;
        $branchId = $record->branch_id;

        $leadId = ($record instanceof Lead) ? $record->id : $record->lead_id;
        $dealId = ($record instanceof Deal) ? $record->id : null;
        $contactId = ($record instanceof Lead) ? $record->contact_id : $record->contact_id;

        return FollowUp::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,
            'lead_id' => $leadId,
            'deal_id' => $dealId,
            'contact_id' => $contactId,
            'channel' => $draftData['channel'] ?? 'email',
            'status' => 'draft',
            'subject' => $draftData['subject'] ?? 'Sales Follow-up',
            'content' => $draftData['content'] ?? '',
            'tone' => $draftData['tone'] ?? 'consultative',
            'language' => $draftData['language'] ?? 'en',
            'scheduled_at' => $options['scheduled_at'] ?? now()->addHours(2),
        ]);
    }

    public function approve(FollowUp $followUp, ?int $userId = null): FollowUp
    {
        $followUp->update([
            'status' => 'approved',
            'approved_by' => $userId ?? auth()->id(),
            'approved_at' => now(),
        ]);

        return $followUp;
    }

    public function send(FollowUp $followUp): FollowUp
    {
        // Guardrails check: ensure quiet hours (e.g. between 9 PM and 8 AM)
        $currentHour = now()->hour;
        if ($currentHour < 8 || $currentHour >= 21) {
            // Re-schedule for 9 AM tomorrow
            $followUp->update([
                'status' => 'scheduled',
                'scheduled_at' => now()->addDay()->setTime(9, 0),
            ]);
            return $followUp;
        }

        // Dispatch via simulated channel adapter
        $followUp->update([
            'status' => 'sent',
            'sent_at' => now(),
            'open_count' => 0,
            'click_count' => 0,
        ]);

        if ($followUp->lead) {
            $followUp->lead->update(['last_contacted_at' => now()]);
        }

        return $followUp;
    }
}
