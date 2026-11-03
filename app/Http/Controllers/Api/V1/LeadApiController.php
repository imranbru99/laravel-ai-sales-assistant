<?php

namespace App\Http\Controllers\Api\V1;

use App\Core\Ai\AiGateway;
use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\LeadScore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Lead::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->input('grade'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('contact_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $leads = $query->latest()->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $leads->items(),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'last_page' => $leads->lastPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
            ],
        ]);
    }

    public function store(Request $request, AiGateway $aiGateway): JsonResponse
    {
        $validated = $request->validate([
            'contact_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:50',
            'budget' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        $user = $request->user();
        $validated['tenant_id'] = $user?->tenant_id;
        $validated['branch_id'] = $user?->branch_id;
        $validated['owner_id'] = $user?->id;
        $validated['status'] = $validated['status'] ?? 'new';

        $lead = Lead::create($validated);

        // Auto-score lead with AI
        $scoreResult = $aiGateway->scoreLead($lead);
        $lead->update([
            'score' => $scoreResult['score'],
            'score_grade' => $scoreResult['grade'],
            'score_explanation' => $scoreResult,
            'recommended_next_action' => $scoreResult['recommended_action'] ?? null,
            'last_scored_at' => now(),
        ]);

        LeadScore::create([
            'tenant_id' => $lead->tenant_id,
            'lead_id' => $lead->id,
            'score' => $scoreResult['score'],
            'score_grade' => $scoreResult['grade'],
            'rule_score' => $scoreResult['rule_score'] ?? 0,
            'engagement_score' => $scoreResult['engagement_score'] ?? 0,
            'ai_score' => $scoreResult['ai_score'] ?? $scoreResult['score'],
            'explanation' => $scoreResult['explanation'] ?? '',
            'recommended_action' => $scoreResult['recommended_action'] ?? '',
            'event_trigger' => 'api_created',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead created and scored successfully.',
            'data' => $lead->fresh(),
        ], 201);
    }

    public function show(Lead $lead): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $lead->load(['company', 'contact', 'owner']),
        ]);
    }

    public function score(Lead $lead, AiGateway $aiGateway): JsonResponse
    {
        $scoreResult = $aiGateway->scoreLead($lead);
        $oldScore = $lead->score ?? 0;
        $delta = $scoreResult['score'] - $oldScore;

        $lead->update([
            'score' => $scoreResult['score'],
            'score_grade' => $scoreResult['grade'],
            'score_explanation' => $scoreResult,
            'recommended_next_action' => $scoreResult['recommended_action'] ?? null,
            'last_scored_at' => now(),
        ]);

        LeadScore::create([
            'tenant_id' => $lead->tenant_id,
            'lead_id' => $lead->id,
            'score' => $scoreResult['score'],
            'score_grade' => $scoreResult['grade'],
            'rule_score' => $scoreResult['rule_score'] ?? 0,
            'engagement_score' => $scoreResult['engagement_score'] ?? 0,
            'ai_score' => $scoreResult['ai_score'] ?? $scoreResult['score'],
            'explanation' => $scoreResult['explanation'] ?? '',
            'recommended_action' => $scoreResult['recommended_action'] ?? '',
            'event_trigger' => 'api_rescore',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Lead re-scored successfully.',
            'data' => [
                'lead_id' => $lead->id,
                'score' => $scoreResult['score'],
                'grade' => $scoreResult['grade'],
                'delta' => $delta,
                'explanation' => $scoreResult['explanation'] ?? '',
                'recommended_action' => $scoreResult['recommended_action'] ?? '',
            ],
        ]);
    }
}
