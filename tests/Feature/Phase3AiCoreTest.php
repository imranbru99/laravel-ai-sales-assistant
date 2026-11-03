<?php

use App\Core\Ai\AiGateway;
use App\Core\Ai\FollowUpPlanner;
use App\Core\Ai\ScoringEngine;
use App\Core\Tenancy\TenantContext;
use App\Models\AiLog;
use App\Models\FollowUp;
use App\Models\Lead;
use App\Models\LeadScore;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ModuleRegistrySeeder']);
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\RolesAndPermissionsSeeder']);
});

test('ai gateway scores lead with grade explanation and recommendation', function () {
    $tenant = Tenant::create(['name' => 'Cybernetics Inc', 'slug' => 'cybernetics']);
    app(TenantContext::class)->setTenant($tenant);

    $lead = Lead::create([
        'contact_name' => 'Tariq Rahman',
        'company_name' => 'Bengal Logistics',
        'email' => 'tariq@bengallogistics.com',
        'phone' => '+8801811223344',
        'budget' => 25000,
        'source' => 'referral',
        'status' => 'qualified',
        'last_contacted_at' => now()->subDay(),
    ]);

    $scoringEngine = app(ScoringEngine::class);
    $scoredLead = $scoringEngine->scoreLead($lead, 'test_run');

    expect($scoredLead->score)->toBeGreaterThanOrEqual(80)
        ->and($scoredLead->score_grade)->toBe('A')
        ->and($scoredLead->score_explanation)->not->toBeEmpty()
        ->and($scoredLead->recommended_next_action)->not->toBeEmpty();

    // Verify history recorded in lead_scores
    expect(LeadScore::where('lead_id', $lead->id)->count())->toBe(1);

    // Verify usage logged in ai_logs
    expect(AiLog::where('action_type', 'lead_scoring')->count())->toBe(1);
});

test('follow up planner creates drafts in multiple channels and languages', function () {
    $tenant = Tenant::create(['name' => 'Delta Global', 'slug' => 'delta']);
    app(TenantContext::class)->setTenant($tenant);

    $lead = Lead::create([
        'contact_name' => 'Samantha Smith',
        'company_name' => 'Acme Corporation',
        'email' => 'samantha@acme.com',
    ]);

    $planner = app(FollowUpPlanner::class);

    // 1. English Email Draft
    $emailFollowUp = $planner->createDraft($lead, [
        'channel' => 'email',
        'language' => 'en',
    ]);

    expect($emailFollowUp->channel)->toBe('email')
        ->and($emailFollowUp->status)->toBe('draft')
        ->and($emailFollowUp->content)->toContain('Samantha Smith')
        ->and($emailFollowUp->content)->toContain('Acme Corporation');

    // 2. Bengali WhatsApp Draft
    $bengaliFollowUp = $planner->createDraft($lead, [
        'channel' => 'whatsapp',
        'language' => 'bn',
    ]);

    expect($bengaliFollowUp->channel)->toBe('whatsapp')
        ->and($bengaliFollowUp->language)->toBe('bn')
        ->and($bengaliFollowUp->content)->toContain('আসসালামু আলাইকুম');
});

test('follow up approval and dispatch workflow transitions status', function () {
    $tenant = Tenant::create(['name' => 'Pioneer Hub', 'slug' => 'pioneer']);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    $this->actingAs($user);
    app(TenantContext::class)->setTenant($tenant);

    $lead = Lead::create([
        'contact_name' => 'Michael Scott',
        'email' => 'michael@dundermifflin.com',
    ]);

    $planner = app(FollowUpPlanner::class);
    $followUp = $planner->createDraft($lead);

    expect($followUp->status)->toBe('draft');

    // Approve
    $approved = $planner->approve($followUp, $user->id);
    expect($approved->status)->toBe('approved')
        ->and($approved->approved_by)->toBe($user->id);

    // Send
    $sent = $planner->send($approved);
    expect($sent->status)->toBeIn(['sent', 'scheduled']);
});

test('ai copilot responds with tenant context and data insights', function () {
    $tenant = Tenant::create(['name' => 'Orbit SaaS', 'slug' => 'orbit']);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    app(TenantContext::class)->setTenant($tenant);

    Lead::create([
        'contact_name' => 'Prospect A',
        'score' => 90,
        'score_grade' => 'A',
    ]);

    $aiGateway = app(AiGateway::class);
    $reply = $aiGateway->askCopilot('Which hot leads do I have?', $tenant, $user);

    expect($reply)->toContain('Grade A')
        ->and($reply)->toContain('Orbit SaaS');
});
