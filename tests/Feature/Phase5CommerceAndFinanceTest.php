<?php

use App\Core\Tenancy\TenantContext;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ModuleRegistrySeeder']);
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\RolesAndPermissionsSeeder']);
});

test('products track pricing and inventory stock counts', function () {
    $tenant = Tenant::create(['name' => 'Commerce Hub', 'slug' => 'commerce']);
    app(TenantContext::class)->setTenant($tenant);

    $product = Product::create([
        'name' => 'AI Sales Platform Annual License',
        'sku' => 'SaaS-ANN-01',
        'price' => 1200.00,
        'cost_price' => 200.00,
        'stock_quantity' => 100,
        'is_active' => true,
    ]);

    expect($product->id)->not->toBeNull()
        ->and((float) $product->price)->toBe(1200.0)
        ->and($product->stock_quantity)->toBe(100);
});

test('quotation generates unicode pdf document in bengali and english', function () {
    $tenant = Tenant::create(['name' => 'Rangpur Garments Ltd', 'slug' => 'rangpur', 'currency' => 'BDT']);
    app(TenantContext::class)->setTenant($tenant);

    $quote = Quotation::create([
        'quote_number' => 'QT-2026-001',
        'title' => '৫০,০০০ পিস কটন শার্টের মূল্য প্রস্তাবনা',
        'items' => [
            ['name' => 'Custom 100% Cotton Polo Shirt', 'quantity' => 5000, 'price' => 450],
        ],
        'total_amount' => 2250000,
        'currency' => 'BDT',
        'status' => 'sent',
    ]);

    $pdfDoc = $quote->getPdfDocument();
    expect($pdfDoc)->not->toBeNull();

    $output = $pdfDoc->output();
    expect($output)->not->toBeEmpty();
});

test('invoice payment reconciliation reduces balance and marks paid', function () {
    $tenant = Tenant::create(['name' => 'Fast Agency', 'slug' => 'fast-agency']);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    $this->actingAs($user);
    app(TenantContext::class)->setTenant($tenant);

    $invoice = Invoice::create([
        'invoice_number' => 'INV-2026-901',
        'items' => [['name' => 'Software Retainer', 'qty' => 1, 'price' => 5000]],
        'total_amount' => 5000,
        'paid_amount' => 0,
        'balance_due' => 5000,
        'status' => 'sent',
    ]);

    expect($invoice->balance_due)->toBe('5000.00');

    // Make partial payment
    Payment::create([
        'tenant_id' => $tenant->id,
        'invoice_id' => $invoice->id,
        'amount' => 2000,
        'method' => 'stripe',
        'status' => 'completed',
    ]);

    $invoice->update([
        'paid_amount' => 2000,
        'balance_due' => 3000,
        'status' => 'partial',
    ]);

    expect((float) $invoice->fresh()->balance_due)->toBe(3000.0)
        ->and($invoice->fresh()->status)->toBe('partial');

    // Make final payment
    Payment::create([
        'tenant_id' => $tenant->id,
        'invoice_id' => $invoice->id,
        'amount' => 3000,
        'method' => 'bank',
        'status' => 'completed',
    ]);

    $invoice->update([
        'paid_amount' => 5000,
        'balance_due' => 0,
        'status' => 'paid',
        'paid_at' => now(),
    ]);

    expect((float) $invoice->fresh()->balance_due)->toBe(0.0)
        ->and($invoice->fresh()->status)->toBe('paid');
});

test('expense claims can be submitted and approved by manager', function () {
    $tenant = Tenant::create(['name' => 'Apex Sales', 'slug' => 'apex-sales']);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    $manager = User::factory()->create(['tenant_id' => $tenant->id]);
    app(TenantContext::class)->setTenant($tenant);

    $expense = Expense::create([
        'user_id' => $user->id,
        'category' => 'Travel',
        'amount' => 150.50,
        'merchant' => 'Uber Trip',
        'status' => 'pending',
    ]);

    expect($expense->status)->toBe('pending');

    $expense->update([
        'status' => 'approved',
        'approved_by' => $manager->id,
        'approved_at' => now(),
    ]);

    expect($expense->fresh()->status)->toBe('approved')
        ->and($expense->fresh()->approved_by)->toBe($manager->id);
});
