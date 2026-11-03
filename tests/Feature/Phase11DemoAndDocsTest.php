<?php

use App\Models\Lead;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

test('demo install command seeds 3 tenants, users, and 100+ leads', function () {
    $this->artisan('demo:install')
        ->assertSuccessful();

    // Verify Tenants
    expect(Tenant::where('slug', 'nova-trading')->exists())->toBeTrue();
    expect(Tenant::where('slug', 'brightedge-agency')->exists())->toBeTrue();
    expect(Tenant::where('slug', 'rangpur-garments')->exists())->toBeTrue();

    // Verify Super Admin
    $superAdmin = User::withoutTenancy()->where('email', 'me@imrandev.bd')->first();
    expect($superAdmin)->not->toBeNull();
    expect($superAdmin->is_super_admin)->toBeTrue();

    // Verify Nova Trading Scored Leads
    $nova = Tenant::where('slug', 'nova-trading')->first();
    $leadsCount = Lead::where('tenant_id', $nova->id)->count();
    expect($leadsCount)->toBeGreaterThanOrEqual(100);

    // Verify A/B/C/D grades and explanation
    $sampleLead = Lead::where('tenant_id', $nova->id)->whereNotNull('score')->first();
    expect($sampleLead->score)->toBeGreaterThan(0);
    expect($sampleLead->score_grade)->toBeIn(['A', 'B', 'C', 'D']);
    expect($sampleLead->score_explanation)->toBeArray();
});

test('docs generate readme command updates README with 76 modules and 11 presets', function () {
    $this->artisan('docs:generate-readme')
        ->assertSuccessful();

    $readmePath = base_path('README.md');
    expect(File::exists($readmePath))->toBeTrue();

    $content = File::get($readmePath);
    expect($content)->toContain('AI Sales Assistant');
    expect($content)->toContain('imrandevbd/drutogoti');
    expect($content)->toContain('imrandevbd/laravel-ai-hub');
    expect($content)->toContain('11 Pre-Packaged Business Presets');
    expect($content)->toContain('76 Module Registry');
    expect($content)->toContain('Imran Ahmed');
    expect($content)->toContain('me@imrandev.bd');
});
