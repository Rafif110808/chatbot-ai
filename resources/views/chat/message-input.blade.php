<div class="border-t border-stone-200 dark:border-stone-700 bg-white dark:bg-stone-900 px-4 py-3 transition-colors">
    <form @submit.prevent="sendMessage()" class="max-w-4xl mx-auto flex gap-2">


        <textarea x-model="newMessage"
                  @keydown.enter.prevent="sendMessage()"
                  placeholder="Ketik pesan ke Rafif Assistant..."
                  class="flex-1 resize-none rounded-xl border-stone-200 dark:border-stone-600 bg-stone-50 dark:bg-stone-800 focus:border-amber-400 focus:ring-amber-400 placeholder-stone-400 dark:placeholder-stone-500 text-stone-700 dark:text-stone-200 px-4 py-3 text-sm"
                  rows="1"
                  x-ref="messageInput"
                  :disabled="isLoading">
        </textarea>

        <button type="submit"
                :disabled="!newMessage.trim() || isLoading"
                :class="newMessage.trim() && !isLoading ? 'bg-amber-500 hover:bg-amber-600 shadow-md shadow-amber-200 dark:shadow-amber-900/30' : 'bg-stone-300 dark:bg-stone-600 cursor-not-allowed'"
                class="rounded-xl px-4 text-white transition flex items-center justify-center">
            <svg x-show="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V5m0 0l-7 7m7-7l7 7" />
            </svg>
            <svg x-show="isLoading" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
        </button>
    </form>

    <p class="text-xs text-stone-400 dark:text-stone-500 text-center mt-2">
        Rafif Assistant bisa saja tidak akurat. Gunakan dengan bijak.
    </p>
</div>