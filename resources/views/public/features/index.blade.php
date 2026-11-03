<x-layouts.public title="All 76 Modules — AI Sales Assistant">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="text-xs uppercase tracking-widest font-bold text-amber-400">Complete Feature Registry</span>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white mt-2">76 Specialized Modules</h1>
            <p class="text-slate-400 mt-4 text-base">
                Explore every module available on the platform. All modules can be switched ON or OFF per business and per branch.
            </p>
        </div>

        <!-- Category Filters -->
        <div class="flex flex-wrap items-center justify-center gap-2 mb-12">
            <a href="/features" class="px-4 py-2 rounded-xl text-xs font-semibold border transition-all {{ empty($activeCategory) ? 'bg-amber-500 text-slate-950 border-amber-500 shadow-md shadow-amber-500/20' : 'bg-slate-900 text-slate-300 border-slate-800 hover:border-slate-700' }}">
                All Modules ({{ $totalCount }})
            </a>
            @foreach($categories as $catKey => $cat)
                <a href="/features?category={{ $catKey }}" class="px-4 py-2 rounded-xl text-xs font-semibold border transition-all {{ ($activeCategory === $catKey) ? 'bg-amber-500 text-slate-950 border-amber-500 shadow-md shadow-amber-500/20' : 'bg-slate-900 text-slate-300 border-slate-800 hover:border-slate-700' }}">
                    {{ $cat['name'] }}
                </a>
            @endforeach
        </div>

        <!-- Modules Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($modules as $key => $mod)
                <div class="bg-slate-900/60 p-6 rounded-2xl border border-slate-800 hover:border-amber-500/50 transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-0.5 rounded-full bg-slate-800 text-amber-400 border border-slate-700">
                                {{ $mod['category'] }}
                            </span>
                            @if(!empty($mod['price_monthly']))
                                <span class="text-xs text-slate-400">${{ $mod['price_monthly'] }}/mo</span>
                            @else
                                <span class="text-xs text-emerald-400 font-semibold">Included</span>
                            @endif
                        </div>

                        <h3 class="text-lg font-bold text-white group-hover:text-amber-400 transition-colors">
                            {{ $mod['name'] }}
                        </h3>

                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                            {{ $mod['description'] }}
                        </p>

                        <!-- Feature bullets -->
                        @if(!empty($mod['features']))
                            <ul class="mt-4 space-y-1.5 text-[11px] text-slate-400">
                                @foreach(array_slice($mod['features'], 0, 3) as $feature)
                                    <li class="flex items-start gap-1.5">
                                        <span class="text-amber-400 font-bold">&check;</span>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between">
                        <span class="text-[11px] text-slate-500">{{ count($mod['features'] ?? []) }} capabilities</span>
                        <a href="/features/{{ Str::slug($mod['name']) }}" class="text-xs font-semibold text-amber-400 hover:underline">
                            Module Details &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-slate-400">
                    No modules match your current filter. <a href="/features" class="text-amber-400 underline">Reset filters</a>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.public>
