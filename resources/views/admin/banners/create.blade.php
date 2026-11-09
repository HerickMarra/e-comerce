<x-admin-layout>
    @section('page_title', 'Novo Banner')

    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.banners.index') }}"
                    class="p-2 text-slate-400 hover:text-slate-700 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 transition-colors">
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Adicionar Novo Banner</h1>
                    <p class="text-sm text-slate-500 mt-1">Cadastre um vídeo ou imagem para a rotação automática da home</p>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl text-sm font-bold">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8"
            x-data="{ mediaType: 'video' }">
            @csrf

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm p-8 space-y-8">
                {{-- Tipo de Mídia --}}
                <div>
                    <label class="text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 mb-3 block italic">
                        Tipo de Banner
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="flex items-center gap-4 p-5 border-2 rounded-2xl cursor-pointer transition-all"
                            :class="mediaType === 'video' ? 'border-primary bg-primary/5' : 'border-slate-100 bg-slate-50/50 hover:border-slate-200'">
                            <input type="radio" name="media_type" value="video" class="hidden" x-model="mediaType">
                            <div class="size-10 rounded-xl flex items-center justify-center"
                                :class="mediaType === 'video' ? 'bg-primary/20 text-primary' : 'bg-slate-200 text-slate-400'">
                                <span class="material-symbols-outlined">smart_display</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">Vídeo de Fundo</p>
                                <p class="text-xs text-slate-500">YouTube, Vimeo ou arquivo MP4</p>
                            </div>
                        </label>

                        <label class="flex items-center gap-4 p-5 border-2 rounded-2xl cursor-pointer transition-all"
                            :class="mediaType === 'image' ? 'border-primary bg-primary/5' : 'border-slate-100 bg-slate-50/50 hover:border-slate-200'">
                            <input type="radio" name="media_type" value="image" class="hidden" x-model="mediaType">
                            <div class="size-10 rounded-xl flex items-center justify-center"
                                :class="mediaType === 'image' ? 'bg-primary/20 text-primary' : 'bg-slate-200 text-slate-400'">
                                <span class="material-symbols-outlined">image</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">Apenas Imagem</p>
                                <p class="text-xs text-slate-500">Banner estático de alta resolução</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Título --}}
                <div>
                    <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 block">
                        Título / Identificador Interno
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary transition-all"
                        placeholder="Ex: Treino Corrida Masculino 01">
                </div>

                {{-- Bloco de Vídeo --}}
                <div x-show="mediaType === 'video'" class="space-y-6 p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">videocam</span>
                        Configuração do Vídeo
                    </h3>

                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 block">
                            Link do YouTube ou URL do Vídeo
                        </label>
                        <input type="text" name="video_url" value="{{ old('video_url') }}"
                            class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary transition-all font-mono"
                            placeholder="https://www.youtube.com/watch?v=...">
                        <p class="text-[11px] text-slate-400 mt-1">Aceita links normais do YouTube (ex: https://youtube.com/watch?v=...) ou links diretos .mp4.</p>
                    </div>

                    <div class="relative flex items-center py-2">
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                        <span class="flex-shrink mx-4 text-xs font-bold text-slate-400 uppercase">Ou envie um arquivo MP4</span>
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 block">
                            Upload de Vídeo (.mp4, .webm)
                        </label>
                        <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:opacity-90 cursor-pointer">
                    </div>
                </div>

                {{-- Imagem de Fundo / Poster --}}
                <div class="space-y-6 p-6 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">image</span>
                        <span x-text="mediaType === 'video' ? 'Imagem de Capa (Placeholder enquanto o vídeo carrega)' : 'Imagem do Banner'"></span>
                    </h3>

                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 block">
                            Upload de Imagem (Recomendado 1920x1080)
                        </label>
                        <input type="file" name="image_file" accept="image/*"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:opacity-90 cursor-pointer">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 block">
                            Ou URL Externa da Imagem
                        </label>
                        <input type="text" name="image_url" value="{{ old('image_url') }}"
                            class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary transition-all font-mono"
                            placeholder="https://images.unsplash.com/...">
                    </div>
                </div>

                {{-- Botão de Ação --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 block">
                            Texto do Botão
                        </label>
                        <input type="text" name="btn_text" value="{{ old('btn_text', 'Ver Catálogo') }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary transition-all">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 block">
                            Link do Botão
                        </label>
                        <input type="text" name="btn_link" value="{{ old('btn_link', '/categorias') }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary transition-all">
                    </div>
                </div>

                {{-- Status --}}
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-primary focus:ring-primary/20 size-5">
                        <div>
                            <span class="text-sm font-bold text-slate-900 dark:text-white block">Banner Ativo</span>
                            <span class="text-xs text-slate-500">Incluir este banner no sorteio aleatório ao carregar a página inicial.</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('admin.banners.index') }}"
                    class="px-6 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold text-sm hover:bg-slate-50 transition-all">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-primary text-white rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all">
                    Salvar Banner
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>

