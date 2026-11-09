<x-storefront-layout :seo="$seo">
    @php
        $availableSizes = $product->availableSizes();
        $hasSizes = !empty($availableSizes);
        $hasColors = $product->colors->count() > 0;
        $variantsJson = $product->variants->map(function($v) {
            return [
                'id' => $v->id,
                'size' => (string) $v->size,
                'color_name' => $v->color_name,
                'color_hex' => $v->color_hex,
                'stock' => (int) $v->stock,
            ];
        })->values();
        $initialImage = $product->images->where('is_main', true)->first()?->path ?? ($product->images->first()?->path ?? '');
        $productStock = (int) $product->stock;
        $storageBaseUrl = asset('storage');
    @endphp

    <script>
        function productDetailComponent() {
            return {
                variants: @json($variantsJson),
                activeImage: @json($initialImage),
                storageBaseUrl: @json($storageBaseUrl),
                quantity: 1,
                selectedColor: '',
                selectedName: '',
                selectedSize: '',
                showSizeGuide: false,
                activeTab: 'specs',
                hasSizes: @json($hasSizes),
                hasColors: @json($hasColors),
                productStock: @json($productStock),
                errorField: '',
                errorMessage: '',

                getVariant(size, colorName) {
                    if (!size) return null;
                    return this.variants.find(function(v) {
                        const matchSize = String(v.size) === String(size);
                        if (!colorName && !this.selectedColor) return matchSize;
                        const matchColor = (v.color_name && v.color_name === colorName) || (v.color_hex && v.color_hex === this.selectedColor);
                        return matchSize && matchColor;
                    }.bind(this));
                },

                getVariantStock(size, colorName) {
                    const v = this.getVariant(size, colorName);
                    return v ? v.stock : 0;
                },

                isSizeInStockForColor(size) {
                    if (!this.selectedName && !this.selectedColor) {
                        return this.variants.some(v => String(v.size) === String(size) && v.stock > 0);
                    }
                    return this.getVariantStock(size, this.selectedName) > 0;
                },

                isColorInStockForSize(colorName, colorHex) {
                    if (!this.selectedSize) {
                        return this.variants.some(v => (v.color_name === colorName || v.color_hex === colorHex) && v.stock > 0);
                    }
                    const v = this.variants.find(v => String(v.size) === String(this.selectedSize) && (v.color_name === colorName || v.color_hex === colorHex));
                    return v ? v.stock > 0 : false;
                },

                get currentVariant() {
                    if (!this.selectedSize) return null;
                    if (this.hasColors && !this.selectedName && !this.selectedColor) return null;
                    return this.getVariant(this.selectedSize, this.selectedName);
                },

                get currentStock() {
                    if (this.currentVariant) {
                        return this.currentVariant.stock;
                    }
                    if (this.selectedSize && (!this.hasColors || this.selectedName)) {
                        return 0;
                    }
                    return this.productStock;
                },

                get isCombinationOutOfStock() {
                    if (this.selectedSize && (!this.hasColors || this.selectedName || this.selectedColor)) {
                        return this.currentStock <= 0;
                    }
                    return false;
                },

                validatePurchase(event) {
                    this.errorField = '';
                    this.errorMessage = '';

                    if (this.hasSizes && !this.selectedSize) {
                        if (event) event.preventDefault();
                        this.errorField = 'size';
                        this.errorMessage = 'Por favor, selecione o tamanho do tênis antes de comprar!';
                        this.$nextTick(() => {
                            const el = document.getElementById('size-selector-block');
                            if (el) {
                                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        });
                        return false;
                    }

                    if (this.hasColors && !this.selectedColor) {
                        if (event) event.preventDefault();
                        this.errorField = 'color';
                        this.errorMessage = 'Por favor, selecione a cor desejada antes de comprar!';
                        this.$nextTick(() => {
                            const el = document.getElementById('color-selector-block');
                            if (el) {
                                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        });
                        return false;
                    }

                    if (this.isCombinationOutOfStock) {
                        if (event) event.preventDefault();
                        this.errorMessage = 'Desculpe, a numeração ' + this.selectedSize + (this.selectedName ? ' na cor ' + this.selectedName : '') + ' está esgotada!';
                        return false;
                    }

                    return true;
                },

                mainImageUrl() {
                    if (!this.activeImage) return '';
                    return this.activeImage.startsWith('http') ? this.activeImage : this.storageBaseUrl + '/' + this.activeImage;
                }
            };
        }
    </script>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10 pb-24 sm:pb-10" x-data="productDetailComponent()">
        {{-- Breadcrumb estruturado --}}
        <nav aria-label="Breadcrumb" class="flex mb-6 text-xs font-medium text-slate-400">
            <ol class="flex items-center space-x-2" itemscope itemtype="https://schema.org/BreadcrumbList">
                <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <a itemprop="item" class="hover:text-primary transition-colors" href="/"><span itemprop="name">Home</span></a>
                    <meta itemprop="position" content="1" />
                </li>
                @if($product->categories->first())
                    <li class="flex items-center space-x-1.5" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                        <span class="material-symbols-outlined text-xs">chevron_right</span>
                        <a itemprop="item" class="hover:text-primary transition-colors"
                            href="{{ route('search', ['categories' => [$product->categories->first()->id]]) }}">
                            <span itemprop="name">{{ $product->categories->first()->name }}</span>
                        </a>
                        <meta itemprop="position" content="2" />
                    </li>
                @endif
                <li class="flex items-center space-x-1.5 text-slate-800 dark:text-white font-bold" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
                    <span class="material-symbols-outlined text-xs">chevron_right</span>
                    <span itemprop="name" class="truncate max-w-[220px] sm:max-w-md">{{ $product->name }}</span>
                    <meta itemprop="position" content="{{ $product->categories->first() ? '3' : '2' }}" />
                </li>
            </ol>
        </nav>

        {{-- Grid Principal de Vendas (2 Colunas Objetivas) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            
            {{-- COLUNA ESQUERDA: Galeria de Fotos e Descrição --}}
            <div class="lg:col-span-7 space-y-6">
                {{-- Foto Principal --}}
                <div class="relative aspect-[4/3] sm:aspect-[16/11] rounded-3xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200/80 dark:border-slate-800 shadow-sm flex items-center justify-center group">
                    @if($product->hasDiscount())
                        <div class="absolute top-4 left-4 z-10 px-3 py-1 bg-red-600 text-white text-xs font-black uppercase rounded-full shadow-md tracking-wider">
                            {{ $product->discountPercentage() }}% OFF
                        </div>
                    @endif

                    <template x-if="activeImage">
                        <img :alt="'{{ $product->name }} {{ $product->brand ? '- ' . $product->brand : '' }} - Foto Principal'"
                            class="w-full h-full object-cover transition-opacity duration-300"
                            :src="mainImageUrl()"
                            fetchpriority="high"
                            decoding="async" />
                    </template>
                    <template x-if="!activeImage">
                        <div class="flex flex-col items-center justify-center text-slate-300 dark:text-slate-600">
                            <span class="material-symbols-outlined text-7xl">steps</span>
                            <span class="text-xs font-bold uppercase mt-2 tracking-widest">Sem Foto</span>
                        </div>
                    </template>
                </div>

                {{-- Miniaturas da Galeria --}}
                @if($product->images->count() > 1)
                    <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-hide">
                        @foreach($product->images as $image)
                            <button type="button" @click="activeImage = '{{ $image->path }}'"
                                :class="activeImage === '{{ $image->path }}' ? 'border-primary ring-2 ring-primary/30 scale-105' : 'border-slate-200 dark:border-slate-800 opacity-60 hover:opacity-100'"
                                class="w-20 h-20 rounded-2xl overflow-hidden border-2 transition-all p-0 flex-shrink-0 bg-slate-50 dark:bg-slate-800">
                                <img alt="{{ $product->name }} - Foto {{ $loop->iteration }}" 
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                    decoding="async"
                                    src="{{ str_starts_with($image->path, 'http') ? $image->path : asset('storage/' . $image->path) }}" />
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- INFORMAÇÕES DO PRODUTO NO DESKTOP (Abaixo da Imagem / Galeria) --}}
                <div class="hidden lg:block bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-100 dark:border-slate-800 shadow-sm space-y-5">
                    <div class="flex items-center gap-6 border-b border-slate-100 dark:border-slate-800 pb-3">
                        <button type="button" @click="activeTab = 'specs'"
                            class="text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 pb-3 -mb-3"
                            :class="activeTab === 'specs' ? 'text-primary border-b-2 border-primary' : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'">
                            <span class="material-symbols-outlined text-base">tune</span>
                            Ficha Técnica
                        </button>
                        <button type="button" @click="activeTab = 'desc'"
                            class="text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 pb-3 -mb-3"
                            :class="activeTab === 'desc' ? 'text-primary border-b-2 border-primary' : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'">
                            <span class="material-symbols-outlined text-base">description</span>
                            Detalhes do Tênis
                        </button>
                        <button type="button" @click="activeTab = 'warranty'"
                            class="text-xs font-black uppercase tracking-wider transition-all flex items-center gap-2 pb-3 -mb-3"
                            :class="activeTab === 'warranty' ? 'text-primary border-b-2 border-primary' : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'">
                            <span class="material-symbols-outlined text-base">verified</span>
                            Garantia & Troca
                        </button>
                    </div>

                    {{-- Aba Specs Desktop --}}
                    <div x-show="activeTab === 'specs'" x-transition class="space-y-4 pt-1">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <span class="text-[10px] uppercase font-black tracking-wider text-slate-400 block mb-0.5">Marca</span>
                                <span class="text-sm font-black text-slate-900 dark:text-white">{{ $product->brand ?: 'Adidas' }}</span>
                            </div>
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <span class="text-[10px] uppercase font-black tracking-wider text-slate-400 block mb-0.5">Gênero</span>
                                <span class="text-sm font-black text-slate-900 dark:text-white">{{ $product->gender ?: 'Unissex' }}</span>
                            </div>
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <span class="text-[10px] uppercase font-black tracking-wider text-slate-400 block mb-0.5">Linha / Modelo</span>
                                <span class="text-sm font-black text-slate-900 dark:text-white truncate block">{{ $product->model_line ?: 'Performance Running' }}</span>
                            </div>
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                                <span class="text-[10px] uppercase font-black tracking-wider text-slate-400 block mb-0.5">Cabedal</span>
                                <span class="text-sm font-black text-slate-900 dark:text-white truncate block">{{ $product->material ?: 'Mesh Respirável' }}</span>
                            </div>
                            <div class="p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 col-span-2">
                                <span class="text-[10px] uppercase font-black tracking-wider text-slate-400 block mb-0.5">Solado & Amortecimento</span>
                                <span class="text-sm font-black text-slate-900 dark:text-white truncate block">{{ $product->sole_material ?: 'Borracha de alta tração com amortecimento' }}</span>
                            </div>
                        </div>

                        @if($product->technical_specifications)
                            <div class="p-4 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800 text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                <strong class="text-slate-900 dark:text-white block mb-1 text-[11px] uppercase tracking-wider">Destaques Adicionais:</strong>
                                {{ $product->technical_specifications }}
                            </div>
                        @endif
                    </div>

                    {{-- Aba Descrição Desktop --}}
                    <div x-show="activeTab === 'desc'" x-transition class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed pt-1">
                        @if($product->description)
                            <p class="whitespace-pre-line">{{ $product->description }}</p>
                        @else
                            <p class="italic text-slate-400">Calçado de alta qualidade desenvolvido com foco em durabilidade, amortecimento e estabilidade para corrida e treino diário.</p>
                        @endif
                    </div>

                    {{-- Aba Garantia Desktop --}}
                    <div x-show="activeTab === 'warranty'" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                        <div class="flex items-start gap-3 p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <span class="material-symbols-outlined text-emerald-600 text-xl shrink-0 mt-0.5">verified</span>
                            <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                <strong class="text-slate-900 dark:text-white block mb-0.5 font-bold">100% Original e Autêntico</strong>
                                Todos os nossos produtos acompanham caixa oficial e garantia do fabricante contra defeitos.
                            </div>
                        </div>
                        <div class="flex items-start gap-3 p-3.5 bg-slate-50 dark:bg-slate-800/60 rounded-2xl border border-slate-100 dark:border-slate-800">
                            <span class="material-symbols-outlined text-primary text-xl shrink-0 mt-0.5">sync_alt</span>
                            <div class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                <strong class="text-slate-900 dark:text-white block mb-0.5 font-bold">Primeira Troca Grátis</strong>
                                Se o tamanho não ficar perfeito, realizamos a primeira troca sem custo adicional em até 7 dias corridos.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUNA DIREITA: Área de Compra e Conversão (Objetiva & Sem Distrações) --}}
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-100 dark:border-slate-800 shadow-sm space-y-6">
                    
                    {{-- Marca & Título --}}
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[11px] font-black uppercase tracking-widest text-primary bg-primary/10 px-3 py-1 rounded-full">
                                {{ $product->brand ?: 'Adidas' }} • {{ $product->model_line ?: 'Running' }}
                            </span>
                            @if($product->stock > 0 && $product->stock <= 5)
                                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 dark:bg-amber-950/40 px-2.5 py-0.5 rounded-full flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">local_fire_department</span>
                                    Últimos {{ $product->stock }} pares
                                </span>
                            @endif
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white leading-tight">
                            {{ $product->name }}
                        </h1>
                    </div>

                    {{-- Preço e Condições de Pagamento --}}
                    <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        @if($product->hasDiscount())
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-semibold text-slate-400 line-through">
                                    De R$ {{ number_format($product->compare_price, 2, ',', '.') }}
                                </span>
                                <span class="text-[10px] font-black px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 uppercase">
                                    Economize R$ {{ number_format($product->compare_price - $product->price, 2, ',', '.') }}
                                </span>
                            </div>
                        @endif
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                                R$ {{ number_format($product->price, 2, ',', '.') }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-sm text-emerald-600">credit_card</span>
                            em até <strong>10x de R$ {{ number_format($product->price / 10, 2, ',', '.') }}</strong> sem juros
                        </p>
                    </div>

                    {{-- Seletor de Tamanho (BR) --}}
                    @if(!empty($availableSizes))
                        <div id="size-selector-block"
                            class="transition-all duration-300 rounded-2xl p-2 -m-2"
                            :class="errorField === 'size' ? 'ring-2 ring-red-500 bg-red-50/50 dark:bg-red-950/20 shadow-sm' : ''">
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-base" :class="errorField === 'size' ? 'text-red-500' : 'text-primary'">straighten</span>
                                    <span>Tamanho:</span>
                                    <template x-if="selectedSize">
                                        <span class="text-primary font-black text-sm" x-text="'Nº ' + selectedSize"></span>
                                    </template>
                                    <template x-if="!selectedSize">
                                        <span class="font-bold text-xs" :class="errorField === 'size' ? 'text-red-600 dark:text-red-400 font-black animate-pulse' : 'text-slate-400'"
                                            x-text="errorField === 'size' ? '⚠️ Selecione um tamanho' : 'Selecione'"></span>
                                    </template>
                                </label>
                                <button type="button" @click="showSizeGuide = true"
                                    class="text-xs font-bold text-slate-500 hover:text-primary flex items-center gap-1 transition-colors underline underline-offset-2">
                                    Guia de tamanhos
                                </button>
                            </div>

                            <div class="grid grid-cols-4 sm:grid-cols-5 gap-2.5">
                                @foreach($availableSizes as $sizeOption)
                                    <button type="button"
                                        @click="selectedSize = '{{ $sizeOption }}'; if(errorField === 'size') { errorField = ''; errorMessage = ''; }"
                                        :class="{
                                            'bg-slate-900 text-white dark:bg-white dark:text-slate-900 border-slate-900 dark:border-white shadow-md ring-2 ring-primary/30 scale-105': selectedSize === '{{ $sizeOption }}',
                                            'opacity-50 bg-slate-50 dark:bg-slate-800 text-slate-400 border-dashed border-slate-300 dark:border-slate-700': !isSizeInStockForColor('{{ $sizeOption }}') && selectedSize !== '{{ $sizeOption }}',
                                            'border-red-300 dark:border-red-800 hover:border-red-500': errorField === 'size' && selectedSize !== '{{ $sizeOption }}',
                                            'bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:border-slate-400': isSizeInStockForColor('{{ $sizeOption }}') && selectedSize !== '{{ $sizeOption }}'
                                        }"
                                        class="py-3 px-2 rounded-2xl border-2 font-black text-sm transition-all flex flex-col items-center justify-center relative">
                                        <span>{{ $sizeOption }}</span>
                                        <template x-if="!isSizeInStockForColor('{{ $sizeOption }}')">
                                            <span class="text-[8px] font-bold text-red-500 uppercase tracking-tighter -mt-0.5">Esgotado</span>
                                        </template>
                                    </button>
                                @endforeach
                            </div>
                            <div x-show="errorField === 'size'" x-cloak class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">error</span>
                                <span>Por favor, clique em um dos tamanhos acima.</span>
                            </div>
                        </div>
                    @endif

                    {{-- Seletor de Cores (se houver mais de uma) --}}
                    @if($product->colors->count() > 0)
                        <div id="color-selector-block"
                            class="transition-all duration-300 rounded-2xl p-2 -m-2"
                            :class="errorField === 'color' ? 'ring-2 ring-red-500 bg-red-50/50 dark:bg-red-950/20 shadow-sm' : ''">
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-base" :class="errorField === 'color' ? 'text-red-500' : 'text-purple-600'">palette</span>
                                    <span>Cor:</span>
                                    <template x-if="selectedName || selectedColor">
                                        <span class="text-primary font-bold text-xs" x-text="selectedName || selectedColor"></span>
                                    </template>
                                    <template x-if="!selectedColor">
                                        <span class="font-bold text-xs" :class="errorField === 'color' ? 'text-red-600 dark:text-red-400 font-black animate-pulse' : 'text-slate-400'"
                                            x-text="errorField === 'color' ? '⚠️ Selecione uma cor' : 'Selecione'"></span>
                                    </template>
                                </span>
                            </div>
                            <div class="flex items-center gap-3">
                                @foreach($product->colors as $color)
                                    <button
                                        type="button"
                                        @click="selectedColor = '{{ $color->hex_code }}'; selectedName = '{{ $color->color_name }}'; if(errorField === 'color') { errorField = ''; errorMessage = ''; }"
                                        :class="{
                                            'ring-2 ring-primary ring-offset-2 scale-110 border-slate-900': selectedColor === '{{ $color->hex_code }}',
                                            'opacity-40 border-dashed': !isColorInStockForSize('{{ $color->color_name }}', '{{ $color->hex_code }}') && selectedColor !== '{{ $color->hex_code }}',
                                            'border-red-400 hover:scale-105': errorField === 'color' && selectedColor !== '{{ $color->hex_code }}',
                                            'border-slate-200 dark:border-slate-700 hover:scale-105': isColorInStockForSize('{{ $color->color_name }}', '{{ $color->hex_code }}') && selectedColor !== '{{ $color->hex_code }}'
                                        }"
                                        class="size-9 rounded-2xl border-2 transition-all shadow-sm relative"
                                        style="background-color: {{ $color->hex_code }}"
                                        title="{{ $color->color_name ?? $color->hex_code }}">
                                        <template x-if="!isColorInStockForSize('{{ $color->color_name }}', '{{ $color->hex_code }}')">
                                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                                <div class="w-full h-0.5 bg-red-500 -rotate-45"></div>
                                            </div>
                                        </template>
                                    </button>
                                @endforeach
                            </div>
                            <div x-show="errorField === 'color'" x-cloak class="mt-2 text-xs font-bold text-red-600 dark:text-red-400 flex items-center gap-1">
                                <span class="material-symbols-outlined text-sm">error</span>
                                <span>Por favor, escolha uma das opções de cor acima.</span>
                            </div>
                        </div>
                    @endif

                    {{-- Ficha Técnica e Detalhes Compactos (Visível apenas no MOBILE, logo após selecionar tamanho e cor) --}}
                    <div class="lg:hidden rounded-2xl border border-slate-200/80 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 p-4 space-y-3">
                        <div class="flex items-center gap-4 border-b border-slate-200 dark:border-slate-700/80 pb-2.5">
                            <button type="button" @click="activeTab = 'specs'"
                                class="text-[11px] font-black uppercase tracking-wider transition-all flex items-center gap-1.5"
                                :class="activeTab === 'specs' ? 'text-primary border-b-2 border-primary pb-2.5 -mb-2.5' : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'">
                                <span class="material-symbols-outlined text-sm">tune</span>
                                Ficha Técnica
                            </button>
                            <button type="button" @click="activeTab = 'desc'"
                                class="text-[11px] font-black uppercase tracking-wider transition-all flex items-center gap-1.5"
                                :class="activeTab === 'desc' ? 'text-primary border-b-2 border-primary pb-2.5 -mb-2.5' : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'">
                                <span class="material-symbols-outlined text-sm">description</span>
                                Detalhes
                            </button>
                            <button type="button" @click="activeTab = 'warranty'"
                                class="text-[11px] font-black uppercase tracking-wider transition-all flex items-center gap-1.5"
                                :class="activeTab === 'warranty' ? 'text-primary border-b-2 border-primary pb-2.5 -mb-2.5' : 'text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'">
                                <span class="material-symbols-outlined text-sm">verified</span>
                                Garantia
                            </button>
                        </div>

                        {{-- Aba: Ficha Técnica Compacta --}}
                        <div x-show="activeTab === 'specs'" x-transition class="space-y-2">
                            <div class="grid grid-cols-2 gap-x-4 gap-y-1.5 text-xs">
                                <div class="flex items-baseline justify-between border-b border-slate-200/50 dark:border-slate-700/50 py-1">
                                    <span class="text-[11px] text-slate-400 font-medium">Marca</span>
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $product->brand ?: 'Adidas' }}</span>
                                </div>
                                <div class="flex items-baseline justify-between border-b border-slate-200/50 dark:border-slate-700/50 py-1">
                                    <span class="text-[11px] text-slate-400 font-medium">Gênero</span>
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $product->gender ?: 'Unissex' }}</span>
                                </div>
                                <div class="flex items-baseline justify-between border-b border-slate-200/50 dark:border-slate-700/50 py-1 col-span-2 sm:col-span-1">
                                    <span class="text-[11px] text-slate-400 font-medium">Linha / Modelo</span>
                                    <span class="font-bold text-slate-900 dark:text-white truncate max-w-[140px]">{{ $product->model_line ?: 'Performance Running' }}</span>
                                </div>
                                <div class="flex items-baseline justify-between border-b border-slate-200/50 dark:border-slate-700/50 py-1 col-span-2 sm:col-span-1">
                                    <span class="text-[11px] text-slate-400 font-medium">Cabedal</span>
                                    <span class="font-bold text-slate-900 dark:text-white truncate max-w-[140px]">{{ $product->material ?: 'Mesh Respirável' }}</span>
                                </div>
                                <div class="flex items-baseline justify-between py-1 col-span-2">
                                    <span class="text-[11px] text-slate-400 font-medium">Amortecimento / Solado</span>
                                    <span class="font-bold text-slate-900 dark:text-white truncate max-w-[220px]">{{ $product->sole_material ?: 'Borracha com amortecimento' }}</span>
                                </div>
                            </div>

                            @if($product->technical_specifications)
                                <div class="p-2.5 bg-white dark:bg-slate-800 rounded-xl text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed border border-slate-100 dark:border-slate-700/80">
                                    <strong class="text-slate-900 dark:text-white block mb-0.5 text-[10px] uppercase tracking-wider">Destaques Adicionais:</strong>
                                    {{ $product->technical_specifications }}
                                </div>
                            @endif
                        </div>

                        {{-- Aba: Detalhes do Tênis --}}
                        <div x-show="activeTab === 'desc'" x-transition class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed py-1">
                            @if($product->description)
                                <p class="whitespace-pre-line line-clamp-5">{{ $product->description }}</p>
                            @else
                                <p class="italic text-slate-400">Calçado de alta qualidade desenvolvido com foco em durabilidade, amortecimento e estabilidade para corrida e treino diário.</p>
                            @endif
                        </div>

                        {{-- Aba: Garantia & Troca --}}
                        <div x-show="activeTab === 'warranty'" x-transition class="space-y-1.5 text-xs py-1">
                            <div class="flex items-center gap-2 p-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700/80">
                                <span class="material-symbols-outlined text-emerald-600 text-sm">verified</span>
                                <span class="text-[11px] text-slate-700 dark:text-slate-300"><strong>100% Original:</strong> Caixa original com garantia e procedência.</span>
                            </div>
                            <div class="flex items-center gap-2 p-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700/80">
                                <span class="material-symbols-outlined text-primary text-sm">sync_alt</span>
                                <span class="text-[11px] text-slate-700 dark:text-slate-300"><strong>Troca Garantida:</strong> Primeira troca de tamanho grátis em até 7 dias.</span>
                            </div>
                        </div>
                    </div>

                    {{-- Status de Disponibilidade em Tempo Real --}}
                    <template x-if="selectedSize && (!hasColors || selectedName || selectedColor)">
                        <div>
                            <template x-if="!isCombinationOutOfStock">
                                <div class="p-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 rounded-xl text-xs font-bold text-emerald-800 dark:text-emerald-300 flex items-center justify-between shadow-sm">
                                    <span class="flex items-center gap-1.5">
                                        <span class="material-symbols-outlined text-sm text-emerald-600">check_circle</span>
                                        <span>Tamanho <strong x-text="selectedSize"></strong><span x-show="selectedName" x-text="' • ' + selectedName"></span>: Em estoque</span>
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/60 text-emerald-800 dark:text-emerald-200 text-[10px] font-black">
                                        <span x-text="currentStock"></span> pares disponíveis
                                    </span>
                                </div>
                            </template>
                            <template x-if="isCombinationOutOfStock">
                                <div class="p-3 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/60 rounded-xl text-xs font-bold text-red-700 dark:text-red-300 flex items-center gap-2 shadow-sm">
                                    <span class="material-symbols-outlined text-base text-red-500 shrink-0">block</span>
                                    <span>Tamanho <strong x-text="selectedSize"></strong><span x-show="selectedName" x-text="' na cor ' + selectedName"></span> está <strong>Esgotado</strong> no momento.</span>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Botão de Compra Direta e Quantidade --}}
                    <form action="{{ route('cart.add', $product) }}" method="POST"
                        @submit="if(!validatePurchase($event)) return false;" class="space-y-4">
                        @csrf
                        <input type="hidden" name="quantity" :value="quantity">
                        <input type="hidden" name="color" :value="selectedColor">
                        <input type="hidden" name="color_name" :value="selectedName">
                        <input type="hidden" name="size" :value="selectedSize">
                        <input type="hidden" name="product_variant_id" :value="currentVariant ? currentVariant.id : ''">

                        {{-- Mensagem de Alerta Visual ao Tentar Comprar Sem Selecionar --}}
                        <div x-show="errorMessage" x-cloak x-transition
                            class="p-3 bg-red-50 dark:bg-red-950/60 border border-red-200 dark:border-red-800 rounded-xl flex items-center gap-2.5 text-xs font-black text-red-600 dark:text-red-400 shadow-sm animate-pulse">
                            <span class="material-symbols-outlined text-lg text-red-500 shrink-0">warning</span>
                            <span x-text="errorMessage"></span>
                        </div>

                        <button type="submit"
                            :disabled="isCombinationOutOfStock || {{ $product->stock <= 0 ? 'true' : 'false' }}"
                            :class="(isCombinationOutOfStock || {{ $product->stock <= 0 ? 'true' : 'false' }}) 
                                ? 'bg-slate-300 dark:bg-slate-800 text-slate-500 dark:text-slate-400 cursor-not-allowed shadow-none' 
                                : 'bg-primary hover:opacity-95 text-white shadow-lg shadow-primary/30 active:scale-[0.99]'"
                            class="w-full font-black py-4 px-6 rounded-2xl transition-all flex items-center justify-center gap-3 text-base uppercase tracking-wider">
                            <span class="material-symbols-outlined text-2xl" x-text="(isCombinationOutOfStock || {{ $product->stock <= 0 ? 'true' : 'false' }}) ? 'block' : 'shopping_bag'"></span>
                            <span x-text="isCombinationOutOfStock ? 'Variação Esgotada' : ({{ $product->stock <= 0 ? 'true' : 'false' }} ? 'Tênis Esgotado' : 'Comprar Agora')"></span>
                        </button>
                    </form>

                    {{-- Badges de Confiança Imediata --}}
                    <div class="grid grid-cols-3 gap-2 py-4 border-y border-slate-100 dark:border-slate-800 text-center">
                        <div class="flex flex-col items-center">
                            <span class="material-symbols-outlined text-emerald-600 text-xl mb-1">verified</span>
                            <span class="text-[10px] font-bold text-slate-800 dark:text-slate-200">100% Original</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <span class="material-symbols-outlined text-primary text-xl mb-1">storefront</span>
                            <span class="text-[10px] font-bold text-slate-800 dark:text-slate-200">Retirada Grátis</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <span class="material-symbols-outlined text-amber-500 text-xl mb-1">sync_alt</span>
                            <span class="text-[10px] font-bold text-slate-800 dark:text-slate-200">Troca Fácil</span>
                        </div>
                    </div>

                    {{-- Cálculo Rápido de Frete e Retirada na Loja --}}
                    <div class="space-y-3" x-data="{ 
                        zipCode: '{{ Auth::check() ? (Auth::user()->addresses()->where('is_default', true)->first()?->zip_code ?? Auth::user()->addresses()->first()?->zip_code ?? '') : '' }}',
                        shippingResult: null,
                        error: null,
                        loading: false,
                        calculate() {
                            const cleanZip = this.zipCode.replace(/\D/g, '');
                            if (cleanZip.length < 8) return;
                            
                            this.loading = true;
                            this.error = null;
                            this.shippingResult = null;

                            fetch('{{ route('shipping.calculate') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    cep: this.zipCode,
                                    product_id: {{ $product->id }},
                                    quantity: 1
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                this.loading = false;
                                if (data.error) {
                                    this.error = data.error;
                                } else {
                                    this.shippingResult = data.cotacoes;
                                }
                            })
                            .catch(() => {
                                this.loading = false;
                                this.error = 'Erro ao consultar frete.';
                            });
                        }
                    }">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-slate-300">
                            <span class="flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-base text-primary">local_shipping</span>
                                Calcular Frete & Prazo
                            </span>
                            @if(App\Models\Setting::get('enable_store_pickup', '1') === '1')
                                <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-950/40 px-2 py-0.5 rounded-full">
                                    Retirada Física Grátis
                                </span>
                            @endif
                        </div>

                        <div class="flex gap-2">
                            <input type="text" x-model="zipCode" x-mask="99999-999" @keyup.enter="calculate()"
                                class="flex-1 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary text-slate-900 dark:text-white"
                                placeholder="Seu CEP (00000-000)" />
                            <button type="button" @click="calculate()" :disabled="loading"
                                class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold px-4 py-2.5 rounded-xl text-xs hover:opacity-90 transition-all disabled:opacity-50 flex items-center justify-center min-w-[75px]">
                                <span x-show="!loading">OK</span>
                                <span x-show="loading" class="material-symbols-outlined animate-spin text-sm">sync</span>
                            </button>
                        </div>

                        {{-- Card de Retirada na Loja Rápido --}}
                        @if(App\Models\Setting::get('enable_store_pickup', '1') === '1')
                            <div class="p-3 bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-800/30 rounded-xl flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2.5">
                                    <span class="material-symbols-outlined text-emerald-600 text-lg">storefront</span>
                                    <div>
                                        <span class="font-bold text-slate-800 dark:text-slate-200 block">Retirar na Loja Física</span>
                                        <span class="text-[10px] text-slate-500">Pronto em até 2h úteis</span>
                                    </div>
                                </div>
                                <span class="text-xs font-black text-emerald-600 uppercase">Grátis</span>
                            </div>
                        @endif

                        {{-- Erro Frete --}}
                        <div x-show="error" x-cloak class="p-2.5 bg-red-50 text-red-600 rounded-xl text-xs font-bold" x-text="error"></div>

                        {{-- Resultados de Envio --}}
                        <div x-show="shippingResult && shippingResult.length > 0" x-cloak class="space-y-1.5 pt-1">
                            <template x-for="option in shippingResult" :key="option.servico">
                                <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-800 rounded-xl text-xs">
                                    <div>
                                        <span class="font-bold text-slate-900 dark:text-white" x-text="option.servico"></span>
                                        <span class="text-[10px] text-slate-400 block" x-text="'Prazo: ' + option.prazo + ' dias úteis'"></span>
                                    </div>
                                    <span class="font-black text-primary" x-text="'R$ ' + parseFloat(option.valor).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Barra Fixa no Mobile para Compra Instantânea --}}
        <div class="fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 p-3 sm:hidden shadow-2xl flex items-center justify-between gap-3">
            <div>
                <span class="text-[10px] text-slate-400 block uppercase font-bold">Por apenas</span>
                <span class="text-lg font-black text-slate-900 dark:text-white">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
            </div>
            <form action="{{ route('cart.add', $product) }}" method="POST"
                @submit="if(!validatePurchase($event)) return false;" class="flex-1 max-w-[200px]">
                @csrf
                <input type="hidden" name="quantity" :value="quantity">
                <input type="hidden" name="color" :value="selectedColor">
                <input type="hidden" name="color_name" :value="selectedName">
                <input type="hidden" name="size" :value="selectedSize">
                <button type="submit"
                    {{ $product->stock <= 0 ? 'disabled' : '' }}
                    class="w-full bg-primary text-white font-black py-3 px-4 rounded-xl text-xs uppercase tracking-wider flex items-center justify-center gap-1.5 shadow-md shadow-primary/20">
                    <span class="material-symbols-outlined text-base">shopping_bag</span>
                    Comprar
                </button>
            </form>
        </div>

        {{-- Modal Guia de Tamanhos (BR) --}}
        <div x-show="showSizeGuide" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 max-w-lg w-full space-y-6 shadow-2xl border border-slate-100 dark:border-slate-800"
                @click.away="showSizeGuide = false">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined">straighten</span>
                        </div>
                        <div>
                            <h3 class="text-base font-black text-slate-900 dark:text-white">Tabela de Medidas (Padrão BR)</h3>
                            <p class="text-xs text-slate-400">Encontre o tamanho perfeito para seu tênis</p>
                        </div>
                    </div>
                    <button type="button" @click="showSizeGuide = false"
                        class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-800 text-[10px] font-black uppercase tracking-wider text-slate-400">
                                <th class="py-2.5 px-3">Tamanho BR</th>
                                <th class="py-2.5 px-3">Palmilha (cm)</th>
                                <th class="py-2.5 px-3">EUA (US)</th>
                                <th class="py-2.5 px-3">Europa (EUR)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:border-slate-800 font-semibold text-slate-700 dark:text-slate-300">
                            <tr><td class="py-2.5 px-3 font-black text-primary">36 BR</td><td class="py-2.5 px-3">24,0 cm</td><td class="py-2.5 px-3">5.5 US</td><td class="py-2.5 px-3">37.5 EU</td></tr>
                            <tr><td class="py-2.5 px-3 font-black text-primary">37 BR</td><td class="py-2.5 px-3">24,5 cm</td><td class="py-2.5 px-3">6.0 US</td><td class="py-2.5 px-3">38.0 EU</td></tr>
                            <tr><td class="py-2.5 px-3 font-black text-primary">38 BR</td><td class="py-2.5 px-3">25,0 cm</td><td class="py-2.5 px-3">7.0 US</td><td class="py-2.5 px-3">39.0 EU</td></tr>
                            <tr><td class="py-2.5 px-3 font-black text-primary">39 BR</td><td class="py-2.5 px-3">25,5 cm</td><td class="py-2.5 px-3">7.5 US</td><td class="py-2.5 px-3">40.0 EU</td></tr>
                            <tr><td class="py-2.5 px-3 font-black text-primary">40 BR</td><td class="py-2.5 px-3">26,5 cm</td><td class="py-2.5 px-3">8.5 US</td><td class="py-2.5 px-3">41.0 EU</td></tr>
                            <tr><td class="py-2.5 px-3 font-black text-primary">41 BR</td><td class="py-2.5 px-3">27,5 cm</td><td class="py-2.5 px-3">9.5 US</td><td class="py-2.5 px-3">42.5 EU</td></tr>
                            <tr><td class="py-2.5 px-3 font-black text-primary">42 BR</td><td class="py-2.5 px-3">28,0 cm</td><td class="py-2.5 px-3">10.0 US</td><td class="py-2.5 px-3">43.0 EU</td></tr>
                            <tr><td class="py-2.5 px-3 font-black text-primary">43 BR</td><td class="py-2.5 px-3">29,0 cm</td><td class="py-2.5 px-3">11.0 US</td><td class="py-2.5 px-3">44.5 EU</td></tr>
                            <tr><td class="py-2.5 px-3 font-black text-primary">44 BR</td><td class="py-2.5 px-3">30,0 cm</td><td class="py-2.5 px-3">12.0 US</td><td class="py-2.5 px-3">45.0 EU</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-3.5 bg-slate-50 dark:bg-slate-800 rounded-2xl text-[11px] text-slate-500 leading-relaxed">
                    💡 <strong>Como medir seu pé:</strong> Pise descalço sobre uma folha de papel com o calcanhar apoiado na parede, risque na ponta do dedo mais longo e meça com uma régua em centímetros.
                </div>

                <button type="button" @click="showSizeGuide = false"
                    class="w-full py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-xl font-bold text-xs uppercase tracking-wider">
                    Entendi, Voltar para o Tênis
                </button>
            </div>
        </div>
    </main>
</x-storefront-layout>
