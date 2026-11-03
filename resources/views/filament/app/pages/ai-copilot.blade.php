<x-filament-panels::page>
    <div class="mx-auto flex h-[78vh] max-w-4xl flex-col rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        {{-- Header Banner --}}
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white shadow-sm">
                    <x-filament::icon icon="heroicon-o-sparkles" class="h-5 w-5" />
                </span>
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">AI Sales Copilot</h3>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Active & Connected to Tenant CRM Data
                    </p>
                </div>
            </div>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-[11px] font-semibold text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300">
                GPT-4o & AI Hub Connected
            </span>
        </div>

        {{-- Chat Message Thread --}}
        <div class="flex-1 space-y-4 overflow-y-auto p-6" id="copilot-thread">
            @foreach ($conversation as $msg)
                <div class="flex items-start gap-3 {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                    @if ($msg['role'] === 'assistant')
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-100 text-indigo-700 dark:bg-indigo-950 dark:text-indigo-300">
                            <x-filament::icon icon="heroicon-o-cpu-chip" class="h-4 w-4" />
                        </span>
                    @endif

                    <div class="max-w-[80%] rounded-2xl px-4 py-3 text-sm {{ $msg['role'] === 'user' ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-gray-100 text-gray-900 dark:bg-gray-800 dark:text-gray-100 rounded-tl-none border border-gray-200/60 dark:border-gray-700/60' }}">
                        <div class="prose prose-sm dark:prose-invert max-w-none">
                            {!! Str::markdown($msg['content']) !!}
                        </div>
                        <span class="mt-1 block text-[10px] {{ $msg['role'] === 'user' ? 'text-indigo-200 text-right' : 'text-gray-400' }}">
                            {{ $msg['time'] }}
                        </span>
                    </div>

                    @if ($msg['role'] === 'user')
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-200 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <x-filament::icon icon="heroicon-o-user" class="h-4 w-4" />
                        </span>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Quick Prompt Chips --}}
        <div class="flex flex-wrap gap-2 border-t border-gray-100 px-6 py-2.5 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-900/40">
            <button
                type="button"
                wire:click="askPrompt('Which hot leads have not been contacted in 5 days?')"
                class="rounded-full border border-gray-200 bg-white px-3 py-1 text-xs text-gray-700 hover:border-indigo-400 hover:text-indigo-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-indigo-500"
            >
                🔥 Show neglected Hot leads
            </button>
            <button
                type="button"
                wire:click="askPrompt('Give me a pipeline summary and at-risk deals')"
                class="rounded-full border border-gray-200 bg-white px-3 py-1 text-xs text-gray-700 hover:border-indigo-400 hover:text-indigo-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-indigo-500"
            >
                📊 Pipeline summary & stalled deals
            </button>
            <button
                type="button"
                wire:click="askPrompt('Draft a high-converting follow-up for stalled proposals')"
                class="rounded-full border border-gray-200 bg-white px-3 py-1 text-xs text-gray-700 hover:border-indigo-400 hover:text-indigo-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:border-indigo-500"
            >
                ✍️ Draft follow-up for stalled proposals
            </button>
        </div>

        {{-- Input Box --}}
        <div class="border-t border-gray-100 p-4 dark:border-gray-800">
            <form wire:submit="sendMessage" class="flex items-center gap-2">
                <input
                    type="text"
                    wire:model="message"
                    placeholder="Ask Copilot about leads, deals, drafting messages, or account summaries..."
                    class="flex-1 rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                />
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-sm hover:bg-indigo-500 focus:outline-none"
                >
                    <x-filament::icon icon="heroicon-m-paper-airplane" class="h-5 w-5" />
                </button>
            </form>
        </div>
    </div>
</x-filament-panels::page>
