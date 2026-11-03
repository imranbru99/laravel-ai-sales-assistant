<x-layouts.public :title="$module['name'] . ' — AI Sales Assistant'">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-400 mb-8">
            <a href="/" class="hover:text-white">Home</a>
            <span>&rsaquo;</span>
            <a href="/features" class="hover:text-white">Features</a>
            <span>&rsaquo;</span>
            <span class="text-amber-400">{{ $module['name'] }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <!-- Main Content Column -->
            <div class="lg:col-span-8">
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-xs uppercase tracking-wider font-semibold px-3 py-1 rounded-full bg-slate-900 text-amber-400 border border-slate-800">
                        {{ $category['name'] }}
                    </span>
                    @if(!empty($module['price_monthly']))
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-900 text-slate-300 border border-slate-800">
                            ${{ $module['price_monthly'] }}/month addon
                        </span>
                    @else
                        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                            Included in Core
                        </span>
                    @endif
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">
                    {{ $module['name'] }}
                </h1>

                <p class="mt-4 text-base sm:text-lg text-slate-300 leading-relaxed">
                    {{ $module['description'] }}
                </p>

                <!-- What You Get Section -->
                <div class="mt-12 bg-slate-900/50 p-6 sm:p-8 rounded-2xl border border-slate-800">
                    <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        What You Get With This Module
                    </h2>

                    <ul class="space-y-4">
                        @foreach($module['features'] ?? [] as $feat)
                            <li class="flex items-start gap-3 text-sm text-slate-300">
                                <span class="h-5 w-5 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">&check;</span>
                                <span class="leading-relaxed">{{ $feat }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Dependencies & Architecture -->
                @if(!empty($module['dependencies']))
                    <div class="mt-8 bg-slate-950 p-6 rounded-2xl border border-slate-800">
                        <h3 class="text-sm font-semibold text-slate-200 mb-2">Module Dependencies</h3>
                        <p class="text-xs text-slate-400 mb-3">Enabling this module automatically configures or requires:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($module['dependencies'] as $dep)
                                <span class="text-xs font-medium px-3 py-1 rounded-lg bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ ucwords(str_replace('_', ' ', $dep)) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Column -->
            <div class="lg:col-span-4 space-y-6">
                <!-- CTA Card -->
                <div class="bg-gradient-to-br from-slate-900 to-slate-950 p-6 rounded-2xl border border-slate-800 shadow-xl">
                    <h3 class="text-base font-bold text-white">Experience in Live Demo</h3>
                    <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                        Test {{ $module['name'] }} in our interactive demo workspace with live seeded demo data.
                    </p>

                    <a href="/demo" class="mt-6 w-full py-3 px-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-center text-xs shadow-lg shadow-amber-500/20 block transition-all">
                        Launch Live CRM Demo &rarr;
                    </a>

                    <div class="mt-4 text-center">
                        <a href="/pricing" class="text-xs text-slate-400 hover:text-white underline">View SaaS Pricing Plans</a>
                    </div>
                </div>

                <!-- Related in Category -->
                @if(!empty($relatedModules))
                    <div class="bg-slate-950 p-6 rounded-2xl border border-slate-800">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Related in {{ $category['name'] }}</h3>
                        <div class="space-y-4">
                            @foreach($relatedModules as $rel)
                                <div>
                                    <a href="/features/{{ $rel['slug'] }}" class="text-sm font-semibold text-white hover:text-amber-400 transition-colors block">
                                        {{ $rel['name'] }}
                                    </a>
                                    <p class="text-[11px] text-slate-400 line-clamp-2 mt-1">{{ $rel['description'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.public>
