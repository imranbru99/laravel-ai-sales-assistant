<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prompts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->string('key')->index();
            $table->string('name');
            $table->string('category')->default('general');
            $table->text('template');
            $table->json('variables')->nullable();
            $table->string('model')->nullable();
            $table->integer('version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tenant_id', 'key']);
        });

        Schema::create('brand_voices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('tone')->default('consultative');
            $table->text('style_guide')->nullable();
            $table->json('banned_words')->nullable();
            $table->json('sample_phrases')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['tenant_id', 'is_default']);
        });

        Schema::create('lead_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->integer('score');
            $table->char('score_grade', 1); // A, B, C, D
            $table->integer('rule_score')->default(0);
            $table->integer('engagement_score')->default(0);
            $table->integer('ai_score')->default(0);
            $table->text('explanation')->nullable();
            $table->json('breakdown')->nullable();
            $table->text('recommended_action')->nullable();
            $table->string('event_trigger')->default('manual');
            $table->timestamps();

            $table->index(['tenant_id', 'lead_id']);
            $table->index(['tenant_id', 'created_at']);
        });

        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->cascadeOnDelete();
            $table->foreignId('deal_id')->nullable()->constrained('deals')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->string('channel')->default('email'); // email, sms, whatsapp, call, linkedin
            $table->string('status')->default('draft'); // draft, scheduled, approved, sent, rejected, failed
            $table->string('subject')->nullable();
            $table->text('content');
            $table->string('tone')->default('consultative');
            $table->string('language', 10)->default('en');
            $table->dateTime('scheduled_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->integer('open_count')->default(0);
            $table->integer('click_count')->default(0);
            $table->dateTime('replied_at')->nullable();
            $table->string('reply_sentiment')->nullable(); // positive, neutral, negative, objection
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'scheduled_at']);
            $table->index(['tenant_id', 'lead_id']);
        });

        Schema::create('ai_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action_type')->index(); // lead_scoring, followup_draft, copilot_chat, meeting_summary, enrichment
            $table->string('provider')->default('demo'); // demo, openai, gemini, claude, groq
            $table->string('model')->default('demo-v1');
            $table->integer('input_tokens')->default(0);
            $table->integer('output_tokens')->default(0);
            $table->decimal('cost', 8, 4)->default(0);
            $table->integer('latency_ms')->default(150);
            $table->string('feedback')->nullable(); // thumbs_up, thumbs_down
            $table->text('feedback_notes')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_logs');
        Schema::dropIfExists('follow_ups');
        Schema::dropIfExists('lead_scores');
        Schema::dropIfExists('brand_voices');
        Schema::dropIfExists('prompts');
    }
};
