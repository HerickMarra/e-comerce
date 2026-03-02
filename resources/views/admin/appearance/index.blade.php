<x-admin-layout>
    @section('page_title', 'Aparência da Loja')

    <div class="mb-8">
        <h2 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Aparência da Home</h2>
        <p class="text-slate-500 text-lg mt-2">MCustomize os textos, botões e elementos visuais da página inicial.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl flex justify-between items-center"
            x-data="{ show: true }" x-show="show">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-500">check_circle</span>
                <span class="font-medium font-bold text-sm">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
    @endif

    <form action="{{ route('admin.appearance.update') }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Seção Hero --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-8">
            <div class="flex items-center gap-4 mb-8">
                <div
                    class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <span class="material-symbols-outlined text-2xl">web</span>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">Seção Principal (Hero Card)</h3>
                    <p class="text-slate-500 text-sm mt-1">Configure o topo da página inicial.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Textos -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Tag/Selo (Ex:
                            Coleção 2024)</label>
                        <input type="text" name="home_hero_tag"
                            value="{{ old('home_hero_tag', $settings['home_hero_tag']) }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Título
                            Principal</label>
                        <input type="text" name="home_hero_title"
                            value="{{ old('home_hero_title', $settings['home_hero_title']) }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">
                        <p class="text-xs text-slate-500 mt-1">Dica: Use a tag <code>&lt;br&gt;</code> para forçar
                            quebras de linha estéticas.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Subtítulo /
                            Descrição</label>
                        <textarea name="home_hero_subtitle" rows="3"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">{{ old('home_hero_subtitle', $settings['home_hero_subtitle']) }}</textarea>
                    </div>
                </div>

                <!-- Botão e Mídia -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Link do Botão
                            Secundário</label>
                        <input type="text" name="home_hero_btn_link"
                            value="{{ old('home_hero_btn_link', $settings['home_hero_btn_link']) }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Texto do Botão
                            Secundário</label>
                        <input type="text" name="home_hero_btn_text"
                            value="{{ old('home_hero_btn_text', $settings['home_hero_btn_text']) }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">URL Embed do
                            Vídeo (YouTube)</label>
                        <textarea name="home_hero_video_url" rows="3"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all font-mono text-xs">{{ old('home_hero_video_url', $settings['home_hero_video_url']) }}</textarea>
                        <p class="text-xs text-slate-500 mt-1">Cole a URL do `src` inteiro do Iframe com todos os
                            parâmetros desejados.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">URL Imagem
                            Placeholder (Fallback)</label>
                        <input type="text" name="home_hero_image_fallback"
                            value="{{ old('home_hero_image_fallback', $settings['home_hero_image_fallback']) }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-4">
            <button type="submit"
                class="bg-primary text-white font-bold px-8 py-3.5 rounded-xl hover:scale-105 transition-all shadow-lg hover:shadow-primary/30 flex items-center gap-2">
                <span class="material-symbols-outlined">save</span>
                Salvar Configurações
            </button>
        </div>
    </form>
</x-admin-layout>