<x-admin-layout>
    @section('page_title', 'Banners & Vídeos da Home')

    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-3xl">view_carousel</span>
                Banners da Home
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Adicione múltiplos vídeos (YouTube ou MP4) e imagens. A cada acesso ou recarregamento da loja, um banner ativo é sorteado aleatoriamente.
            </p>
        </div>
        <a href="{{ route('admin.banners.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary text-white text-sm font-bold rounded-xl shadow-lg shadow-primary/20 transition-all hover:scale-105">
            <span class="material-symbols-outlined text-lg">add</span>
            Adicionar Novo Banner
        </a>
    </div>

    {{-- Info Alert --}}
    <div class="mb-6 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-2xl flex items-start gap-4">
        <div class="size-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0 mt-0.5">
            <span class="material-symbols-outlined">shuffle</span>
        </div>
        <div>
            <h4 class="text-xs font-bold uppercase tracking-wider text-blue-900 mb-1">Randomização Automática</h4>
            <p class="text-xs text-blue-700 leading-relaxed">
                Você pode cadastrar quantos vídeos e imagens desejar. Todos os itens marcados com status <strong>Ativo</strong> entrarão no sorteio aleatório a cada reload da página inicial.
            </p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
            class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl flex items-center gap-3 text-sm font-bold">
            <span class="material-symbols-outlined">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                    <th class="text-left px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Preview</th>
                    <th class="text-left px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Título / Identificação</th>
                    <th class="text-left px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Tipo</th>
                    <th class="text-left px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Origem da Mídia</th>
                    <th class="text-center px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                    <th class="text-right px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 dark:divide-slate-800">
                @forelse($banners as $banner)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="w-24 h-14 rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 relative border border-slate-200 dark:border-slate-700">
                                @if($banner->effective_image_url)
                                    <img src="{{ $banner->effective_image_url }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-400">
                                        <span class="material-symbols-outlined">{{ $banner->media_type === 'video' ? 'videocam' : 'image' }}</span>
                                    </div>
                                @endif
                                @if($banner->media_type === 'video')
                                    <div class="absolute bottom-1 right-1 bg-black/70 text-white rounded px-1.5 py-0.5 text-[9px] font-bold flex items-center gap-0.5">
                                        <span class="material-symbols-outlined text-[10px]">play_arrow</span>
                                        <span>VÍDEO</span>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-900 dark:text-white block">{{ $banner->title ?: 'Sem título' }}</span>
                            <span class="text-xs text-slate-400 block mt-0.5">Botão: {{ $banner->btn_text }} &rarr; {{ $banner->btn_link }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($banner->media_type === 'video')
                                <span class="px-3 py-1 bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">smart_display</span>
                                    Vídeo
                                </span>
                            @else
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 rounded-full text-xs font-bold inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">image</span>
                                    Imagem
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 max-w-xs">
                            @if($banner->video_file)
                                <span class="text-xs font-mono text-slate-600 dark:text-slate-400 block truncate" title="{{ $banner->video_file }}">
                                    📁 Arquivo MP4: {{ basename($banner->video_file) }}
                                </span>
                            @elseif($banner->video_url)
                                <a href="{{ $banner->video_url }}" target="_blank" class="text-xs font-mono text-primary hover:underline block truncate" title="{{ $banner->video_url }}">
                                    🔗 {{ $banner->video_url }}
                                </a>
                            @elseif($banner->image_file)
                                <span class="text-xs font-mono text-slate-600 dark:text-slate-400 block truncate">
                                    📁 Arquivo: {{ basename($banner->image_file) }}
                                </span>
                            @elseif($banner->image_url)
                                <a href="{{ $banner->image_url }}" target="_blank" class="text-xs font-mono text-primary hover:underline block truncate">
                                    🔗 {{ $banner->image_url }}
                                </a>
                            @else
                                <span class="text-xs text-slate-400 italic">Nenhuma mídia informada</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.banners.toggle-status', $banner) }}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" 
                                    class="px-3 py-1 rounded-full text-xs font-bold transition-all {{ $banner->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                    {{ $banner->is_active ? '● Ativo' : '○ Inativo' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.banners.edit', $banner) }}"
                                    class="p-2 text-slate-400 hover:text-slate-900 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                                    title="Editar">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </a>
                                <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" 
                                    onsubmit="return confirm('Tem certeza que deseja excluir este banner?');" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                        title="Excluir">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <span class="material-symbols-outlined text-4xl mb-2 text-slate-300 block">movie</span>
                            Nenhum banner cadastrado ainda. Clique em "Adicionar Novo Banner" acima.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-admin-layout>

