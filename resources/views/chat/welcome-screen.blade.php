<div x-show="!activeConversation" class="flex-1 flex flex-col items-center justify-center px-4 text-center">
    <div class="w-16 h-16 rounded-full bg-amber-500 flex items-center justify-center mb-6 shadow-lg shadow-amber-200 dark:shadow-amber-900/30">
        <span class="text-white text-2xl font-bold">R</span>
    </div>

    <h1 class="text-3xl font-bold text-stone-700 dark:text-stone-200 mb-2">
        Hallo, <span class="text-amber-600 dark:text-amber-400">{{ Auth::user()->name }}</span>!
    </h1>

    <p class="text-stone-400 dark:text-stone-500 mb-8 max-w-md">
        Saya <strong class="text-stone-600 dark:text-stone-300">Rafif Assistant</strong> — asisten AI pribadimu. 
        Aku siap membantu menjawab pertanyaan, menjelaskan konsep, atau sekadar ngobrol santai.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 max-w-lg w-full">
        <button @click="suggestPrompt('Jelaskan apa itu Laravel dengan bahasa sederhana')"
                class="px-4 py-3 rounded-xl bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700 text-stone-600 dark:text-stone-300 text-sm hover:border-amber-300 dark:hover:border-amber-600 hover:text-amber-600 dark:hover:text-amber-400 transition text-left">
            <span class="font-medium block">⚡ Belajar Laravel</span>
            <span class="text-xs text-stone-400 dark:text-stone-500 mt-1 block">Jelaskan dengan bahasa sederhana</span>
        </button>

        <button @click="suggestPrompt('Bantu saya menulis kode PHP untuk login system')"
                class="px-4 py-3 rounded-xl bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700 text-stone-600 dark:text-stone-300 text-sm hover:border-amber-300 dark:hover:border-amber-600 hover:text-amber-600 dark:hover:text-amber-400 transition text-left">
            <span class="font-medium block">💻 Bantu Coding</span>
            <span class="text-xs text-stone-400 dark:text-stone-500 mt-1 block">Bantu menulis kode PHP</span>
        </button>

        <button @click="suggestPrompt('Apa rekomendasi project untuk portfolio pemula?')"
                class="px-4 py-3 rounded-xl bg-white dark:bg-stone-800 border border-stone-200 dark:border-stone-700 text-stone-600 dark:text-stone-300 text-sm hover:border-amber-300 dark:hover:border-amber-600 hover:text-amber-600 dark:hover:text-amber-400 transition text-left">
            <span class="font-medium block">🚀 Ide Project</span>
            <span class="text-xs text-stone-400 dark:text-stone-500 mt-1 block">Rekomendasi portfolio pemula</span>
        </button>
    </div>

    <p class="text-xs text-stone-300 dark:text-stone-600 mt-8">Ketik pesan di bawah untuk memulai percakapan</p>
</div>