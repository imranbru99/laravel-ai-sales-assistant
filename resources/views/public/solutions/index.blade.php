<x-layouts.public title="Business Solutions & Presets — AI Sales Assistant">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs uppercase tracking-widest font-bold text-amber-400">Industry-Tailored Blueprints</span>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white mt-2">11 Ready-Made Business Presets</h1>
            <p class="text-slate-400 mt-4 text-base">
                Whether you run a garment export merchandising house, an architectural B2B agency, or a healthcare clinic, launch your workspace in 1 click.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($presets as $pkey => $preset)
                <div class="bg-slate-900/60 p-8 rounded-2xl border border-slate-800 hover:border-amber-500/50 transition-all flex flex-col justify-between group">
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold px-2.5 py-0.5 rounded-full bg-slate-800 text-amber-400 border border-slate-700">
                            Preset
                        </span>
                        <h2 class="text-xl font-bold text-white mt-3 group-hover:text-amber-400 transition-colors">
                            {{ $preset['name'] }}
                        </h2>
                        <p class="text-xs text-slate-400 mt-3 leading-relaxed">
                            {{ $preset['description'] }}
                        </p>

                        <div class="mt-6 pt-4 border-t border-slate-800 text-xs text-slate-300">
                            <strong class="text-white">{{ count($preset['modules']) }} modules</strong> pre-activated:
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                @foreach(array_slice($preset['modules'], 0, 5) as $m)
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-slate-950 text-slate-400 border border-slate-800">
                                        {{ ucwords(str_replace('_', ' ', $m)) }}
                                    </span>
                                @endforeach
                                @if(count($preset['modules']) > 5)
                                    <span class="text-[10px] px-2 py-0.5 rounded bg-slate-950 text-amber-400 border border-slate-800">
                                        +{{ count($preset['modules']) - 5 }} more
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t border-slate-800">
                        <a href="/solutions/{{ $pkey }}" class="text-xs font-bold text-amber-400 hover:underline flex items-center justify-between">
                            <span>Explore Preset Blueprint</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.public>
