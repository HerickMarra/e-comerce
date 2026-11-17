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

    @php
        $currentLoginImg = $settings['auth_login_image'] ?? '';
        $loginPreviewUrl = $currentLoginImg 
            ? (str_starts_with($currentLoginImg, 'http') ? $currentLoginImg : asset('storage/' . $currentLoginImg))
            : 'https://images.unsplash.com/photo-1542282088-72c9c27ed0cd?auto=format&fit=crop&q=80&w=2000';
    @endphp

    <form action="{{ route('admin.appearance.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Seção Hero --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-5 sm:p-8">
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
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">URL do Vídeo
                            (YouTube)</label>
                        <input type="text" name="home_hero_video_url"
                            value="{{ old('home_hero_video_url', $settings['home_hero_video_url']) }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all font-mono text-sm"
                            placeholder="Ex: https://www.youtube.com/watch?v=uFZRqTRk8-o">
                        <p class="text-xs text-slate-500 mt-1">Basta colar o link normal do vídeo do YouTube. O sistema
                            cuidará da formatação para o fundo do site.</p>
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

        {{-- Seção: Imagem da Tela de Login / Cadastro --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-5 sm:p-8"
             x-data="{
                 loginPreview: '{{ $loginPreviewUrl }}',
                 hasCustomImage: {{ !empty($currentLoginImg) ? 'true' : 'false' }},
                 loginBadge: '{{ addslashes($settings['auth_login_badge'] ?? 'Acesso Exclusivo') }}',
                 loginTitle: '{{ addslashes($settings['auth_login_title'] ?? 'Sua próxima missão começa aqui.') }}',
                 loginSubtitle: '{{ addslashes($settings['auth_login_subtitle'] ?? 'Equipamentos de airsoft, pescaria, barracas e vestuário tático.') }}',
                 imageUrl: '{{ str_starts_with($currentLoginImg, 'http') ? $currentLoginImg : '' }}',
                 handleFileUpload(e) {
                     let file = e.target.files[0];
                     if (file) {
                         let reader = new FileReader();
                         reader.onload = ev => {
                             this.loginPreview = ev.target.result;
                             this.hasCustomImage = true;
                             $refs.removeLoginImg.value = '0';
                         };
                         reader.readAsDataURL(file);
                     }
                 },
                 updateFromUrl() {
                     if (this.imageUrl.trim().length > 5) {
                         this.loginPreview = this.imageUrl.trim();
                         this.hasCustomImage = true;
                         $refs.removeLoginImg.value = '0';
                     }
                 },
                 resetToDefault() {
                     this.loginPreview = 'https://images.unsplash.com/photo-1542282088-72c9c27ed0cd?auto=format&fit=crop&q=80&w=2000';
                     this.hasCustomImage = false;
                     this.imageUrl = '';
                     if ($refs.loginFileInput) $refs.loginFileInput.value = '';
                     $refs.removeLoginImg.value = '1';
                 }
             }">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <span class="material-symbols-outlined text-2xl">wallpaper</span>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Imagem da Tela de Login & Cadastro</h3>
                        <p class="text-slate-500 text-sm mt-1">Personalize a foto de destaque lateral e textos exibidos na página de autenticação dos clientes.</p>
                    </div>
                </div>
                <a href="{{ route('login') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs transition-colors shrink-0">
                    <span class="material-symbols-outlined text-sm">open_in_new</span>
                    <span>Ver Tela de Login</span>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                {{-- Coluna 1: Preview Realista da Tela de Login --}}
                <div class="lg:col-span-5 space-y-3">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">
                        Pré-visualização ao Vivo
                    </label>
                    <div class="relative w-full aspect-[4/5] rounded-3xl overflow-hidden shadow-2xl border border-slate-200 dark:border-slate-800 bg-slate-950 flex flex-col justify-end p-6 select-none group">
                        {{-- Imagem de Fundo com Fade --}}
                        <img :src="loginPreview"
                             alt="Banner de Login"
                             class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                             onerror="this.src='https://images.unsplash.com/photo-1542282088-72c9c27ed0cd?auto=format&fit=crop&q=80&w=2000'">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/45 to-black/20"></div>

                        {{-- Textos Sobrepostos Dinâmicos --}}
                        <div class="relative z-10 space-y-3 text-white">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md border border-white/20 text-white text-[10px] font-black uppercase tracking-widest">
                                <span class="material-symbols-outlined text-xs text-primary">verified</span>
                                <span x-text="loginBadge || 'Acesso Exclusivo'"></span>
                            </div>
                            
                            <blockquote class="text-xl sm:text-2xl font-black leading-snug drop-shadow-md">
                                "<span x-text="loginTitle || 'Sua próxima missão começa aqui.'"></span>"
                            </blockquote>

                            <p class="text-xs text-slate-300 font-medium leading-relaxed line-clamp-2"
                               x-text="loginSubtitle || 'Equipamentos táticos e artigos de aventura.'"></p>

                            <p class="pt-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">
                                — {{ $appSettings['store_name'] ?? config('app.name') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-1 text-xs">
                        <span class="text-slate-400">Resolução recomendada: <strong>1920x1080px</strong> ou vertical</span>
                        <template x-if="hasCustomImage">
                            <button type="button" @click="resetToDefault()"
                                    class="text-red-500 hover:text-red-700 font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">restart_alt</span>
                                <span>Restaurar Padrão</span>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Coluna 2: Formulário de Configuração da Imagem e Textos --}}
                <div class="lg:col-span-7 space-y-6">
                    {{-- Input de Upload de Imagem --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Enviar Arquivo de Imagem (PNG, JPG, WEBP)
                        </label>
                        <div class="flex items-center gap-3">
                            <label class="flex-1 flex flex-col items-center justify-center p-5 border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-primary dark:hover:border-primary rounded-2xl cursor-pointer bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100/50 dark:hover:bg-slate-800 transition-all">
                                <span class="material-symbols-outlined text-3xl text-slate-400 mb-1">cloud_upload</span>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-200">Clique para selecionar imagem do seu computador</span>
                                <span class="text-[10px] text-slate-400 mt-0.5">JPG, PNG, WEBP (máx. 10MB)</span>
                                <input type="file" name="auth_login_image" x-ref="loginFileInput" @change="handleFileUpload($event)" accept="image/*" class="hidden">
                            </label>
                        </div>
                    </div>

                    {{-- Ou URL Externa --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                            Ou utilize uma URL Externa (Link de Imagem)
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="url" name="auth_login_image_url" x-model="imageUrl" @input="updateFromUrl()"
                                   placeholder="https://images.unsplash.com/... ou link direto da imagem"
                                   class="flex-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-slate-900 dark:text-white text-xs sm:text-sm font-mono focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all">
                            <button type="button" @click="updateFromUrl()"
                                    class="px-4 py-3 bg-slate-900 dark:bg-slate-700 text-white rounded-xl text-xs font-bold hover:bg-slate-800 transition-colors shrink-0">
                                Aplicar URL
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1.5">Você pode colar um link direto de qualquer imagem pública da internet.</p>
                    </div>

                    <input type="hidden" name="remove_auth_login_image" x-ref="removeLoginImg" value="0">

                    {{-- Textos Opcionais da Tela de Login --}}
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-4">
                        <span class="text-xs font-black uppercase tracking-wider text-slate-400 block">
                            Frases de Destaque no Login & Registro
                        </span>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Selo / Badge (Ex: Acesso Exclusivo)
                                </label>
                                <input type="text" name="auth_login_badge" x-model="loginBadge"
                                       value="{{ old('auth_login_badge', $settings['auth_login_badge']) }}"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                    Frase Principal / Título
                                </label>
                                <input type="text" name="auth_login_title" x-model="loginTitle"
                                       value="{{ old('auth_login_title', $settings['auth_login_title']) }}"
                                       class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                                Subtítulo / Mensagem Descritiva
                            </label>
                            <textarea name="auth_login_subtitle" x-model="loginSubtitle" rows="2"
                                      class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs font-medium text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-primary outline-none leading-relaxed">{{ old('auth_login_subtitle', $settings['auth_login_subtitle']) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end">
            <button type="submit"
                class="w-full sm:w-auto justify-center bg-primary text-white font-bold px-8 py-3.5 rounded-xl hover:scale-105 transition-all shadow-lg hover:shadow-primary/30 flex items-center gap-2">
                <span class="material-symbols-outlined">save</span>
                Salvar Configurações
            </button>
        </div>
    </form>
</x-admin-layout>