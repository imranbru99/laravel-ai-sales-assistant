<x-layouts.public :title="$preset['name'] . ' — Business Solution Blueprint'">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-400 mb-8">
            <a href="/" class="hover:text-white">Home</a>
            <span>&rsaquo;</span>
            <a href="/solutions" class="hover:text-white">Solutions</a>
            <span>&rsaquo;</span>
            <span class="text-amber-400">{{ $preset['name'] }}</span>
        </nav>

        <div class="max-w-3xl mb-12">
            <span class="text-xs uppercase tracking-widest font-bold text-amber-400">Business Solution Blueprint</span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-white mt-2">
                {{ $preset['name'] }}
            </h1>
            <p class="text-slate-300 mt-4 text-base sm:text-lg leading-relaxed">
                {{ $preset['description'] }}
            </p>
            <div class="mt-6 flex items-center gap-4">
                <a href="/demo" class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-lg shadow-amber-500/20">
                    Launch With This Preset (Demo) &rarr;
                </a>
            </div>
        </div>

        <!-- Enabled Modules Grid -->
        <div class="mt-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-white">
                    Included Modules in this Blueprint ({{ count($enabledModules) }})
                </h2>
                <span class="text-xs text-slate-400">Configured and ready out of the box</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($enabledModules as $key => $mod)
                    <div class="bg-slate-900/60 p-6 rounded-2xl border border-slate-800 hover:border-slate-700 transition-all flex flex-col justify-between">
                        <div>
                            <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-0.5 rounded-full bg-slate-800 text-amber-400 border border-slate-700">
                                {{ $mod['category'] }}
                            </span>
                            <h3 class="text-base font-bold text-white mt-2">{{ $mod['name'] }}</h3>
                            <p class="text-xs text-slate-400 mt-2 line-clamp-3">{{ $mod['description'] }}</p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-800/80">
                            <a href="/features/{{ $mod['slug'] }}" class="text-xs font-semibold text-amber-400 hover:underline">
                                View Module Details &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.public>
