<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        {{-- Input Form --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-4 text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-adjustments-horizontal" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                Lead Parameters & Signals
            </h3>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Contact Name</label>
                    <input type="text" wire:model="contactName" class="w-full rounded-xl border border-gray-300 px-3.5 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Company / Organization</label>
                    <input type="text" wire:model="companyName" class="w-full rounded-xl border border-gray-300 px-3.5 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input type="email" wire:model="email" class="w-full rounded-xl border border-gray-300 px-3.5 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Phone</label>
                        <input type="text" wire:model="phone" class="w-full rounded-xl border border-gray-300 px-3.5 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Lead Source</label>
                        <select wire:model="source" class="w-full rounded-xl border border-gray-300 px-3.5 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            <option value="website">Website Inbound</option>
                            <option value="referral">VIP Referral</option>
                            <option value="campaign">Outbound Campaign</option>
                            <option value="ad">Paid Advertising</option>
                            <option value="event">Conference</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Estimated Budget ($)</label>
                        <input type="number" wire:model="budget" class="w-full rounded-xl border border-gray-300 px-3.5 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                    </div>
                </div>

                <div class="pt-2">
                    <button
                        type="button"
                        wire:click="simulate"
                        class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-indigo-500"
                    >
                        ⚡ Re-Calculate AI Score & Explanation
                    </button>
                </div>
            </div>
        </div>

        {{-- Scoring Output Card --}}
        <div class="flex flex-col justify-between rounded-2xl border border-gray-200 bg-gradient-to-br from-indigo-50/40 via-white to-purple-50/20 p-6 shadow-sm dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
            @if ($result)
                <div>
                    <div class="flex items-center justify-between border-b border-gray-200/80 pb-4 dark:border-gray-800">
                        <div>
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wider">Overall Score</span>
                            <div class="flex items-baseline gap-3">
                                <span class="text-4xl font-extrabold text-gray-900 dark:text-white">{{ $result['score'] }}<span class="text-xl font-normal text-gray-400">/100</span></span>
                                <span class="rounded-lg px-2.5 py-1 text-sm font-bold uppercase {{ $result['grade'] === 'A' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : ($result['grade'] === 'B' ? 'bg-indigo-100 text-indigo-800' : 'bg-amber-100 text-amber-800') }}">
                                    Grade {{ $result['grade'] }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right text-xs text-gray-500">
                            <div>Rule Score: <strong class="text-gray-900 dark:text-white">{{ $result['rule_score'] ?? 0 }}/40</strong></div>
                            <div>Engagement: <strong class="text-gray-900 dark:text-white">{{ $result['engagement_score'] ?? 0 }}/30</strong></div>
                            <div>AI Qualitative: <strong class="text-gray-900 dark:text-white">{{ $result['ai_score'] ?? 0 }}/30</strong></div>
                        </div>
                    </div>

                    {{-- Explanation --}}
                    <div class="mt-5 space-y-3">
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">Why this score?</h4>
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 leading-relaxed bg-white/70 p-3.5 rounded-xl border border-gray-200/60 dark:bg-gray-800/60 dark:border-gray-700/60">
                                {{ $result['explanation'] }}
                            </p>
                        </div>

                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 flex items-center gap-1.5">
                                <x-filament::icon icon="heroicon-o-light-bulb" class="h-4 w-4" />
                                Recommended Next Action
                            </h4>
                            <div class="mt-1 text-xs font-medium text-gray-800 dark:text-gray-200 bg-indigo-50/60 p-3.5 rounded-xl border border-indigo-100 dark:bg-indigo-950/40 dark:border-indigo-900/40">
                                {{ $result['recommended_action'] }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200/60 dark:border-gray-800 text-[11px] text-gray-400 flex items-center justify-between">
                    <span>Generated via AiGateway</span>
                    <span>Provider: Demo Engine</span>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
