<?php

use App\Core\Tenancy\TenantContext;
use App\Models\CallTranscript;
use App\Models\InboxConversation;
use App\Models\InboxMessage;
use App\Models\Lead;
use App\Models\LeadCaptureForm;
use App\Models\Sequence;
use App\Models\SequenceEnrollment;
use App\Models\SequenceStep;
use App\Models\Tenant;
use App\Models\Workflow;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ModuleRegistrySeeder']);
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\RolesAndPermissionsSeeder']);
});

test('inbox conversation threads messages with channel and internal notes', function () {
    $tenant = Tenant::create(['name' => 'Comms Hub', 'slug' => 'comms']);
    app(TenantContext::class)->setTenant($tenant);

    $lead = Lead::create([
        'contact_name' => 'David Warner',
        'email' => 'david@cricket.test',
    ]);

    $conversation = InboxConversation::create([
        'lead_id' => $lead->id,
        'channel' => 'whatsapp',
        'subject' => 'Product Inquiry WhatsApp',
    ]);

    // Inbound contact message
    InboxMessage::create([
        'conversation_id' => $conversation->id,
        'sender_type' => 'contact',
        'sender_name' => 'David Warner',
        'channel' => 'whatsapp',
        'content' => 'Hello, I would like to know pricing for enterprise.',
    ]);

    // Internal whisper note
    InboxMessage::create([
        'conversation_id' => $conversation->id,
        'sender_type' => 'agent',
        'sender_name' => 'Imran Ahmed',
        'channel' => 'whatsapp',
        'content' => 'High-value buyer; verified budget > $10,000.',
        'is_internal_note' => true,
    ]);

    expect($conversation->messages()->count())->toBe(2)
        ->and($conversation->messages()->where('is_internal_note', true)->count())->toBe(1);
});

test('sequences structure multi-step outreach cadences', function () {
    $tenant = Tenant::create(['name' => 'Cadence Co', 'slug' => 'cadence']);
    app(TenantContext::class)->setTenant($tenant);

    $sequence = Sequence::create([
        'name' => 'B2B Cold Outreach 4-Step',
        'is_active' => true,
    ]);

    SequenceStep::create([
        'sequence_id' => $sequence->id,
        'step_number' => 1,
        'delay_days' => 0,
        'channel' => 'email',
        'subject' => 'Quick question regarding your sales workflow',
        'content_template' => 'Hi {{name}}, noticed your team is expanding...',
    ]);

    SequenceStep::create([
        'sequence_id' => $sequence->id,
        'step_number' => 2,
        'delay_days' => 3,
        'channel' => 'whatsapp',
        'content_template' => 'Hi {{name}}, sharing quick 2-min case study...',
    ]);

    $lead = Lead::create(['contact_name' => 'Emma Watson', 'email' => 'emma@cinema.test']);

    $enrollment = SequenceEnrollment::create([
        'sequence_id' => $sequence->id,
        'lead_id' => $lead->id,
        'current_step' => 1,
        'status' => 'active',
    ]);

    expect($sequence->steps()->count())->toBe(2)
        ->and($enrollment->status)->toBe('active')
        ->and($enrollment->lead->id)->toBe($lead->id);
});

test('lead capture forms automatically generate universal multilingual slugs', function () {
    $tenant = Tenant::create(['name' => 'Form Builder Ltd', 'slug' => 'forms']);
    app(TenantContext::class)->setTenant($tenant);

    $form = LeadCaptureForm::create([
        'name' => 'সফটওয়্যার ডেমো বুকিং ফর্ম ২০২৬',
        'slug' => 'software-demo-form-2026',
        'fields' => ['name' => 'text', 'email' => 'email'],
        'submit_button_text' => 'বুকিং নিশ্চিত করুন',
    ]);

    expect($form->id)->not->toBeNull()
        ->and($form->slug)->not->toBeEmpty();
});

test('call transcripts record summaries action items and objections', function () {
    $tenant = Tenant::create(['name' => 'Speech AI', 'slug' => 'speech']);
    app(TenantContext::class)->setTenant($tenant);

    $transcript = CallTranscript::create([
        'title' => 'Discovery Call - Rangpur Garments',
        'duration_seconds' => 745,
        'transcript_text' => "Rep: Hello Mr. Kabir. Kabir: Yes, we need attendance machine integration for 500 factory workers.",
        'summary' => 'Client requires biometric attendance device integration for garment factory.',
        'action_items' => ['Send technical spec for ZKTeco driver', 'Prepare quotation for 3 devices'],
        'sentiment' => 'positive',
        'objections' => ['Need offline LAN sync support'],
    ]);

    expect($transcript->duration_seconds)->toBe(745)
        ->and($transcript->action_items)->toHaveCount(2)
        ->and($transcript->objections)->toContain('Need offline LAN sync support');
});
