<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbox_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel')->default('email'); // email, sms, whatsapp, webchat, facebook
            $table->string('subject')->nullable();
            $table->string('status')->default('open'); // open, pending, closed
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'channel']);
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('inbox_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained('inbox_conversations')->cascadeOnDelete();
            $table->string('sender_type')->default('contact'); // contact, agent, bot, system
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sender_name')->nullable();
            $table->string('channel')->default('email');
            $table->text('content');
            $table->boolean('is_internal_note')->default(false);
            $table->string('status')->default('delivered'); // delivered, read, failed
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });

        Schema::create('sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('total_enrolled')->default(0);
            $table->integer('total_completed')->default(0);
            $table->timestamps();
        });

        Schema::create('sequence_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sequence_id')->constrained('sequences')->cascadeOnDelete();
            $table->integer('step_number')->default(1);
            $table->integer('delay_days')->default(1);
            $table->string('channel')->default('email'); // email, sms, whatsapp, task
            $table->string('subject')->nullable();
            $table->text('content_template');
            $table->boolean('is_ai_personalized')->default(true);
            $table->timestamps();

            $table->index(['sequence_id', 'step_number']);
        });

        Schema::create('sequence_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('sequence_id')->constrained('sequences')->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->cascadeOnDelete();
            $table->integer('current_step')->default(1);
            $table->string('status')->default('active'); // active, paused_on_reply, completed, cancelled
            $table->timestamp('enrolled_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('trigger_event'); // LeadCreated, EmailReplied, StageChanged, PaymentReceived
            $table->json('conditions')->nullable();
            $table->json('actions'); // create_task, move_stage, send_notification, call_webhook, score_lead
            $table->boolean('is_active')->default(true);
            $table->integer('run_count')->default(0);
            $table->timestamp('last_run_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'trigger_event', 'is_active']);
        });

        Schema::create('workflow_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('workflow_id')->constrained('workflows')->cascadeOnDelete();
            $table->string('trigger_event');
            $table->string('status')->default('success'); // success, failed
            $table->json('payload')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['workflow_id', 'created_at']);
        });

        Schema::create('lead_capture_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->index();
            $table->text('description')->nullable();
            $table->json('fields'); // array of fields
            $table->string('submit_button_text')->default('Submit');
            $table->string('redirect_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('views_count')->default(0);
            $table->integer('submissions_count')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });

        Schema::create('call_transcripts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignId('deal_id')->nullable()->constrained('deals')->nullOnDelete();
            $table->string('title');
            $table->integer('duration_seconds')->default(0);
            $table->longText('transcript_text');
            $table->text('summary')->nullable();
            $table->json('action_items')->nullable();
            $table->string('sentiment')->default('positive');
            $table->json('objections')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'lead_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('call_transcripts');
        Schema::dropIfExists('lead_capture_forms');
        Schema::dropIfExists('workflow_logs');
        Schema::dropIfExists('workflows');
        Schema::dropIfExists('sequence_enrollments');
        Schema::dropIfExists('sequence_steps');
        Schema::dropIfExists('sequences');
        Schema::dropIfExists('inbox_messages');
        Schema::dropIfExists('inbox_conversations');
    }
};
