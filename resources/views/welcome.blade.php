<x-storefront-layout>
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="relative h-[500px] md:h-[600px] w-full rounded-3xl overflow-hidden mb-16 shadow-2xl group"
             x-data="{ videoLoaded: false }"
             x-init="setTimeout(() => videoLoaded = true, 2000)">
            <div
                class="absolute inset-0 bg-gradient-to-b md:bg-gradient-to-r from-black/70 via-black/40 to-transparent z-10 transition-opacity group-hover:opacity-70">
            </div>
            
            <!-- Placeholder Image (Fades out when video loads) -->
            <img alt="Modern Living Room"
                class="absolute inset-0 w-full h-full object-cover z-[5] transition-opacity duration-1000"
                :class="videoLoaded ? 'opacity-0' : 'opacity-100'"
                src="{{ $heroSettings['image_fallback'] }}" />

            <!-- Video Background -->
            <iframe 
                @load="videoLoaded = true"
                class="absolute inset-0 w-[300%] h-[300%] -left-[100%] -top-[100%] md:w-[150%] md:h-[150%] md:-left-[25%] md:-top-[25%] lg:w-full lg:h-full lg:left-0 lg:top-0 object-cover scale-[1.5] lg:scale-125 pointer-events-none z-0" 
                src="{{ $heroSettings['video_url'] }}" 
                frameborder="0" 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                allowfullscreen>
            </iframe>
            <div class="relative z-20 h-full flex flex-col justify-center px-6 md:px-16 max-w-3xl">
                <span class="text-primary font-bold tracking-[0.2em] uppercase text-xs md:text-sm mb-4">{{ $heroSettings['tag'] }}</span>
                <h1 class="text-4xl md:text-7xl font-bold text-white leading-tight mb-6">
                    {!! strip_tags($heroSettings['title'], '<br>') !!}
                </h1>
                <p class="text-lg md:text-xl text-white/90 mb-8 md:mb-10 max-w-lg leading-relaxed">
                    {{ $heroSettings['subtitle'] }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 md:gap-5">
                    <a href="{{ route('search') }}"
                        class="bg-primary text-white px-8 md:px-10 py-3 md:py-4 rounded-xl font-bold transition-all shadow-xl shadow-primary/30 inline-block text-center">
                        Explorar Agora
                    </a>
                    <a href="{{ $heroSettings['btn_link'] }}"
                        class="bg-white/10 hover:bg-white/20 backdrop-blur-md text-white border border-white/40 px-8 md:px-10 py-3 md:py-4 rounded-xl font-bold transition-all text-center">
                        {{ $heroSettings['btn_text'] }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <section class="mb-20">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Compre por Categoria</h2>
                    <p class="text-slate-500">Mobiliário premium para cada ambiente</p>
                </div>
                <a class="text-primary font-bold text-sm hover:underline flex items-center gap-1" href="{{ route('categories.index') }}">
                    Ver todas categorias <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                @forelse($categories as $category)
                    <div class="group cursor-pointer">
                        <a href="{{ route('search', ['categories' => [$category->id]]) }}" class="block">
                            <div class="relative aspect-[4/5] rounded-2xl overflow-hidden mb-4 shadow-lg">
                                @if($category->image_path)
                                    <img alt="{{ $category->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                        src="{{ str_starts_with($category->image_path, 'http') ? $category->image_path : asset('storage/' . $category->image_path) }}" />
                                @else
                                    <div class="w-full h-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                        <span class="material-symbols-outlined text-slate-300 text-5xl">category</span>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors"></div>
                                <div class="absolute bottom-6 left-6 text-white text-shadow">
                                    <h3 class="text-xl font-bold">{{ $category->name }}</h3>
                                    <p class="text-sm opacity-90">{{ $category->products->count() }} Itens</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <p class="col-span-full text-center text-slate-500 py-10">Nenhuma categoria cadastrada ainda.</p>
                @endforelse
            </div>
        </section>

        <!-- Best Sellers -->
        <section class="mb-24">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white">Novidades</h2>
                    <p class="text-slate-500 mt-1">Confira os últimos lançamentos que acabaram de chegar em nossa loja</p>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                @forelse($products as $product)
                    <div class="group relative">
                        <a href="{{ route('product.show', $product->slug) }}" class="absolute inset-0 z-10" aria-label="Ver {{ $product->name }}"></a>
                        <div class="relative aspect-square rounded-2xl overflow-hidden bg-slate-50 dark:bg-slate-800 mb-5 border border-slate-100 dark:border-slate-800">
                            @php 
                                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
                            @endphp
                            @if($mainImage)
                                <img alt="{{ $product->name }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                    src="{{ str_starts_with($mainImage->path, 'http') ? $mainImage->path : asset('storage/' . $mainImage->path) }}" />
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-100">
                                    <span class="material-symbols-outlined text-slate-300 text-6xl">image</span>
                                </div>
                            @endif
                            <button
                                class="absolute top-5 right-5 p-2.5 bg-white/90 dark:bg-slate-900/90 backdrop-blur rounded-full opacity-0 group-hover:opacity-100 transition-opacity shadow-sm">
                                <span class="material-symbols-outlined text-xl text-slate-600">favorite</span>
                            </button>
                            <div
                                class="absolute bottom-5 inset-x-5 translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <button
                                    class="w-full bg-slate-900 dark:bg-primary text-white py-4 rounded-xl font-bold text-sm shadow-xl transition-colors">
                                    Ver produto
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-between items-start mb-1 px-1">
                            <h4 class="font-semibold text-slate-900 dark:text-slate-200 line-clamp-1">{{ $product->name }}</h4>
                            <p class="font-bold text-primary whitespace-nowrap">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                        </div>
                        <p class="text-sm text-slate-500 mb-3 px-1">{{ $product->categories->pluck('name')->implode(' • ') }}</p>
                        <div class="flex items-center gap-1.5 px-1">
                            <span class="material-symbols-outlined text-amber-400 text-lg fill-1">star</span>
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">5.0</span>
                            <span class="text-xs text-slate-400">(Novo)</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                            <span class="material-symbols-outlined text-slate-300 text-4xl">inventory_2</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900">Nenhum produto encontrado</h3>
                        <p class="text-slate-500 mt-2">Estamos preparando novidades incríveis para você!</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Im Feeling Lucky -->
        <section class="mb-24 mt-40">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white">Estou com Sorte</h2>
                    <p class="text-slate-500 mt-1">Achados surpreendentes e aleatórios especialmente para o seu lar</p>
                </div>
                <div class="flex items-center justify-center size-12 rounded-2xl bg-amber-100 text-amber-600">
                    <span class="material-symbols-outlined text-2xl animate-bounce">casino</span>
                </div>

            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                @forelse($luckyProducts as $product)
                    <div class="group relative">
                        <a href="{{ route('product.show', $product->slug) }}" class="absolute inset-0 z-10" aria-label="Ver {{ $product->name }}"></a>
                        <div class="relative aspect-[4/5] rounded-3xl overflow-hidden bg-slate-50 dark:bg-slate-800 mb-5 border border-slate-100 dark:border-slate-800">
                            @php 
                                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
                            @endphp
                            @if($mainImage)
                                <img alt="{{ $product->name }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                                    src="{{ str_starts_with($mainImage->path, 'http') ? $mainImage->path : asset('storage/' . $mainImage->path) }}" />
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-slate-100 dark:bg-slate-800">
                                    <span class="material-symbols-outlined text-slate-300 dark:text-slate-600 text-6xl">image</span>
                                </div>
                            @endif
                            
                            <!-- Overlay Gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                            <div class="absolute bottom-5 inset-x-5 translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition-all duration-300 z-20">
                                <a href="{{ route('product.show', $product->slug) }}"
                                    class="w-full bg-slate-900/90 backdrop-blur-md text-white border border-white/20 py-4 rounded-xl font-bold text-sm hover:bg-white hover:text-slate-900 transition-colors flex justify-center items-center">
                                    Quero este!
                                </a>
                            </div>
                        </div>
                        <div class="flex justify-between items-start mb-2 px-2">
                            <h4 class="font-bold text-slate-900 dark:text-white line-clamp-1 text-lg">{{ $product->name }}</h4>
                        </div>
                        <div class="flex justify-between items-center px-2">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $product->categories->first()?->name ?? 'Móveis' }}</p>
                            <p class="font-black text-primary text-lg">R$ {{ number_format($product->price, 2, ',', '.') }}</p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-10 text-center">
                        <p class="text-slate-500">Sem produtos no momento.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Categorias Aleatórias -->
        @foreach($randomSections as $category)
            @if($category->section_products->count() > 0)
                <section class="mb-24">
                    <div class="flex items-center justify-between mb-10">
                        <div>
                            <h2 class="text-3xl font-bold text-slate-900 dark:text-white">{{ $category->name }}</h2>
                            <p class="text-slate-500 mt-1 pb-1 border-b-2 border-primary/20 inline-block">Nossas melhores escolhas em {{ strtolower($category->name) }} para você</p>
                        </div>
                        <a href="{{ route('search', ['categories' => [$category->id]]) }}" class="text-primary font-bold text-sm hover:underline flex items-center gap-1">
                            Ver todos <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                        @foreach($category->section_products as $product)
                            <div class="group relative">
                                <a href="{{ route('product.show', $product->slug) }}" class="absolute inset-0 z-10" aria-label="Ver {{ $product->name }}"></a>
                                <div class="relative aspect-square rounded-2xl overflow-hidden bg-slate-50 dark:bg-slate-800 mb-5 border border-slate-100 dark:border-slate-800">
                                    @php 
                                        $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
                                    @endphp
                                    @if($mainImage)
                                        <img alt="{{ $product->name }}"
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
                    </div>
                </section>
            @endif
        @endforeach
    </section>
</x-storefront-layout>