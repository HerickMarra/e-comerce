<x-storefront-layout :seo="$seo">
    <style>
        .touch-continuous-wrapper {
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
            overscroll-behavior-x: contain;
            position: relative;
            width: 100%;
            padding-bottom: 1rem;
            cursor: grab;
            user-select: none;
            -webkit-user-select: none;
        }
        .touch-continuous-wrapper:active {
            cursor: grabbing;
        }
        .touch-continuous-wrapper::-webkit-scrollbar {
            display: none;
        }

        @media (max-width: 767px) {
            .touch-continuous-wrapper {
                mask-image: linear-gradient(to right, transparent 0%, black 3%, black 97%, transparent 100%);
                -webkit-mask-image: linear-gradient(to right, transparent 0%, black 3%, black 97%, transparent 100%);
            }
        }

        @media (min-width: 768px) {
            .touch-continuous-wrapper {
                overflow: visible !important;
                mask-image: none !important;
                -webkit-mask-image: none !important;
                cursor: default;
                user-select: auto;
                -webkit-user-select: auto;
            }
        }
    </style>

    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
        <div class="relative h-[300px] sm:h-[420px] md:h-[600px] w-full rounded-3xl overflow-hidden mb-10 md:mb-16 shadow-2xl group"
             x-data="{ videoPlaying: false }"
             @hero-video-active.window="videoPlaying = true">
            
            {{-- Gradiente protetor com link para o catálogo (bloqueia 100% de toques/cliques no iframe do YouTube) --}}
            <a href="{{ $heroSettings['btn_link'] }}"
               class="absolute inset-0 bg-gradient-to-b md:bg-gradient-to-r from-black/75 via-black/40 to-transparent z-10 transition-opacity group-hover:opacity-70 cursor-pointer block"
               aria-label="{{ $heroSettings['btn_text'] ?? 'Explorar Catálogo' }}">
            </a>
            
            <!-- Placeholder / Image Banner: Permanece 100% visível cobrindo o player até o vídeo começar a rodar de verdade -->
            @if(!empty($heroSettings['image_fallback']))
                <img alt="{{ $heroSettings['title'] ?? ($appSettings['store_name'] ?? 'Banner Promocional') }}"
                    fetchpriority="high"
                    loading="eager"
                    decoding="async"
                    class="absolute inset-0 w-full h-full object-cover z-[5] transition-opacity duration-700 ease-out"
                    :class="(videoPlaying && '{{ !empty($heroSettings['video_url']) ? '1' : '' }}') ? 'opacity-0 pointer-events-none' : 'opacity-100'"
                    src="{{ $heroSettings['image_fallback'] }}" />
            @endif

            <!-- Video Background -->
            @if(!empty($heroSettings['video_url']))
                @if(!empty($heroSettings['is_direct_video']))
                    <video autoplay muted loop playsinline
                        @playing="videoPlaying = true"
                        class="absolute inset-0 w-full h-full object-cover pointer-events-none z-0">
                        <source src="{{ $heroSettings['video_url'] }}">
                    </video>
                @else
                    {{-- Container do Player com escala para cortar bordas do YouTube e opacidade controlada --}}
                    <div class="absolute inset-0 overflow-hidden pointer-events-none select-none z-0">
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[160%] h-[160%] min-w-[120%] min-h-[120%] pointer-events-none transition-opacity duration-700"
                             :class="videoPlaying ? 'opacity-100' : 'opacity-0'">
                            <iframe 
                                id="hero-yt-iframe"
                                class="w-full h-full object-cover pointer-events-none" 
                                src="{{ $heroSettings['video_url'] }}" 
                                frameborder="0" 
                                tabindex="-1"
                                aria-hidden="true"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                @endif
            @endif
            <div class="relative z-20 h-full flex flex-col justify-between py-6 md:py-10 px-6 md:px-14 max-w-2xl">
                <div>
                    {{-- No mobile exibe o logo sutil, no desktop uma tag discreta sem poluir com logo duplo --}}
                    @if($appSettings['store_logo'])
                        <img src="{{ str_starts_with($appSettings['store_logo'], 'http') ? $appSettings['store_logo'] : asset('storage/' . $appSettings['store_logo']) }}" 
                             alt="{{ $appSettings['store_name'] }}"
                             class="h-8 sm:h-10 w-auto object-contain drop-shadow md:hidden" />
                    @endif
                    <div class="hidden md:inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-black/40 backdrop-blur-md border border-white/20 text-white text-xs font-semibold tracking-wider select-none">
                        <span class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="uppercase tracking-widest text-[11px] font-bold">Lançamentos 2026</span>
                    </div>
                </div>

                {{-- Botão de Ação Único e Minimalista --}}
                <div>
                    <a href="{{ $heroSettings['btn_link'] }}"
                        class="inline-flex items-center gap-2.5 bg-white text-slate-900 hover:bg-slate-100 font-bold px-7 sm:px-8 py-3.5 sm:py-4 rounded-full shadow-2xl transition-all tracking-wide text-xs sm:text-sm uppercase hover:scale-[1.02] active:scale-[0.98]">
                        <span>{{ $heroSettings['btn_text'] ?? 'Explorar Catálogo' }}</span>
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Barra de Pesquisa Minimalista Flutuante (Estilo Cápsula Moderna) --}}
        <section class="relative -mt-5 sm:-mt-8 mb-12 sm:mb-16 z-30 max-w-2xl sm:max-w-3xl mx-auto px-3 sm:px-4">
            <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-2xl rounded-full p-1.5 sm:p-2 pl-4 sm:pl-6 border border-slate-200/80 dark:border-slate-800 shadow-xl shadow-slate-900/5 hover:border-slate-300 dark:hover:border-slate-700 transition-all">
                <form action="{{ route('search') }}" method="GET" class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-400 text-xl sm:text-2xl shrink-0 select-none">search</span>
                    
                    <input type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Buscar por modelo, marca (Nike, Adidas, Corre 3...), tamanho..."
                        class="w-full bg-transparent text-slate-900 dark:text-white placeholder-slate-400 text-xs sm:text-sm font-medium focus:outline-none border-none ring-0 focus:ring-0 p-0"
                        autocomplete="off" />

                    <button type="submit"
                        class="bg-primary hover:bg-primary/95 text-white font-bold text-xs sm:text-sm px-5 sm:px-6 py-2.5 sm:py-3 rounded-full transition-all shadow-md shadow-primary/20 flex items-center gap-1.5 shrink-0 uppercase tracking-wider select-none active:scale-[0.98]">
                        <span>Buscar</span>
                        <span class="material-symbols-outlined text-sm hidden sm:inline">arrow_forward</span>
                    </button>
                </form>
            </div>

            {{-- Tags em linha sutil e discreta --}}
            <div class="mt-3 flex items-center justify-center gap-2 text-[11px] text-slate-400 dark:text-slate-500 overflow-x-auto scrollbar-hide select-none">
                <span class="font-semibold text-slate-500 dark:text-slate-400 shrink-0">Em alta:</span>
                <div class="flex items-center gap-2 shrink-0">
                    @php
                        $trendingTags = ['Pegasus 40', 'Ultraboost Light', 'Corre 3', 'Vaporfly 3', 'Gel-Nimbus', 'Deviate Nitro'];
                    @endphp
                    @foreach($trendingTags as $tag)
                        <a href="{{ route('search', ['q' => $tag]) }}"
                           class="hover:text-primary transition-colors hover:underline">
                           {{ $tag }}
                        </a>
                        @if(!$loop->last)
                            <span class="text-slate-300 dark:text-slate-700 select-none">•</span>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section class="mb-20">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
                @forelse($categories as $category)
                    <div class="group cursor-pointer">
                        <a href="{{ route('search', ['categories' => [$category->id]]) }}" class="block">
                            <div class="relative aspect-[3/4] rounded-2xl overflow-hidden mb-4 shadow-lg">
                                @if($category->image_path)
                                    <img alt="Categoria de Tênis {{ $category->name }}"
                                        loading="lazy"
                                        decoding="async"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                        src="{{ str_starts_with($category->image_path, 'http') ? $category->image_path : asset('storage/' . $category->image_path) }}" />
                                @else
                                    <div class="w-full h-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-slate-300 text-5xl">category</span>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors"></div>
                                <div class="absolute bottom-6 left-6 text-white text-shadow">
                                    <h3 class="text-lg md:text-xl font-bold">{{ $category->name }}</h3>
                                    <p class="text-xs md:text-sm opacity-90">{{ $category->products_count ?? $category->products->count() }} Itens</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <p class="col-span-full text-center text-slate-500 py-10">Nenhuma categoria cadastrada ainda.</p>
                @endforelse
            </div>
        </section>


        <!-- Im Feeling Lucky -->
        <section class="mb-20 md:mb-24 mt-16 md:mt-28">
            <div class="flex items-center justify-between mb-8 md:mb-10">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">Estou com Sorte</h2>
                    <p class="text-slate-500 text-sm mt-1">Lançamentos exclusivos e modelos selecionados para seu estilo</p>
                </div>
                <div class="flex items-center justify-center size-12 rounded-2xl bg-amber-100 text-amber-600">
                    <span class="material-symbols-outlined text-2xl animate-bounce">casino</span>
                </div>
            </div>

            <div x-data="touchContinuousCarousel()"
                 x-init="initCarousel($el)"
                 class="touch-continuous-wrapper">
                <div class="flex md:grid md:grid-cols-2 lg:grid-cols-4 gap-5 md:gap-10 w-max md:w-full">
                    {{-- Grupo 1 (Exibido no desktop e primeira metade no mobile) --}}
                    @forelse($luckyProducts as $product)
                        <div class="group relative w-[260px] sm:w-[280px] md:w-auto shrink-0 md:shrink">
                            <a href="{{ route('product.show', $product->slug) }}" class="absolute inset-0 z-10" aria-label="Ver {{ $product->name }}"></a>
                            <div class="relative aspect-[3/4] rounded-3xl overflow-hidden bg-slate-50 dark:bg-slate-800 mb-5 border border-slate-100 dark:border-slate-800 shadow-sm">
                                @php 
                                    $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
                                @endphp
                                @if($mainImage)
                                    <img alt="Tênis {{ $product->name }}{{ $product->brand ? ' - ' . $product->brand : '' }}"
                                        loading="lazy"
                                        decoding="async"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                        src="{{ str_starts_with($mainImage->path, 'http') ? $mainImage->path : asset('storage/' . $mainImage->path) }}" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-slate-100 dark:bg-slate-800">
                                        <span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-6xl">image</span>
                                    </div>
                                @endif
                                
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                <div class="absolute bottom-5 inset-x-5 translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300 z-20">
                                    <a href="{{ route('product.show', $product->slug) }}"
                                        class="w-full bg-slate-900/90 backdrop-blur-md text-white border border-white/20 py-4 rounded-xl font-bold text-sm hover:bg-white hover:text-slate-900 transition-colors flex justify-center items-center">
                                        Quero este!
                                    </a>
                                </div>
                            </div>
                            <div class="flex justify-between items-start mb-2 px-2">
                                <h4 class="font-bold text-slate-900 dark:text-white line-clamp-1 text-base sm:text-lg">{{ $product->name }}</h4>
                            </div>
                            <div class="flex justify-between items-center px-2">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $product->categories->first()?->name ?? 'Tênis' }}</p>
                                <p class="font-black text-primary text-base sm:text-lg">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-10 text-center">
                            <p class="text-slate-500">Sem produtos no momento.</p>
                        </div>
                    @endforelse

                    {{-- Grupo 2 (Duplicata para loop infinito e contínuo no mobile, oculto no desktop) --}}
                    @if($luckyProducts->count() > 1)
                        @foreach($luckyProducts as $product)
                            <div class="group relative w-[260px] sm:w-[280px] shrink-0 md:hidden" aria-hidden="true">
                                <a href="{{ route('product.show', $product->slug) }}" class="absolute inset-0 z-10" aria-label="Ver {{ $product->name }}"></a>
                                <div class="relative aspect-[3/4] rounded-3xl overflow-hidden bg-slate-50 dark:bg-slate-800 mb-5 border border-slate-100 dark:border-slate-800 shadow-sm">
                                    @php 
                                        $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
                                    @endphp
                                    @if($mainImage)
                                        <img alt="Tênis {{ $product->name }}{{ $product->brand ? ' - ' . $product->brand : '' }}"
                                            loading="lazy"
                                            decoding="async"
                                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                            src="{{ str_starts_with($mainImage->path, 'http') ? $mainImage->path : asset('storage/' . $mainImage->path) }}" />
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 dark:bg-slate-800">
                                            <span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-6xl">image</span>
                                        </div>
                                    @endif
                                    
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                    <div class="absolute bottom-5 inset-x-5 translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300 z-20">
                                        <a href="{{ route('product.show', $product->slug) }}"
                                            class="w-full bg-slate-900/90 backdrop-blur-md text-white border border-white/20 py-4 rounded-xl font-bold text-sm hover:bg-white hover:text-slate-900 transition-colors flex justify-center items-center">
                                            Quero este!
                                        </a>
                                    </div>
                                </div>
                                <div class="flex justify-between items-start mb-2 px-2">
                                    <h4 class="font-bold text-slate-900 dark:text-white line-clamp-1 text-base sm:text-lg">{{ $product->name }}</h4>
                                </div>
                                <div class="flex justify-between items-center px-2">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $product->categories->first()?->name ?? 'Tênis' }}</p>
                                    <p class="font-black text-primary text-base sm:text-lg">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>

        <!-- Categorias Aleatórias -->
        @foreach($randomSections as $category)
            @if($category->section_products->count() > 0)
                <section class="mb-20 md:mb-24">
                    <div class="flex items-end justify-between mb-8 md:mb-10">
                        <div>
                            <h2 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ $category->name }}</h2>
                            <p class="text-slate-500 text-sm">Nossas melhores escolhas em {{ strtolower($category->name) }} para você</p>
                        </div>
                        <a href="{{ route('search', ['categories' => [$category->id]]) }}" class="text-primary font-bold text-sm hover:underline flex items-center gap-1 whitespace-nowrap">
                            Ver todos<span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>

                    <div x-data="touchContinuousCarousel()"
                         x-init="initCarousel($el)"
                         class="touch-continuous-wrapper">
                        <div class="flex md:grid md:grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 md:gap-10 w-max md:w-full">
                            {{-- Grupo 1 --}}
                            @foreach($category->section_products as $product)
                                <div class="group relative w-[260px] sm:w-[280px] md:w-auto shrink-0 md:shrink">
                                    <a href="{{ route('product.show', $product->slug) }}" class="absolute inset-0 z-10" aria-label="Ver {{ $product->name }}"></a>
                                    <div class="relative aspect-[3/4] rounded-2xl overflow-hidden bg-slate-50 dark:bg-slate-800 mb-5 border border-slate-100 dark:border-slate-800 shadow-sm">
                                        @php 
                                            $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
                                        @endphp
                                        @if($mainImage)
                                            <img alt="Tênis {{ $product->name }}{{ $product->brand ? ' - ' . $product->brand : '' }}"
                                                loading="lazy"
                                                decoding="async"
                                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                                src="{{ str_starts_with($mainImage->path, 'http') ? $mainImage->path : asset('storage/' . $mainImage->path) }}" />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-slate-100 border border-slate-200">
                                                <span class="material-symbols-outlined text-slate-300 text-6xl">image</span>
                                            </div>
                                        @endif
                                        <button
                                            class="absolute top-5 right-5 p-2.5 bg-white/90 dark:bg-slate-900/90 backdrop-blur rounded-full opacity-0 group-hover:opacity-100 transition-opacity shadow-sm">
                                            <span class="material-symbols-outlined text-xl text-slate-600">favorite</span>
                                        </button>
                                        <div
                                            class="absolute bottom-5 inset-x-5 translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                            <a href="{{ route('product.show', $product->slug) }}"
                                                class="w-full bg-slate-900 dark:bg-primary text-white py-4 rounded-xl font-bold text-sm shadow-xl transition-colors flex justify-center items-center">
                                                Ver produto
                                            </a>
                                        </div>
                                    </div>
                                    <div class="flex justify-between items-start mb-1 px-1">
                                        <h4 class="font-semibold text-slate-900 dark:text-slate-200 line-clamp-1">{{ $product->name }}</h4>
                                        <p class="font-bold text-primary whitespace-nowrap">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                                    </div>
                                    <p class="text-sm text-slate-500 mb-3 px-1">{{ $product->categories->pluck('name')->implode(' • ') }}</p>
                                </div>
                            @endforeach

                            {{-- Grupo 2 (Duplicata para loop infinito e contínuo no mobile) --}}
                            @if($category->section_products->count() > 1)
                                @foreach($category->section_products as $product)
                                    <div class="group relative w-[260px] sm:w-[280px] shrink-0 md:hidden" aria-hidden="true">
                                        <a href="{{ route('product.show', $product->slug) }}" class="absolute inset-0 z-10" aria-label="Ver {{ $product->name }}"></a>
                                        <div class="relative aspect-[3/4] rounded-2xl overflow-hidden bg-slate-50 dark:bg-slate-800 mb-5 border border-slate-100 dark:border-slate-800 shadow-sm">
                                            @php 
                                                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
                                            @endphp
                                            @if($mainImage)
                                                <img alt="Tênis {{ $product->name }}{{ $product->brand ? ' - ' . $product->brand : '' }}"
                                                    loading="lazy"
                                                    decoding="async"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                                    src="{{ str_starts_with($mainImage->path, 'http') ? $mainImage->path : asset('storage/' . $mainImage->path) }}" />
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-slate-100 border border-slate-200">
                                                    <span class="material-symbols-outlined text-slate-300 text-6xl">image</span>
                                                </div>
                                            @endif
                                            <button
                                                class="absolute top-5 right-5 p-2.5 bg-white/90 dark:bg-slate-900/90 backdrop-blur rounded-full opacity-0 group-hover:opacity-100 transition-opacity shadow-sm">
                                                <span class="material-symbols-outlined text-xl text-slate-600">favorite</span>
                                            </button>
                                            <div
                                                class="absolute bottom-5 inset-x-5 translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                                <a href="{{ route('product.show', $product->slug) }}"
                                                    class="w-full bg-slate-900 dark:bg-primary text-white py-4 rounded-xl font-bold text-sm shadow-xl transition-colors flex justify-center items-center">
                                                    Ver produto
                                                </a>
                                            </div>
                                        </div>
                                        <div class="flex justify-between items-start mb-1 px-1">
                                            <h4 class="font-semibold text-slate-900 dark:text-slate-200 line-clamp-1">{{ $product->name }}</h4>
                                            <p class="font-bold text-primary whitespace-nowrap">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                                        </div>
                                        <p class="text-sm text-slate-500 mb-3 px-1">{{ $product->categories->pluck('name')->implode(' • ') }}</p>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </section>
            @endif
        @endforeach

        <!-- Card / Banner CTA Explorar Premium -->
        <section class="mt-14 sm:mt-20 mb-8 sm:mb-12">
            <div class="relative overflow-hidden rounded-[2rem] sm:rounded-[2.5rem] bg-gradient-to-br from-slate-950 via-slate-900 to-black text-white p-6 sm:p-10 lg:p-14 shadow-2xl border border-white/10 group">
                {{-- Efeitos de iluminação de fundo (orbes com glow suave) --}}
                <div class="absolute -right-20 -top-20 w-80 h-80 sm:w-[28rem] sm:h-[28rem] bg-primary/25 rounded-full blur-3xl pointer-events-none animate-pulse duration-1000"></div>
                <div class="absolute -left-20 -bottom-20 w-72 h-72 sm:w-96 sm:h-96 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute inset-0 bg-[radial-gradient(#ffffff0a_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>

                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    
                    {{-- Coluna de Conteúdo (7 colunas no desktop) --}}
                    <div class="lg:col-span-7 space-y-4 sm:space-y-6">
                        {{-- Tag Badge Superior --}}
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-primary/15 text-primary border border-primary/30 text-[11px] sm:text-xs font-black uppercase tracking-wider backdrop-blur-md shadow-xs">
                            <span class="size-2 rounded-full bg-primary animate-ping"></span>
                            <span class="material-symbols-outlined text-sm">explore</span>
                            Feed Infinito de Descoberta
                        </div>

                        {{-- Título Principal com Gradiente e Destaque --}}
                        <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-[1.15] text-white">
                            Descubra seu próximo tênis no feed que 
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-emerald-400 to-teal-300">
                                nunca acaba.
                            </span>
                        </h2>

                        {{-- Subtítulo --}}
                        <p class="text-slate-300 text-xs sm:text-base leading-relaxed max-w-xl font-medium">
                            Navegue por todos os modelos da loja em ordem dinâmica e aleatória a cada visita. Uma experiência imersiva com rolagem contínua, fotos reais, cores e estoque em tempo real.
                        </p>

                        {{-- Badges de Recursos Exclusivos --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3 pt-1">
                            <div class="p-2.5 sm:p-3 rounded-2xl bg-white/[0.04] border border-white/10 backdrop-blur-xs flex items-center gap-2.5">
                                <div class="size-8 rounded-xl bg-primary/20 text-primary flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-lg">all_inclusive</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] sm:text-xs font-bold text-white truncate">Rolagem Infinita</p>
                                    <p class="text-[9px] sm:text-[10px] text-slate-400 truncate">Sem paginação</p>
                                </div>
                            </div>

                            <div class="p-2.5 sm:p-3 rounded-2xl bg-white/[0.04] border border-white/10 backdrop-blur-xs flex items-center gap-2.5">
                                <div class="size-8 rounded-xl bg-amber-400/20 text-amber-400 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-lg">shuffle</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] sm:text-xs font-bold text-white truncate">Ordem Dinâmica</p>
                                    <p class="text-[9px] sm:text-[10px] text-slate-400 truncate">Aleatória a cada visita</p>
                                </div>
                            </div>

                            <div class="col-span-2 sm:col-span-1 p-2.5 sm:p-3 rounded-2xl bg-white/[0.04] border border-white/10 backdrop-blur-xs flex items-center gap-2.5">
                                <div class="size-8 rounded-xl bg-emerald-400/20 text-emerald-400 flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-lg">verified</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[11px] sm:text-xs font-bold text-white truncate">100% Originais</p>
                                    <p class="text-[9px] sm:text-[10px] text-slate-400 truncate">Estoque real</p>
                                </div>
                            </div>
                        </div>

                        {{-- Botão de Ação Principal e Contador --}}
                        <div class="pt-2 sm:pt-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                            <a href="{{ route('explore') }}"
                               class="group/btn relative inline-flex items-center justify-center gap-3 px-8 py-4 sm:py-4.5 rounded-2xl bg-primary hover:bg-primary/95 text-white font-black text-sm sm:text-base shadow-2xl shadow-primary/35 hover:shadow-primary/50 transition-all duration-300 hover:scale-[1.02] active:scale-[0.98] overflow-hidden text-center">
                                <span class="relative z-10">Explorar Todos os Tênis</span>
                                <span class="material-symbols-outlined text-xl transition-transform duration-300 group-hover/btn:translate-x-1 relative z-10">arrow_forward</span>
                                {{-- Shimmer effect no hover --}}
                                <div class="absolute inset-0 -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>
                            </a>

                            <div class="flex items-center gap-2 text-xs text-slate-400 justify-center sm:justify-start">
                                <span class="size-2 rounded-full bg-emerald-400"></span>
                                <span>+{{ $totalActiveProducts ?? 40 }} tênis disponíveis no catálogo</span>
                            </div>
                        </div>
                    </div>

                    {{-- Coluna Visual: Deck 3D de Tênis em Destaque (5 colunas no desktop) --}}
                    <div class="lg:col-span-5 relative mt-4 lg:mt-0 flex items-center justify-center">
                        <div class="relative w-full max-w-sm sm:max-w-md h-64 sm:h-80 flex items-center justify-center select-none">
                            
                            @php
                                $previewProducts = $luckyProducts->take(3);
                            @endphp

                            @if($previewProducts->count() >= 2)
                                {{-- Card 1 (Fundo inclinado à esquerda) --}}
                                @php
                                    $p1 = $previewProducts[0];
                                    $img1 = $p1->images->where('is_main', true)->first() ?? $p1->images->first();
                                    $src1 = $img1 ? (str_starts_with($img1->path, 'http') ? $img1->path : asset('storage/' . $img1->path)) : null;
                                @endphp
                                <div class="absolute w-44 sm:w-52 aspect-[3/4] -rotate-12 -translate-x-14 sm:-translate-x-16 -translate-y-4 rounded-3xl bg-slate-800/90 border border-white/10 p-3 shadow-2xl backdrop-blur-md opacity-70 group-hover:opacity-90 group-hover:-rotate-6 transition-all duration-700 pointer-events-none">
                                    <div class="w-full h-3/4 rounded-2xl overflow-hidden bg-slate-900 mb-2">
                                        @if($src1)
                                            <img src="{{ $src1 }}" alt="{{ $p1->name }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <p class="text-[10px] font-bold text-white truncate">{{ $p1->name }}</p>
                                    <p class="text-[11px] font-black text-emerald-400">R$ {{ number_format($p1->price, 2, ',', '.') }}</p>
                                </div>

                                {{-- Card 2 (Fundo inclinado à direita) --}}
                                @if(isset($previewProducts[1]))
                                    @php
                                        $p2 = $previewProducts[1];
                                        $img2 = $p2->images->where('is_main', true)->first() ?? $p2->images->first();
                                        $src2 = $img2 ? (str_starts_with($img2->path, 'http') ? $img2->path : asset('storage/' . $img2->path)) : null;
                                    @endphp
                                    <div class="absolute w-44 sm:w-52 aspect-[3/4] rotate-12 translate-x-14 sm:translate-x-16 translate-y-3 rounded-3xl bg-slate-800/90 border border-white/10 p-3 shadow-2xl backdrop-blur-md opacity-70 group-hover:opacity-90 group-hover:rotate-6 transition-all duration-700 pointer-events-none">
                                        <div class="w-full h-3/4 rounded-2xl overflow-hidden bg-slate-900 mb-2">
                                            @if($src2)
                                                <img src="{{ $src2 }}" alt="{{ $p2->name }}" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <p class="text-[10px] font-bold text-white truncate">{{ $p2->name }}</p>
                                        <p class="text-[11px] font-black text-emerald-400">R$ {{ number_format($p2->price, 2, ',', '.') }}</p>
                                    </div>
                                @endif

                                {{-- Card Central (Em destaque no meio) --}}
                                @php
                                    $pMain = $previewProducts[2] ?? $previewProducts[0];
                                    $imgMain = $pMain->images->where('is_main', true)->first() ?? $pMain->images->first();
                                    $srcMain = $imgMain ? (str_starts_with($imgMain->path, 'http') ? $imgMain->path : asset('storage/' . $imgMain->path)) : null;
                                @endphp
                                <a href="{{ route('explore') }}"
                                   class="relative z-20 w-48 sm:w-56 aspect-[3/4] rounded-3xl bg-slate-900/95 border-2 border-primary/50 p-3.5 shadow-2xl backdrop-blur-xl group-hover:scale-105 group-hover:border-primary transition-all duration-500 block cursor-pointer">
                                    <div class="relative w-full h-3/4 rounded-2xl overflow-hidden bg-slate-800 mb-2.5">
                                        @if($srcMain)
                                            <img src="{{ $srcMain }}" alt="{{ $pMain->name }}" class="w-full h-full object-cover">
                                        @endif
                                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-primary text-white text-[9px] font-black uppercase tracking-wider shadow-md">
                                            Explorar
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div class="min-w-0 pr-1">
                                            <p class="text-[10px] text-slate-400 uppercase font-black truncate">{{ $pMain->brand ?: 'Tênis' }}</p>
                                            <p class="text-xs font-bold text-white truncate">{{ $pMain->name }}</p>
                                        </div>
                                        <span class="text-xs font-black text-emerald-400 shrink-0">
                                            R$ {{ number_format($pMain->price, 2, ',', '.') }}
                                        </span>
                                    </div>
                                </a>
                            @else
                                {{-- Fallback decorativo com ícone dinâmico caso não haja produtos suficientes --}}
                                <a href="{{ route('explore') }}"
                                   class="w-56 h-72 rounded-3xl bg-slate-900 border border-white/10 p-6 flex flex-col items-center justify-center text-center shadow-2xl group-hover:scale-105 transition-all">
                                    <div class="size-20 rounded-3xl bg-primary/20 text-primary flex items-center justify-center mb-4">
                                        <span class="material-symbols-outlined text-4xl animate-bounce">explore</span>
                                    </div>
                                    <p class="text-sm font-bold text-white mb-1">Feed de Descoberta</p>
                                    <p class="text-xs text-slate-400">Clique para navegar sem limites</p>
                                </a>
                            @endif

                        </div>
                    </div>

                </div>
            </div>
        </section>
    </section>

    <script>
        function touchContinuousCarousel() {
            return {
                initCarousel(el) {
                    if (!el) return;

                    let isTouching = false;
                    let resumeTimeout = null;
                    let animFrame = null;
                    let currentScroll = el.scrollLeft;
                    const speed = 0.65; // ~40px por segundo, rotação suave e lenta

                    const stopAutoScroll = () => {
                        isTouching = true;
                        if (resumeTimeout) clearTimeout(resumeTimeout);
                    };

                    const startResumeTimer = () => {
                        if (resumeTimeout) clearTimeout(resumeTimeout);
                        resumeTimeout = setTimeout(() => {
                            isTouching = false;
                        }, 2200); // 2.2s de pausa após o usuário soltar o dedo
                    };

                    // Eventos de toque para o celular (arraste do dedo livre e natural)
                    el.addEventListener('touchstart', stopAutoScroll, { passive: true });
                    el.addEventListener('touchend', startResumeTimer, { passive: true });
                    el.addEventListener('touchcancel', startResumeTimer, { passive: true });

                    // Manter o acumulador perfeitamente alinhado com o movimento do dedo
                    el.addEventListener('scroll', () => {
                        if (isTouching) {
                            currentScroll = el.scrollLeft;
                        }
                    }, { passive: true });

                    // Suporte para mouse drag no desktop/emuladores
                    let isMouseDown = false;
                    let startX = 0;
                    let scrollStart = 0;

                    el.addEventListener('mousedown', (e) => {
                        if (window.innerWidth >= 768) return;
                        isMouseDown = true;
                        stopAutoScroll();
                        startX = e.pageX - el.offsetLeft;
                        scrollStart = el.scrollLeft;
                    });

                    window.addEventListener('mousemove', (e) => {
                        if (!isMouseDown) return;
                        e.preventDefault();
                        const x = e.pageX - el.offsetLeft;
                        const walk = (x - startX) * 1.5;
                        el.scrollLeft = scrollStart - walk;
                        currentScroll = el.scrollLeft;
                    });

                    window.addEventListener('mouseup', () => {
                        if (!isMouseDown) return;
                        isMouseDown = false;
                        startResumeTimer();
                    });

                    // Loop suave de rotação contínua
                    const step = () => {
                        if (window.innerWidth < 768 && !isTouching) {
                            const half = el.scrollWidth / 2;
                            if (half > 0) {
                                currentScroll += speed;
                                if (currentScroll >= half) {
                                    currentScroll -= half;
                                } else if (currentScroll <= 0) {
                                    currentScroll += half;
                                }
                                el.scrollLeft = currentScroll;
                            }
                        }
                        animFrame = requestAnimationFrame(step);
                    };

                    animFrame = requestAnimationFrame(step);

                    window.addEventListener('resize', () => {
                        if (window.innerWidth >= 768) {
                            el.scrollLeft = 0;
                        }
                    });
                }
            };
        }
    </script>

    @if(!empty($heroSettings['video_url']) && empty($heroSettings['is_direct_video']))
        @push('scripts')
        <script>
            (function() {
                var ytScriptLoaded = false;
                function loadYTApi() {
                    if (window.YT && window.YT.Player) {
                        initHeroYT();
                        return;
                    }
                    if (!ytScriptLoaded) {
                        ytScriptLoaded = true;
                        var tag = document.createElement('script');
                        tag.src = "https://www.youtube.com/iframe_api";
                        var firstScriptTag = document.getElementsByTagName('script')[0];
                        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
                    }
                }

                function initHeroYT() {
                    var iframe = document.getElementById('hero-yt-iframe');
                    if (!iframe) return;

                    try {
                        new YT.Player('hero-yt-iframe', {
                            events: {
                                'onReady': function(event) {
                                    event.target.mute();
                                    event.target.playVideo();
                                },
                                'onStateChange': function(event) {
                                    // 1 = YT.PlayerState.PLAYING (Vídeo já está reproduzindo)
                                    if (event.data === 1) {
                                        window.dispatchEvent(new CustomEvent('hero-video-active'));
                                    }
                                    // 0 = YT.PlayerState.ENDED (Loop contínuo sem interface de playlist)
                                    if (event.data === 0) {
                                        event.target.seekTo(0);
                                        event.target.playVideo();
                                    }
                                    // 2 = YT.PlayerState.PAUSED (Retoma imediatamente se o navegador pausar)
                                    if (event.data === 2) {
                                        event.target.playVideo();
                                    }
                                }
                            }
                        });
                    } catch(e) {
                        setTimeout(function() {
                            window.dispatchEvent(new CustomEvent('hero-video-active'));
                        }, 2500);
                    }
                }

                window.onYouTubeIframeAPIReady = initHeroYT;

                if (document.readyState === 'complete' || document.readyState === 'interactive') {
                    loadYTApi();
                } else {
                    window.addEventListener('DOMContentLoaded', loadYTApi);
                }

                // Fallback de segurança para garantir que a imagem não fique presa se o YouTube for bloqueado
                setTimeout(function() {
                    window.dispatchEvent(new CustomEvent('hero-video-active'));
                }, 4000);
            })();
        </script>
        @endpush
    @endif
</x-storefront-layout>