<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('module_key')->default('leads'); // leads, deals, invoices, attendance
            $table->json('fields');
            $table->json('filters')->nullable();
            $table->string('group_by')->nullable();
            $table->string('chart_type')->default('bar'); // bar, line, pie, table
            $table->string('schedule_frequency')->nullable(); // daily, weekly, monthly
            $table->string('schedule_email')->nullable();
            $table->timestamps();
        });

        Schema::create('approval_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->morphs('approvable');
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->string('type'); // discount, expense, quote_over_limit, leave
            $table->integer('tier')->default(1);
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('decision_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('integration_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('provider'); // google, microsoft365, slack, telegram, shopify, woocommerce, meta_lead_ads
            $table->json('credentials')->nullable();
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->dateTime('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'provider']);
        });

        Schema::create('outbound_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('url');
            $table->string('secret');
            $table->json('events'); // ['lead.created', 'deal.won', 'invoice.paid']
            $table->boolean('is_active')->default(true);
            $table->integer('failure_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbound_webhooks');
        Schema::dropIfExists('integration_connections');
        Schema::dropIfExists('approval_requests');
        Schema::dropIfExists('custom_reports');
    }
};
