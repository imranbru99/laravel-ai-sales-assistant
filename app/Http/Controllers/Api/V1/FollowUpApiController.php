<?php

namespace App\Http\Controllers\Api\V1;

use App\Core\Ai\AiGateway;
use App\Http\Controllers\Controller;
use App\Models\Deal;
use App\Models\FollowUp;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FollowUpApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = FollowUp::query();

        if ($request->filled('lead_id')) {
            $query->where('lead_id', $request->integer('lead_id'));
        }

        if ($request->filled('deal_id')) {
            $query->where('deal_id', $request->integer('deal_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('channel')) {
            $query->where('channel', $request->input('channel'));
        }

        $followUps = $query->with(['lead', 'deal', 'contact'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $followUps->items(),
            'meta' => [
                'current_page' => $followUps->currentPage(),
                'last_page' => $followUps->lastPage(),
                'per_page' => $followUps->perPage(),
                'total' => $followUps->total(),
            ],
        ]);
    }

    public function draft(Request $request, AiGateway $aiGateway): JsonResponse
    {
        $validated = $request->validate([
            'lead_id' => 'required_without:deal_id|nullable|exists:leads,id',
            'deal_id' => 'required_without:lead_id|nullable|exists:deals,id',
            'channel' => 'nullable|string|in:email,sms,whatsapp,call,linkedin',
            'tone' => 'nullable|string|in:consultative,friendly,formal,urgent',
            'language' => 'nullable|string|max:10',
            'save' => 'nullable|boolean',
        ]);

        $record = !empty($validated['lead_id'])
            ? Lead::findOrFail($validated['lead_id'])
            : Deal::findOrFail($validated['deal_id']);

        $options = [
            'channel' => $validated['channel'] ?? 'email',
            'tone' => $validated['tone'] ?? 'consultative',
            'language' => $validated['language'] ?? 'en',
        ];

        $draft = $aiGateway->draftFollowUp($record, $options);

        $savedFollowUp = null;
        if (!empty($validated['save'])) {
            $user = $request->user();
            $savedFollowUp = FollowUp::create([
                'tenant_id' => $record->tenant_id,
                'branch_id' => $record->branch_id,
                'lead_id' => ($record instanceof Lead) ? $record->id : null,
                'deal_id' => ($record instanceof Deal) ? $record->id : null,
                'contact_id' => $record->contact_id ?? null,
                'channel' => $options['channel'],
                'status' => 'draft',
                'subject' => $draft['subject'] ?? null,
                'content' => $draft['content'] ?? '',
                'tone' => $options['tone'],
                'language' => $options['language'],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'draft' => $draft,
                'follow_up' => $savedFollowUp,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lead_id' => 'nullable|exists:leads,id',
            'deal_id' => 'nullable|exists:deals,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'channel' => 'required|string|in:email,sms,whatsapp,call,linkedin',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'tone' => 'nullable|string',
            'language' => 'nullable|string|max:10',
            'status' => 'nullable|string|in:draft,scheduled,approved,sent',
            'scheduled_at' => 'nullable|date',
        ]);

        $user = $request->user();
        $validated['tenant_id'] = $user?->tenant_id;
        $validated['branch_id'] = $user?->branch_id;
        $validated['status'] = $validated['status'] ?? 'draft';

        $followUp = FollowUp::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Follow-up created successfully.',
            'data' => $followUp,
        ], 201);
    }
}
