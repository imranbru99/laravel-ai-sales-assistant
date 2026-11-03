<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('parent_company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('name');
            $table->string('domain')->nullable()->index();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('industry')->nullable();
            $table->string('employee_range')->nullable();
            $table->decimal('annual_revenue', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->string('tax_id')->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->json('social_links')->nullable();
            $table->json('custom_fields')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'name']);
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('full_name')->index();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable()->index();
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->json('social_profiles')->nullable();
            $table->boolean('gdpr_consent')->default(false);
            $table->timestamp('gdpr_consent_at')->nullable();
            $table->json('custom_fields')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->string('contact_name');
            $table->string('company_name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable()->index();
            $table->string('status')->default('new'); // new, contacted, qualified, proposal, won, lost
            $table->string('lifecycle_stage')->default('lead'); // lead, marketing_qualified, sales_qualified, opportunity, customer
            $table->string('source')->default('website'); // website, referral, outreach, campaign, ad, event, other
            $table->string('source_details')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->integer('score')->default(50);
            $table->char('score_grade', 1)->default('C'); // A, B, C, D
            $table->json('score_explanation')->nullable();
            $table->text('recommended_next_action')->nullable();
            $table->timestamp('last_scored_at')->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamp('sla_due_at')->nullable();
            $table->string('lost_reason')->nullable();
            $table->json('custom_fields')->nullable();
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'score']);
            $table->index(['tenant_id', 'score_grade']);
            $table->index(['tenant_id', 'owner_id']);
        });

        Schema::create('pipelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('name');
            $table->string('code', 50)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
        });

        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pipeline_id')->constrained('pipelines')->cascadeOnDelete();
            $table->string('name');
            $table->integer('win_probability')->default(50); // 0 - 100
            $table->integer('sort_order')->default(0);
            $table->integer('rotting_days')->default(7);
            $table->string('color', 20)->default('#4f46e5');
            $table->timestamps();

            $table->index(['pipeline_id', 'sort_order']);
        });

        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('pipeline_id')->constrained('pipelines')->cascadeOnDelete();
            $table->foreignId('stage_id')->constrained('pipeline_stages')->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->string('title');
            $table->decimal('value', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');
            $table->date('expected_close_date')->nullable();
            $table->string('status')->default('open'); // open, won, lost
            $table->timestamp('won_at')->nullable();
            $table->timestamp('lost_at')->nullable();
            $table->string('lost_reason')->nullable();
            $table->integer('score')->default(50);
            $table->json('products')->nullable();
            $table->json('custom_fields')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'pipeline_id', 'stage_id']);
            $table->index(['tenant_id', 'owner_id']);
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('deal_id')->nullable()->constrained('deals')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('type'); // call, email, meeting, note, visit, task, whatsapp, sms
            $table->string('subject');
            $table->text('description')->nullable();
            $table->dateTime('due_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->string('outcome')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'is_completed']);
            $table->index(['tenant_id', 'due_at']);
        });

        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->morphs('notable');
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->timestamps();

            $table->index(['tenant_id', 'notable_type', 'notable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('deals');
        Schema::dropIfExists('pipeline_stages');
        Schema::dropIfExists('pipelines');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('companies');
    }
};
