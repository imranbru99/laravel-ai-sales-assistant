<?php

namespace App\Core\Ai;

use App\Models\Lead;
use App\Models\LeadScore;

class ScoringEngine
{
    protected AiGateway $aiGateway;

    public function __construct(AiGateway $aiGateway)
    {
        $this->aiGateway = $aiGateway;
    }

    public function scoreLead(Lead $lead, string $eventTrigger = 'manual'): Lead
    {
        $scoringResult = $this->aiGateway->scoreLead($lead);

        // Record in historical timeline
        LeadScore::create([
            'tenant_id' => $lead->tenant_id,
            'lead_id' => $lead->id,
            'score' => $scoringResult['score'],
            'score_grade' => $scoringResult['grade'],
            'rule_score' => $scoringResult['rule_score'] ?? 0,
            'engagement_score' => $scoringResult['engagement_score'] ?? 0,
            'ai_score' => $scoringResult['ai_score'] ?? 0,
            'explanation' => $scoringResult['explanation'] ?? '',
            'breakdown' => $scoringResult['breakdown'] ?? [],
            'recommended_action' => $scoringResult['recommended_action'] ?? '',
            'event_trigger' => $eventTrigger,
        ]);

        // Update the lead record directly
        $lead->update([
            'score' => $scoringResult['score'],
            'score_grade' => $scoringResult['grade'],
            'score_explanation' => [
                'summary' => $scoringResult['explanation'] ?? '',
                'rule_score' => $scoringResult['rule_score'] ?? 0,
                'engagement_score' => $scoringResult['engagement_score'] ?? 0,
                'ai_score' => $scoringResult['ai_score'] ?? 0,
            ],
            'recommended_next_action' => $scoringResult['recommended_action'] ?? '',
            'last_scored_at' => now(),
        ]);

        return $lead;
    }

    public function applyDecay(Lead $lead, int $decayPoints = 5): Lead
    {
        // If lead was inactive for > 14 days, gently decay score
        if ($lead->last_contacted_at && $lead->last_contacted_at->diffInDays(now()) > 14) {
            $newScore = max(10, $lead->score - $decayPoints);
            $newGrade = match (true) {
                $newScore >= 80 => 'A',
                $newScore >= 65 => 'B',
                $newScore >= 50 => 'C',
                default => 'D',
            };

            $lead->update([
                'score' => $newScore,
                'score_grade' => $newGrade,
                'last_scored_at' => now(),
            ]);

            LeadScore::create([
                'tenant_id' => $lead->tenant_id,
                'lead_id' => $lead->id,
                'score' => $newScore,
                'score_grade' => $newGrade,
                'explanation' => "Time decay applied (-{$decayPoints} pts) due to >14 days without interaction.",
                'event_trigger' => 'time_decay_job',
            ]);
        }

        return $lead;
    }
}
