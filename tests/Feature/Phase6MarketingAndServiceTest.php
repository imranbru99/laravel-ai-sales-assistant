<?php

use App\Core\Tenancy\TenantContext;
use App\Models\Campaign;
use App\Models\CmsPage;
use App\Models\Contact;
use App\Models\Coupon;
use App\Models\Lead;
use App\Models\Survey;
use App\Models\SurveyResponse;
use App\Models\Tenant;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\ModuleRegistrySeeder']);
    $this->artisan('db:seed', ['--class' => 'Database\\Seeders\\RolesAndPermissionsSeeder']);
});

test('marketing campaigns record delivery metrics', function () {
    $tenant = Tenant::create(['name' => 'Marketing Hub', 'slug' => 'marketing']);
    app(TenantContext::class)->setTenant($tenant);

    $campaign = Campaign::create([
        'name' => 'Q4 Festive B2B Offer',
        'type' => 'email',
        'subject' => 'Special 20% discount on sales automation licenses',
        'status' => 'completed',
        'sent_count' => 1500,
        'open_count' => 640,
        'click_count' => 210,
    ]);

    expect($campaign->id)->not->toBeNull()
        ->and($campaign->sent_count)->toBe(1500)
        ->and($campaign->open_count)->toBe(640);
});

test('cms pages generate universal slugs with multilingual support', function () {
    $tenant = Tenant::create(['name' => 'CMS Tenant', 'slug' => 'cms']);
    app(TenantContext::class)->setTenant($tenant);

    $page = CmsPage::create([
        'title' => 'আমাদের সম্পর্কে ও কোম্পানির লক্ষ্য',
        'content' => 'আমরা আধুনিক বিক্রয় অটোমেশন সফটওয়্যার সরবরাহ করি।',
        'is_published' => true,
    ]);

    expect($page->slug)->not->toBeEmpty()
        ->and($page->slug)->toContain('আমাদের-সম্পর্কে-ও-কোম্পানির-লক্ষ্য');
});

test('helpdesk tickets track priority status and replies', function () {
    $tenant = Tenant::create(['name' => 'Support Desk', 'slug' => 'support']);
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    app(TenantContext::class)->setTenant($tenant);

    $contact = Contact::create([
        'first_name' => 'Karim',
        'email' => 'karim@client.test',
    ]);

    $ticket = Ticket::create([
        'contact_id' => $contact->id,
        'assigned_to' => $user->id,
        'ticket_number' => 'TCK-2026-001',
        'subject' => 'Need help configuring WhatsApp Cloud API',
        'description' => 'We received the webhook token but need assistance with the callback URL.',
        'priority' => 'high',
        'status' => 'new',
    ]);

    TicketReply::create([
        'ticket_id' => $ticket->id,
        'user_id' => $user->id,
        'content' => 'Hello Karim, please paste the verify token inside Settings -> Integrations.',
    ]);

    expect($ticket->replies()->count())->toBe(1)
        ->and($ticket->priority)->toBe('high');
});

test('partner portal allows partners to submit referral prospects', function () {
    $tenant = Tenant::create(['name' => 'Partner Network', 'slug' => 'partner-net']);
    $partnerUser = User::factory()->create(['tenant_id' => $tenant->id]);
    $partnerUser->assignRole('Partner');
    $this->actingAs($partnerUser);
    app(TenantContext::class)->setTenant($tenant);

    $referralLead = Lead::create([
        'contact_name' => 'Sultana Razia',
        'company_name' => 'Dhaka Textile Group',
        'email' => 'razia@dhakatextile.test',
        'source' => 'referral',
        'budget' => 45000,
        'status' => 'new',
    ]);

    expect($referralLead->source)->toBe('referral')
        ->and($referralLead->company_name)->toBe('Dhaka Textile Group');
});
