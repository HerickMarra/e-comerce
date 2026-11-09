<x-storefront-layout :seo="$seo">
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-16 min-h-screen"
        x-data="{ search: '' }">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-6">
            <a href="/" class="hover:text-primary transition-colors flex items-center gap-1">
                <span class="material-symbols-outlined text-sm">home</span>
                Início
            </a>
            <span class="text-slate-300 dark:text-slate-700">/</span>
            <span class="text-slate-700 dark:text-slate-200">Categorias</span>
        </nav>

        {{-- Header Section --}}
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10 pb-8 border-b border-slate-100 dark:border-slate-800">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-widest mb-3">
                    <span class="material-symbols-outlined text-sm">category</span>
                    <span>Coleções & Marcas</span>
                </div>
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                    Nossas Categorias
                </h1>
                <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm sm:text-base max-w-xl">
                    Descubra os melhores modelos, lançamentos e coleções selecionadas para você.
                </p>
            </div>

            {{-- Live Search Input & Counter --}}
            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-72">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                    <input type="text" x-model="search" placeholder="Filtrar categorias..."
                        class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all shadow-sm">
                    <button type="button" x-show="search" @click="search = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                </div>
                <span class="hidden sm:inline-flex px-3.5 py-2.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold whitespace-nowrap">
                    {{ $categories->count() }} {{ Str::plural('marca', $categories->count()) }}
                </span>
            </div>
        </header>

        {{-- Categories Grid: 2 per row on mobile, 3 on tablet, 3-4 on desktop --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
            @foreach($categories as $category)
                @php
                    $imgUrl = '';
                    if ($category->image_path) {
                        $imgUrl = str_starts_with($category->image_path, 'http')
                            ? $category->image_path
                            : asset('storage/' . $category->image_path);
                    }
                @endphp

                <a href="{{ route('search', ['categories' => [$category->id]]) }}"
                    x-show="'{{ strtolower(addslashes($category->name)) }}'.includes(search.toLowerCase().trim())"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="group relative aspect-[3/4] bg-slate-900 rounded-2xl sm:rounded-3xl overflow-hidden shadow-md hover:shadow-2xl hover:shadow-primary/20 transition-all duration-500 border border-slate-100/10 flex flex-col justify-between">

                    {{-- Background Image with Zoom Effect --}}
                    @if($imgUrl)
                        <img src="{{ $imgUrl }}" alt="{{ $category->name }}"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-110">
                    @else
                        <div class="absolute inset-0 flex items-center justify-center bg-gradient-to-br from-slate-800 to-slate-950">
                            <span class="material-symbols-outlined text-5xl sm:text-7xl text-slate-700">category</span>
                        </div>
                    @endif

                    {{-- Gradient Overlay (Darkens on hover for crisp contrast) --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/40 to-black/20 group-hover:from-black/95 group-hover:via-black/50 transition-colors duration-500 z-10"></div>

                    {{-- Top Pill: Tag / Status --}}
                    <div class="relative z-20 p-4 sm:p-5 flex items-start justify-between">
                        <span class="px-2.5 py-1 bg-white/20 backdrop-blur-md text-white text-[10px] sm:text-xs font-bold uppercase tracking-wider rounded-lg border border-white/20 shadow-sm">
                            @if($category->products_count > 0)
                                {{ $category->products_count }} {{ Str::plural('item', $category->products_count) }}
                            @else
                                Coleção
                            @endif
                        </span>
                        <div class="size-8 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center text-white opacity-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-300">
                            <span class="material-symbols-outlined text-sm">arrow_outward</span>
                        </div>
                    </div>

                    {{-- Bottom Content: Title & Call to Action --}}
                    <div class="relative z-20 p-4 sm:p-6 translate-y-1 group-hover:translate-y-0 transition-transform duration-300">
                        <h2 class="text-lg sm:text-2xl md:text-3xl font-black text-white uppercase tracking-tight leading-tight mb-2 drop-shadow group-hover:text-primary transition-colors">
                            {{ $category->name }}
                        </h2>

                        @if($category->description)
                            <p class="text-xs text-white/70 line-clamp-2 hidden sm:block mb-3 font-normal leading-relaxed">
                                {{ $category->description }}
                            </p>
                        @endif

                        <div class="inline-flex items-center gap-1.5 text-white/90 group-hover:text-white text-xs font-bold uppercase tracking-wider transition-colors pt-1">
                            <span>Ver Coleção</span>
                            <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Empty Search Result Message --}}
        <div x-show="search && $el.previousElementSibling.querySelectorAll('a[style*=\'display: none\']').length === {{ $categories->count() }}"
            x-cloak class="py-24 text-center">
            <span class="material-symbols-outlined text-5xl text-slate-300 dark:text-slate-700 mb-3 block">search_off</span>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-1">Nenhuma categoria encontrada</h3>
            <p class="text-sm text-slate-500 mb-4">Não encontramos nenhuma categoria correspondente a "<span x-text="search" class="font-bold"></span>".</p>
            <button type="button" @click="search = ''" class="text-primary text-xs font-bold hover:underline">
                Limpar busca
            </button>
        </div>

        {{-- Empty State (No categories in database) --}}
        @if($categories->isEmpty())
            <div class="py-32 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 p-8">
                <span class="material-symbols-outlined text-7xl text-slate-300 mb-4 block">category</span>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Nenhuma categoria cadastrada</h3>
                <p class="text-slate-500 max-w-sm mx-auto text-sm mb-6">Em breve novas marcas e coleções serão disponibilizadas por aqui.</p>
                <a href="/" class="inline-block bg-primary text-white font-bold py-3 px-8 rounded-xl hover:shadow-lg hover:shadow-primary/20 transition-all text-sm">
                    Voltar para a Página Inicial
                </a>
            </div>
        @endif
    </main>
</x-storefront-layout>
