# Architectural Decisions & Log

## ADR 001: Tenancy and Multi-Branch Architecture
- **Decision**: Single database with `tenant_id` and `branch_id` global scopes on all business entities.
- **Traits**: `BelongsToTenant` and `BelongsToBranch`.
- **Reasoning**: Maximizes operational simplicity, cost efficiency, single-database reporting, cross-branch rollups, and effortless cloud migrations while strictly enforcing isolation via global scopes and policies.

## ADR 002: Modular Architecture & Registry
- **Decision**: Modules reside in `app/Modules/<Name>` containing domain models, migrations, Filament resources/pages/widgets, Livewire components, routes, and a `module.json` manifest.
- **Registry**: Database-backed `modules`, `tenant_modules`, and `branch_modules` managed by a high-performance, Redis-cached `ModuleManager` service.
- **Dynamic Navigation & Scopes**: When a module is turned off for a tenant or branch, Filament navigation items, API routes, and background jobs are immediately deactivated for that tenant/branch context.

## ADR 003: AI Architecture & Offline Demo Driver
- **Decision**: All AI operations route through `App\Core\Ai\AiGateway` wrapping `imrandevbd/laravel-ai-hub`.
- **Demo AI Driver**: When API keys are not supplied or `AI_DRIVER=demo` is configured, realistic, deterministic AI generation occurs offline (scoring, explanations, drafting follow-ups in English and Bengali, summaries).

## ADR 004: First-Party Package Integration (`imrandevbd`)
- **Decision**: Deeply integrate all seven packages (`drutogoti`, `laravel-admin-panel`, `laravel-ai-catalog`, `laravel-ai-hub`, `laravel-attendance-hub`, `laravel-unicode-pdf`, `laravel-universal-slug`).
- **Reasoning**: Leverages proven, specialized solutions for performance profiling, admin dashboards, scraping/enrichment, AI orchestration, biometric attendance, Unicode PDF rendering, and multilingual slugs.
