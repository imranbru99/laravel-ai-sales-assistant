<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'AI Sales Assistant — Drafts Follow-ups, Scores Leads' }}</title>
    <meta name="description" content="Production-grade, modular, multi-tenant sales and business automation platform powered by AI.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 font-sans antialiased selection:bg-amber-500 selection:text-slate-950 min-h-screen flex flex-col">

    <!-- Top Announcement Bar -->
    <div class="bg-gradient-to-r from-amber-600 via-indigo-600 to-purple-600 px-4 py-2 text-center text-xs sm:text-sm font-medium text-white shadow-inner">
        <span>⚡ Built with Laravel 12 &amp; Filament v3 &middot; Fully Modular Architecture with 76 Enterprise Modules</span>
        <a href="/demo" class="ml-3 underline font-bold hover:text-amber-200">Try Live Demo &rarr;</a>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-50 backdrop-blur-xl bg-slate-950/80 border-b border-slate-800/80" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="/" class="flex items-center gap-3 group">
                <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-amber-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-amber-500/20 group-hover:scale-105 transition-transform">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <span class="font-bold text-lg tracking-tight bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent">AI Sales Assistant</span>
                    <span class="hidden sm:inline-block text-[10px] uppercase tracking-wider bg-slate-800 text-amber-400 px-2 py-0.5 rounded-full ml-1 border border-amber-500/20 font-semibold">v1.0</span>
                </div>
            </a>

            <!-- Desktop Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
                <a href="/features" class="hover:text-amber-400 transition-colors">Features (76)</a>
                <a href="/solutions" class="hover:text-amber-400 transition-colors">Solutions</a>
                <a href="/pricing" class="hover:text-amber-400 transition-colors">Pricing</a>
                <a href="/demo" class="hover:text-amber-400 transition-colors text-amber-400 font-semibold flex items-center gap-1.5">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                    </span>
                    Live Demo
                </a>
            </nav>

            <!-- Action Buttons -->
            <div class="hidden md:flex items-center gap-3">
                <a href="/app/login" class="px-4 py-2 text-sm font-medium text-slate-300 hover:text-white transition-colors">
                    Sign In
                </a>
                <a href="/demo" class="px-4 py-2 text-sm font-semibold rounded-lg bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 shadow-md shadow-amber-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                    Launch Demo
                </a>
            </div>

            <!-- Mobile Hamburger -->
            <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-slate-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path x-show="!mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                    <path x-show="mobileOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div x-show="mobileOpen" x-collapse class="md:hidden border-b border-slate-800 bg-slate-950 px-4 pt-2 pb-6 space-y-3">
            <a href="/features" class="block py-2 text-sm font-medium text-slate-200">Features (76)</a>
            <a href="/solutions" class="block py-2 text-sm font-medium text-slate-200">Solutions</a>
            <a href="/pricing" class="block py-2 text-sm font-medium text-slate-200">Pricing</a>
            <a href="/demo" class="block py-2 text-sm font-semibold text-amber-400">Live Demo</a>
            <div class="pt-2 border-t border-slate-800 flex flex-col gap-2">
                <a href="/app/login" class="text-center py-2 text-sm text-slate-300">Sign In</a>
                <a href="/demo" class="text-center py-2.5 rounded-lg bg-amber-500 text-slate-950 font-semibold text-sm">Launch Demo</a>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950 text-slate-400 text-sm py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-1">
                <div class="flex items-center gap-2 mb-3">
                    <div class="h-7 w-7 rounded-lg bg-gradient-to-br from-amber-500 to-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <span class="font-bold text-white tracking-tight">AI Sales Assistant</span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Drafts follow-ups, scores leads, and runs your whole business. Modular, multi-tenant Laravel 12 &amp; Filament v3 architecture.
                </p>
                <div class="mt-4 text-xs text-slate-500">
                    Developed by <a href="https://imrandev.bd" target="_blank" rel="noopener" class="text-amber-400 font-medium hover:underline">Imran Ahmed</a> &middot; <a href="mailto:me@imrandev.bd" class="hover:underline">me@imrandev.bd</a>
                </div>
            </div>

            <div>
                <h4 class="font-semibold text-white mb-3 text-xs uppercase tracking-wider">Product &amp; AI</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="/features" class="hover:text-amber-400 transition-colors">76 Modular Catalog</a></li>
                    <li><a href="/#lead-scoring" class="hover:text-amber-400 transition-colors">AI Lead Scoring Engine</a></li>
                    <li><a href="/#follow-up" class="hover:text-amber-400 transition-colors">Multichannel Follow-up Drafter</a></li>
                    <li><a href="/#copilot" class="hover:text-amber-400 transition-colors">AI Copilot Chat</a></li>
                    <li><a href="/demo" class="hover:text-amber-400 transition-colors">One-Click Demo Personas</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-white mb-3 text-xs uppercase tracking-wider">Business Presets</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="/solutions/agency" class="hover:text-amber-400 transition-colors">B2B Agency &amp; Services</a></li>
                    <li><a href="/solutions/garments" class="hover:text-amber-400 transition-colors">Garment &amp; Merchandising</a></li>
                    <li><a href="/solutions/real_estate" class="hover:text-amber-400 transition-colors">Real Estate &amp; Properties</a></li>
                    <li><a href="/solutions/wholesale" class="hover:text-amber-400 transition-colors">Wholesale &amp; Distribution</a></li>
                    <li><a href="/solutions" class="hover:text-amber-400 transition-colors">View All 11 Presets &rarr;</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-white mb-3 text-xs uppercase tracking-wider">Ecosystem &amp; Packages</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="https://packagist.org/packages/imrandevbd/laravel-ai-hub" target="_blank" class="hover:text-amber-400">laravel-ai-hub (AI Gateway)</a></li>
                    <li><a href="https://packagist.org/packages/imrandevbd/drutogoti" target="_blank" class="hover:text-amber-400">drutogoti (Performance Engine)</a></li>
                    <li><a href="https://packagist.org/packages/imrandevbd/laravel-unicode-pdf" target="_blank" class="hover:text-amber-400">laravel-unicode-pdf (Bangla/RTL PDF)</a></li>
                    <li><a href="https://packagist.org/packages/imrandevbd/laravel-universal-slug" target="_blank" class="hover:text-amber-400">laravel-universal-slug (Multilingual Slugs)</a></li>
                    <li><a href="https://packagist.org/packages/imrandevbd/laravel-attendance-hub" target="_blank" class="hover:text-amber-400">laravel-attendance-hub (ZKTeco Sync)</a></li>
                </ul>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 pt-6 border-t border-slate-900 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500">
            <div>
                &copy; {{ date('Y') }} AI Sales Assistant. All rights reserved. Architected with Imran Dev BD standards.
            </div>
            <div class="flex items-center gap-4 mt-3 sm:mt-0">
                <a href="https://imrandev.bd" target="_blank" class="hover:text-slate-300">Developer Portfolio</a>
                <a href="/api/v1/health" class="hover:text-slate-300">System API Status</a>
                <a href="/super-admin" class="hover:text-amber-400">Super Admin</a>
            </div>
        </div>
    </footer>
</body>
</html>
