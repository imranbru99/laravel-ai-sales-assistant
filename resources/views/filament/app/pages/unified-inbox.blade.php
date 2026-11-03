<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 h-[78vh]">
        {{-- Conversation List --}}
        <div class="flex flex-col rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 lg:col-span-4 overflow-hidden">
            {{-- Filter Tabs --}}
            <div class="flex items-center gap-1 border-b border-gray-100 p-3 dark:border-gray-800 overflow-x-auto">
                <button
                    wire:click="$set('selectedChannel', 'all')"
                    class="rounded-lg px-2.5 py-1 text-xs font-semibold {{ $selectedChannel === 'all' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                >
                    All
                </button>
                <button
                    wire:click="$set('selectedChannel', 'email')"
                    class="rounded-lg px-2.5 py-1 text-xs font-semibold {{ $selectedChannel === 'email' ? 'bg-indigo-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                >
                    Email
                </button>
                <button
                    wire:click="$set('selectedChannel', 'whatsapp')"
                    class="rounded-lg px-2.5 py-1 text-xs font-semibold {{ $selectedChannel === 'whatsapp' ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                >
                    WhatsApp
                </button>
                <button
                    wire:click="$set('selectedChannel', 'sms')"
                    class="rounded-lg px-2.5 py-1 text-xs font-semibold {{ $selectedChannel === 'sms' ? 'bg-sky-600 text-white' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800' }}"
                >
                    SMS
                </button>
            </div>

            {{-- List --}}
            <div class="flex-1 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($this->conversations as $conv)
                    <div
                        wire:click="selectConversation({{ $conv->id }})"
                        class="cursor-pointer p-4 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/60 {{ $activeConversationId === $conv->id ? 'bg-indigo-50/60 border-l-4 border-indigo-600 dark:bg-indigo-950/30' : '' }}"
                    >
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="text-xs font-bold text-gray-900 dark:text-white truncate">
                                {{ $conv->lead?->contact_name ?? $conv->contact?->full_name ?? 'Website Prospect' }}
                            </h4>
                            <span class="text-[10px] text-gray-400">
                                {{ $conv->last_message_at?->diffForHumans(short: true) ?? 'Now' }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-300 truncate font-medium">
                            {{ $conv->subject ?? 'Inquiry Thread' }}
                        </p>
                        <div class="flex items-center gap-1.5 mt-2">
                            <span class="rounded-md px-1.5 py-0.5 text-[10px] font-bold uppercase {{ $conv->channel === 'whatsapp' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : ($conv->channel === 'email' ? 'bg-indigo-100 text-indigo-800' : 'bg-sky-100 text-sky-800') }}">
                                {{ $conv->channel }}
                            </span>
                            @if ($conv->lead && $conv->lead->score_grade)
                                <span class="rounded-md bg-amber-50 px-1.5 py-0.5 text-[10px] font-bold text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                                    Grade {{ $conv->lead->score_grade }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-gray-400">
                        No conversations in this filter.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Active Message Thread --}}
        <div class="flex flex-col rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900 lg:col-span-8 overflow-hidden">
            @if ($this->activeConversation)
                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-3.5 dark:border-gray-800">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-100 text-indigo-700 font-bold text-xs dark:bg-indigo-950 dark:text-indigo-300">
                            {{ strtoupper(substr($this->activeConversation->lead?->contact_name ?? 'C', 0, 2)) }}
                        </span>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $this->activeConversation->lead?->contact_name ?? $this->activeConversation->contact?->full_name ?? 'Prospect' }}
                            </h3>
                            <p class="text-xs text-gray-500">
                                {{ $this->activeConversation->lead?->company_name ?? 'Direct Contact' }} · {{ $this->activeConversation->channel }}
                            </p>
                        </div>
                    </div>
                    <button
                        type="button"
                        wire:click="generateAiReply"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50/70 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 dark:border-indigo-800 dark:bg-indigo-950 dark:text-indigo-300"
                    >
                        <x-filament::icon icon="heroicon-o-sparkles" class="h-3.5 w-3.5" />
                        <span>AI Draft Reply</span>
                    </button>
                </div>

                {{-- Messages --}}
                <div class="flex-1 space-y-4 overflow-y-auto p-6 bg-gray-50/40 dark:bg-gray-950/20">
                    @foreach ($this->activeConversation->messages as $msg)
                        <div class="flex {{ $msg->sender_type === 'agent' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[75%] rounded-2xl p-4 text-xs {{ $msg->is_internal_note ? 'bg-amber-50 border border-amber-200 text-amber-900 dark:bg-amber-950/30 dark:border-amber-800 dark:text-amber-200' : ($msg->sender_type === 'agent' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-900 border border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white') }}">
                                @if ($msg->is_internal_note)
                                    <span class="block font-bold text-[10px] uppercase tracking-wider text-amber-700 dark:text-amber-400 mb-1">🔒 Internal Note (Only visible to team)</span>
                                @endif
                                <div class="whitespace-pre-line leading-relaxed">{{ $msg->content }}</div>
                                <div class="mt-2 flex items-center justify-between text-[10px] {{ $msg->sender_type === 'agent' && !$msg->is_internal_note ? 'text-indigo-200' : 'text-gray-400' }}">
                                    <span>{{ $msg->sender_name ?? 'Sender' }}</span>
                                    <span>{{ $msg->created_at->format('M j, H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Composer --}}
                <div class="border-t border-gray-100 p-4 dark:border-gray-800 bg-white dark:bg-gray-900">
                    <div class="mb-2 flex items-center justify-between text-xs">
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="checkbox" wire:model="isInternalNote" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Internal whisper note</span>
                        </label>
                        <span class="text-gray-400 text-[11px]">Channel: {{ ucfirst($this->activeConversation->channel) }}</span>
                    </div>

                    <div class="flex items-end gap-2">
                        <textarea
                            wire:model="replyContent"
                            rows="3"
                            placeholder="Type response or click 'AI Draft Reply'..."
                            class="flex-1 rounded-xl border border-gray-300 p-3 text-xs dark:border-gray-700 dark:bg-gray-800 dark:text-white focus:outline-none focus:border-indigo-500"
                        ></textarea>
                        <button
                            type="button"
                            wire:click="sendReply"
                            class="h-10 rounded-xl bg-indigo-600 px-5 text-xs font-bold text-white shadow-sm hover:bg-indigo-500"
                        >
                            Send
                        </button>
                    </div>
                </div>
            @else
                <div class="flex-1 flex items-center justify-center p-8 text-center text-gray-400 text-xs">
                    Select a conversation to read and respond.
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
