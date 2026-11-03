<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('email'); // email, sms, whatsapp
            $table->string('status')->default('draft'); // draft, scheduled, sending, completed
            $table->string('subject')->nullable();
            $table->text('content')->nullable();
            $table->integer('sent_count')->default(0);
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->dateTime('scheduled_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'type', 'status']);
        });

        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->index();
            $table->longText('content');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_published')->default(true);
            $table->dateTime('published_at')->nullable();
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->index();
            $table->text('description')->nullable();
            $table->dateTime('event_date');
            $table->string('location')->default('Online Webinar');
            $table->integer('capacity')->default(100);
            $table->integer('registrations_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('type')->default('nps'); // nps, csat, feedback
            $table->json('questions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->integer('score')->default(10);
            $table->text('comment')->nullable();
            $table->string('sentiment')->default('positive');
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('code')->index();
            $table->string('type')->default('percentage'); // percentage, fixed
            $table->decimal('value', 10, 2);
            $table->decimal('min_spend', 10, 2)->default(0);
            $table->integer('max_uses')->default(100);
            $table->integer('uses_count')->default(0);
            $table->date('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('ticket_number')->index();
            $table->string('subject');
            $table->text('description');
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('new'); // new, in_progress, pending_client, resolved, closed
            $table->dateTime('sla_due_at')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'priority']);
        });

        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->text('content');
            $table->boolean('is_internal_note')->default(false);
            $table->timestamps();
        });

        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->index();
            $table->string('category')->default('General');
            $table->longText('content');
            $table->boolean('is_public')->default(true);
            $table->integer('views_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('knowledge_articles');
        Schema::dropIfExists('ticket_replies');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('surveys');
        Schema::dropIfExists('events');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('campaigns');
    }
};
