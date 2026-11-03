<x-filament-panels::page>
    <div class="space-y-8">
        {{-- Business Type Presets Section --}}
        <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-indigo-50/50 via-white to-purple-50/30 p-6 shadow-sm dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/20">
            <div class="mb-4 flex flex-col justify-between gap-2 sm:flex-row sm:items-center">
                <div>
                    <h3 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-sparkles" class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                        Business Presets (1-Click Setup)
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Choose your industry preset to instantly activate the recommended modules and workflows for your business.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach ($this->presets as $preset)
                    <div class="group relative flex flex-col justify-between rounded-xl border border-gray-200/80 bg-white p-4 transition-all hover:border-indigo-400 hover:shadow-md dark:border-gray-800 dark:bg-gray-800/80 dark:hover:border-indigo-500">
                        <div>
                            <div class="mb-2 flex items-center gap-2">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                                    <x-filament::icon icon="{{ $preset->icon ?? 'heroicon-o-briefcase' }}" class="h-4 w-4" />
                                </span>
                                <h4 class="font-semibold text-gray-900 dark:text-white text-sm">{{ $preset->name }}</h4>
                            </div>
                            <p class="text-xs text-gray-500 line-clamp-2 dark:text-gray-400 mb-3">
                                {{ $preset->description }}
                            </p>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700/60">
                            <span class="text-[11px] font-medium text-gray-400">
                                {{ count($preset->modules ?? []) }} modules
                            </span>
                            <button
                                wire:click="applyPreset('{{ $preset->key }}')"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 hover:bg-indigo-600 hover:text-white transition-colors dark:bg-indigo-950/60 dark:text-indigo-300 dark:hover:bg-indigo-600 dark:hover:text-white"
                            >
                                <span>Apply</span>
                                <x-filament::icon icon="heroicon-m-arrow-right" class="h-3 w-3" />
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex flex-wrap gap-1.5">
                <button
                    wire:click="$set('selectedCategory', 'all')"
                    class="rounded-lg px-3 py-1.5 text-xs font-medium transition-all {{ $selectedCategory === 'all' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300' }}"
                >
                    All Modules
                </button>
                @foreach ($this->categories as $catKey => $category)
                    <button
                        wire:click="$set('selectedCategory', '{{ $catKey }}')"
                        class="rounded-lg px-3 py-1.5 text-xs font-medium transition-all {{ $selectedCategory === $catKey ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300' }}"
                    >
                        {{ $category['name'] }}
                    </button>
                @endforeach
            </div>

            <div class="w-full sm:w-72">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search 76 modules..."
                    class="w-full rounded-xl border border-gray-300 bg-white px-3.5 py-2 text-xs text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                />
            </div>
        </div>

        {{-- Modules Grid --}}
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->modules as $mod)
                <div class="relative flex flex-col justify-between rounded-2xl border transition-all duration-200 {{ $mod['is_enabled'] ? 'border-indigo-200 bg-white shadow-sm ring-1 ring-indigo-500/10 dark:border-indigo-900/40 dark:bg-gray-900' : 'border-gray-200 bg-gray-50/60 opacity-80 dark:border-gray-800 dark:bg-gray-900/40' }} p-5">
                    <div>
                        {{-- Header --}}
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $mod['is_enabled'] ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-400' : 'bg-gray-200 text-gray-600 dark:bg-gray-800 dark:text-gray-400' }}">
                                    <x-filament::icon icon="{{ $mod['icon'] }}" class="h-5 w-5" />
                                </span>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm dark:text-white leading-tight">
                                        {{ $mod['name'] }}
                                    </h4>
                                    <span class="text-[11px] font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">
                                        {{ $mod['category'] }}
                                    </span>
                                </div>
                            </div>

                            @if ($mod['is_core'])
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                    Core
                                </span>
                            @else
                                <button
                                    wire:click="toggleModule('{{ $mod['key'] }}')"
                                    wire:loading.attr="disabled"
                                    type="button"
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $mod['is_enabled'] ? 'bg-indigo-600' : 'bg-gray-300 dark:bg-gray-700' }}"
                                >
                                    <span class="sr-only">Toggle module</span>
                                    <span
                                        aria-hidden="true"
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $mod['is_enabled'] ? 'translate-x-5' : 'translate-x-0' }}"
                                    ></span>
                                </button>
                            @endif
                        </div>

                        {{-- Description --}}
                        <p class="text-xs text-gray-600 dark:text-gray-300 mb-4 line-clamp-2">
                            {{ $mod['description'] }}
                        </p>

                        {{-- What You Get Features --}}
                        @if (!empty($mod['features']))
                            <div class="mb-4 space-y-1.5 rounded-xl bg-gray-50/80 p-3 dark:bg-gray-800/50">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 block">
                                    What You Get:
                                </span>
                                @foreach (array_slice($mod['features'], 0, 3) as $feature)
                                    <div class="flex items-start gap-1.5 text-xs text-gray-700 dark:text-gray-300">
                                        <x-filament::icon icon="heroicon-m-check-circle" class="h-3.5 w-3.5 text-emerald-500 shrink-0 mt-0.5" />
                                        <span class="text-[11px] leading-snug">{{ $feature }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800 text-xs">
                        <div class="flex items-center gap-2">
                            @if ($mod['price_monthly'] > 0)
                                <span class="font-bold text-gray-900 dark:text-white">${{ number_format($mod['price_monthly'], 0) }}<span class="text-[10px] font-normal text-gray-500">/mo</span></span>
                            @else
                                <span class="font-semibold text-emerald-600 dark:text-emerald-400">Included</span>
                            @endif

                            @if (!empty($mod['dependencies']))
                                <span class="text-[10px] text-amber-600 dark:text-amber-400" title="Requires: {{ implode(', ', $mod['dependencies']) }}">
                                    ⚠️ {{ count($mod['dependencies']) }} req
                                </span>
                            @endif
                        </div>

                        <span class="inline-flex items-center gap-1 font-semibold text-[11px] {{ $mod['is_enabled'] ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400' }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $mod['is_enabled'] ? 'bg-indigo-500 animate-pulse' : 'bg-gray-400' }}"></span>
                            {{ $mod['is_enabled'] ? 'Active' : 'Disabled' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
