<div x-show="activeConversation" class="flex-1 overflow-y-auto px-4 py-6 space-y-5 chat-scrollbar" x-ref="chatContainer">

    <template x-for="msg in messages" :key="msg.id">
        <div :class="'flex ' + (msg.role === 'user' ? 'justify-end' : 'justify-start')">
            <div :class="'max-w-[78%] ' + (msg.role === 'user'
                ? 'bg-amber-100 dark:bg-amber-900/50 text-amber-900 dark:text-amber-100 rounded-2xl rounded-br-sm px-5 py-3.5 shadow-sm'
                : 'bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700 text-stone-700 dark:text-stone-200 rounded-2xl rounded-bl-sm px-5 py-3.5 shadow-sm')">
                <div class="text-[15px] leading-relaxed prose prose-stone dark:prose-invert max-w-none prose-headings:font-semibold prose-code:before:content-none prose-code:after:content-none prose-p:my-1" x-html="msg.content"></div>
            </div>
        </div>
    </template>

    <div x-show="isLoading">
        <x-typing-indicator />
    </div>

    <div x-ref="scrollAnchor"></div>
</div>