<?php

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('homepage loads successfully with hero and interactive sections', function () {
    $response = $this->get('/');
    $response->assertSuccessful();
    $response->assertSee('Drafts follow-ups, scores leads');
    $response->assertSee('Interactive AI Lead Scoring Simulator');
    $response->assertSee('Multichannel AI Follow-up Drafter');
    $response->assertSee('76 Specialized Modules');
});

test('features index displays all modules and supports category filtering', function () {
    $response = $this->get('/features');
    $response->assertSuccessful();
    $response->assertSee('76 Specialized Modules');

    // Test category filter
    $crmResponse = $this->get('/features?category=crm');
    $crmResponse->assertSuccessful();
    $crmResponse->assertSee('Leads Management');
});

test('feature detail page loads for a valid module slug', function () {
    $response = $this->get('/features/ai-lead-scoring-engine');
    $response->assertSuccessful();
    $response->assertSee('AI Lead Scoring Engine');
    $response->assertSee('What You Get With This Module');
});

test('solutions index lists 11 business presets', function () {
    $response = $this->get('/solutions');
    $response->assertSuccessful();
    $response->assertSee('11 Ready-Made Business Presets');
    $response->assertSee('Garment &amp; Textile Merchandiser', false);
    $response->assertSee('B2B Agency &amp; Professional Services', false);
});

test('solution detail page displays preset blueprint and enabled modules', function () {
    $response = $this->get('/solutions/garments');
    $response->assertSuccessful();
    $response->assertSee('Garment &amp; Textile Merchandiser', false);
    $response->assertSee('Included Modules in this Blueprint');
});

test('pricing page loads with tiers and quotas', function () {
    $response = $this->get('/pricing');
    $response->assertSuccessful();
    $response->assertSee('Plans Built For Any Growth Stage');
    $response->assertSee('Starter');
    $response->assertSee('Growth');
    $response->assertSee('Enterprise');
});

test('demo switcher displays 8 personas and allows 1-click login', function () {
    $response = $this->get('/demo');
    $response->assertSuccessful();
    $response->assertSee('One-Click Demo Personas');
    $response->assertSee('Super Administrator');
    $response->assertSee('Senior Account Executive');

    // Test 1-click login as sales rep
    $loginResponse = $this->post('/demo/login/sales_rep');
    $loginResponse->assertRedirect('/app');
    $this->assertAuthenticated();
});
