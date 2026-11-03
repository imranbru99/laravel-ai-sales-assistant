<?php

use App\Http\Controllers\Api\V1\DealApiController;
use App\Http\Controllers\Api\V1\FollowUpApiController;
use App\Http\Controllers\Api\V1\LeadApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'healthy',
            'version' => '1.0.0',
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return response()->json([
                'user' => $request->user()->load(['branch', 'roles']),
                'tenant_id' => $request->user()->tenant_id,
            ]);
        });

        // Leads
        Route::get('/leads', [LeadApiController::class, 'index']);
        Route::post('/leads', [LeadApiController::class, 'store']);
        Route::get('/leads/{lead}', [LeadApiController::class, 'show']);
        Route::post('/leads/{lead}/score', [LeadApiController::class, 'score']);

        // Deals
        Route::get('/deals', [DealApiController::class, 'index']);
        Route::post('/deals', [DealApiController::class, 'store']);
        Route::get('/deals/{deal}', [DealApiController::class, 'show']);

        // Follow-ups
        Route::get('/follow-ups', [FollowUpApiController::class, 'index']);
        Route::post('/follow-ups/draft', [FollowUpApiController::class, 'draft']);
        Route::post('/follow-ups', [FollowUpApiController::class, 'store']);
    });
});
