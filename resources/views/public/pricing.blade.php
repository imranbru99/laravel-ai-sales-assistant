<x-layouts.public title="Transparent Modular Pricing — AI Sales Assistant">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16" x-data="{ billing: 'monthly' }">
        <!-- Header -->
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="text-xs uppercase tracking-widest font-bold text-amber-400">Simple &amp; Predictable</span>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white mt-2">Plans Built For Any Growth Stage</h1>
            <p class="text-slate-400 mt-4 text-base">
                Start with core sales automation and add specialized modules as your operations expand.
            </p>

            <!-- Billing Toggle -->
            <div class="mt-8 inline-flex items-center p-1 rounded-xl bg-slate-900 border border-slate-800">
                <button type="button" @click="billing = 'monthly'" :class="billing === 'monthly' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 font-medium'" class="px-5 py-2 rounded-lg text-xs transition-all">
                    Monthly Billing
                </button>
                <button type="button" @click="billing = 'yearly'" :class="billing === 'yearly' ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-400 font-medium'" class="px-5 py-2 rounded-lg text-xs transition-all flex items-center gap-1.5">
                    <span>Annual Billing</span>
                    <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Save 20%</span>
                </button>
            </div>
        </div>

        <!-- Pricing Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            @foreach($plans as $plan)
                <div class="bg-slate-900/60 p-8 rounded-3xl border {{ !empty($plan->is_featured) ? 'border-amber-500 shadow-2xl shadow-amber-500/10 relative' : 'border-slate-800' }} flex flex-col justify-between">
                    @if(!empty($plan->is_featured))
                        <div class="absolute -top-3.5 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full bg-amber-500 text-slate-950 text-[10px] font-extrabold uppercase tracking-wider">
                            Most Popular Choice
                        </div>
                    @endif

                    <div>
                        <h3 class="text-xl font-bold text-white">{{ $plan->name }}</h3>
                        <p class="text-xs text-slate-400 mt-2 min-h-[36px]">{{ $plan->description }}</p>

                        <!-- Price -->
                        <div class="mt-6 flex items-baseline gap-1">
                            <span class="text-4xl font-extrabold text-white" x-text="billing === 'monthly' ? '${{ (int)$plan->price_monthly }}' : '${{ (int)($plan->price_yearly / 12) }}'"></span>
                            <span class="text-slate-400 text-xs font-medium">/ month</span>
                        </div>
                        <div class="text-[11px] text-slate-500 mt-1" x-show="billing === 'yearly'">
                            Billed annually (${{ (int)$plan->price_yearly }}/yr)
                        </div>

                        <!-- Quota List -->
                        <ul class="mt-8 space-y-3 pt-6 border-t border-slate-800 text-xs text-slate-300">
                            <li class="flex items-center gap-2">
                                <span class="text-amber-400 font-bold">&check;</span>
                                <span><strong>{{ $plan->max_users }}</strong> Team Members</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-amber-400 font-bold">&check;</span>
                                <span><strong>{{ $plan->max_branches }}</strong> {{ $plan->max_branches > 1 ? 'Branches / Warehouses' : 'Branch Location' }}</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-amber-400 font-bold">&check;</span>
                                <span><strong>{{ number_format($plan->max_ai_tokens) }}</strong> AI Tokens / Month</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-amber-400 font-bold">&check;</span>
                                <span><strong>{{ round($plan->max_storage_mb / 1024, 1) }} GB</strong> Document Storage</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-amber-400 font-bold">&check;</span>
                                <span>AI Lead Scoring + AI Follow-up Drafter</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-amber-400 font-bold">&check;</span>
                                <span>Universal Multilingual Unicode PDF</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-800">
                        <a href="/demo" class="w-full py-3 px-4 rounded-xl {{ !empty($plan->is_featured) ? 'bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold' : 'bg-slate-800 hover:bg-slate-700 text-white font-semibold' }} text-center text-xs block transition-all shadow-md">
                            Test in Live Demo &rarr;
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- FAQ -->
        <div class="mt-24 max-w-3xl mx-auto pt-16 border-t border-slate-900">
            <h2 class="text-2xl font-bold text-white text-center mb-8">Frequently Asked Questions</h2>

            <div class="space-y-6 text-sm">
                <div class="bg-slate-900/40 p-6 rounded-2xl border border-slate-800/80">
                    <h3 class="font-bold text-white">Can I turn off modules we don't need?</h3>
                    <p class="text-slate-400 text-xs mt-2 leading-relaxed">
                        Yes! That is the core architecture of AI Sales Assistant. Inside the Module Marketplace, toggle any module OFF. It immediately hides navigation, routes, widgets, and scheduled jobs without deleting your underlying data.
                    </p>
                </div>

                <div class="bg-slate-900/40 p-6 rounded-2xl border border-slate-800/80">
                    <h3 class="font-bold text-white">How does AI Lead Scoring calculate grades?</h3>
                    <p class="text-slate-400 text-xs mt-2 leading-relaxed">
                        Our scoring engine evaluates fit, engagement, budget, source recency, and AI content analysis via <strong>laravel-ai-hub</strong>. Each lead receives a score (0–100), a letter grade (A/B/C/D), an explanation, and recommended next actions.
                    </p>
                </div>

                <div class="bg-slate-900/40 p-6 rounded-2xl border border-slate-800/80">
                    <h3 class="font-bold text-white">Does the app support local payment methods and attendance devices?</h3>
                    <p class="text-slate-400 text-xs mt-2 leading-relaxed">
                        Yes. For billing and payments, we integrate bKash, Nagad, Rocket, Stripe, and PayPal. For employee attendance, <strong>laravel-attendance-hub</strong> connects directly with ZKTeco and IP biometric attendance machines.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.public>
