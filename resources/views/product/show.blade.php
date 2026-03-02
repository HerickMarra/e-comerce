<x-storefront-layout>
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ 
        activeImage: '{{ $product->images->where('is_main', true)->first()?->path ?? ($product->images->first()?->path ?? '') }}',
        quantity: 1,
        isZoomed: false,
        zoomX: 0,
        zoomY: 0,
        selectedColor: '{{ $product->colors->first()?->hex_code }}',
        selectedName: '{{ $product->colors->first()?->color_name }}',
        mainImageUrl() {
            if (!this.activeImage) return '';
            return this.activeImage.startsWith('http') ? this.activeImage : '{{ asset('storage') }}/' + this.activeImage;
        },
        handleMouseMove(e) {
            if (!this.isZoomed) return;
            const { left, top, width, height } = e.currentTarget.getBoundingClientRect();
            this.zoomX = ((e.pageX - left - window.scrollX) / width) * 100;
            this.zoomY = ((e.pageY - top - window.scrollY) / height) * 100;
        }
    }">
        <nav aria-label="Breadcrumb" class="flex mb-8 text-sm font-medium text-slate-400">
            <ol class="flex items-center space-x-2">
                <li><a class="hover:text-primary transition-colors" href="/">Home</a></li>
                @if($product->categories->first())
                    <li class="flex items-center space-x-2">
                        <span class="material-symbols-outlined text-sm">chevron_right</span>
                        <a class="hover:text-primary transition-colors"
                            href="#">{{ $product->categories->first()->name }}</a>
                    </li>
                @endif
                <li class="flex items-center space-x-2 text-slate-900 dark:text-white">
                    <span class="material-symbols-outlined text-sm">chevron_right</span>
                    <span>{{ $product->name }}</span>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <!-- Galeria de Imagens -->
            <div class="space-y-6">
                <div class="aspect-[4/3] rounded-3xl overflow-hidden bg-slate-100 dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-800 relative cursor-zoom-in"
                    @mouseenter="isZoomed = true" @mouseleave="isZoomed = false" @mousemove="handleMouseMove($event)">
                    <template x-if="activeImage">
                        <img :alt="'{{ $product->name }}'"
                            class="w-full h-full object-cover transition-transform duration-200 ease-out"
                            :src="mainImageUrl()"
                            :style="isZoomed ? `transform: scale(2); transform-origin: ${zoomX}% ${zoomY}%` : ''" />
                    </template>
                    <template x-if="!activeImage">
                        <div class="w-full h-full flex items-center justify-center bg-slate-100">
                            <span class="material-symbols-outlined text-slate-300 text-8xl">image</span>
                        </div>
                    </template>
                </div>

                @if($product->images->count() > 1)
                    <div class="grid grid-cols-4 gap-4">
                        @foreach($product->images as $image)
                            <button @click="activeImage = '{{ $image->path }}'"
                                :class="activeImage === '{{ $image->path }}' ? 'border-primary ring-2 ring-primary/20' : 'border-transparent opacity-70 hover:opacity-100'"
                                class="aspect-square rounded-xl overflow-hidden border-2 transition-all p-0">
                                <img alt="Thumbnail" class="w-full h-full object-cover"
                                    src="{{ str_starts_with($image->path, 'http') ? $image->path : asset('storage/' . $image->path) }}" />
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- Descrição do Produto (Movida para a esquerda para não empurrar os botões) --}}
                @if($product->description)
                    <div class="pt-10 mt-6 border-t border-slate-100 dark:border-slate-800"
                         x-data="{ expanded: false, isClamped: true }"
                         x-init="setTimeout(() => { if($refs.desc) isClamped = $refs.desc.scrollHeight > 200 }, 1500)">
                        <span
                            class="block text-sm font-bold text-slate-900 dark:text-white mb-4 uppercase tracking-[0.15em]">Sobre
                            o Produto</span>
                        <div
                            class="relative overflow-hidden transition-all duration-500 ease-in-out"
                            :class="!expanded ? 'max-h-48' : 'max-h-[2000px]'">
                            <div
                                x-ref="desc"
                                class="prose prose-sm dark:prose-invert text-slate-600 dark:text-slate-400 leading-relaxed max-w-none">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                            
                            <div x-show="isClamped && !expanded" 
                                 class="absolute bottom-0 inset-x-0 h-24 bg-gradient-to-t from-white dark:from-slate-900 to-transparent pointer-events-none">
                            </div>
                        </div>
                        
                        <button x-show="isClamped" 
                                @click="expanded = !expanded" 
                                class="mt-4 text-primary font-bold text-xs uppercase tracking-[0.2em] flex items-center gap-2 hover:gap-3 transition-all"
                                type="button">
                            <span x-text="expanded ? 'Ver menos' : 'Ver mais completo'">Ver mais completo</span>
                            <span class="material-symbols-outlined text-sm transition-transform duration-300" :class="expanded ? 'rotate-180' : ''">expand_more</span>
                        </button>
                    </div>
                @endif
            </div>

            <!-- Detalhes do Produto -->
            <div class="flex flex-col">
                <div class="mb-2">
                    <span class="text-primary font-bold tracking-widest uppercase text-xs">Coleção Premium
                        {{ date('Y') }}</span>
                </div>
                <h1 class="text-4xl font-bold text-slate-900 dark:text-white mb-4 leading-tight">{{ $product->name }}
                </h1>

                <div class="flex items-center gap-4 mb-8">
                    <div class="flex items-center gap-1">
                        @for($i = 0; $i < 5; $i++)
                            <span class="material-symbols-outlined text-amber-400 fill-1">star</span>
                        @endfor
                    </div>
                    <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">5.0</span>
                    <span class="text-sm text-slate-400">(Novo Lançamento)</span>
                </div>

                <div class="mb-10">
                    <span class="text-3xl font-bold text-slate-900 dark:text-white">R$
                        {{ number_format($product->price, 2, ',', '.') }}</span>
                    <p class="text-sm text-slate-500 mt-1">ou 10x de R$
                        {{ number_format($product->price / 10, 2, ',', '.') }} sem juros
                    </p>
                </div>

                <div class="space-y-8 mb-10">
                    @if($product->colors->count() > 0)
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span
                                    class="block text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wide">Cor:
                                    <span class="text-primary ml-2 uppercase"
                                        x-text="selectedName || selectedColor"></span></span>
                            </div>
                            <div class="flex items-center gap-4">
                                @foreach($product->colors as $color)
                                    <button
                                        type="button"
                                        @click="selectedColor = '{{ $color->hex_code }}'; selectedName = '{{ $color->color_name }}'"
                                        :class="selectedColor === '{{ $color->hex_code }}' ? 'ring-2 ring-primary/20 ring-offset-2 border-primary scale-110' : 'border-slate-200 ring-1 ring-slate-100 shadow-sm'"
                                        class="w-10 h-10 rounded-full border-2 transition-all hover:scale-110"
                                        style="background-color: {{ $color->hex_code }}"
                                        title="{{ $color->color_name ?? $color->hex_code }}">
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <span
                            class="block text-sm font-bold text-slate-900 dark:text-white mb-4 uppercase tracking-wide">Quantidade</span>
                        <div
                            class="flex items-center w-32 border border-slate-200 dark:border-slate-700 rounded-xl px-2 py-1 bg-white dark:bg-slate-800 shadow-sm {{ $product->stock <= 0 ? 'opacity-50 pointer-events-none' : '' }}">
                            <button @click="if(quantity > 1) quantity--"
                                class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined">remove</span>
                            </button>
                            <input
                                class="w-full text-center border-none bg-transparent focus:ring-0 font-bold text-slate-900 dark:text-white"
                                type="text" x-model="quantity" readonly />
                            <button @click="if(quantity < {{ $product->stock }}) quantity++"
                                class="w-8 h-8 flex items-center justify-center text-slate-500 hover:text-primary transition-colors"
                                :disabled="quantity >= {{ $product->stock }}">
                                <span class="material-symbols-outlined">add</span>
                            </button>
                        </div>
                        @if($product->stock > 0 && $product->stock <= 5)
                            <p class="text-[10px] font-bold text-amber-600 mt-2 uppercase tracking-widest">Apenas {{ $product->stock }} unidades em estoque!</p>
                        @endif
                    </div>
                </div>

                <!-- Cálculo de Frete -->
                 <div class="mb-10 p-6 bg-slate-50 dark:bg-slate-800/50 rounded-3xl border border-slate-100 dark:border-slate-800"
                    x-data="{ 
                        zipCode: '{{ Auth::check() ? (Auth::user()->addresses()->where('is_default', true)->first()?->zip_code ?? Auth::user()->addresses()->first()?->zip_code ?? '') : '' }}',
                        shippingResult: null,
                        error: null,
                        loading: false,
                        showAddressSelector: false,
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
                                    quantity: this.quantity
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
                            .catch(err => {
                                this.loading = false;
                                this.error = 'Ocorreu um erro ao calcular o frete.';
                                console.error(err);
                            });
                        },
                        selectAddress(cep) {
                            this.zipCode = cep;
                            this.showAddressSelector = false;
                            this.calculate();
                        }
                    }" x-init="if(zipCode) calculate()">

                    <div class="flex items-center gap-2 mb-4">
                        <span class="material-symbols-outlined text-primary text-xl">local_shipping</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">Calcular
                            Frete</span>
                    </div>

                    <div class="flex gap-2 relative">
                        <div class="relative flex-1">
                            <input type="text" x-model="zipCode" x-mask="99999-999" @keyup.enter="calculate()"
                                class="w-full bg-white dark:bg-slate-900 border-2 border-slate-100 dark:border-slate-700 rounded-2xl px-5 py-3 text-sm font-bold focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all placeholder:text-slate-300"
                                placeholder="00000-000" />

                            @auth
                                @if(Auth::user()->addresses->count() > 0)
                                    <button @click="showAddressSelector = !showAddressSelector"
                                        class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-xl">location_on</span>
                                    </button>
                                @endif
                            @endauth
                        </div>
                        <button @click="calculate()" :disabled="loading"
                            class="bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold px-8 rounded-2xl hover:scale-105 transition-all disabled:opacity-50 flex items-center justify-center min-w-[120px]">
                            <template x-if="!loading"><span>Calcular</span></template>
                            <template x-if="loading"><span
                                    class="material-symbols-outlined animate-spin text-xl">sync</span></template>
                        </button>
                    </div>
                    
                    <p class="text-[10px] text-slate-500 mt-3 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[12px]">info</span>
                        Aqui é apenas uma simulação. A escolha oficial do frete é feita na etapa final de pagamento.
                    </p>

                    <div class="flex gap-2 relative">
                        <!-- Seletor de Endereço (Dropdown) -->
                        @auth
                            <div x-show="showAddressSelector" x-cloak @click.away="showAddressSelector = false"
                                class="absolute top-full left-0 right-0 mt-3 p-4 bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-2xl z-20 space-y-2 max-h-60 overflow-y-auto"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2 mb-2">Seus
                                    Endereços Salvos</p>
                                @foreach(Auth::user()->addresses as $addr)
                                    <button @click="selectAddress('{{ $addr->zip_code }}')"
                                        class="w-full text-left p-3 rounded-2xl hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all border border-transparent hover:border-slate-100 dark:hover:border-slate-700 group">
                                        <div class="flex items-center gap-3">
                                            <span
                                                class="material-symbols-outlined text-slate-300 group-hover:text-primary transition-colors text-lg">
                                                {{ $addr->label === 'Trabalho' ? 'work' : 'home' }}
                                            </span>
                                            <div>
                                                <p class="text-xs font-bold text-slate-900 dark:text-white">
                                                    {{ $addr->label ?? 'Endereço' }}</p>
                                                <p class="text-[10px] text-slate-500 font-medium">{{ $addr->street }},
                                                    {{ $addr->number }}</p>
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        @endauth
                    </div>

                    <!-- Mensagem de Erro -->
                    <div x-show="error" x-cloak x-transition class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/30 rounded-2xl text-red-600 dark:text-red-400 text-xs font-bold flex items-center gap-3">
                        <span class="material-symbols-outlined text-lg">error</span>
                        <span x-text="error"></span>
                    </div>

                    <!-- Resultados do Frete -->
                    <div x-show="shippingResult" x-cloak x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-y-4"
                        x-transition:enter-end="opacity-100 translate-y-0" class="mt-6 space-y-3">
                        <template x-for="option in shippingResult" :key="option.servico">
                            <div
                                class="flex items-center justify-between p-4 bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm hover:border-primary/30 transition-colors group">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 bg-slate-50 dark:bg-slate-800 rounded-xl flex items-center justify-center text-slate-400 group-hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined text-xl"
                                            x-text="option.servico.toLowerCase().includes('express') || option.servico.toLowerCase().includes('sedex') ? 'bolt' : 'local_shipping'"></span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-900 dark:text-white"
                                            x-text="option.servico"></span>
                                        <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider"
                                            x-text="'Prazo: ' + option.prazo + (option.prazo == 1 ? ' dia útil' : ' dias úteis')"></span>
                                    </div>
                                </div>
                                <span class="font-black text-primary"
                                    x-text="'R$ ' + parseFloat(option.valor).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <form action="{{ route('cart.add', $product) }}" method="POST" class="flex gap-4 mb-12">
                    @csrf
                    <input type="hidden" name="quantity" :value="quantity">
                    <input type="hidden" name="color" :value="selectedColor">
                    <input type="hidden" name="color_name" :value="selectedName">
                    <button
                        type="submit"
                        {{ $product->stock <= 0 ? 'disabled' : '' }}
                        class="flex-1 {{ $product->stock <= 0 ? 'bg-slate-400 cursor-not-allowed' : 'bg-primary shadow-primary/30' }} text-white font-bold py-5 rounded-2xl transition-all shadow-xl flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">{{ $product->stock <= 0 ? 'block' : 'shopping_bag' }}</span>
                        {{ $product->stock <= 0 ? 'Produto Esgotado' : 'Adicionar ao Carrinho' }}
                    </button>
                    <button
                        type="button"
                        class="w-16 h-16 flex items-center justify-center rounded-2xl border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-slate-400 hover:text-red-500">
                        <span class="material-symbols-outlined">favorite</span>
                    </button>
                </form>

                <!-- Accordion Simulado -->
                <div class="divide-y divide-slate-100 dark:divide-slate-800 border-y border-slate-100 dark:border-slate-800"
                    x-data="{ openTab: null }">
                    @if($product->technical_specifications)
                    <div class="py-5">
                        <button @click="openTab = openTab === 'specs' ? null : 'specs'"
                            class="flex items-center justify-between w-full font-bold text-slate-900 dark:text-white uppercase tracking-wide text-sm">
                            Especificações Técnicas
                            <span class="material-symbols-outlined transitioning-transform duration-300"
                                :class="openTab === 'specs' ? 'rotate-45' : ''">add</span>
                        </button>
                        <div x-show="openTab === 'specs'" x-collapse
                            class="mt-4 text-sm text-slate-500 leading-relaxed whitespace-pre-line">
                            {{ $product->technical_specifications }}
                        </div>
                    </div>
                    @endif
                    <div class="py-5">
                        <button @click="openTab = openTab === 'delivery' ? null : 'delivery'"
                            class="flex items-center justify-between w-full font-bold text-slate-900 dark:text-white uppercase tracking-wide text-sm">
                            Entrega e Montagem Elite
                            <span class="material-symbols-outlined transitioning-transform duration-300"
                                :class="openTab === 'delivery' ? 'rotate-45' : ''">add</span>
                        </button>
                        <div x-show="openTab === 'delivery'" x-collapse
                            class="mt-4 text-sm text-slate-500 leading-relaxed">
                            <p>Oferecemos entrega especializada e montagem profissional inclusa para garantir que sua
                                peça chegue impecável ao seu lar.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-storefront-layout>