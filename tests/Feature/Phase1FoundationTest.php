<?php

use App\Core\Modules\ModuleManager;
use App\Core\Tenancy\TenantContext;
use App\Models\Branch;
use App\Models\BusinessPreset;
use App\Models\Department;
use App\Models\Module;
use App\Models\Tenant;
use App\Models\User;
use ImranDev\UnicodePdf\Facades\UnicodePdf;
use ImranDev\UniversalSlug\Facades\UniversalSlug;
use ImranDevBd\AiCatalog\Facades\AiCatalog;
use ImranDevBd\AiHub\Facades\AIHub;
use ImranDevBd\AttendanceHub\Facades\AttendanceHub;
use ImranDevBD\LaravelAdminPanel\Facades\AdminPanel;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ModuleRegistrySeeder']);
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\RolesAndPermissionsSeeder']);
});

test('first-party packages from imrandevbd are installed and callable', function () {
    expect(class_exists(\Drutogoti\DrutogotiServiceProvider::class))->toBeTrue();
    expect(class_exists(AdminPanel::class))->toBeTrue();
    expect(class_exists(AiCatalog::class))->toBeTrue();
    expect(class_exists(AIHub::class))->toBeTrue();
    expect(class_exists(AttendanceHub::class))->toBeTrue();
    expect(class_exists(UnicodePdf::class))->toBeTrue();
    expect(class_exists(UniversalSlug::class))->toBeTrue();

    // Universal slug works with multilingual scripts
    $slug = UniversalSlug::generate('গ্রাহক অনুসন্ধান ও বিক্রয় ড্যাশবোর্ড');
    expect($slug)->toContain('গ্রাহক-অনুসন্ধান-ও-');
});

test('tenant global scope isolates tenant records', function () {
    $tenantA = Tenant::create([
        'name' => 'Tenant Alpha',
        'slug' => 'alpha',
        'currency' => 'USD',
    ]);

    $tenantB = Tenant::create([
        'name' => 'Tenant Beta',
        'slug' => 'beta',
        'currency' => 'BDT',
    ]);

    // Create departments under Tenant A
    app(TenantContext::class)->setTenant($tenantA);
    $deptA = Department::create(['name' => 'Sales Dept Alpha']);

    // Create department under Tenant B
    app(TenantContext::class)->setTenant($tenantB);
    $deptB = Department::create(['name' => 'Sales Dept Beta']);

    // Now query as Tenant A
    app(TenantContext::class)->setTenant($tenantA);
    $resultsA = Department::all();
    expect($resultsA)->toHaveCount(1)
        ->and($resultsA->first()->id)->toBe($deptA->id);

    // Query as Tenant B
    app(TenantContext::class)->setTenant($tenantB);
    $resultsB = Department::all();
    expect($resultsB)->toHaveCount(1)
        ->and($resultsB->first()->id)->toBe($deptB->id);
});

test('module manager toggles modules and resolves dependencies', function () {
    $tenant = Tenant::create([
        'name' => 'Acme Agency',
        'slug' => 'acme-agency',
    ]);

    $moduleManager = app(ModuleManager::class);

    // Initially core module is enabled
    expect($moduleManager->isModuleEnabled('auth_security', $tenant))->toBeTrue();

    // Enabling a module with dependencies auto-enables dependencies
    // ai_lead_scoring depends on leads
    $moduleManager->enableModule('ai_lead_scoring', $tenant);

    expect($moduleManager->isModuleEnabled('ai_lead_scoring', $tenant))->toBeTrue()
        ->and($moduleManager->isModuleEnabled('leads', $tenant))->toBeTrue();

    // Cannot disable a core module
    expect(fn () => $moduleManager->disableModule('auth_security', $tenant))
        ->toThrow(\InvalidArgumentException::class);
});

test('applying business preset enables the exact module set', function () {
    $tenant = Tenant::create([
        'name' => 'Smart Retail Shop',
        'slug' => 'smart-retail',
    ]);

    $moduleManager = app(ModuleManager::class);
    $moduleManager->applyPreset($tenant, 'retail');

    // Retail preset includes point_of_sale and inventory_warehouse
    expect($moduleManager->isModuleEnabled('point_of_sale', $tenant))->toBeTrue()
        ->and($moduleManager->isModuleEnabled('inventory_warehouse', $tenant))->toBeTrue();
});
