<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DemoLoginController extends Controller
{
    public function index(): View
    {
        $personas = [
            [
                'key' => 'super_admin',
                'role' => 'Super Administrator',
                'name' => 'Imran Ahmed',
                'email' => 'me@imrandev.bd',
                'badge' => 'Platform Operator',
                'color' => 'amber',
                'destination' => 'Super Admin Panel (/super-admin)',
                'description' => 'Full multi-tenant control: tenant provisioning, SaaS billing, module switches, and platform telemetry.',
            ],
            [
                'key' => 'tenant_admin',
                'role' => 'Tenant Executive (MD)',
                'name' => 'Tariq Al-Mansoor',
                'email' => 'admin@novatrading.com',
                'badge' => 'Business Owner',
                'color' => 'indigo',
                'destination' => 'Tenant App (/app)',
                'description' => 'Executive business management: module marketplace, company settings, reports, approvals, and AI configuration.',
            ],
            [
                'key' => 'sales_rep',
                'role' => 'Senior Account Executive',
                'name' => 'Ayesha Rahman',
                'email' => 'sales@novatrading.com',
                'badge' => 'Sales Leader',
                'color' => 'emerald',
                'destination' => 'Tenant App (/app)',
                'description' => 'Daily AI sales operations: hot lead scoring, auto follow-up drafts, pipeline deals, unified inbox, and client quotes.',
            ],
            [
                'key' => 'finance_manager',
                'role' => 'Head of Finance',
                'name' => 'Kamal Hossain',
                'email' => 'finance@novatrading.com',
                'badge' => 'Finance & Billing',
                'color' => 'blue',
                'destination' => 'Tenant App (/app)',
                'description' => 'Invoicing, multi-currency collections (bKash/Stripe), Unicode PDF receipts, and employee expense claims.',
            ],
            [
                'key' => 'support_lead',
                'role' => 'Support Specialist',
                'name' => 'Nusrat Jahan',
                'email' => 'support@novatrading.com',
                'badge' => 'Customer Care',
                'color' => 'teal',
                'destination' => 'Tenant App (/app)',
                'description' => 'Helpdesk tickets, SLA escalation timers, live knowledge base articles, and automated customer replies.',
            ],
            [
                'key' => 'hr_officer',
                'role' => 'HR & Attendance Lead',
                'name' => 'Rafiqul Islam',
                'email' => 'hr@novatrading.com',
                'badge' => 'People & Field',
                'color' => 'violet',
                'destination' => 'Tenant App (/app)',
                'description' => 'Biometric attendance logs via laravel-attendance-hub, shift rosters, leave requests, and payroll slips.',
            ],
            [
                'key' => 'client',
                'role' => 'B2B Client (Customer Portal)',
                'name' => 'David Sterling',
                'email' => 'david@sterlingglobal.com',
                'badge' => 'Client Portal',
                'color' => 'cyan',
                'destination' => 'Client Portal (/portal)',
                'description' => 'Client self-service: view active quotations, approve estimates, download invoices, pay online, and open tickets.',
            ],
            [
                'key' => 'partner',
                'role' => 'Affiliate Channel Partner',
                'name' => 'Sophia Chen',
                'email' => 'sophia@chenpartners.com',
                'badge' => 'Partner Portal',
                'color' => 'rose',
                'destination' => 'Partner Portal (/partner)',
                'description' => 'Referral deal registration, commission statements, marketing co-branded assets, and payout tracking.',
            ],
        ];

        return view('public.demo', compact('personas'));
    }

    public function login(string $role): RedirectResponse
    {
        $emailMap = [
            'super_admin' => ['email' => 'me@imrandev.bd', 'url' => '/super-admin', 'is_super' => true],
            'tenant_admin' => ['email' => 'admin@novatrading.com', 'url' => '/app'],
            'sales_rep' => ['email' => 'sales@novatrading.com', 'url' => '/app'],
            'finance_manager' => ['email' => 'finance@novatrading.com', 'url' => '/app'],
            'support_lead' => ['email' => 'support@novatrading.com', 'url' => '/app'],
            'hr_officer' => ['email' => 'hr@novatrading.com', 'url' => '/app'],
            'client' => ['email' => 'david@sterlingglobal.com', 'url' => '/portal'],
            'partner' => ['email' => 'sophia@chenpartners.com', 'url' => '/partner'],
        ];

        if (!isset($emailMap[$role])) {
            return redirect('/demo')->with('error', 'Unknown demo persona.');
        }

        $target = $emailMap[$role];
        $user = User::where('email', $target['email'])->first();

        if (!$user) {
            // Find any user matching the profile or create one on the fly
            if (!empty($target['is_super'])) {
                $user = User::firstOrCreate(
                    ['email' => $target['email']],
                    [
                        'name' => 'Imran Ahmed',
                        'password' => bcrypt('password'),
                        'is_super_admin' => true,
                        'is_active' => true,
                    ]
                );
            } else {
                $tenant = Tenant::first() ?? Tenant::create([
                    'name' => 'Nova Trading Co.',
                    'slug' => 'nova-trading',
                    'plan' => 'growth',
                    'status' => 'active',
                ]);

                $user = User::firstOrCreate(
                    ['email' => $target['email']],
                    [
                        'tenant_id' => $tenant->id,
                        'name' => ucwords(str_replace('_', ' ', $role)),
                        'password' => bcrypt('password'),
                        'is_super_admin' => false,
                        'is_active' => true,
                    ]
                );
            }
        }

        Auth::login($user);
        session()->regenerate();

        return redirect($target['url']);
    }
}
