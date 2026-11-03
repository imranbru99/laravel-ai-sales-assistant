<x-filament-panels::page>
    <div class="mx-auto max-w-4xl space-y-8">
        {{-- Progress Bar --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400">
                    Step {{ $currentStep }} of {{ $totalSteps }}
                </span>
                <span class="text-xs font-semibold text-gray-500">
                    @if ($currentStep === 1) Business Type & Preset
                    @elseif ($currentStep === 2) Regional & Localization
                    @elseif ($currentStep === 3) Branches & Structure
                    @else AI Engine & Brand Voice @endif
                </span>
            </div>
            <div class="h-2 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                <div class="h-full bg-gradient-to-r from-indigo-500 to-purple-600 transition-all duration-300" style="width: {{ ($currentStep / $totalSteps) * 100 }}%"></div>
            </div>
        </div>

        {{-- Step Content --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-8 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            @if ($currentStep === 1)
                {{-- Step 1: Industry Preset --}}
                <div class="space-y-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Choose Your Business Industry Preset</h2>
                        <p class="text-sm text-gray-500">Pick the archetype that best matches your operations. This enables the relevant CRM, AI, and operations modules immediately.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
                        @foreach (config('features.presets', []) as $key => $preset)
                            <div
                                wire:click="$set('selectedPreset', '{{ $key }}')"
                                class="cursor-pointer rounded-xl border-2 p-4 transition-all {{ $selectedPreset === $key ? 'border-indigo-600 bg-indigo-50/40 ring-2 ring-indigo-500/20 dark:border-indigo-500 dark:bg-indigo-950/20' : 'border-gray-200 hover:border-gray-300 dark:border-gray-800 dark:hover:border-gray-700' }}"
                            >
                                <div class="flex items-center gap-2.5 mb-2">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                        <x-filament::icon icon="{{ $preset['icon'] ?? 'heroicon-o-briefcase' }}" class="h-4 w-4" />
                                    </span>
                                    <h4 class="font-bold text-sm text-gray-900 dark:text-white">{{ $preset['name'] }}</h4>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                                    {{ $preset['description'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>

            @elseif ($currentStep === 2)
                {{-- Step 2: Localization --}}
                <div class="space-y-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Regional Settings & Currency</h2>
                        <p class="text-sm text-gray-500">Set up your company display name, reporting currency, and local timezone.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Company / Organization Name</label>
                            <input type="text" wire:model="companyName" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Primary Currency</label>
                            <select wire:model="currency" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                <option value="USD">USD ($) - US Dollar</option>
                                <option value="BDT">BDT (৳) - Bangladeshi Taka</option>
                                <option value="EUR">EUR (€) - Euro</option>
                                <option value="GBP">GBP (£) - British Pound</option>
                                <option value="AUD">AUD ($) - Australian Dollar</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Default UI Language</label>
                            <select wire:model="locale" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                <option value="en">English (US/UK)</option>
                                <option value="bn">বাংলা (Bengali)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Timezone</label>
                            <select wire:model="timezone" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                <option value="UTC">UTC (Universal Time)</option>
                                <option value="Asia/Dhaka">Asia/Dhaka (GMT+6)</option>
                                <option value="America/New_York">America/New York (EST)</option>
                                <option value="Europe/London">Europe/London (GMT)</option>
                                <option value="Asia/Dubai">Asia/Dubai (GST)</option>
                            </select>
                        </div>
                    </div>
                </div>

            @elseif ($currentStep === 3)
                {{-- Step 3: Branches --}}
                <div class="space-y-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Multi-Branch Architecture</h2>
                        <p class="text-sm text-gray-500">Configure your headquarters and initial branch offices. Each branch can have its own team, stock, and module overrides.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Headquarters Branch Name</label>
                            <input type="text" wire:model="hqName" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Headquarters City</label>
                            <input type="text" wire:model="hqCity" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Secondary Branch / Outlet (Optional)</label>
                            <input type="text" wire:model="secondaryBranchName" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" />
                        </div>
                    </div>
                </div>

            @else
                {{-- Step 4: AI Engine & Brand Voice --}}
                <div class="space-y-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">AI Engine & Brand Voice</h2>
                        <p class="text-sm text-gray-500">Configure how the AI will draft follow-up messages and score leads for your team.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">AI Provider Engine</label>
                            <select wire:model="aiProvider" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                                <option value="demo">Demo AI Driver (Offline - Realistic instant drafts & scores)</option>
                                <option value="openai">OpenAI (GPT-4o / GPT-4o-mini)</option>
                                <option value="gemini">Google Gemini 2.0 / 1.5 Pro</option>
                                <option value="claude">Anthropic Claude 3.5 Sonnet</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Brand Voice & Communication Style</label>
                            <textarea wire:model="brandVoiceTone" rows="3" class="w-full rounded-xl border border-gray-300 px-3.5 py-2.5 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"></textarea>
                            <p class="text-[11px] text-gray-400 mt-1">This guidance steers tone, formality, and phrasing across all AI follow-up drafts and customer responses.</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Wizard Footer Navigation --}}
            <div class="mt-8 flex items-center justify-between border-t border-gray-100 pt-6 dark:border-gray-800">
                @if ($currentStep > 1)
                    <button
                        wire:click="previousStep"
                        type="button"
                        class="rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200"
                    >
                        Back
                    </button>
                @else
                    <div></div>
                @endif

                @if ($currentStep < $totalSteps)
                    <button
                        wire:click="nextStep"
                        type="button"
                        class="rounded-xl bg-indigo-600 px-6 py-2.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none"
                    >
                        Next Step
                    </button>
                @else
                    <button
                        wire:click="finishOnboarding"
                        wire:loading.attr="disabled"
                        type="button"
                        class="rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-7 py-2.5 text-xs font-bold text-white shadow-md hover:from-indigo-500 hover:to-purple-500 focus:outline-none"
                    >
                        Launch My Platform 🚀
                    </button>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
