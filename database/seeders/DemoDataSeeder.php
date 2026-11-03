<?php

namespace Database\Seeders;

use App\Core\Modules\ModuleManager;
use App\Core\Tenancy\TenantContext;
use App\Models\ApprovalRequest;
use App\Models\Branch;
use App\Models\Company;
use App\Models\Contact;
use App\Models\CustomReport;
use App\Models\Deal;
use App\Models\Employee;
use App\Models\FollowUp;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\LeadScore;
use App\Models\Payment;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Product;
use App\Models\Quotation;
use App\Models\SaasPlan;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use ImranDevBd\AttendanceHub\Models\AttendanceDevice;
use ImranDevBd\AttendanceHub\Models\AttendanceLog;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(ModuleRegistrySeeder::class);

        // 1. SaaS Plans
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'For small businesses, shops, and boutique sales teams.',
                'price_monthly' => 49.00,
                'price_yearly' => 470.00,
                'max_users' => 5,
                'max_branches' => 1,
                'max_storage_mb' => 2048,
                'max_ai_tokens' => 250000,
                'is_active' => true,
                'is_featured' => false,
            ],
            [
                'name' => 'Growth',
                'slug' => 'growth',
                'description' => 'For scaling B2B agencies, distributors, real-estate firms, and clinics.',
                'price_monthly' => 149.00,
                'price_yearly' => 1430.00,
                'max_users' => 20,
                'max_branches' => 5,
                'max_storage_mb' => 10240,
                'max_ai_tokens' => 1000000,
                'is_active' => true,
                'is_featured' => true,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For garment exporters, institutions, enterprises, and high-volume operations.',
                'price_monthly' => 399.00,
                'price_yearly' => 3830.00,
                'max_users' => 100,
                'max_branches' => 25,
                'max_storage_mb' => 51200,
                'max_ai_tokens' => 5000000,
                'is_active' => true,
                'is_featured' => false,
            ],
        ];

        foreach ($plans as $p) {
            SaasPlan::updateOrCreate(['slug' => $p['slug']], $p);
        }

        // 2. Platform Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'me@imrandev.bd'],
            [
                'name' => 'Imran Ahmed',
                'password' => Hash::make('password'),
                'is_super_admin' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // 3. Tenant 1: Nova Trading Co. (Wholesale & Distribution)
        $nova = Tenant::updateOrCreate(
            ['slug' => 'nova-trading'],
            [
                'name' => 'Nova Trading Co.',
                'plan' => 'growth',
                'business_type' => 'wholesale',
                'status' => 'active',
                'currency' => 'USD',
                'timezone' => 'Asia/Dhaka',
                'locale' => 'en',
            ]
        );
        app(ModuleManager::class)->applyPreset($nova, 'wholesale');

        $novaHq = Branch::updateOrCreate(
            ['tenant_id' => $nova->id, 'code' => 'HQ'],
            [
                'name' => 'Headquarters (Dhaka)',
                'is_headquarters' => true,
                'is_active' => true,
                'city' => 'Dhaka',
                'country' => 'Bangladesh',
            ]
        );

        $novaPort = Branch::updateOrCreate(
            ['tenant_id' => $nova->id, 'code' => 'PORT'],
            [
                'name' => 'Chittagong Port Terminal',
                'is_headquarters' => false,
                'is_active' => true,
                'city' => 'Chittagong',
                'country' => 'Bangladesh',
            ]
        );

        // Nova Users
        $novaAdmin = User::updateOrCreate(
            ['email' => 'admin@novatrading.com'],
            [
                'tenant_id' => $nova->id,
                'branch_id' => $novaHq->id,
                'name' => 'Tariq Al-Mansoor',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $novaAdmin->assignRole('Business Owner');

        $novaSales = User::updateOrCreate(
            ['email' => 'sales@novatrading.com'],
            [
                'tenant_id' => $nova->id,
                'branch_id' => $novaHq->id,
                'name' => 'Ayesha Rahman',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $novaSales->assignRole('Sales Rep');

        $novaFinance = User::updateOrCreate(
            ['email' => 'finance@novatrading.com'],
            [
                'tenant_id' => $nova->id,
                'branch_id' => $novaHq->id,
                'name' => 'Kamal Hossain',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $novaFinance->assignRole('Finance / Accountant');

        $novaSupport = User::updateOrCreate(
            ['email' => 'support@novatrading.com'],
            [
                'tenant_id' => $nova->id,
                'branch_id' => $novaHq->id,
                'name' => 'Nusrat Jahan',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $novaSupport->assignRole('Customer Support Agent');

        $novaHr = User::updateOrCreate(
            ['email' => 'hr@novatrading.com'],
            [
                'tenant_id' => $nova->id,
                'branch_id' => $novaHq->id,
                'name' => 'Rafiqul Islam',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $novaHr->assignRole('HR Manager');

        // External portal users
        $clientUser = User::updateOrCreate(
            ['email' => 'david@sterlingglobal.com'],
            [
                'tenant_id' => $nova->id,
                'branch_id' => $novaHq->id,
                'name' => 'David Sterling',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $clientUser->assignRole('Customer');

        $partnerUser = User::updateOrCreate(
            ['email' => 'sophia@chenpartners.com'],
            [
                'tenant_id' => $nova->id,
                'branch_id' => $novaHq->id,
                'name' => 'Sophia Chen',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $partnerUser->assignRole('Partner');

        // Context for Nova
        app(TenantContext::class)->setTenant($nova);
        app(TenantContext::class)->setBranch($novaHq);

        // Seed Pipeline and Stages for Nova
        $pipeline = Pipeline::updateOrCreate(
            ['tenant_id' => $nova->id, 'name' => 'Wholesale Distribution Pipeline'],
            ['is_default' => true]
        );

        $stagesData = [
            ['name' => 'New Inquiries', 'sort_order' => 1, 'win_probability' => 10, 'color' => '#64748b'],
            ['name' => 'Qualification & AI Scored', 'sort_order' => 2, 'win_probability' => 25, 'color' => '#0284c7'],
            ['name' => 'Quotation Sent', 'sort_order' => 3, 'win_probability' => 50, 'color' => '#d97706'],
            ['name' => 'Contract Negotiation', 'sort_order' => 4, 'win_probability' => 75, 'color' => '#9333ea'],
            ['name' => 'Closed Won', 'sort_order' => 5, 'win_probability' => 100, 'color' => '#16a34a'],
            ['name' => 'Closed Lost', 'sort_order' => 6, 'win_probability' => 0, 'color' => '#dc2626'],
        ];

        $stages = [];
        foreach ($stagesData as $s) {
            $stages[] = PipelineStage::updateOrCreate(
                ['pipeline_id' => $pipeline->id, 'name' => $s['name']],
                $s
            );
        }

        // Seed Products for Nova
        $prod1 = Product::updateOrCreate(
            ['tenant_id' => $nova->id, 'sku' => 'NOVA-IND-500'],
            [
                'name' => 'Industrial Solar Inverter 500kW',
                'description' => 'Commercial grade 3-phase hybrid inverter with SCADA telemetry.',
                'price' => 12500.00,
                'cost_price' => 8800.00,
                'currency' => 'USD',
                'is_active' => true,
            ]
        );

        $prod2 = Product::updateOrCreate(
            ['tenant_id' => $nova->id, 'sku' => 'NOVA-BAT-100'],
            [
                'name' => 'Lithium Energy Storage Rack 100kWh',
                'description' => 'Modular LiFePO4 battery cabinet with fire suppression.',
                'price' => 18000.00,
                'cost_price' => 13500.00,
                'currency' => 'USD',
                'is_active' => true,
            ]
        );

        // Seed 100+ Leads for Nova with realistic scores, explanations, and score histories
        $leadSources = ['referral', 'website', 'inbound_form', 'cold_outreach', 'linkedin_ad', 'trade_show'];
        $companiesPool = [
            'Pacific Energy Group', 'Bengal Maritime Logistics', 'Vertex Industrial Corp',
            'Siam Agro Exporters', 'Delta Global Logistics', 'Zenith Manufacturing Ltd',
            'Apex Textile Mills', 'Kalyan Healthcare Systems', 'Horizon Power Solutions',
            'Silverline Distribution', 'Summit Communications', 'Transcom Commercial Hub'
        ];

        for ($i = 1; $i <= 100; $i++) {
            $cName = $companiesPool[$i % count($companiesPool)] . ' #' . $i;
            $score = rand(35, 96);
            $grade = $score >= 85 ? 'A' : ($score >= 70 ? 'B' : ($score >= 50 ? 'C' : 'D'));
            $budget = rand(10, 120) * 1000;
            $source = $leadSources[$i % count($leadSources)];

            $lead = Lead::updateOrCreate(
                ['tenant_id' => $nova->id, 'email' => "procurement{$i}@partner{$i}.com"],
                [
                    'branch_id' => ($i % 2 === 0) ? $novaHq->id : $novaPort->id,
                    'owner_id' => $novaSales->id,
                    'contact_name' => "Commercial Director {$i}",
                    'company_name' => $cName,
                    'phone' => '+88017' . sprintf('%08d', rand(10000000, 99999999)),
                    'status' => ($score >= 75) ? 'qualified' : (($score >= 50) ? 'contacted' : 'new'),
                    'source' => $source,
                    'budget' => $budget,
                    'score' => $score,
                    'score_grade' => $grade,
                    'score_explanation' => [
                        'score' => $score,
                        'grade' => $grade,
                        'signals' => [
                            'budget_score' => (int)($budget / 1500),
                            'source_weight' => ($source === 'referral' ? 25 : 12),
                            'decision_maker' => true,
                        ],
                        'explanation' => "Verified commercial buyer from {$cName}. Strong intent and budget of \${$budget} from {$source}.",
                        'recommended_action' => "Send technical specification and follow-up proposal.",
                    ],
                    'recommended_next_action' => 'Send consultative quote and schedule alignment call.',
                    'last_scored_at' => now()->subHours(rand(1, 48)),
                ]
            );

            // Seed Lead Score history
            LeadScore::create([
                'tenant_id' => $nova->id,
                'lead_id' => $lead->id,
                'score' => $score,
                'score_grade' => $grade,
                'rule_score' => (int)($score * 0.4),
                'engagement_score' => (int)($score * 0.3),
                'ai_score' => (int)($score * 0.3),
                'explanation' => "Scored by AI Lead Scoring Engine. Demographic fit verified.",
                'recommended_action' => "Schedule technical discovery session.",
                'event_trigger' => 'batch_seeder',
                'created_at' => now()->subDays(rand(1, 14)),
            ]);

            // For top 30 leads, create Deals and Follow-up drafts
            if ($i <= 30) {
                $stageIndex = ($score >= 85) ? 3 : (($score >= 70) ? 2 : 1);
                $deal = Deal::updateOrCreate(
                    ['tenant_id' => $nova->id, 'title' => "Supply Contract: {$cName}"],
                    [
                        'branch_id' => $novaHq->id,
                        'pipeline_id' => $pipeline->id,
                        'stage_id' => $stages[$stageIndex]->id,
                        'lead_id' => $lead->id,
                        'owner_id' => $novaSales->id,
                        'value' => $budget,
                        'currency' => 'USD',
                        'score' => $score,
                        'status' => 'open',
                        'expected_close_date' => now()->addDays(rand(10, 60)),
                    ]
                );

                // Create follow up
                FollowUp::create([
                    'tenant_id' => $nova->id,
                    'branch_id' => $novaHq->id,
                    'lead_id' => $lead->id,
                    'deal_id' => $deal->id,
                    'channel' => ($i % 2 === 0) ? 'email' : 'whatsapp',
                    'status' => ($i % 3 === 0) ? 'approved' : 'draft',
                    'subject' => "Quotation Follow-up: Renewable Equipment Package for {$cName}",
                    'content' => "Dear {$lead->contact_name},\n\nFollowing our review of your requirements for {$cName}, we have prepared proposal #QT-2026. Please let us know if you require technical drawings or delivery terms.\n\nWarm regards,\nAyesha Rahman\nNova Trading Co.",
                    'tone' => 'consultative',
                    'language' => 'en',
                    'scheduled_at' => now()->addDays(rand(1, 3)),
                ]);
            }
        }

        // Seed Quotations & Invoices for Nova
        $quote = Quotation::create([
            'tenant_id' => $nova->id,
            'branch_id' => $novaHq->id,
            'quote_number' => 'QT-NOVA-2026-001',
            'title' => 'Renewable Equipment Package',
            'items' => [
                ['name' => 'Industrial Solar Inverter 500kW', 'quantity' => 2, 'unit_price' => 12500, 'total' => 25000],
                ['name' => 'Lithium Energy Storage Rack 100kWh', 'quantity' => 2, 'unit_price' => 18000, 'total' => 36000],
            ],
            'subtotal' => 61000.00,
            'tax_amount' => 3050.00,
            'discount_amount' => 1000.00,
            'total_amount' => 63050.00,
            'currency' => 'USD',
            'status' => 'accepted',
            'valid_until' => now()->addDays(25),
        ]);

        $invoice = Invoice::create([
            'tenant_id' => $nova->id,
            'branch_id' => $novaHq->id,
            'invoice_number' => 'INV-NOVA-2026-001',
            'items' => $quote->items,
            'subtotal' => 61000.00,
            'tax_amount' => 3050.00,
            'discount_amount' => 1000.00,
            'total_amount' => 63050.00,
            'paid_amount' => 63050.00,
            'balance_due' => 0.00,
            'currency' => 'USD',
            'status' => 'paid',
            'due_date' => now()->addDays(27),
            'paid_at' => now()->subDay(),
        ]);

        Payment::create([
            'tenant_id' => $nova->id,
            'invoice_id' => $invoice->id,
            'amount' => 63050.00,
            'currency' => 'USD',
            'method' => 'stripe',
            'transaction_id' => 'ch_3N8' . Str::random(18),
            'status' => 'completed',
        ]);

        // Biometric Attendance Device and Logs via laravel-attendance-hub
        $device = AttendanceDevice::firstOrCreate(
            ['tenant_id' => (string) $nova->id, 'serial_number' => 'ZK-NOVA-DHAKA-01'],
            [
                'name' => 'Dhaka HQ Front Biometric Turnstile',
                'ip' => '192.168.1.200',
                'port' => 4370,
                'provider' => 'zkteco',
                'status' => 'online',
            ]
        );

        $employee = Employee::firstOrCreate(
            ['tenant_id' => $nova->id, 'employee_id' => 'EMP-NOVA-001'],
            [
                'user_id' => $novaSales->id,
                'first_name' => 'Ayesha',
                'last_name' => 'Rahman',
                'designation' => 'Senior Account Executive',
                'department' => 'Sales',
                'salary' => 4500.00,
                'join_date' => now()->subMonths(18),
            ]
        );

        AttendanceLog::create([
            'tenant_id' => (string) $nova->id,
            'device_id' => $device->id,
            'device_user_id' => '1001',
            'employee_id' => 'EMP-NOVA-001',
            'punched_at' => now()->startOfDay()->addHours(8)->addMinutes(45),
            'punch_type' => 'check_in',
            'verify_mode' => 'fingerprint',
            'punch_hash' => md5("nova_punch_seed_{$device->id}"),
        ]);

        // Custom Reports & Approvals
        CustomReport::create([
            'tenant_id' => $nova->id,
            'name' => 'Monthly Pipeline Value by Stage',
            'module_key' => 'deals',
            'fields' => ['id', 'title', 'value', 'stage_id', 'probability'],
            'group_by' => 'stage_id',
            'chart_type' => 'bar',
        ]);

        ApprovalRequest::create([
            'tenant_id' => $nova->id,
            'approvable_type' => Quotation::class,
            'approvable_id' => $quote->id,
            'requested_by' => $novaSales->id,
            'type' => 'discount',
            'tier' => 1,
            'status' => 'approved',
            'approved_by' => $novaAdmin->id,
            'approved_at' => now()->subDays(4),
            'decision_notes' => 'Authorized 1.6% VIP enterprise volume rebate.',
        ]);

        // 4. Tenant 2: BrightEdge Agency (B2B Agency & Services)
        $agency = Tenant::updateOrCreate(
            ['slug' => 'brightedge-agency'],
            [
                'name' => 'BrightEdge Agency',
                'plan' => 'starter',
                'business_type' => 'agency',
                'status' => 'active',
                'currency' => 'USD',
                'timezone' => 'America/New_York',
                'locale' => 'en',
            ]
        );
        app(ModuleManager::class)->applyPreset($agency, 'agency');

        $agencyHq = Branch::updateOrCreate(
            ['tenant_id' => $agency->id, 'code' => 'STUDIO'],
            [
                'name' => 'Manhattan Studio',
                'is_headquarters' => true,
                'is_active' => true,
                'city' => 'New York',
                'country' => 'USA',
            ]
        );

        $agencyAdmin = User::updateOrCreate(
            ['email' => 'director@brightedge.com'],
            [
                'tenant_id' => $agency->id,
                'branch_id' => $agencyHq->id,
                'name' => 'Marcus Vance',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $agencyAdmin->assignRole('Business Owner');

        // 5. Tenant 3: Rangpur Garments Ltd. (Garment & Textile Merchandiser)
        $garment = Tenant::updateOrCreate(
            ['slug' => 'rangpur-garments'],
            [
                'name' => 'Rangpur Garments Ltd.',
                'plan' => 'enterprise',
                'business_type' => 'garments',
                'status' => 'active',
                'currency' => 'BDT',
                'timezone' => 'Asia/Dhaka',
                'locale' => 'bn',
            ]
        );
        app(ModuleManager::class)->applyPreset($garment, 'garments');

        $garmentHq = Branch::updateOrCreate(
            ['tenant_id' => $garment->id, 'code' => 'FAC1'],
            [
                'name' => 'EPZ Manufacturing Complex',
                'is_headquarters' => true,
                'is_active' => true,
                'city' => 'Rangpur',
                'country' => 'Bangladesh',
            ]
        );

        $garmentAdmin = User::updateOrCreate(
            ['email' => 'merchandiser@rangpurgarments.com'],
            [
                'tenant_id' => $garment->id,
                'branch_id' => $garmentHq->id,
                'name' => 'Md. Farhad Uddin',
                'password' => Hash::make('password'),
                'is_super_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $garmentAdmin->assignRole('Business Owner');

        // Reset tenant context
        app(TenantContext::class)->setTenant(null);
        app(TenantContext::class)->setBranch(null);
    }
}
