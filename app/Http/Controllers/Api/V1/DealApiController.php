<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DealApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Deal::query();

        if ($request->filled('pipeline_id')) {
            $query->where('pipeline_id', $request->integer('pipeline_id'));
        }

        if ($request->filled('stage_id')) {
            $query->where('stage_id', $request->integer('stage_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->input('search')}%");
        }

        $deals = $query->with(['pipeline', 'stage', 'company', 'contact', 'owner'])
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $deals->items(),
            'meta' => [
                'current_page' => $deals->currentPage(),
                'last_page' => $deals->lastPage(),
                'per_page' => $deals->perPage(),
                'total' => $deals->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'pipeline_id' => 'required|exists:pipelines,id',
            'stage_id' => 'required|exists:pipeline_stages,id',
            'company_id' => 'nullable|exists:companies,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'lead_id' => 'nullable|exists:leads,id',
            'value' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:10',
            'status' => 'nullable|string|max:50',
            'expected_close_date' => 'nullable|date',
            'probability' => 'nullable|integer|min:0|max:100',
            'tags' => 'nullable|array',
        ]);

        $user = $request->user();
        $validated['tenant_id'] = $user?->tenant_id;
        $validated['branch_id'] = $user?->branch_id;
        $validated['owner_id'] = $user?->id;
        $validated['status'] = $validated['status'] ?? 'open';
        $validated['currency'] = $validated['currency'] ?? 'USD';

        $deal = Deal::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Deal created successfully.',
            'data' => $deal->load(['pipeline', 'stage', 'owner']),
        ], 201);
    }

    public function show(Deal $deal): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $deal->load(['pipeline', 'stage', 'company', 'contact', 'owner', 'lead']),
        ]);
    }
}
