<?php

use App\Core\Modules\ModuleManager;
use App\Core\Tenancy\TenantContext;
use App\Filament\App\Resources\LeadResource;
use App\Models\Activity;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ModuleRegistrySeeder']);
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\RolesAndPermissionsSeeder']);
});

test('crm lead can be created and computes grade badges', function () {
    $tenant = Tenant::create(['name' => 'Apex Agency', 'slug' => 'apex']);
    app(TenantContext::class)->setTenant($tenant);

    $lead = Lead::create([
        'contact_name' => 'John Doe',
        'company_name' => 'Apex Global',
        'email' => 'john@apex.test',
        'status' => 'new',
        'score' => 88,
        'score_grade' => 'A',
    ]);

    expect($lead->id)->not->toBeNull()
        ->and($lead->tenant_id)->toBe($tenant->id)
        ->and($lead->score)->toBe(88)
        ->and($lead->score_grade)->toBe('A')
        ->and($lead->getGradeBadgeColor())->toBe('success');
});

test('companies and contacts hierarchy with gdpr consent', function () {
    $tenant = Tenant::create(['name' => 'Tech Corp', 'slug' => 'tech-corp']);
    app(TenantContext::class)->setTenant($tenant);

    $company = Company::create([
        'name' => 'Tech Solutions Ltd',
        'domain' => 'techsol.com',
        'industry' => 'SaaS',
    ]);

    $contact = Contact::create([
        'company_id' => $company->id,
        'first_name' => 'Sarah',
        'last_name' => 'Connor',
        'email' => 'sarah@techsol.com',
        'gdpr_consent' => true,
        'gdpr_consent_at' => now(),
    ]);

    expect($contact->full_name)->toBe('Sarah Connor')
        ->and($contact->company->id)->toBe($company->id)
        ->and($contact->gdpr_consent)->toBeTrue();
});

test('pipeline and deals calculate stage probabilities and rotting flags', function () {
    $tenant = Tenant::create(['name' => 'Sales Pro', 'slug' => 'sales-pro']);
    app(TenantContext::class)->setTenant($tenant);

    $pipeline = Pipeline::create(['name' => 'Direct B2B Sales', 'is_default' => true]);
    $stage = PipelineStage::create([
        'pipeline_id' => $pipeline->id,
        'name' => 'Proposal Sent',
        'win_probability' => 70,
        'rotting_days' => 5,
    ]);

    $deal = Deal::create([
        'pipeline_id' => $pipeline->id,
        'stage_id' => $stage->id,
        'title' => 'Enterprise Retainer',
        'value' => 25000,
        'status' => 'open',
    ]);

    expect($deal->stage->win_probability)->toBe(70)
        ->and($deal->isRotting())->toBeFalse();

    // Now simulate deal updated 10 days ago
    $deal->updated_at = now()->subDays(10);
    $deal->saveQuietly();

    expect($deal->isRotting())->toBeTrue();
});

test('lead resource canAccess respects module manager toggle', function () {
    $tenant = Tenant::create(['name' => 'Modular Tenant', 'slug' => 'modular']);
    app(TenantContext::class)->setTenant($tenant);

    $moduleManager = app(ModuleManager::class);

    // When leads is enabled
    $moduleManager->enableModule('leads', $tenant);
    expect(LeadResource::canAccess())->toBeTrue();

    // When leads is disabled
    $moduleManager->disableModule('leads', $tenant);
    expect(LeadResource::canAccess())->toBeFalse();
});
