<x-layouts.public title="Interactive Live Demo Switcher — AI Sales Assistant">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-14">
            <span class="text-xs uppercase tracking-widest font-bold text-amber-400">Zero-Friction Exploration</span>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white mt-2">One-Click Demo Personas</h1>
            <p class="text-slate-400 mt-4 text-base">
                Click any role card below to instantly sign in and experience that exact perspective with pre-seeded enterprise data.
            </p>
        </div>

        @if(session('error'))
            <div class="max-w-md mx-auto mb-8 p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs text-center font-medium">
                {{ session('error') }}
            </div>
        @endif

        <!-- Personas Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($personas as $persona)
                <div class="bg-slate-900/60 p-6 rounded-2xl border border-slate-800 hover:border-amber-500/50 transition-all flex flex-col justify-between group shadow-lg">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-[10px] uppercase tracking-wider font-semibold px-2 py-0.5 rounded-full bg-slate-800 text-amber-400 border border-slate-700">
                                {{ $persona['badge'] }}
                            </span>
                            <span class="text-[10px] text-slate-500 font-mono">1-Click</span>
                        </div>

                        <h3 class="text-lg font-bold text-white group-hover:text-amber-400 transition-colors">
                            {{ $persona['role'] }}
                        </h3>

                        <div class="mt-2 text-xs text-slate-300 font-medium">
                            {{ $persona['name'] }}
                        </div>
                        <div class="text-[11px] text-slate-500 font-mono">
                            {{ $persona['email'] }}
                        </div>

                        <p class="text-xs text-slate-400 mt-4 leading-relaxed">
                            {{ $persona['description'] }}
                        </p>
                    </div>

                    <div class="mt-8 pt-4 border-t border-slate-800 space-y-3">
                        <div class="text-[10px] text-slate-500 truncate">
                            Target: <span class="text-slate-400 font-mono">{{ $persona['destination'] }}</span>
                        </div>

                        <form method="POST" action="/demo/login/{{ $persona['key'] }}">
                            @csrf
                            <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-md shadow-amber-500/20 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                                <span>Sign In as {{ explode(' ', $persona['role'])[0] }}</span>
                                <span>&rarr;</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-16 text-center text-xs text-slate-500">
            Note: All demo accounts share the password <code class="px-2 py-0.5 rounded bg-slate-900 text-amber-400 font-mono">password</code> if manual login is preferred.
        </div>
    </div>
</x-layouts.public>
