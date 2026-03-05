<x-storefront-layout>
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="cartManager">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div class="lg:col-span-8">
                <h1 class="text-3xl font-light mb-10 text-slate-900 dark:text-white">Seu Carrinho</h1>

                <div x-show="status === 'item-removed'" x-transition x-cloak
                    class="mb-6 p-4 bg-amber-50 border border-amber-100 text-amber-700 rounded-2xl flex items-center gap-3 font-bold text-sm shadow-sm">
                    <span class="material-symbols-outlined">delete</span>
                    Item removido do carrinho.
                </div>

                <div x-show="hasStockIssues" x-cloak x-transition
                    class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl flex items-center gap-3 font-bold text-sm shadow-sm">
                    <span class="material-symbols-outlined">error</span>
                    Existem itens com problemas de estoque no seu carrinho. Por favor, remova-os ou ajuste a quantidade
                    para prosseguir.
                </div>

                @if($errors->any())
                    <div
                        class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl space-y-1 font-bold text-sm shadow-sm">
                        @foreach($errors->all() as $error)
                            <div class="flex items-center gap-3">
                                <span class="material-symbols-outlined text-sm">warning</span>
                                {{ $error }}
                            </div>
                        @endforeach
                    </div>
                @endif

                <template x-if="itemsCount > 0">
                    <div class="space-y-8">
                        <template x-for="(item, id) in items" :key="id">
                            <div class="flex gap-6 pb-8 border-b border-slate-100 dark:border-slate-800">
                                <div
                                    class="w-32 h-40 bg-slate-100 dark:bg-slate-800 rounded-xl overflow-hidden shrink-0">
                                    <img :alt="item.name" class="w-full h-full object-cover"
                                        :src="item.image.startsWith('http') ? item.image : '/storage/' + item.image">
                                </div>
                                <div class="flex-1 flex flex-col justify-between py-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="text-lg font-medium text-slate-900 dark:text-white"
                                                x-text="item.name"></h3>
                                            <template x-if="item.color_name && !item.available_colors?.length">
                                                <p
                                                    class="text-xs text-slate-500 mt-1 uppercase tracking-wider font-bold">
                                                    Cor: <span x-text="item.color_name"></span></p>
                                            </template>

                                            <template x-if="item.available_colors && item.available_colors.length > 0">
                                                <div class="mt-3">
                                                    <p
                                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">
                                                        Alterar Cor</p>
                                                    <div class="flex flex-wrap gap-2">
                                                        <template x-for="color in item.available_colors"
                                                            :key="color.id">
                                                            <button type="button"
                                                                @click="updateColor(id, color.hex_code, color.color_name)"
                                                                class="size-6 rounded-full border-2 transition-all hover:scale-110"
                                                                :class="item.color === color.hex_code ? 'border-primary ring-2 ring-primary/20' : 'border-slate-100 dark:border-slate-800'"
                                                                :style="'background-color: ' + color.hex_code"
                                                                :title="color.color_name">
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </template>

                                            <template x-if="item.out_of_stock">
                                                <span
                                                    class="inline-flex mt-2 px-2 py-0.5 bg-red-100 text-red-600 text-[10px] font-bold uppercase rounded-md">Esgotado</span>
                                            </template>
                                            <template x-if="item.insufficient_stock">
                                                <span
                                                    class="inline-flex mt-2 px-2 py-0.5 bg-amber-100 text-amber-600 text-[10px] font-bold uppercase rounded-md">
                                                    Apenas <span class="mx-1" x-text="item.available_stock"></span>
                                                    disponíveis
                                                </span>
                                            </template>
                                        </div>
                                        <p class="text-lg font-semibold text-slate-900 dark:text-white"
                                            x-text="formatMoney(item.price)"></p>
                                    </div>
                                    <div class="flex items-center justify-between mt-4">
                                        <div
                                            class="flex items-center border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
                                            <button type="button" @click="updateQty(id, item.quantity - 1)"
                                                class="w-10 h-10 flex items-center justify-center text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors"
                                                :disabled="item.quantity <= 1">-</button>
                                            <span
                                                class="w-10 text-center text-sm font-medium border-none bg-transparent"
                                                x-text="item.quantity"></span>
                                            <button type="button" @click="updateQty(id, item.quantity + 1)"
                                                class="w-10 h-10 flex items-center justify-center text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">+</button>
                                        </div>

                                        <button type="button" @click="removeItem(id)"
                                            class="text-xs text-slate-400 hover:text-red-500 uppercase tracking-widest font-bold transition-colors">Remover</button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>

                <div x-show="itemsCount === 0" x-transition x-cloak
                    class="py-20 text-center bg-slate-50 dark:bg-slate-900/50 rounded-[2.5rem] border-2 border-dashed border-slate-100 dark:border-slate-800">
                    <span class="material-symbols-outlined text-6xl text-slate-200 mb-4">shopping_bag</span>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Seu carrinho está vazio</h2>
                    <p class="text-slate-500 mb-8">Encontre peças exclusivas para transformar sua casa.</p>
                    <a href="/"
                        class="inline-flex items-center justify-center px-8 py-4 bg-primary text-white font-bold rounded-2xl hover:scale-105 transition-all shadow-xl shadow-primary/20">
                        Explorar Coleções
                    </a>
                </div>

            </div>

            <aside class="lg:col-span-4">
                <div
                    class="sticky top-32 bg-slate-50 dark:bg-slate-900 rounded-2xl p-8 border border-slate-100 dark:border-slate-800">
                    <h2 class="text-xl font-medium mb-6 text-slate-900 dark:text-white">Resumo do Pedido</h2>
                    <div class="space-y-4 mb-6 border-b border-slate-200 dark:border-slate-800 pb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-500">Subtotal</span>
                            <span class="font-bold text-slate-900 dark:text-white" x-text="formatMoney(total)"></span>
                        </div>
                        <div class="space-y-3">
                            <span class="text-sm text-slate-500">Entrega / Retirada</span>

                            {{-- Always-visible: Store Pickup --}}
                            @if(App\Models\Setting::get('enable_store_pickup', '1') === '1')
                                <label class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all"
                                    :class="selectedShipping?._type === 'pickup' ? 'border-primary bg-primary/5' : 'border-slate-100 hover:border-slate-300'">
                                    <input type="radio" class="accent-primary"
                                        :checked="selectedShipping?._type === 'pickup'"
                                        @change="selectShipping({ _type: 'pickup', modalidade: 'Retirar na Loja', prazo: 0, valor: 0, simulacao_id: null })">
                                    <div class="flex-1">
                                        <p
                                            class="text-xs font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-sm text-primary">store</span>
                                            Retirar na Loja
                                        </p>
                                        <p class="text-[10px] text-slate-500">Combine o horário após o pedido</p>
                                    </div>
                                    <p class="text-sm font-bold text-emerald-500">Grátis</p>
                                </label>
                            @endif

                            {{-- Divider --}}
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-px bg-slate-100"></div>
                                <span class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">ou calcule
                                    o frete</span>
                                <div class="flex-1 h-px bg-slate-100"></div>
                            </div>

                            <div class="flex gap-2">
                                <input id="cep-input"
                                    class="flex-1 bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2 text-sm focus:ring-primary focus:border-primary outline-none"
                                    placeholder="00000-000" type="text" x-mask="99999-999" value="{{ $defaultCep }}"
                                    @keydown.enter.prevent="calculateShipping()" />
                                <button type="button" @click="calculateShipping()" :disabled="shippingLoading"
                                    class="px-4 py-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 rounded-lg text-xs font-bold uppercase transition-colors hover:bg-slate-800 disabled:opacity-50">
                                    <span x-show="!shippingLoading">OK</span>
                                    <span x-show="shippingLoading"
                                        class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                </button>
                            </div>

                            {{-- Error --}}
                            <p x-show="shippingError" x-text="shippingError" class="text-xs text-red-500 font-medium">
                            </p>

                            {{-- EnviaMais Shipping Options --}}
                            <div x-show="shippingOptions.length > 0" class="space-y-2">
                                <template x-for="(opt, i) in shippingOptions" :key="i">
                                    <label
                                        class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all"
                                        :class="selectedShipping?._type !== 'pickup' && selectedShipping?.modalidade === opt.modalidade && selectedShipping?.simulacao_id === opt.simulacao_id
                                               ? 'border-primary bg-primary/5' : 'border-slate-100 hover:border-slate-300'">
                                        <input type="radio" class="accent-primary"
                                            :checked="selectedShipping?._type !== 'pickup' && selectedShipping?.modalidade === opt.modalidade && selectedShipping?.simulacao_id === opt.simulacao_id"
                                            @change="selectShipping(opt)">
                                        <div class="flex-1">
                                            <p class="text-xs font-bold text-slate-900 dark:text-white"
                                                x-text="opt.servico"></p>
                                            <p class="text-[10px] text-slate-500"><span x-text="opt.prazo"></span> dias
                                                úteis</p>
                                        </div>
                                        <p class="text-sm font-bold text-primary" x-text="formatMoney(opt.valor)"></p>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center mb-8">
                        <span class="text-lg font-medium text-slate-900 dark:text-white">Total</span>
                        <div class="text-right">
                            <span class="text-2xl font-bold text-primary"
                                x-text="formatMoney(total + (selectedShipping ? selectedShipping.valor : 0))"></span>
                            <p x-show="selectedShipping" class="text-[10px] text-slate-400 mt-0.5">inclui frete <span
                                    x-text="formatMoney(selectedShipping?.valor ?? 0)"></span></p>
                        </div>
                    </div>

                    <div x-show="itemsCount > 0">
                        <button x-show="hasStockIssues" disabled
                            class="block w-full text-center bg-slate-400 text-white font-bold py-4 rounded-xl cursor-not-allowed uppercase tracking-widest text-sm mb-4">
                            Itens Indisponíveis
                        </button>

                        <div x-show="!hasStockIssues">
                            <a :href="selectedShipping ? '{{ route('checkout') }}' : 'javascript:void(0)'"
                                :class="selectedShipping ? 'bg-primary shadow-primary/20 cursor-pointer' : 'bg-slate-300 cursor-not-allowed shadow-none'"
                                class="block w-full text-center text-white font-bold py-4 rounded-xl transition-all shadow-lg uppercase tracking-widest text-sm mb-2">
                                Finalizar Compra
                            </a>
                            <p x-show="!selectedShipping"
                                class="text-[10px] text-amber-600 font-bold text-center mb-4 flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-xs">info</span>
                                Selecione o frete para continuar
                            </p>
                        </div>
                    </div>

                    <a href="/"
                        class="block w-full text-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition-colors text-sm font-medium underline underline-offset-4">
                        Continuar Comprando
                    </a>

                    <div
                        class="mt-8 pt-8 border-t border-slate-200 dark:border-slate-800 flex flex-wrap gap-4 justify-center grayscale opacity-50">
                        <div class="h-4 w-8 bg-slate-200 rounded"></div>
                        <div class="h-4 w-8 bg-slate-200 rounded"></div>
                        <div class="h-4 w-8 bg-slate-200 rounded"></div>
                    </div>
                </div>
            </aside>

            @if($recommended->count() > 0)
                <div class="lg:col-span-8">
                    <section class="">
                        <h2 class="text-xl font-medium mb-8">Produtos Recomendados</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            @foreach($recommended as $prod)
                                <div class="group cursor-pointer"
                                    onclick="window.location='{{ route('product.show', $prod->slug) }}'">
                                    <div
                                        class="aspect-square bg-slate-100 dark:bg-slate-800 rounded-xl overflow-hidden mb-4 relative text-slate-900 dark:text-white flex items-center justify-center">
                                        @php
                                            $mainImg = $prod->images->where('is_main', true)->first();
                                            $imgPath = $mainImg ? ($mainImg->path ?? null) : null;
                                        @endphp
                                        @if($imgPath)
                                            <img alt="{{ $prod->name }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                                src="{{ str_starts_with($imgPath, 'http') ? $imgPath : asset('storage/' . $imgPath) }}">
                                        @else
                                            <span class="material-symbols-outlined text-4xl opacity-20">image</span>
                                        @endif
                                        <button
                                            class="absolute bottom-4 right-4 w-10 h-10 bg-white dark:bg-slate-800 rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition-opacity border border-slate-100 dark:border-slate-800">
                                            <span class="material-symbols-outlined text-slate-900 dark:text-white">add</span>
                                        </button>
                                    </div>
                                    <h4 class="text-sm font-medium text-slate-900 dark:text-white">{{ $prod->name }}</h4>
                                    <p class="text-sm text-slate-500">R$ {{ number_format($prod->price, 2, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>
            @endif
        </div>
    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cartManager', () => ({
                items: @json($cart),
                total: {{ $total }},
                status: '',
                hasStockIssues: {{ $hasStockIssues ? 'true' : 'false' }},
                init() {
                    this.$nextTick(() => {
                        const cep = document.getElementById('cep-input')?.value;
                        if (cep && cep.replace(/\D/g, '').length === 8) {
                            this.calculateShipping();
                        }
                    });
                },

                // Shipping
                shippingLoading: false,
                shippingOptions: [],
                shippingError: '',
                selectedShipping: JSON.parse(localStorage.getItem('selected_shipping') || 'null'),

                get itemsCount() {
                    return Object.keys(this.items).length;
                },

                calculateShipping() {
                    const cep = document.getElementById('cep-input').value;
                    if (!cep || cep.replace(/\D/g, '').length < 8) {
                        this.shippingError = 'CEP inválido.';
                        return;
                    }

                    this.shippingLoading = true;
                    this.shippingError = '';
                    this.shippingOptions = [];

                    fetch('{{ route('shipping.calculate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ cep })
                    })
                        .then(res => res.json())
                        .then(data => {
                            this.shippingLoading = false;
                            if (data.cotacoes) {
                                this.shippingOptions = data.cotacoes;
                                if (data.cotacoes.length > 0) {
                                    this.selectShipping(data.cotacoes[0]); // auto-select cheapest
                                }
                            } else {
                                this.shippingError = data.error || 'Erro ao calcular frete.';
                            }
                        })
                        .catch(() => {
                            this.shippingLoading = false;
                            this.shippingError = 'Erro de conexão. Tente novamente.';
                        });
                },

                selectShipping(option) {
                    this.selectedShipping = option;
                    localStorage.setItem('selected_shipping', JSON.stringify(option));
                },

                updateQty(id, qty) {
                    if (qty < 1) return;

                    fetch(`/carrinho/update/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ quantity: qty })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.items = data.cart;
                                this.total = data.total;
                                this.hasStockIssues = data.hasStockIssues;
                            }
                        });
                },

                updateColor(id, hex, name) {
                    fetch(`/carrinho/update/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            color: hex,
                            color_name: name
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.items = data.cart;
                                this.total = data.total;
                                this.hasStockIssues = data.hasStockIssues;
                                this.updateCountBadge(data.count);
                            }
                        });
                },

                removeItem(id) {
                    fetch(`/carrinho/remove/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                this.items = data.cart;
                                this.total = data.total;
                                this.hasStockIssues = data.hasStockIssues;
                                this.status = 'item-removed';
                                this.updateCountBadge(data.count);
                                setTimeout(() => this.status = '', 3000);
                            }
                        });
                },

                updateCountBadge(count) {
                    const badge = document.getElementById('cart-count-badge');
                    if (badge) {
                        badge.innerText = count;
                        badge.style.display = count > 0 ? 'flex' : 'none';
                    }
                },

                formatMoney(amount) {
                    return 'R$ ' + Number(amount || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
            }));
        });
    </script>
</x-storefront-layout>