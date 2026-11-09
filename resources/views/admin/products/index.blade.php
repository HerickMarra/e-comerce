<x-admin-layout>
    @section('page_title', 'Tênis & Calçados')

    <div class="space-y-6 sm:space-y-8" x-data="{
        filterOpen: false,
        activeSearch: '{{ request('search') }}',
        activeCategory: '{{ request('category') }}',
        activeStatus: '{{ request('status') }}',
        activeStock: '{{ request('stock_status') }}',
        activeSort: '{{ request('sort', 'latest') }}'
    }">
        {{-- Top Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-black uppercase tracking-wider mb-2">
                    <span class="material-symbols-outlined text-sm">steps</span>
                    <span>Catálogo de Calçados • {{ $metrics['total'] }} {{ $metrics['total'] == 1 ? 'modelo' : 'modelos' }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Tênis & Sneakers</h1>
                <p class="text-slate-500 text-xs sm:text-sm mt-0.5">Gerencie os modelos, numerações ativas, estoque por par e preços de venda.</p>
            </div>
            <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
                <a href="{{ route('admin.products.create') }}"
                    class="inline-flex items-center justify-center px-5 sm:px-6 py-3.5 bg-primary hover:opacity-95 text-white font-bold rounded-2xl transition-all shadow-lg shadow-primary/25 gap-2 text-xs sm:text-sm w-full sm:w-auto shrink-0 active:scale-[0.98]">
                    <span class="material-symbols-outlined text-xl">add</span>
                    <span>Cadastrar Novo Tênis</span>
                </a>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-950/40 border-l-4 border-emerald-500 p-4 rounded-r-2xl flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-emerald-500 text-xl">check_circle</span>
                    <p class="text-emerald-700 dark:text-emerald-300 font-bold text-xs sm:text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- KPI Summary Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            {{-- Total de Modelos --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-3 sm:gap-4">
                <div class="size-11 sm:size-12 rounded-2xl bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-xl sm:text-2xl">inventory_2</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block truncate">Modelos</span>
                    <span class="text-lg sm:text-2xl font-black text-slate-900 dark:text-white leading-none block">{{ $metrics['total'] }}</span>
                    <span class="text-[10px] text-slate-400 truncate block mt-0.5">cadastrados</span>
                </div>
            </div>

            {{-- Tênis Ativos --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-3 sm:gap-4">
                <div class="size-11 sm:size-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-xl sm:text-2xl">check_circle</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block truncate">Ativos na Loja</span>
                    <span class="text-lg sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 leading-none block">{{ $metrics['active'] }}</span>
                    <span class="text-[10px] text-slate-400 truncate block mt-0.5">visíveis aos clientes</span>
                </div>
            </div>

            {{-- Pares em Estoque --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-3 sm:gap-4">
                <div class="size-11 sm:size-12 rounded-2xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-xl sm:text-2xl">steps</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block truncate">Pares em Estoque</span>
                    <span class="text-lg sm:text-2xl font-black text-slate-900 dark:text-white leading-none block">{{ $metrics['total_stock'] }}</span>
                    <span class="text-[10px] text-slate-400 truncate block mt-0.5">unidades totais</span>
                </div>
            </div>

            {{-- Alerta de Estoque --}}
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-3 sm:gap-4">
                <div class="size-11 sm:size-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-xl sm:text-2xl">warning</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block truncate">Alerta Estoque</span>
                    <span class="text-lg sm:text-2xl font-black text-amber-600 dark:text-amber-400 leading-none block">{{ $metrics['low_stock'] + $metrics['out_of_stock'] }}</span>
                    <span class="text-[10px] text-slate-400 truncate block mt-0.5">{{ $metrics['out_of_stock'] }} esgotados • {{ $metrics['low_stock'] }} baixos</span>
                </div>
            </div>
        </div>

        {{-- Search & Filters Toolbar --}}
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-6 border border-slate-100 dark:border-slate-800 shadow-sm space-y-4">
            <form action="{{ route('admin.products.index') }}" method="GET" class="space-y-4">
                <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                    {{-- Search Input --}}
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl">search</span>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Buscar por nome do tênis, marca (Nike, Adidas...), modelo ou SKU..."
                            class="w-full pl-11 pr-10 py-3.5 bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 rounded-2xl text-xs sm:text-sm font-medium focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all placeholder:text-slate-400">
                        @if(request('search'))
                            <a href="{{ route('admin.products.index', request()->except('search')) }}"
                                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200"
                                title="Limpar busca">
                                <span class="material-symbols-outlined text-base">close</span>
                            </a>
                        @endif
                    </div>

                    {{-- Actions Row --}}
                    <div class="flex items-center gap-2">
                        <button type="button" @click="filterOpen = !filterOpen"
                            class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-4 py-3.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold rounded-2xl text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer"
                            :class="filterOpen ? 'bg-slate-900 text-white dark:bg-primary dark:text-white' : ''">
                            <span class="material-symbols-outlined text-lg">tune</span>
                            <span>Filtros</span>
                            @if(request('category') || request('status') || request('stock_status') || (request('sort') && request('sort') !== 'latest'))
                                <span class="size-2 rounded-full bg-primary ring-2 ring-white dark:ring-slate-900"></span>
                            @endif
                        </button>

                        <button type="submit"
                            class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 font-bold rounded-2xl text-xs transition-all shadow-md cursor-pointer">
                            <span>Filtrar</span>
                        </button>
                    </div>
                </div>

                {{-- Collapsible Filter Drawer --}}
                <div x-show="filterOpen" x-cloak x-transition class="pt-4 border-t border-slate-100 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    {{-- Categoria --}}
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Categoria</label>
                        <select name="category"
                            class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 rounded-xl px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-primary outline-none">
                            <option value="">Todas as Categorias</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Estoque --}}
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Nível de Estoque</label>
                        <select name="stock_status"
                            class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 rounded-xl px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-primary outline-none">
                            <option value="">Todos os Estoques</option>
                            <option value="in_stock" {{ request('stock_status') === 'in_stock' ? 'selected' : '' }}>Em Estoque (> 3)</option>
                            <option value="low_stock" {{ request('stock_status') === 'low_stock' ? 'selected' : '' }}>Estoque Baixo (1 a 3)</option>
                            <option value="out_of_stock" {{ request('stock_status') === 'out_of_stock' ? 'selected' : '' }}>Esgotados (0 pares)</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Status na Loja</label>
                        <select name="status"
                            class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 rounded-xl px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-primary outline-none">
                            <option value="">Todos os Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Apenas Ativos</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Apenas Inativos</option>
                        </select>
                    </div>

                    {{-- Ordenação --}}
                    <div>
                        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Ordenar Por</label>
                        <select name="sort"
                            class="w-full bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 rounded-xl px-3.5 py-2.5 text-xs font-medium focus:ring-2 focus:ring-primary outline-none">
                            <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>Mais Recentes</option>
                            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Nome (A-Z)</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Maior Preço</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Menor Preço</option>
                            <option value="stock_desc" {{ request('sort') === 'stock_desc' ? 'selected' : '' }}>Maior Estoque</option>
                            <option value="stock_asc" {{ request('sort') === 'stock_asc' ? 'selected' : '' }}>Menor Estoque</option>
                        </select>
                    </div>
                </div>

                {{-- Badges de Filtros Ativos --}}
                @if(request('search') || request('category') || request('status') || request('stock_status') || (request('sort') && request('sort') !== 'latest'))
                    <div class="flex items-center gap-2 flex-wrap pt-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Filtros Ativos:</span>
                        @if(request('search'))
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-primary/10 text-primary text-xs font-bold">
                                Busca: "{{ request('search') }}"
                                <a href="{{ route('admin.products.index', request()->except('search')) }}"><span class="material-symbols-outlined text-xs">close</span></a>
                            </span>
                        @endif
                        @if(request('category'))
                            @php $activeCatObj = $categories->firstWhere('id', request('category')); @endphp
                            @if($activeCatObj)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold">
                                    Cat: {{ $activeCatObj->name }}
                                    <a href="{{ route('admin.products.index', request()->except('category')) }}"><span class="material-symbols-outlined text-xs">close</span></a>
                                </span>
                            @endif
                        @endif
                        @if(request('stock_status'))
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-300 text-xs font-bold">
                                Estoque: {{ request('stock_status') === 'in_stock' ? 'Em Estoque' : (request('stock_status') === 'low_stock' ? 'Estoque Baixo' : 'Esgotado') }}
                                <a href="{{ route('admin.products.index', request()->except('stock_status')) }}"><span class="material-symbols-outlined text-xs">close</span></a>
                            </span>
                        @endif
                        @if(request('status'))
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold">
                                {{ request('status') === 'active' ? 'Ativos' : 'Inativos' }}
                                <a href="{{ route('admin.products.index', request()->except('status')) }}"><span class="material-symbols-outlined text-xs">close</span></a>
                            </span>
                        @endif
                        <a href="{{ route('admin.products.index') }}"
                            class="text-xs font-bold text-red-500 hover:text-red-700 underline ml-auto">
                            Limpar Todos os Filtros
                        </a>
                    </div>
                @endif
            </form>
        </div>

        {{-- ======================================================== --}}
        {{-- VIEW MOBILE: FEED DE CARDS MODERNOS (APARECE EM < md)   --}}
        {{-- ======================================================== --}}
        <div class="md:hidden space-y-4">
            @forelse($products as $product)
                @php
                    $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
                    $sizes = $product->sizes ?? [];
                    $colors = $product->colors ?? collect();
                @endphp
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-5 border border-slate-100 dark:border-slate-800 shadow-sm space-y-4 transition-all">
                    {{-- Top Card Header: Foto + Detalhes Principais --}}
                    <div class="flex items-start gap-3.5">
                        <div class="size-20 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 relative group">
                            @if($mainImage)
                                <img src="{{ str_starts_with($mainImage->path, 'http') ? $mainImage->path : asset('storage/' . $mainImage->path) }}"
                                    alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="material-symbols-outlined text-slate-400 text-2xl">steps</span>
                            @endif

                            @if($product->hasDiscount())
                                <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded-md bg-red-600 text-white font-black text-[9px]">
                                    -{{ $product->discountPercentage() }}%
                                </span>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            {{-- Brand & Status Row --}}
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-[10px] font-black uppercase tracking-wider text-primary truncate">
                                    {{ $product->brand ?: 'Sem Marca' }}
                                </span>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wide {{ $product->is_active ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                                    <span class="size-1.5 rounded-full {{ $product->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-slate-400' }}"></span>
                                    <span>{{ $product->is_active ? 'Ativo' : 'Inativo' }}</span>
                                </span>
                            </div>

                            {{-- Product Name --}}
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm leading-snug line-clamp-2">
                                {{ $product->name }}
                            </h3>

                            @if($product->model_line || $product->gender)
                                <p class="text-[11px] text-slate-400 mt-0.5 truncate">
                                    {{ $product->model_line }}{{ $product->model_line && $product->gender ? ' • ' : '' }}{{ $product->gender }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Middle Row: Preço & Estoque --}}
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-800/80">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Preço</span>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-base font-black text-slate-900 dark:text-white">
                                    R$ {{ number_format($product->price, 2, ',', '.') }}
                                </span>
                                @if($product->hasDiscount())
                                    <span class="text-xs text-slate-400 line-through">
                                        R$ {{ number_format($product->compare_price, 2, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Disponibilidade</span>
                            @if($product->stock <= 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-300">
                                    <span class="size-1.5 rounded-full bg-red-500"></span>
                                    <span>Esgotado</span>
                                </span>
                            @elseif($product->stock <= 3)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">
                                    <span class="size-1.5 rounded-full bg-amber-500"></span>
                                    <span>Resta {{ $product->stock }} {{ $product->stock == 1 ? 'par' : 'pares' }}</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300">
                                    <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                    <span>{{ $product->stock }} pares</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Sizes & Colors Pills --}}
                    @if(!empty($sizes) || $colors->isNotEmpty())
                        <div class="space-y-2 pt-1 text-xs">
                            @if(!empty($sizes))
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mr-1">Tamanhos:</span>
                                    @foreach(array_slice($sizes, 0, 7) as $s)
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-[10px]">
                                            {{ $s }}
                                        </span>
                                    @endforeach
                                    @if(count($sizes) > 7)
                                        <span class="text-[10px] font-bold text-slate-400">+{{ count($sizes) - 7 }}</span>
                                    @endif
                                </div>
                            @endif

                            @if($colors->isNotEmpty())
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mr-1">Cores:</span>
                                    @foreach($colors->take(5) as $c)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium text-[10px]">
                                            <span class="size-2 rounded-full border border-slate-300 dark:border-slate-600" style="background-color: {{ $c->hex_code }}"></span>
                                            <span>{{ $c->color_name }}</span>
                                        </span>
                                    @endforeach
                                    @if($colors->count() > 5)
                                        <span class="text-[10px] font-bold text-slate-400">+{{ $colors->count() - 5 }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Bottom Action Buttons --}}
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <a href="{{ route('admin.products.edit', $product) }}"
                            class="flex-1 py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 text-xs font-bold transition-all flex items-center justify-center gap-2 shadow-sm">
                            <span class="material-symbols-outlined text-base">edit</span>
                            <span>Editar Tênis</span>
                        </a>

                        <a href="{{ route('product.show', $product->slug) }}" target="_blank"
                            class="size-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center hover:bg-primary hover:text-white transition-all shrink-0"
                            title="Ver anúncio na loja">
                            <span class="material-symbols-outlined text-lg">visibility</span>
                        </a>

                        <button type="button"
                            onclick="confirmDelete('{{ route('admin.products.destroy', $product) }}', '{{ addslashes($product->name) }}')"
                            class="size-10 rounded-xl bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shrink-0"
                            title="Excluir tênis">
                            <span class="material-symbols-outlined text-lg">delete</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="p-8 sm:p-12 text-center bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 space-y-3">
                    <div class="size-16 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto text-slate-400">
                        <span class="material-symbols-outlined text-3xl">steps</span>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Nenhum tênis encontrado</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">Tente alterar ou limpar os filtros de busca para encontrar o modelo desejado.</p>
                    <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold text-slate-700 dark:text-slate-300">
                        <span>Limpar Filtros</span>
                    </a>
                </div>
            @endforelse
        </div>

        {{-- ======================================================== --}}
        {{-- VIEW DESKTOP: TABELA ELEGANTE E COMPLETA (md:block)      --}}
        {{-- ======================================================== --}}
        <div class="hidden md:block bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-collapse min-w-[850px]">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 uppercase text-[10px] font-black text-slate-400 tracking-wider">
                            <th class="px-6 py-4">Tênis / Modelo</th>
                            <th class="px-6 py-4">Numerações (BR)</th>
                            <th class="px-6 py-4">Cores Cadastradas</th>
                            <th class="px-6 py-4 text-right">Preço de Venda</th>
                            <th class="px-6 py-4 text-center">Estoque Total</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($products as $product)
                            @php
                                $mainImage = $product->images->where('is_main', true)->first() ?? $product->images->first();
                                $sizes = $product->sizes ?? [];
                                $colors = $product->colors ?? collect();
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                                {{-- Modelo & Foto --}}
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="size-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden border border-slate-200 dark:border-slate-700 shrink-0 relative group-hover:border-primary transition-colors">
                                            @if($mainImage)
                                                <img src="{{ str_starts_with($mainImage->path, 'http') ? $mainImage->path : asset('storage/' . $mainImage->path) }}"
                                                    alt="{{ $product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="material-symbols-outlined text-slate-400 text-2xl">steps</span>
                                            @endif
                                        </div>
                                        <div class="min-w-0 max-w-xs">
                                            <div class="flex items-center gap-1.5 mb-1">
                                                @if($product->brand)
                                                    <span class="text-[10px] font-black uppercase text-primary tracking-wider bg-primary/10 px-2 py-0.5 rounded-full">
                                                        {{ $product->brand }}
                                                    </span>
                                                @endif
                                                @if($product->gender)
                                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-500 uppercase">
                                                        {{ $product->gender }}
                                                    </span>
                                                @endif
                                            </div>
                                            <a href="{{ route('admin.products.edit', $product) }}"
                                                class="font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors text-sm truncate block"
                                                title="{{ $product->name }}">
                                                {{ $product->name }}
                                            </a>
                                            @if($product->model_line)
                                                <p class="text-[11px] text-slate-400 truncate">{{ $product->model_line }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Numerações (BR) --}}
                                <td class="px-6 py-5">
                                    @if(!empty($sizes) && count($sizes) > 0)
                                        <div class="flex flex-wrap items-center gap-1 max-w-[200px]">
                                            @foreach(array_slice($sizes, 0, 5) as $s)
                                                <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded text-[11px] font-bold">
                                                    {{ $s }}
                                                </span>
                                            @endforeach
                                            @if(count($sizes) > 5)
                                                <span class="text-[10px] font-bold text-slate-400">+{{ count($sizes) - 5 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Padrão Único</span>
                                    @endif
                                </td>

                                {{-- Cores Cadastradas --}}
                                <td class="px-6 py-5">
                                    @if($colors->isNotEmpty())
                                        <div class="flex items-center gap-1.5 flex-wrap max-w-[180px]">
                                            @foreach($colors->take(4) as $c)
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[10px] font-medium text-slate-600 dark:text-slate-300" title="{{ $c->color_name }}">
                                                    <span class="size-2 rounded-full border border-slate-300 dark:border-slate-600" style="background-color: {{ $c->hex_code }}"></span>
                                                    <span>{{ $c->color_name }}</span>
                                                </span>
                                            @endforeach
                                            @if($colors->count() > 4)
                                                <span class="text-[10px] font-bold text-slate-400">+{{ $colors->count() - 4 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[11px] text-slate-400 italic">Sem cores cadastradas</span>
                                    @endif
                                </td>

                                {{-- Preço --}}
                                <td class="px-6 py-5 text-right whitespace-nowrap">
                                    @if($product->hasDiscount())
                                        <span class="text-[10px] text-slate-400 line-through block">
                                            R$ {{ number_format($product->compare_price, 2, ',', '.') }}
                                        </span>
                                    @endif
                                    <span class="font-black text-slate-900 dark:text-white text-sm">
                                        R$ {{ number_format($product->price, 2, ',', '.') }}
                                    </span>
                                    @if($product->hasDiscount())
                                        <span class="inline-block text-[9px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 px-1.5 py-0.5 rounded ml-1">
                                            -{{ $product->discountPercentage() }}%
                                        </span>
                                    @endif
                                </td>

                                {{-- Estoque --}}
                                <td class="px-6 py-5 text-center whitespace-nowrap">
                                    @if($product->stock <= 0)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-300">
                                            <span class="size-1.5 rounded-full bg-red-500"></span>
                                            <span>Esgotado</span>
                                        </span>
                                    @elseif($product->stock <= 3)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-black bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">
                                            <span class="size-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                            <span>{{ $product->stock }} {{ $product->stock == 1 ? 'par' : 'pares' }}</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                            <span class="size-1.5 rounded-full bg-emerald-500"></span>
                                            <span>{{ $product->stock }} pares</span>
                                        </span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-5 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider {{ $product->is_active ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' : 'bg-slate-100 dark:bg-slate-800 text-slate-400' }}">
                                        <span class="size-1.5 rounded-full {{ $product->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span>{{ $product->is_active ? 'Ativo' : 'Inativo' }}</span>
                                    </span>
                                </td>

                                {{-- Ações --}}
                                <td class="px-6 py-5 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('product.show', $product->slug) }}" target="_blank"
                                            class="size-9 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-400 flex items-center justify-center hover:bg-primary/10 hover:text-primary transition-all"
                                            title="Ver na loja">
                                            <span class="material-symbols-outlined text-lg">visibility</span>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="size-9 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-400 flex items-center justify-center hover:bg-primary hover:text-white transition-all shadow-sm"
                                            title="Editar tênis">
                                            <span class="material-symbols-outlined text-lg">edit</span>
                                        </a>
                                        <button type="button"
                                            onclick="confirmDelete('{{ route('admin.products.destroy', $product) }}', '{{ addslashes($product->name) }}')"
                                            class="size-9 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-all cursor-pointer"
                                            title="Excluir tênis">
                                            <span class="material-symbols-outlined text-lg">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="size-16 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center">
                                            <span class="material-symbols-outlined text-4xl text-slate-300">steps</span>
                                        </div>
                                        <h3 class="text-slate-900 dark:text-white font-bold">Nenhum tênis encontrado</h3>
                                        <p class="text-slate-500 text-sm">Tente redefinir os filtros de busca para visualizar os calçados.</p>
                                        <a href="{{ route('admin.products.index') }}"
                                            class="mt-2 inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 text-white dark:bg-white dark:text-slate-900 font-bold rounded-xl text-xs">
                                            <span class="material-symbols-outlined text-base">refresh</span>
                                            <span>Limpar Filtros</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Paginação Responsiva --}}
        @if($products->hasPages())
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-4 sm:p-6 border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-xs text-slate-500 font-medium text-center sm:text-left">
                    Mostrando <strong class="text-slate-900 dark:text-white">{{ $products->firstItem() ?? 0 }}</strong> a <strong class="text-slate-900 dark:text-white">{{ $products->lastItem() ?? 0 }}</strong> de <strong class="text-slate-900 dark:text-white">{{ $products->total() }}</strong> tênis cadastrados
                </div>
                <div>
                    {{ $products->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm hidden">
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-sm w-full space-y-6 shadow-2xl border border-slate-100 dark:border-slate-800 animate-in fade-in zoom-in duration-200">
            <div class="size-14 rounded-2xl bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 flex items-center justify-center mx-auto shadow-sm">
                <span class="material-symbols-outlined text-3xl">delete</span>
            </div>
            <div class="text-center space-y-2">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Excluir Tênis?</h3>
                <p class="text-slate-500 text-xs leading-relaxed">
                    Você tem certeza que deseja remover <strong id="deleteProductName" class="text-slate-900 dark:text-white"></strong>? Esta ação removerá o tênis da loja.
                </p>
            </div>
            <form id="deleteForm" method="POST" class="grid grid-cols-2 gap-3">
                @csrf
                @method('DELETE')
                <button type="button" onclick="closeDeleteModal()"
                    class="py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs transition-colors cursor-pointer">
                    Cancelar
                </button>
                <button type="submit"
                    class="py-3 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition-colors shadow-lg shadow-red-600/30 cursor-pointer">
                    Sim, Excluir
                </button>
            </form>
        </div>
    </div>

    <script>
        function confirmDelete(url, name) {
            document.getElementById('deleteForm').action = url;
            document.getElementById('deleteProductName').innerText = name;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-admin-layout>
