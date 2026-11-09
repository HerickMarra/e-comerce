<x-admin-layout>
    @section('page_title', 'Editar Banner')

    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 sm:mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.banners.index') }}"
                    class="p-2 text-slate-400 hover:text-slate-700 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 transition-colors shrink-0">
                    <span class="material-symbols-outlined text-lg">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white">Editar Banner #{{ $banner->id }}</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Atualize as informações do vídeo ou imagem do banner</p>
                </div>
            </div>

            <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST"
                onsubmit="return confirm('Tem certeza que deseja excluir este banner?');" class="w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full sm:w-auto justify-center px-4 py-2.5 bg-red-50 text-red-600 rounded-xl text-xs font-bold hover:bg-red-100 transition-colors flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-base">delete</span>
                    Excluir Banner
                </button>
            </form>
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

        <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="space-y-8"
            x-data="{ mediaType: '{{ old('media_type', $banner->media_type) }}' }">
            @csrf
            @method('PUT')

            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm p-5 sm:p-8 space-y-8">
                {{-- Preview Atual --}}
                @if($banner->effective_image_url || $banner->effective_video_url)
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row items-center gap-4">
                        <div class="w-40 h-24 rounded-xl overflow-hidden bg-black relative flex-shrink-0">
                            @if($banner->effective_image_url)
                                <img src="{{ $banner->effective_image_url }}" alt="Preview" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div class="flex-1 text-xs space-y-1">
                            <span class="font-bold text-slate-800 dark:text-slate-200 block text-sm">{{ $banner->title ?: 'Sem título' }}</span>
                            <p class="text-slate-500">Tipo: <strong class="uppercase text-slate-700 dark:text-slate-300">{{ $banner->media_type }}</strong></p>
                            @if($banner->video_file)
                                <p class="text-slate-500 font-mono">Arquivo MP4: {{ basename($banner->video_file) }}</p>
                            @elseif($banner->video_url)
                                <p class="text-slate-500 font-mono truncate max-w-md">Vídeo: {{ $banner->video_url }}</p>
                            @endif
                        </div>
                    </div>
                @endif

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
                    <input type="text" name="title" value="{{ old('title', $banner->title) }}"
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
                        <input type="text" name="video_url" value="{{ old('video_url', $banner->video_url) }}"
                            class="w-full bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary transition-all font-mono"
                            placeholder="https://www.youtube.com/watch?v=...">
                    </div>

                    <div class="relative flex items-center py-2">
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                        <span class="flex-shrink mx-4 text-xs font-bold text-slate-400 uppercase">Ou substitua com um arquivo MP4</span>
                        <div class="flex-grow border-t border-slate-200 dark:border-slate-700"></div>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 block">
                            Subir Novo Arquivo de Vídeo (.mp4)
                        </label>
                        <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg,video/quicktime"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:opacity-90 cursor-pointer">
                        @if($banner->video_file)
                            <label class="inline-flex items-center gap-2 mt-2 text-xs text-red-500 cursor-pointer">
                                <input type="checkbox" name="remove_video_file" value="1" class="rounded text-red-500">
                                Remover arquivo atual de vídeo ({{ basename($banner->video_file) }})
                            </label>
                        @endif
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
                            Subir Nova Imagem
                        </label>
                        <input type="file" name="image_file" accept="image/*"
                            class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-900 file:text-white hover:file:opacity-90 cursor-pointer">
                        @if($banner->image_file)
                            <label class="inline-flex items-center gap-2 mt-2 text-xs text-red-500 cursor-pointer">
                                <input type="checkbox" name="remove_image_file" value="1" class="rounded text-red-500">
                                Remover imagem enviada ({{ basename($banner->image_file) }})
                            </label>
                        @endif
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5 block">
                            Ou URL Externa da Imagem
                        </label>
                        <input type="text" name="image_url" value="{{ old('image_url', $banner->image_url) }}"
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
                        <input type="text" name="btn_text" value="{{ old('btn_text', $banner->btn_text) }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary transition-all">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-2 block">
                            Link do Botão
                        </label>
                        <input type="text" name="btn_link" value="{{ old('btn_link', $banner->btn_link) }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-sm focus:ring-primary focus:border-primary transition-all">
                    </div>
                </div>

                {{-- Status --}}
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }} class="rounded border-slate-300 text-primary focus:ring-primary/20 size-5">
                        <div>
                            <span class="text-sm font-bold text-slate-900 dark:text-white block">Banner Ativo</span>
                            <span class="text-xs text-slate-500">Incluir este banner no sorteio aleatório ao carregar a página inicial.</span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4">
                <a href="{{ route('admin.banners.index') }}"
                    class="px-6 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold text-sm hover:bg-slate-50 transition-all text-center w-full sm:w-auto">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-primary text-white rounded-xl font-bold text-sm shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all text-center w-full sm:w-auto">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>

