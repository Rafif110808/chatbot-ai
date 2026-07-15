<div x-show="activeConversation" class="flex-1 overflow-y-auto px-4 py-6 space-y-5 chat-scrollbar" x-ref="chatContainer">

    <template x-for="(msg, i) in messages" :key="msg.id">
        <div :class="'flex ' + (msg.role === 'user' ? 'justify-end' : 'justify-start')">
            <div :class="'max-w-[78%] group ' + (msg.role === 'user'
                ? 'bg-amber-100 dark:bg-amber-900/50 text-amber-900 dark:text-amber-100 rounded-2xl rounded-br-sm px-5 py-3.5 shadow-sm'
                : 'bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700 text-stone-700 dark:text-stone-200 rounded-2xl rounded-bl-sm px-5 py-3.5 shadow-sm')">
                <div class="text-[15px] leading-relaxed prose prose-stone dark:prose-invert max-w-none prose-headings:font-semibold prose-code:before:content-none prose-code:after:content-none prose-p:my-1" x-html="msg.content"></div>
                <template x-if="msg.role === 'assistant'">
                    <div class="mt-2 flex items-center gap-1 text-xs text-stone-400 dark:text-stone-500">
                        <button @click="regenerateResponse(i)" :disabled="isLoading"
                                class="flex items-center gap-1 hover:text-amber-500 dark:hover:text-amber-400 transition px-1.5 py-0.5 rounded hover:bg-stone-100 dark:hover:bg-stone-700 disabled:opacity-50">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Regenerate
                        </button>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <div x-show="isLoading">
        <x-typing-indicator />
    </div>

    <div x-ref="scrollAnchor"></div>
</div>