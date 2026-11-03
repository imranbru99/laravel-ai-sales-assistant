<?php

use App\Core\Tenancy\TenantContext;
use App\Models\ApprovalRequest;
use App\Models\Branch;
use App\Models\CustomReport;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->tenant = Tenant::create([
        'name' => 'Analytics Corp',
        'slug' => 'analytics-corp',
        'is_active' => true,
    ]);

    $this->branch = Branch::create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Headquarters',
        'code' => 'HQ',
        'is_default' => true,
    ]);

    $this->user = User::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'name' => 'Gov Admin',
        'email' => 'gov@analyticscorp.com',
        'password' => bcrypt('password'),
    ]);

    app(TenantContext::class)->setTenant($this->tenant);
    app(TenantContext::class)->setBranch($this->branch);
});

test('api health check endpoint returns 200', function () {
    $response = $this->getJson('/api/v1/health');
    $response->assertStatus(200)
        ->assertJson([
            'status' => 'healthy',
            'version' => '1.0.0',
        ]);
});

test('authenticated user can create and auto-score lead via api', function () {
    Sanctum::actingAs($this->user);

    $payload = [
        'contact_name' => 'Sarah Connor',
        'company_name' => 'Cyberdyne Systems',
        'email' => 'sarah@cyberdyne.io',
        'source' => 'linkedin',
        'budget' => 25000,
    ];

    $response = $this->postJson('/api/v1/leads', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.contact_name', 'Sarah Connor');

    $leadId = $response->json('data.id');
    $lead = Lead::find($leadId);

    expect($lead)->not->toBeNull();
    expect($lead->score)->toBeGreaterThan(0);
    expect($lead->score_grade)->toBeIn(['A', 'B', 'C', 'D']);
});

test('authenticated user can rescore lead via api', function () {
    Sanctum::actingAs($this->user);

    $lead = Lead::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'contact_name' => 'John Wick',
        'company_name' => 'Continental',
        'email' => 'john@continental.com',
        'source' => 'referral',
        'budget' => 75000,
    ]);

    $response = $this->postJson("/api/v1/leads/{$lead->id}/score");

    $response->assertStatus(200)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.lead_id', $lead->id);

    expect($response->json('data.score'))->toBeGreaterThan(0);
});

test('authenticated user can draft ai follow-up via api', function () {
    Sanctum::actingAs($this->user);

    $lead = Lead::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'contact_name' => 'Elena Fisher',
        'company_name' => 'Naughty Dog Inc',
        'email' => 'elena@nd.com',
    ]);

    $response = $this->postJson('/api/v1/follow-ups/draft', [
        'lead_id' => $lead->id,
        'channel' => 'email',
        'tone' => 'consultative',
        'language' => 'en',
        'save' => true,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('success', true);

    expect($response->json('data.draft.subject'))->not->toBeEmpty();
    expect($response->json('data.follow_up'))->not->toBeNull();
});

test('custom reports can be created and retrieved', function () {
    $report = CustomReport::create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Q4 Sales Pipeline Funnel',
        'module_key' => 'deals',
        'chart_type' => 'bar',
        'fields' => ['id', 'title', 'value', 'stage_id'],
        'group_by' => 'stage_id',
    ]);

    expect($report->exists)->toBeTrue();
    expect($report->fields)->toBeArray()->toContain('value');
});

test('multi-tier approval workflow creates and approves request', function () {
    $approver = User::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'name' => 'Finance Director',
        'email' => 'cfo@analyticscorp.com',
        'password' => bcrypt('password'),
    ]);

    $approval = ApprovalRequest::create([
        'tenant_id' => $this->tenant->id,
        'approvable_type' => User::class,
        'approvable_id' => $this->user->id,
        'requested_by' => $this->user->id,
        'type' => 'discount',
        'tier' => 2,
        'status' => 'pending',
    ]);

    expect($approval->status)->toBe('pending');

    $approval->update([
        'status' => 'approved',
        'approved_by' => $approver->id,
        'approved_at' => now(),
        'decision_notes' => 'Approved per enterprise volume policy.',
    ]);

    expect($approval->fresh()->status)->toBe('approved');
    expect($approval->fresh()->approved_by)->toBe($approver->id);
});
