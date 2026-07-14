<div x-show="sidebarOpen" class="w-72 bg-stone-100 dark:bg-stone-900 text-stone-700 dark:text-stone-200 flex flex-col shrink-0 border-r border-stone-200 dark:border-stone-800 transition-colors">
    <div class="p-4 border-b border-stone-200 dark:border-stone-800">
        <button @click="newConversation()" class="w-full flex items-center gap-2 px-4 py-2.5 rounded-lg border border-stone-300 dark:border-stone-600 text-stone-600 dark:text-stone-300 hover:bg-stone-200 dark:hover:bg-stone-700 hover:text-stone-800 dark:hover:text-white transition text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Chat Baru
        </button>
    </div>

    <div class="flex-1 overflow-y-auto p-2 space-y-1">
        <template x-for="conv in conversations" :key="conv.id">
            <div @click="selectConversation(conv.id)"
                 :class="activeConversation === conv.id 
                     ? 'bg-amber-100 dark:bg-stone-700 text-amber-800 dark:text-white' 
                     : 'text-stone-500 dark:text-stone-400 hover:bg-stone-200 dark:hover:bg-stone-700/50 hover:text-stone-700 dark:hover:text-stone-200'"
                 class="group flex items-center gap-2 px-3 py-2.5 rounded-lg cursor-pointer transition text-sm">

                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>

                <template x-if="editingTitle === conv.id">
                    <input @click.stop
                           @keydown.enter="updateConversationTitle(conv)"
                           @keydown.escape="editingTitle = null"
                           @click.outside="editingTitle = null"
                           x-model="conv.title"
                           x-init="$nextTick(() => $el.focus())"
                           class="flex-1 bg-white dark:bg-stone-600 text-stone-800 dark:text-white px-2 py-0.5 rounded text-sm outline-none border border-amber-500">
                </template>

                <template x-if="editingTitle !== conv.id">
                    <span class="truncate flex-1" x-text="conv.title || 'Percakapan baru'"
                          @dblclick="startEditingTitle(conv)"></span>
                </template>

                <button @click.stop="startEditingTitle(conv)"
                        class="opacity-0 group-hover:opacity-100 text-stone-400 dark:text-stone-500 hover:text-amber-500 dark:hover:text-amber-400 transition p-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>

                <button @click.stop="deleteConversation(conv.id)"
                        class="opacity-0 group-hover:opacity-100 text-stone-400 dark:text-stone-500 hover:text-red-500 dark:hover:text-red-400 transition p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </template>
    </div>
</div>