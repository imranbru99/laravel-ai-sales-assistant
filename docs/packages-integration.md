# First-Party Package Integrations (`imrandevbd`)

AI Sales Assistant integrates seven specialized packages developed by [Imran Ahmed](https://imrandev.bd) (`imrandevbd`). This document details how each package is integrated and utilized across the application.

---

## 1. `imrandevbd/drutogoti` (⚡ Global Performance Layer)

- **Purpose**: Real-time performance monitoring, automated N+1 query detection, slow query diagnostics, cache hit/miss telemetry, cookie payload bloat alerts, and image asset scanner.
- **Service Provider**: `Drutogoti\DrutogotiServiceProvider`
- **Configuration**: `config/drutogoti.php`
- **Application Integration**:
  - Automatically captures query latency, memory consumption, and cache metrics across all tenant panels and public marketing pages.
  - Suggestion engine detects duplicate database queries and alerts developers and administrators via suggestions tables.
  - Offloaded asynchronously to ensure zero response-time overhead on production requests.

---

## 2. `imrandevbd/laravel-admin-panel` (📊 Dashboard & Admin Framework)

- **Purpose**: Visual widgets, metrics cards, operational KPIs, layout enhancements, and administrative UI utilities.
- **Service Provider**: `ImranDevBD\LaravelAdminPanel\AdminPanelServiceProvider`
- **Facade**: `ImranDevBD\LaravelAdminPanel\Facades\AdminPanel`
- **Configuration**: `config/admin-panel.php`
- **Application Integration**:
  - Powers dashboard overview cards (MRR, active leads, pipeline velocity, attendance summaries).
  - Supplies quick-action components, role-specific metrics, and unified administrative styling alongside Filament panels.

---

## 3. `imrandevbd/laravel-ai-catalog` (🌐 Lead Enrichment, Web Scraping & Catalog Import)

- **Purpose**: Scraping public company websites, competitor product data, catalog extraction, and intelligent lead enrichment.
- **Service Provider**: `ImranDevBd\AiCatalog\AiCatalogServiceProvider`
- **Facade**: `ImranDevBd\AiCatalog\Facades\AiCatalog`
- **Configuration**: `config/ai-catalog.php`
- **Application Integration**:
  - Used in **Lead Enrichment**: automatically extracts company description, industry classification, employee range, social links, and technology stack from prospect URLs.
  - Generates catalog descriptions, brand voice alignments, and product intelligence.
  - Respects robots.txt, rate limits, and stores attribution metadata.

---

## 4. `imrandevbd/laravel-ai-hub` (🧠 Universal AI Engine)

- **Purpose**: All generative AI capabilities: Lead Scoring, Follow-up Drafting, Chat Copilot, Meeting Summarization, Sentiment Analysis, and Sales Coaching.
- **Service Provider**: `ImranDevBd\AiHub\AiHubServiceProvider`
- **Facade**: `ImranDevBd\AiHub\Facades\AIHub`
- **Configuration**: `config/ai-hub.php`
- **Application Integration**:
  - Abstracted through `App\Core\Ai\AiGateway` to ensure tenant-level model configuration, token tracking, cost budgeting, and fallback mechanisms.
  - Supports OpenAI, Google Gemini, Anthropic Claude, Groq, and an offline **Demo AI Driver** providing realistic instant outputs without API keys.

---

## 5. `imrandevbd/laravel-attendance-hub` (⏱️ Attendance & Biometric Device Integration)

- **Purpose**: Hardware and biometric terminal integration (ZKTeco, TCP/UDP, push/pull protocols), device sync, attendance logs, and staff verification.
- **Service Provider**: `ImranDevBd\AttendanceHub\AttendanceHubServiceProvider`
- **Facade**: `ImranDevBd\AttendanceHub\Facades\AttendanceHub`
- **Configuration**: `config/attendance-hub.php`
- **Application Integration**:
  - Powers the **Attendance Module**: manages physical biometric devices across multiple branches.
  - Handles automated attendance sync jobs, check-in/check-out timestamp logging, overtime calculations, and shift tracking for sales reps and office staff.

---

## 6. `imrandevbd/laravel-unicode-pdf` (📄 Multilingual Unicode PDF Engine)

- **Purpose**: Flawless PDF document generation with complete complex-script and RTL support (Bengali, Arabic, Hindi, CJK, English).
- **Service Provider**: `ImranDev\UnicodePdf\UnicodePdfServiceProvider`
- **Facade**: `ImranDev\UnicodePdf\Facades\UnicodePdf`
- **Configuration**: `config/unicode-pdf.php`
- **Application Integration**:
  - Generates Quotations, Invoices, Proposals, Employee Payslips, and Executive Reports.
  - Uses native typography presets (e.g. `bengali`, `arabic`, `english`) with SolaimanLipi, Kalpurush, Amiri, and Noto Sans fallbacks.

---

## 7. `imrandevbd/laravel-universal-slug` (🔗 Universal Multilingual Slugs)

- **Purpose**: Clean, Unicode-safe, SEO-friendly slugs in any language and writing system without `%20` or corruption.
- **Service Provider**: `ImranDev\UniversalSlug\UniversalSlugServiceProvider`
- **Facade**: `ImranDev\UniversalSlug\Facades\UniversalSlug`
- **Configuration**: `config/universal-slug.php`
- **Application Integration**:
  - Used across all models requiring URL identifiers: Public Marketing Pages, Blog Posts, Product Catalog, Public Booking links, and Lead Capture Forms.
  - Supports automatic 301 redirects on slug updates via `slug_histories` table and atomic uniqueness locking.
