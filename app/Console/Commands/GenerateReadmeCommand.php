<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateReadmeCommand extends Command
{
    protected $signature = 'docs:generate-readme';
    protected $description = 'Generate full production README.md directly from config/features.php';

    public function handle(): int
    {
        $this->info('Generating comprehensive README.md from feature registry...');

        $categories = config('features.categories', []);
        $modules = config('features.modules', []);
        $presets = config('features.presets', []);

        $out = [];
        $out[] = '# AI Sales Assistant';
        $out[] = '';
        $out[] = '> **"Drafts follow-ups, scores leads, and runs your whole business."**';
        $out[] = '';
        $out[] = '[![PHP](https://img.shields.io/badge/PHP-8.3%2B-777BB4?style=flat&logo=php)](https://php.net)';
        $out[] = '[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)';
        $out[] = '[![Filament](https://img.shields.io/badge/Filament-3.x-F59E0B?style=flat)](https://filamentphp.com)';
        $out[] = '[![TailwindCSS](https://img.shields.io/badge/Tailwind-4.x-06B6D4?style=flat&logo=tailwindcss)](https://tailwindcss.com)';
        $out[] = '[![License](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)';
        $out[] = '';
        $out[] = 'A production-grade, multi-tenant, multi-branch, **fully modular** sales and business operating platform. Any business (small retail shop, wholesale distributor, B2B agency, garment merchandiser, real-estate firm, clinic, school, or SaaS company) can switch modules **ON** or **OFF** with zero code changes and run as their exact custom business solution.';
        $out[] = '';
        $out[] = '---';
        $out[] = '';
        $out[] = '## 🌟 Core AI Signatures';
        $out[] = '';
        $out[] = '1. **AI Lead Scoring**: Every lead is automatically evaluated (0–100 score, A/B/C/D grade) with plain-language explanations ("why this score") and recommended next actions combining demographic, engagement, recency, and AI analysis.';
        $out[] = '2. **Multichannel AI Follow-up Drafter**: Synthesizes full conversation history, deal stages, and brand voice into personalized follow-up drafts for Email, WhatsApp, SMS, LinkedIn, and Phone scripts in multiple languages (English, Bangla, Arabic).';
        $out[] = '3. **In-App AI Copilot**: Answers natural language questions about pipelines, hot leads, revenue forecasts, and stalled deals.';
        $out[] = '';
        $out[] = '---';
        $out[] = '';
        $out[] = '## 📦 First-Party Packages (Imran Dev BD)';
        $out[] = '';
        $out[] = 'The platform deeply integrates all 7 first-party packages by **Imran Dev BD** (`imrandevbd`):';
        $out[] = '';
        $out[] = '| Package | Role & Integration | Packagist |';
        $out[] = '|---|---|---|';
        $out[] = '| **`imrandevbd/drutogoti`** | High-performance optimization: query caching, response caching, asset minification | [Packagist](https://packagist.org/packages/imrandevbd/drutogoti) |';
        $out[] = '| **`imrandevbd/laravel-admin-panel`** | Base layout, analytics dashboard cards, and admin telemetry | [Packagist](https://packagist.org/packages/imrandevbd/laravel-admin-panel) |';
        $out[] = '| **`imrandevbd/laravel-ai-catalog`** | Lead enrichment, company website scraping, automated product catalog import | [Packagist](https://packagist.org/packages/imrandevbd/laravel-ai-catalog) |';
        $out[] = '| **`imrandevbd/laravel-ai-hub`** | Unified AI gateway across OpenAI, Claude, Gemini: scoring, follow-ups, chat | [Packagist](https://packagist.org/packages/imrandevbd/laravel-ai-hub) |';
        $out[] = '| **`imrandevbd/laravel-attendance-hub`** | Biometric ZKTeco/IP hardware sync, device logs, shift punch management | [Packagist](https://packagist.org/packages/imrandevbd/laravel-attendance-hub) |';
        $out[] = '| **`imrandevbd/laravel-unicode-pdf`** | Native multilingual PDF generation (Bangla, Arabic, Hindi, CJK) for invoices & quotes | [Packagist](https://packagist.org/packages/imrandevbd/laravel-unicode-pdf) |';
        $out[] = '| **`imrandevbd/laravel-universal-slug`** | Universal slug generation across all languages and scripts for public pages | [Packagist](https://packagist.org/packages/imrandevbd/laravel-universal-slug) |';
        $out[] = '';
        $out[] = '---';
        $out[] = '';
        $out[] = '## 🏢 11 Pre-Packaged Business Presets';
        $out[] = '';
        $out[] = 'Apply any preset with 1-click in the tenant settings or during onboarding:';
        $out[] = '';
        $out[] = '| Preset Key | Preset Name | Included Modules | Description |';
        $out[] = '|---|---|---|---|';
        foreach ($presets as $k => $preset) {
            $modCount = count($preset['modules']);
            $out[] = "| `{$k}` | **{$preset['name']}** | {$modCount} modules | {$preset['description']} |";
        }
        $out[] = '';
        $out[] = '---';
        $out[] = '';
        $out[] = '## 🧩 Complete 76 Module Registry';
        $out[] = '';
        $out[] = "The platform includes **" . count($modules) . " enterprise modules** structured into **" . count($categories) . " functional domains**:\n";

        foreach ($categories as $catKey => $cat) {
            $catModules = array_filter($modules, fn ($m) => ($m['category'] ?? '') === $catKey);
            $out[] = "### " . $cat['name'] . " (" . count($catModules) . " modules)";
            $out[] = "*" . $cat['description'] . "*\n";

            foreach ($catModules as $modKey => $mod) {
                $status = !empty($mod['is_core']) ? 'Core' : 'Modular';
                $price = !empty($mod['price_monthly']) ? "(\${$mod['price_monthly']}/mo)" : "(Included)";
                $out[] = "#### " . $mod['name'] . " `[{$modKey}]` {$price}";
                $out[] = $mod['description'] . "\n";
                if (!empty($mod['features'])) {
                    $out[] = "**Key Capabilities:**";
                    foreach ($mod['features'] as $feat) {
                        $out[] = "- " . $feat;
                    }
                    $out[] = "";
                }
            }
            $out[] = "";
        }

        $out[] = '---';
        $out[] = '';
        $out[] = '## 🚀 Quick Start & Installation';
        $out[] = '';
        $out[] = '### Requirements';
        $out[] = '- PHP 8.3 or 8.4+';
        $out[] = '- SQLite 3.35+ or MySQL 8.0+';
        $out[] = '- Composer 2.x';
        $out[] = '- Node.js 20+ & npm';
        $out[] = '';
        $out[] = '### 1. Clone & Install Dependencies';
        $out[] = '```bash';
        $out[] = 'git clone https://github.com/imrandevbd/ai-sales-assistant.git';
        $out[] = 'cd ai-sales-assistant';
        $out[] = 'composer install';
        $out[] = 'npm install';
        $out[] = 'npm run build';
        $out[] = '```';
        $out[] = '';
        $out[] = '### 2. Environment Setup';
        $out[] = '```bash';
        $out[] = 'cp .env.example .env';
        $out[] = 'php artisan key:generate';
        $out[] = 'touch database/database.sqlite # If using SQLite';
        $out[] = '```';
        $out[] = '';
        $out[] = '### 3. Install Enterprise Demo Dataset';
        $out[] = '```bash';
        $out[] = 'php artisan demo:install --fresh';
        $out[] = '```';
        $out[] = '';
        $out[] = '### 4. Run Development Server';
        $out[] = '```bash';
        $out[] = 'php artisan serve';
        $out[] = '```';
        $out[] = '';
        $out[] = '---';
        $out[] = '';
        $out[] = '## 🎭 8 One-Click Demo Personas';
        $out[] = '';
        $out[] = 'Navigate to `http://localhost:8000/demo` or use these credentials (all passwords: `password`):';
        $out[] = '';
        $out[] = '| Persona | Email | Destination Panel | Purpose |';
        $out[] = '|---|---|---|---|';
        $out[] = '| **Super Administrator** | `me@imrandev.bd` | `/super-admin` | Platform multi-tenancy, SaaS billing, telemetry |';
        $out[] = '| **Tenant Managing Director** | `admin@novatrading.com` | `/app` | Executive dashboard, module toggles, settings |';
        $out[] = '| **Senior Account Executive** | `sales@novatrading.com` | `/app` | AI lead scores, follow-up drafter, deals, quotes |';
        $out[] = '| **Head of Finance** | `finance@novatrading.com` | `/app` | Invoicing, bKash/Stripe payments, expenses |';
        $out[] = '| **Customer Support Lead** | `support@novatrading.com` | `/app` | Helpdesk tickets, knowledge base, SLAs |';
        $out[] = '| **HR & Attendance Lead** | `hr@novatrading.com` | `/app` | Attendance machine sync, leave, payroll |';
        $out[] = '| **B2B Client** | `david@sterlingglobal.com` | `/portal` | Client self-service quotes, invoices, tickets |';
        $out[] = '| **Channel Partner** | `sophia@chenpartners.com` | `/partner` | Affiliate referrals, commissions, payouts |';
        $out[] = '';
        $out[] = '---';
        $out[] = '';
        $out[] = '## 🧪 Automated Testing';
        $out[] = '';
        $out[] = 'The application includes an automated Pest test suite covering multi-tenancy, lead scoring, follow-up drafting, REST API, portals, and SaaS admin:';
        $out[] = '';
        $out[] = '```bash';
        $out[] = './vendor/bin/pest';
        $out[] = '```';
        $out[] = '';
        $out[] = '---';
        $out[] = '';
        $out[] = '## 👨‍💻 Developer & Git Author Identity';
        $out[] = '';
        $out[] = '- **Developer Name:** Imran Ahmed';
        $out[] = '- **Git Author & Committer:** Imran Ahmed';
        $out[] = '- **Email:** me@imrandev.bd';
        $out[] = '- **Official Website:** [https://imrandev.bd](https://imrandev.bd)';
        $out[] = '';
        $out[] = '---';
        $out[] = '';
        $out[] = '## 📄 License';
        $out[] = '';
        $out[] = 'This project is licensed under the MIT License.';

        $content = implode("\n", $out);
        File::put(base_path('README.md'), $content);

        $this->info("Generated README.md successfully (" . strlen($content) . " bytes).");
        return Command::SUCCESS;
    }
}
