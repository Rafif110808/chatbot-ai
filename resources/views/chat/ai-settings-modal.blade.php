<div x-show="settingsOpen"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center"
     @click.self="closeSettings()">
    <div class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>
    <div class="relative bg-white dark:bg-stone-800 rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between p-5 border-b border-stone-200 dark:border-stone-700">
            <h2 class="text-lg font-semibold text-stone-700 dark:text-stone-200">&#9881; AI Settings</h2>
            <button @click="closeSettings()" class="text-stone-400 hover:text-stone-600 dark:hover:text-stone-300 transition p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-5 space-y-5">
            {{-- Temperature --}}
            <div>
                <label class="block text-sm font-medium text-stone-600 dark:text-stone-300 mb-1">
                    Temperature: <span class="font-bold text-amber-600" x-text="settingsForm.temperature"></span>
                </label>
                <input type="range" x-model="settingsForm.temperature" min="0" max="2" step="0.1"
                       class="w-full accent-amber-500">
                <div class="flex justify-between text-xs text-stone-400 dark:text-stone-500 mt-0.5">
                    <span>0.0 (Presisi)</span>
                    <span>2.0 (Kreatif)</span>
                </div>
            </div>

            {{-- Max Tokens --}}
            <div>
                <label class="block text-sm font-medium text-stone-600 dark:text-stone-300 mb-1">Max Tokens</label>
                <input type="number" x-model="settingsForm.max_tokens" min="100" max="8192"
                       class="w-full rounded-lg border-stone-300 dark:border-stone-600 bg-stone-50 dark:bg-stone-700 text-stone-700 dark:text-stone-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-400">
                <p class="text-xs text-stone-400 dark:text-stone-500 mt-0.5">Maksimal token yang dihasilkan AI (100 - 8192)</p>
            </div>

            {{-- System Prompt --}}
            <div>
                <label class="block text-sm font-medium text-stone-600 dark:text-stone-300 mb-1">System Prompt <span class="text-stone-400 font-normal">(opsional)</span></label>
                <textarea x-model="settingsForm.system_prompt"
                          placeholder="Contoh: Kamu adalah ahli matematika yang sabar..."
                          class="w-full rounded-lg border-stone-300 dark:border-stone-600 bg-stone-50 dark:bg-stone-700 text-stone-700 dark:text-stone-200 px-3 py-2 text-sm focus:border-amber-400 focus:ring-amber-400 resize-none"
                          rows="3"></textarea>
                <p class="text-xs text-stone-400 dark:text-stone-500 mt-0.5">Instruksi tambahan untuk AI (maks 1000 karakter)</p>
            </div>

            {{-- Language --}}
            <div>
                <label class="block text-sm font-medium text-stone-600 dark:text-stone-300 mb-2">Bahasa Jawaban</label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 px-4 py-2.5 rounded-lg border cursor-pointer transition text-sm"
                           :class="settingsForm.language === 'indonesia'
                               ? 'border-amber-400 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300'
                               : 'border-stone-300 dark:border-stone-600 text-stone-500 dark:text-stone-400 hover:border-stone-400 dark:hover:border-stone-500'">
                        <input type="radio" x-model="settingsForm.language" value="indonesia" class="sr-only">
                        Indonesia
                    </label>
                    <label class="flex items-center gap-2 px-4 py-2.5 rounded-lg border cursor-pointer transition text-sm"
                           :class="settingsForm.language === 'english'
                               ? 'border-amber-400 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300'
                               : 'border-stone-300 dark:border-stone-600 text-stone-500 dark:text-stone-400 hover:border-stone-400 dark:hover:border-stone-500'">
                        <input type="radio" x-model="settingsForm.language" value="english" class="sr-only">
                        English
                    </label>
                </div>
            </div>

            {{-- Answer Length --}}
            <div>
                <label class="block text-sm font-medium text-stone-600 dark:text-stone-300 mb-2">Panjang Jawaban</label>
                <div class="flex gap-2">
                    <template x-for="(label, key) in { short: 'Singkat', medium: 'Sedang', detailed: 'Detail' }" :key="key">
                        <label class="flex-1 text-center px-3 py-2.5 rounded-lg border cursor-pointer transition text-sm"
                               :class="settingsForm.answer_length === key
                                   ? 'border-amber-400 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300'
                                   : 'border-stone-300 dark:border-stone-600 text-stone-500 dark:text-stone-400 hover:border-stone-400 dark:hover:border-stone-500'">
                            <input type="radio" :value="key" x-model="settingsForm.answer_length" class="sr-only">
                            <span x-text="label"></span>
                        </label>
                    </template>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 p-5 border-t border-stone-200 dark:border-stone-700">
            <button @click="closeSettings()"
                    class="px-4 py-2 text-sm font-medium text-stone-500 dark:text-stone-400 hover:text-stone-700 dark:hover:text-stone-200 transition">
                Batal
            </button>
            <button @click="saveSettings()"
                    class="px-5 py-2 text-sm font-medium text-white bg-amber-500 hover:bg-amber-600 rounded-lg transition shadow-md shadow-amber-200 dark:shadow-amber-900/30">
                Simpan Pengaturan
            </button>
        </div>
    </div>
</div>
