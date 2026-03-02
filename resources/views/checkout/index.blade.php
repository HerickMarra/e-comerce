<x-storefront-layout>
    <main class="max-w-7xl mx-auto px-4 md:px-8 py-10 lg:py-16" x-data="checkoutHandler">
        <form action="{{ route('checkout.process') }}" method="POST">
            @csrf
            <div class="flex flex-col lg:flex-row gap-12">
                <div class="flex-1 order-2 lg:order-1">
                    <section class="mb-12">
                        <div class="flex items-center gap-4 mb-8">
                            <span class="text-xs font-medium text-slate-300 font-condensed tracking-tighter">01</span>
                            <h2 class="text-xs font-bold uppercase tracking-[0.2em] text-slate-900 dark:text-white">
                                Entrega</h2>
                            <div class="h-[1px] flex-1 bg-slate-100 dark:bg-slate-800"></div>
                        </div>

                        @if($errors->any())
                            <div
                                class="mb-8 p-6 bg-red-50 border border-red-100 text-red-700 rounded-2xl flex flex-col gap-2 font-bold text-sm shadow-sm animate-pulse">
                                @foreach($errors->all() as $error)
                                    <div class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-sm">error</span>
                                        {{ $error }}
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div
                            class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm rounded-2xl p-6 md:p-10 space-y-10 overflow-hidden">
                            @auth
                                @if($addresses->count() > 0)
                                    <div
                                        class="mb-8 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-slate-100 dark:border-slate-800">
                                        <label
                                            class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 mb-4 block">Selecione
                                            um endereço salvo</label>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($addresses as $addr)
                                                <label
                                                    class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition-all bg-white dark:bg-slate-900 hover:border-primary/50"
                                                    :class="selectedAddressId == {{ $addr->id }} ? 'border-primary ring-1 ring-primary ring-opacity-50' : 'border-slate-100 dark:border-slate-800'">
                                                    <input type="radio" name="saved_address_id" value="{{ $addr->id }}"
                                                        x-model="selectedAddressId" class="hidden"
                                                        @change="useSavedAddress({{ json_encode($addr) }})">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <span
                                                            class="text-[10px] font-black uppercase tracking-widest {{ $addr->is_default ? 'text-primary' : 'text-slate-400' }}">
                                                            {{ $addr->label ?? 'Endereço' }}
                                                            {{ $addr->is_default ? '(Principal)' : '' }}
                                                        </span>
                                                        <div class="size-4 rounded-full border-2 flex items-center justify-center"
                                                            :class="selectedAddressId == {{ $addr->id }} ? 'border-primary' : 'border-slate-200 dark:border-slate-700'">
                                                            <div class="size-2 rounded-full bg-primary"
                                                                x-show="selectedAddressId == {{ $addr->id }}"></div>
                                                        </div>
                                                    </div>
                                                    <p class="text-xs font-bold text-slate-900 dark:text-white">
                                                        {{ $addr->recipient_name }}
                                                    </p>
                                                    <p class="text-[10px] text-slate-500 mt-1">{{ $addr->street }},
                                                        {{ $addr->number }}
                                                    </p>
                                                    <p class="text-[10px] text-slate-500">{{ $addr->city }} - {{ $addr->state }}</p>
                                                </label>
                                            @endforeach
                                            <button type="button"
                                                @click="addressMode = 'manual'; selectedAddressId = null; clearForm()"
                                                class="flex flex-col items-center justify-center p-4 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl hover:border-primary hover:text-primary transition-all text-slate-400 gap-2">
                                                <span class="material-symbols-outlined text-xl">add_circle</span>
                                                <span class="text-[10px] font-black uppercase tracking-widest">Outro
                                                    Endereço</span>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            @endauth

                            <!-- Hidden Fields for the Controller -->
                            <input type="hidden" name="address[zip_code]" x-model="formData.zip_code">
                            <input type="hidden" name="address[street]" x-model="formData.street">
                            <input type="hidden" name="address[number]" x-model="formData.number">
                            <input type="hidden" name="address[neighborhood]" x-model="formData.neighborhood">
                            <input type="hidden" name="address[city]" x-model="formData.city">
                            <input type="hidden" name="address[state]" x-model="formData.state">

                            <!-- Shipping hidden fields (populated by Alpine from localStorage) -->
                            <input type="hidden" name="shipping_amount"
                                x-bind:value="selectedShipping ? selectedShipping.valor : 0">
                            <input type="hidden" name="shipping_label"
                                x-bind:value="selectedShipping ? selectedShipping.modalidade : ''">
                            <input type="hidden" name="shipping_simulacao_id"
                                x-bind:value="selectedShipping ? (selectedShipping.simulacao_id ?? '') : ''">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8"
                                x-show="addressMode === 'manual' || !{{ Auth::check() ? 'true' : 'false' }}"
                                x-transition>
                                <div class="md:col-span-1">
                                    <label
                                        class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 mb-2 block">CEP</label>
                                    <div class="relative group">
                                        <input
                                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg px-4 py-4 font-bold text-lg focus:ring-primary focus:border-primary outline-none transition-all"
                                            placeholder="00000-000" type="text" x-model="formData.zip_code"
                                            x-mask="99999-999" @blur="searchCep" />
                                        <button type="button" @click="searchCep"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] text-primary font-bold uppercase tracking-widest hover:text-primary-dark"
                                            :disabled="loadingCep">
                                            <span x-show="!loadingCep">Buscar</span>
                                            <span x-show="loadingCep" class="flex"><span
                                                    class="material-symbols-outlined animate-spin !text-xs">sync</span></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="hidden md:block"></div>
                                <div class="md:col-span-2">
                                    <label
                                        class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 mb-2 block">Endereço
                                        Completo</label>
                                    <input
                                        class="w-full px-4 py-3 rounded-none border-b border-slate-200 dark:border-slate-700 border-t-0 border-x-0 bg-transparent focus:ring-0 focus:border-primary transition-all text-sm placeholder:text-slate-300 dark:text-white"
                                        placeholder="Ex: Av. Brigadeiro Faria Lima" type="text"
                                        x-model="formData.street" />
                                </div>
                                <div>
                                    <label
                                        class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 mb-2 block">Número
                                        e Complemento</label>
                                    <input
                                        class="w-full px-4 py-3 rounded-none border-b border-slate-200 dark:border-slate-700 border-t-0 border-x-0 bg-transparent focus:ring-0 focus:border-primary transition-all text-sm placeholder:text-slate-300 dark:text-white"
                                        placeholder="Ex: 1234, Ap 52" type="text" x-model="formData.number" />
                                </div>
                                <div>
                                    <label
                                        class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 mb-2 block">Bairro</label>
                                    <input
                                        class="w-full px-4 py-3 rounded-none border-b border-slate-200 dark:border-slate-700 border-t-0 border-x-0 bg-transparent focus:ring-0 focus:border-primary transition-all text-sm placeholder:text-slate-300 dark:text-white"
                                        placeholder="Bairro" type="text" x-model="formData.neighborhood" />
                                </div>
                                <div>
                                    <label
                                        class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 mb-2 block">Cidade</label>
                                    <input
                                        class="w-full px-4 py-3 rounded-none border-b border-slate-200 dark:border-slate-700 border-t-0 border-x-0 bg-transparent focus:ring-0 focus:border-primary transition-all text-sm placeholder:text-slate-300 dark:text-white"
                                        placeholder="Cidade" type="text" x-model="formData.city" />
                                </div>
                                <div>
                                    <label
                                        class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 mb-2 block">Estado</label>
                                    <input
                                        class="w-full px-4 py-3 rounded-none border-b border-slate-200 dark:border-slate-700 border-t-0 border-x-0 bg-transparent focus:ring-0 focus:border-primary transition-all text-sm placeholder:text-slate-300 dark:text-white uppercase"
                                        placeholder="UF" type="text" x-model="formData.state" x-mask="aa" />
                                </div>
                            </div>

                            <div class="pt-6 border-t border-slate-50 dark:border-slate-800">
                                <label
                                    class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 mb-6 block">Modalidade
                                    de Frete</label>

                                <div class="space-y-4">
                                    {{-- Store Pickup Option --}}
                                    @if(App\Models\Setting::get('enable_store_pickup', '1') === '1')
                                        <label
                                            class="relative flex items-center p-5 border rounded-lg cursor-pointer hover:border-primary transition-all group"
                                            :class="selectedShipping?._type === 'pickup' ? 'border-primary bg-emerald-50/10' : 'border-slate-200 dark:border-slate-700'">
                                            <input type="radio" name="shipping_method_radio" value="pickup"
                                                :checked="selectedShipping?._type === 'pickup'"
                                                @change="selectShipping({ _type: 'pickup', servico: 'Retirar na Loja', modalidade: 'Retirar na Loja', prazo: 0, valor: 0, simulacao_id: null })"
                                                class="text-primary focus:ring-primary size-5 border-slate-300 dark:bg-slate-800" />
                                            <div class="ml-5 flex-1">
                                                <span
                                                    class="text-xs font-bold text-slate-900 dark:text-white block uppercase tracking-wider flex items-center gap-2">
                                                    <span
                                                        class="material-symbols-outlined text-sm text-primary">store</span>
                                                    Retirar na Loja
                                                </span>
                                                <span class="text-[11px] text-slate-400">Combine o horário após o
                                                    pedido</span>
                                            </div>
                                            <span class="text-sm font-bold text-emerald-500 uppercase">Grátis</span>
                                        </label>
                                    @endif

                                    {{-- Loading State --}}
                                    <template x-if="shippingLoading">
                                        <div
                                            class="flex items-center justify-center p-8 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                                            <span
                                                class="material-symbols-outlined animate-spin text-primary">sync</span>
                                            <span
                                                class="ml-3 text-xs font-bold text-slate-400 uppercase tracking-widest">Calculando
                                                melhores opções...</span>
                                        </div>
                                    </template>

                                    {{-- Error State --}}
                                    <template x-if="shippingError">
                                        <div
                                            class="p-4 bg-red-50 text-red-600 rounded-xl text-xs font-bold flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm">error</span>
                                            <span x-text="shippingError"></span>
                                        </div>
                                    </template>

                                    {{-- API Shipping Options --}}
                                    <template x-for="(opt, i) in shippingOptions" :key="i">
                                        <label
                                            class="relative flex items-center p-5 border rounded-lg cursor-pointer hover:border-primary transition-all group"
                                            :class="selectedShipping?._type !== 'pickup' && selectedShipping?.modalidade === opt.modalidade && selectedShipping?.simulacao_id === opt.simulacao_id
                                                   ? 'border-primary bg-emerald-50/10' : 'border-slate-200 dark:border-slate-700'">
                                            <input type="radio" name="shipping_method_radio"
                                                :checked="selectedShipping?._type !== 'pickup' && selectedShipping?.modalidade === opt.modalidade && selectedShipping?.simulacao_id === opt.simulacao_id"
                                                @change="selectShipping(opt)"
                                                class="text-primary focus:ring-primary size-5 border-slate-300 dark:bg-slate-800" />
                                            <div class="ml-5 flex-1">
                                                <span
                                                    class="text-xs font-bold text-slate-900 dark:text-white block uppercase tracking-wider"
                                                    x-text="opt.servico"></span>
                                                <span class="text-[11px] text-slate-400"><span
                                                        x-text="opt.prazo"></span> dias úteis</span>
                                            </div>
                                            <span class="text-sm font-bold text-slate-900 dark:text-white"
                                                x-text="formatMoney(opt.valor)"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="mb-12">
                        <div class="flex items-center gap-4 mb-8">
                            <span class="text-xs font-medium text-slate-300 font-condensed tracking-tighter">02</span>
                            <h2 class="text-xs font-bold uppercase tracking-[0.2em] text-slate-900 dark:text-white">
                                Pagamento</h2>
                            <div class="h-[1px] flex-1 bg-slate-100 dark:bg-slate-800"></div>
                        </div>
                        <div
                            class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm rounded-2xl p-6 md:p-10">
                            <!-- Hidden input for payment method -->
                            <input type="hidden" name="payment_method" x-model="paymentMethod">

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10">
                                <div @click="paymentMethod = 'credit_card'"
                                    class="flex flex-col items-center justify-center p-6 border rounded-xl cursor-pointer transition-all gap-3"
                                    :class="paymentMethod === 'credit_card' ? 'border-primary bg-emerald-50/30 ring-1 ring-primary' : 'border-slate-200 dark:border-slate-800 grayscale opacity-60 hover:opacity-100 hover:grayscale-0'">
                                    <span
                                        class="material-symbols-outlined !text-3xl text-slate-700 dark:text-slate-300">credit_card</span>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-widest text-center text-slate-900 dark:text-white">Cartão
                                        de Crédito</span>
                                </div>
                                <div @click="paymentMethod = 'pix'"
                                    class="flex flex-col items-center justify-center p-6 border rounded-xl cursor-pointer transition-all gap-3"
                                    :class="paymentMethod === 'pix' ? 'border-primary bg-emerald-50/30 ring-1 ring-primary' : 'border-slate-200 dark:border-slate-800 grayscale opacity-60 hover:opacity-100 hover:grayscale-0'">
                                    <span
                                        class="material-symbols-outlined !text-3xl text-slate-700 dark:text-slate-300">qr_code_2</span>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-widest text-center text-slate-900 dark:text-white">PIX
                                        <span class="text-primary block mt-1">-5% OFF</span></span>
                                </div>
                                <div @click="paymentMethod = 'boleto'"
                                    class="flex flex-col items-center justify-center p-6 border rounded-xl cursor-pointer transition-all gap-3"
                                    :class="paymentMethod === 'boleto' ? 'border-primary bg-emerald-50/30 ring-1 ring-primary' : 'border-slate-200 dark:border-slate-800 grayscale opacity-60 hover:opacity-100 hover:grayscale-0'">
                                    <span
                                        class="material-symbols-outlined !text-3xl text-slate-700 dark:text-slate-300">receipt_long</span>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-widest text-center text-slate-900 dark:text-white">Boleto
                                        Bancário</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-8"
                                x-show="paymentMethod === 'credit_card'" x-transition x-cloak>
                                <div class="md:col-span-2">
                                    <label
                                        class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 mb-2 block">Nome
                                        no Cartão</label>
                                    <input name="card_holder"
                                        class="w-full px-4 py-3 rounded-none border-b border-slate-200 dark:border-slate-700 border-t-0 border-x-0 bg-transparent focus:ring-0 focus:border-primary transition-all text-sm placeholder:text-slate-300 dark:text-white"
                                        placeholder="JOSÉ SILVA" type="text" />
                                </div>
                                <div class="md:col-span-2">
                                    <label
                                        class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 mb-2 block">Número
                                        do Cartão</label>
                                    <div class="relative">
                                        <input name="card_number"
                                            class="w-full px-4 py-3 rounded-none border-b border-slate-200 dark:border-slate-700 border-t-0 border-x-0 bg-transparent focus:ring-0 focus:border-primary transition-all text-sm placeholder:text-slate-300 dark:text-white"
                                            placeholder="0000 0000 0000 0000" type="text"
                                            x-mask="9999 9999 9999 9999" />
                                        <div
                                            class="absolute right-0 top-1/2 -translate-y-1/2 flex gap-2 opacity-30 grayscale h-6">
                                            <div class="h-4 w-8 bg-slate-200 rounded"></div> <!-- Visa placeholder -->
                                            <div class="h-4 w-8 bg-slate-200 rounded"></div>
                                            <!-- Mastercard placeholder -->
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 mb-2 block">Validade</label>
                                    <input name="card_expiry"
                                        class="w-full px-4 py-3 rounded-none border-b border-slate-200 dark:border-slate-700 border-t-0 border-x-0 bg-transparent focus:ring-0 focus:border-primary transition-all text-sm placeholder:text-slate-300 dark:text-white"
                                        placeholder="MM/AA" type="text" x-mask="99/99" />
                                </div>
                                <div>
                                    <label
                                        class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 mb-2 block">Cód.
                                        Segurança (CVV)</label>
                                    <input name="card_cvv"
                                        class="w-full px-4 py-3 rounded-none border-b border-slate-200 dark:border-slate-700 border-t-0 border-x-0 bg-transparent focus:ring-0 focus:border-primary transition-all text-sm placeholder:text-slate-300 dark:text-white"
                                        placeholder="123" type="text" x-mask="999" />
                                </div>
                                <div class="md:col-span-2">
                                    <label
                                        class="text-[10px] font-semibold uppercase tracking-[0.1em] text-slate-400 mb-2 block">Parcelamento</label>
                                    <select name="installments"
                                        class="w-full px-4 py-3 rounded-none border-b border-slate-200 dark:border-slate-700 border-t-0 border-x-0 bg-transparent focus:ring-0 focus:border-primary transition-all text-sm dark:text-white dark:bg-slate-900 border-0">
                                        <option value="10">Até 10x de R$ {{ number_format($total / 10, 2, ',', '.') }}
                                            sem juros</option>
                                        <option value="1">1x de R$ {{ number_format($total, 2, ',', '.') }} à vista
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="text-center py-8 space-y-4" x-show="paymentMethod === 'pix'" x-transition
                                x-cloak>
                                <div
                                    class="inline-block p-4 bg-slate-50 dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700">
                                    <div
                                        class="size-48 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 flex items-center justify-center p-2">
                                        <span
                                            class="material-symbols-outlined !text-8xl text-slate-100 dark:text-slate-800">qr_code_2</span>
                                    </div>
                                </div>
                                <p
                                    class="text-[11px] font-bold text-slate-900 dark:text-white uppercase tracking-widest">
                                    O QR Code será gerado após finalizar a compra</p>
                            </div>

                            <div class="text-center py-8 space-y-4" x-show="paymentMethod === 'boleto'" x-transition
                                x-cloak>
                                <div
                                    class="inline-block p-10 bg-slate-50 dark:bg-slate-800 rounded-3xl border border-slate-100 dark:border-slate-700">
                                    <span
                                        class="material-symbols-outlined !text-7xl text-slate-300 dark:text-slate-600">receipt_long</span>
                                </div>
                                <p
                                    class="text-[11px] font-bold text-slate-900 dark:text-white uppercase tracking-widest">
                                    O boleto será gerado após finalizar a compra</p>
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="lg:w-[420px] order-1 lg:order-2">
                    <div class="lg:sticky lg:top-28 space-y-6">
                        <div
                            class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 shadow-sm rounded-2xl overflow-hidden p-8">
                            <h3 class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-400 mb-8">Resumo do
                                Pedido</h3>

                            <div class="space-y-6 mb-8 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                                @foreach($cart as $item)
                                    <div class="flex gap-4 items-center">
                                        <div
                                            class="size-16 bg-slate-50 dark:bg-slate-800 rounded flex-shrink-0 overflow-hidden border border-slate-100 dark:border-slate-800">
                                            <img alt="{{ $item['name'] }}" class="h-full w-full object-cover"
                                                src="{{ str_starts_with($item['image'], 'http') ? $item['image'] : asset('storage/' . $item['image']) }}" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4
                                                class="text-[10px] font-bold text-slate-900 dark:text-white uppercase tracking-tight truncate">
                                                {{ $item['name'] }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                @if(isset($item['color']))
                                                    <span
                                                        class="size-2 rounded-full border border-slate-100 dark:border-slate-800"
                                                        style="background-color: {{ $item['color'] }}"></span>
                                                @endif
                                                <p class="text-[9px] text-slate-400 uppercase">
                                                    {{ $item['color_name'] ?? 'Padrão' }} • Qtd: {{ $item['quantity'] }}
                                                </p>
                                            </div>
                                            <p class="text-xs font-semibold text-slate-900 dark:text-white mt-1">R$
                                                {{ number_format($item['price'], 2, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="space-y-3 py-6 border-y border-slate-50 dark:border-slate-800">
                                <div class="flex justify-between text-[11px] uppercase tracking-wider">
                                    <span class="text-slate-400">Subtotal</span>
                                    <span class="font-medium dark:text-white">R$
                                        {{ number_format($subtotal, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-[11px] uppercase tracking-wider">
                                    <span class="text-slate-400">Frete</span>
                                    <span class="font-medium"
                                        :class="selectedShipping && selectedShipping.valor === 0 ? 'text-emerald-500' : 'dark:text-white'">
                                        <template x-if="!selectedShipping">— A selecionar</template>
                                        <template x-if="selectedShipping && selectedShipping.valor === 0">✔
                                            Grátis</template>
                                        <template x-if="selectedShipping && selectedShipping.valor > 0">
                                            <span
                                                x-text="'R$ ' + Number(selectedShipping.valor).toLocaleString('pt-BR', {minimumFractionDigits:2})"></span>
                                        </template>
                                    </span>
                                </div>
                                <div x-show="selectedShipping"
                                    class="text-[10px] text-slate-400 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm"
                                        :class="selectedShipping?._type === 'pickup' ? 'text-primary' : 'text-slate-400'"
                                        x-text="selectedShipping?._type === 'pickup' ? 'store' : 'local_shipping'"></span>
                                    <span x-text="selectedShipping?.modalidade"></span>
                                    <template x-if="selectedShipping && selectedShipping.prazo > 0">
                                        <span x-text="'• ' + selectedShipping.prazo + ' dias úteis'"></span>
                                    </template>
                                </div>
                            </div>

                            <div class="pt-8 flex flex-col gap-1">
                                <div class="flex justify-between items-baseline">
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-[0.25em] text-slate-400">Total</span>
                                    <span
                                        class="text-4xl font-light text-slate-900 dark:text-primary font-condensed tracking-tighter"
                                        x-text="'R$ ' + ({{ $subtotal }} + (selectedShipping ? selectedShipping.valor : 0)).toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2})">R$
                                        {{ number_format($subtotal, 2, ',', '.') }}</span>
                                </div>
                                <p class="text-[9px] text-slate-400 text-right uppercase tracking-widest mt-2">em até
                                    10x sem juros</p>
                            </div>

                            <button type="submit" :disabled="!selectedShipping"
                                :class="selectedShipping ? 'bg-primary shadow-primary/10' : 'bg-slate-300 cursor-not-allowed shadow-none'"
                                class="w-full mt-10 text-white font-bold py-5 rounded-xl transition-all uppercase text-[11px] tracking-[0.2em] flex items-center justify-center gap-3 group">
                                Finalizar Pedido
                                <span
                                    class="material-symbols-outlined !text-lg transition-transform group-hover:translate-x-1">arrow_right_alt</span>
                            </button>
                            <p x-show="!selectedShipping"
                                class="text-[10px] text-amber-600 font-bold text-center mt-3 flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-xs">info</span>
                                Selecione uma opção de frete para finalizar
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-1">
                            <div
                                class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-6 rounded-2xl flex items-start gap-4 shadow-sm">
                                <span class="material-symbols-outlined text-primary !text-2xl">verified_user</span>
                                <div>
                                    <h5
                                        class="text-[10px] font-bold uppercase tracking-widest text-slate-900 dark:text-white">
                                        Segurança Total</h5>
                                    <p class="text-[10px] text-slate-400 mt-1 leading-relaxed">Seus dados bancários são
                                        processados com criptografia de 256 bits.</p>
                                </div>
                            </div>
                            <div
                                class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-6 rounded-2xl flex items-start gap-4 shadow-sm">
                                <span
                                    class="material-symbols-outlined text-slate-400 !text-2xl">workspace_premium</span>
                                <div>
                                    <h5
                                        class="text-[10px] font-bold uppercase tracking-widest text-slate-900 dark:text-white">
                                        Garantia VerveHome</h5>
                                    <p class="text-[10px] text-slate-400 mt-1 leading-relaxed">Suporte executivo e 5
                                        anos de garantia estrutural para sua peça.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </form>
    </main>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('checkoutHandler', () => ({
                addressMode: '{{ (Auth::check() && $addresses->count() > 0) ? 'saved' : 'manual' }}',
                selectedAddressId: {{ (Auth::check() && $addresses->where('is_default', true)->first()) ? $addresses->where('is_default', true)->first()->id : ($addresses->first()?->id ?? 'null') }},
                loadingCep: false,
                paymentMethod: 'credit_card',

                // Shipping Logic
                shippingLoading: false,
                shippingOptions: [],
                shippingError: '',
                selectedShipping: null,

                init() {
                    // Start by trying to calculate if we have a valid CEP
                    if (this.formData.zip_code && this.formData.zip_code.replace(/\D/g, '').length === 8) {
                        this.calculateShipping();
                    }
                },

                calculateShipping() {
                    const cep = this.formData.zip_code.replace(/\D/g, '');
                    if (cep.length < 8) return;

                    this.shippingLoading = true;
                    this.shippingError = '';
                    this.shippingOptions = [];
                    this.selectedShipping = null; // Reset selection on recalculate

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
                },

                formatMoney(amount) {
                    return 'R$ ' + Number(amount || 0).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },

                formData: {
                    zip_code: '{{ (Auth::check() && $addresses->where('is_default', true)->first()) ? $addresses->where('is_default', true)->first()->zip_code : ($addresses->first()?->zip_code ?? '') }}',
                    street: '{{ (Auth::check() && $addresses->where('is_default', true)->first()) ? $addresses->where('is_default', true)->first()->street : ($addresses->first()?->street ?? '') }}',
                    number: '{{ (Auth::check() && $addresses->where('is_default', true)->first()) ? $addresses->where('is_default', true)->first()->number : ($addresses->first()?->number ?? '') }}',
                    neighborhood: '{{ (Auth::check() && $addresses->where('is_default', true)->first()) ? $addresses->where('is_default', true)->first()->neighborhood : ($addresses->first()?->neighborhood ?? '') }}',
                    city: '{{ (Auth::check() && $addresses->where('is_default', true)->first()) ? $addresses->where('is_default', true)->first()->city : ($addresses->first()?->city ?? '') }}',
                    state: '{{ (Auth::check() && $addresses->where('is_default', true)->first()) ? $addresses->where('is_default', true)->first()->state : ($addresses->first()?->state ?? '') }}',
                },

                useSavedAddress(address) {
                    this.addressMode = 'saved';
                    this.formData = {
                        zip_code: address.zip_code,
                        street: address.street,
                        number: address.number,
                        neighborhood: address.neighborhood,
                        city: address.city,
                        state: address.state,
                    };
                    this.calculateShipping();
                },

                clearForm() {
                    this.formData = {
                        zip_code: '',
                        street: '',
                        number: '',
                        neighborhood: '',
                        city: '',
                        state: '',
                    };
                },

                searchCep() {
                    const cep = this.formData.zip_code.replace(/\D/g, '');
                    if (cep.length === 8) {
                        this.loadingCep = true;
                        fetch(`https://viacep.com.br/ws/${cep}/json/`)
                            .then(response => response.json())
                            .then(data => {
                                if (!data.erro) {
                                    this.formData.street = data.logradouro;
                                    this.formData.neighborhood = data.bairro;
                                    this.formData.city = data.localidade;
                                    this.formData.state = data.uf;
                                    this.calculateShipping();
                                }
                            })
                            .finally(() => this.loadingCep = false);
                    }
                }
            }));
        });
    </script>
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</x-storefront-layout>