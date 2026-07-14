<div x-show="activeConversation" class="flex-1 overflow-y-auto px-4 py-6 space-y-4" x-ref="chatContainer">

    <template x-for="msg in messages" :key="msg.id">
        <div :class="'flex ' + (msg.role === 'user' ? 'justify-end' : 'justify-start')">
            <div :class="'max-w-[80%] ' + (msg.role === 'user'
                ? 'bg-amber-100 dark:bg-amber-900/50 text-amber-900 dark:text-amber-100 rounded-2xl rounded-br-sm px-4 py-3'
                : 'bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700 text-stone-700 dark:text-stone-200 rounded-2xl rounded-bl-sm px-4 py-3')">
                <div class="text-sm prose prose-stone dark:prose-invert max-w-none" x-html="msg.content"></div>
            </div>
        </div>
    </template>

    <div x-show="isLoading">
        <x-typing-indicator />
    </div>

    <div x-ref="scrollAnchor"></div>
</div>