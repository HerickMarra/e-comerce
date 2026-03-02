<x-storefront-layout>
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" 
        x-data="searchManager({
            initialMaxPrice: {{ $maxPrice }},
            initialSort: '{{ $sort }}',
            baseUrl: '{{ route('search') }}'
        })">
        
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
            <div>
                <nav aria-label="Breadcrumb" class="flex mb-2 text-xs font-medium text-slate-400">
                    <ol class="flex items-center space-x-2">
                        <li><a class="hover:text-primary transition-colors" href="/">Home</a></li>
                        <li class="flex items-center space-x-2">
                            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                            <span>Busca</span>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-white">
                    @if($query)
                        Resultados para "{{ $query }}"
                    @else
                        Todos os Produtos
                    @endif
                </h1>
                <p class="text-slate-500 text-sm mt-1">Encontramos <span x-text="totalItems">{{ $products->total() }}</span> itens que combinam com sua busca</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center border border-slate-200 dark:border-slate-800 rounded-lg p-1 bg-white dark:bg-slate-900">
                    <button class="p-1.5 rounded bg-slate-100 dark:bg-slate-800 text-primary">
                        <span class="material-symbols-outlined text-xl">grid_view</span>
                    </button>
                    <button class="p-1.5 rounded text-slate-400 hover:text-slate-600 transition-colors">
                        <span class="material-symbols-outlined text-xl">view_list</span>
                    </button>
                </div>
                <div class="relative min-w-[200px]">
                    <select name="sort" x-model="sort" @change="updateFilters()"
                        class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg py-2 pl-4 pr-10 text-sm focus:ring-primary focus:border-primary appearance-none outline-none">
                        <option value="relevance">Mais Relevantes</option>
                        <option value="price_low">Menor Preço</option>
                        <option value="price_high">Maior Preço</option>
                        <option value="newest">Lançamentos</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-10">
            <!-- Sidebar Filters -->
            <aside class="space-y-8">
                <form id="filterForm" x-ref="filterForm" action="{{ route('search') }}" method="GET" @submit.prevent="updateFilters()">
                    <input type="hidden" name="q" value="{{ $query }}">
                    
                    <!-- Categories -->
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-6">
                        <h3 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-xs mb-4 flex items-center justify-between">
                            Categoria
                            <span class="material-symbols-outlined text-sm">remove</span>
                        </h3>
                        <div class="space-y-3">
                            @foreach($categories as $category)
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                        {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}
                                        @change="updateFilters()"
                                        class="rounded border-slate-300 text-primary focus:ring-primary/20">
                                    <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">
                                        {{ $category->name }} ({{ $category->products_count }})
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-6">
                        <h3 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-xs mb-4 flex items-center justify-between">
                            Faixa de Preço
                            <span class="material-symbols-outlined text-sm">remove</span>
                        </h3>
                        <div class="px-2">
                            <input type="range" name="max_price" min="0" max="50000" step="100"
                                x-model="maxPrice" @change="updateFilters()"
                                class="w-full h-1 bg-slate-200 dark:bg-slate-700 rounded-lg appearance-none cursor-pointer accent-primary">
                            <div class="flex items-center justify-between mt-4">
                                <div class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded px-2 py-1 text-xs font-semibold">
                                    R$ 0
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded px-2 py-1 text-xs font-semibold">
                                    R$ <span x-text="new Intl.NumberFormat('pt-BR').format(maxPrice)"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Colors -->
                    <div class="border-b border-slate-100 dark:border-slate-800 pb-6">
                        <h3 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-xs mb-4 flex items-center justify-between">
                            Cor
                            <span class="material-symbols-outlined text-sm">remove</span>
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($availableColors as $color)
                                <label class="relative cursor-pointer">
                                    <input type="checkbox" name="colors[]" value="{{ $color->hex_code }}"
                                        {{ in_array($color->hex_code, $selectedColors) ? 'checked' : '' }}
                                        @change="updateFilters()"
                                        class="sr-only peer">
                                    <div class="w-8 h-8 rounded-full border-2 border-transparent peer-checked:border-primary peer-checked:ring-2 peer-checked:ring-primary/20 peer-checked:ring-offset-2 transition-all hover:scale-110"
                                         style="background-color: {{ $color->hex_code }}"
                                         title="{{ $color->color_name }}"></div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Availability -->
                    <div class="pb-6 mt-6">
                        <h3 class="font-bold text-slate-900 dark:text-white uppercase tracking-wider text-xs mb-4 flex items-center justify-between">
                            Disponibilidade
                            <span class="material-symbols-outlined text-sm">remove</span>
                        </h3>
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="in_stock" value="1"
                                {{ $inStockOnly ? 'checked' : '' }}
                                @change="updateFilters()"
                                class="rounded border-slate-300 text-primary focus:ring-primary/20">
                            <span class="text-sm text-slate-600 dark:text-slate-400 group-hover:text-primary transition-colors">
                                Pronta Entrega
                            </span>
                        </label>
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('search') }}" class="text-xs text-primary font-bold hover:underline">Limpar todos os filtros</a>
                    </div>
                </form>
            </aside>

            <!-- Product Grid -->
            <div class="relative min-h-[400px]">
                <!-- Loading overlay -->
                <div x-show="loading" x-cloak class="absolute inset-0 bg-white/50 dark:bg-background-dark/50 backdrop-blur-[1px] z-10 flex items-center justify-center rounded-3xl">
                    <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
                </div>

                <div id="product-list">
                    @include('search._product_list', ['products' => $products])
                </div>
            </div>
        </div>
    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('searchManager', (config) => ({
                maxPrice: config.initialMaxPrice,
                sort: config.initialSort,
                loading: false,
                totalItems: {{ $products->total() }},
                
                async updateFilters() {
                    this.loading = true;
                    const formData = new FormData(this.$refs.filterForm);
                    
                    // Add current sort to form data
                    formData.set('sort', this.sort);
                    
                    const params = new URLSearchParams(formData);
                    const url = `${config.baseUrl}?${params.toString()}`;
                    
                    try {
                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        
                        const data = await response.json();
                        
                        // Update UI
                        document.getElementById('product-list').innerHTML = data.html;
                        this.totalItems = data.total;
                        
                        // Update URL without reload
                        window.history.pushState({}, '', data.url);
                        
                    } catch (error) {
                        console.error('Search error:', error);
                    } finally {
                        this.loading = false;
                        // Scroll to top of results on mobile
                        if (window.innerWidth < 768) {
                            window.scrollTo({ top: 200, behavior: 'smooth' });
                        }
                    }
                }
            }));
        });
    </script>
</x-storefront-layout>
