<?php

use App\Filament\SuperAdmin\Widgets\PlatformOverviewWidget;
use App\Models\Branch;
use App\Models\ImpersonationLog;
use App\Models\SaasPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->superAdmin = User::create([
        'name' => 'Imran Ahmed',
        'email' => 'me@imrandev.bd',
        'password' => bcrypt('password'),
        'is_super_admin' => true,
        'is_active' => true,
    ]);

    $this->tenant = Tenant::create([
        'name' => 'Acme Corporation',
        'slug' => 'acme-corp',
        'plan' => 'growth',
        'status' => 'active',
        'is_active' => true,
    ]);

    $this->branch = Branch::create([
        'tenant_id' => $this->tenant->id,
        'name' => 'Acme HQ',
        'is_default' => true,
    ]);

    $this->tenantAdmin = User::create([
        'tenant_id' => $this->tenant->id,
        'branch_id' => $this->branch->id,
        'name' => 'Acme Admin',
        'email' => 'admin@acmecorp.com',
        'password' => bcrypt('password'),
        'is_super_admin' => false,
        'is_active' => true,
    ]);
});

test('super admin can access super-admin panel dashboard', function () {
    $response = $this->actingAs($this->superAdmin)->get('/super-admin');
    $response->assertSuccessful();
});

test('regular tenant user is forbidden from super-admin panel', function () {
    $response = $this->actingAs($this->tenantAdmin)->get('/super-admin');
    $response->assertForbidden();
});

test('saas plans can be created with resource quotas', function () {
    $plan = SaasPlan::create([
        'name' => 'Enterprise Plus',
        'slug' => 'enterprise-plus',
        'price_monthly' => 399.00,
        'price_yearly' => 3990.00,
        'max_users' => 50,
        'max_branches' => 10,
        'max_storage_mb' => 20480,
        'max_ai_tokens' => 2000000,
        'is_active' => true,
        'is_featured' => true,
    ]);

    expect($plan->exists)->toBeTrue();
    expect($plan->price_monthly)->toBe('399.00');
    expect($plan->max_ai_tokens)->toBe(2000000);
});

test('impersonation log is recorded properly', function () {
    $log = ImpersonationLog::create([
        'super_admin_id' => $this->superAdmin->id,
        'tenant_id' => $this->tenant->id,
        'impersonated_user_id' => $this->tenantAdmin->id,
        'reason' => 'Troubleshooting billing issue',
        'ip_address' => '127.0.0.1',
    ]);

    expect($log->exists)->toBeTrue();
    expect($log->tenant->name)->toBe('Acme Corporation');
    expect($log->impersonatedUser->email)->toBe('admin@acmecorp.com');
});

test('tenant can be suspended and reactivated', function () {
    expect($this->tenant->status)->toBe('active');

    $this->tenant->update(['status' => 'suspended']);
    expect($this->tenant->fresh()->status)->toBe('suspended');

    $this->tenant->update(['status' => 'active']);
    expect($this->tenant->fresh()->status)->toBe('active');
});
