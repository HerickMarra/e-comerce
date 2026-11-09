<x-storefront-layout :seo="$seo">
    <main class="max-w-7xl mx-auto px-4 md:px-8 py-6 sm:py-10 pb-32 sm:pb-36 lg:pb-16" x-data="checkoutHandler()">
        <form id="checkoutForm" action="{{ route('checkout.process') }}" method="POST">
            @csrf

            {{-- Indicador Visual de Etapas do Checkout --}}
            <div class="flex items-center justify-center gap-2 sm:gap-4 mb-6 sm:mb-8 text-xs font-bold select-none">
                @guest
                    <div class="flex items-center gap-1.5 text-primary">
                        <span class="size-6 rounded-full bg-primary text-white flex items-center justify-center text-[11px] font-black shadow-sm shadow-primary/30">1</span>
                        <span class="text-slate-900 dark:text-white">Identificação</span>
                    </div>
                    <span class="w-6 sm:w-10 h-[2px] bg-slate-200 dark:bg-slate-700"></span>
                @endguest

                <div class="flex items-center gap-1.5" :class="isDeliveryActive ? 'text-primary' : 'text-slate-400'">
                    <span class="size-6 rounded-full flex items-center justify-center text-[11px] font-black transition-colors"
                        :class="isDeliveryActive ? 'bg-primary text-white shadow-sm shadow-primary/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'">{{ Auth::check() ? '1' : '2' }}</span>
                    <span :class="isDeliveryActive ? 'text-slate-900 dark:text-white' : 'text-slate-400'">Entrega</span>
                </div>
                <span class="w-6 sm:w-10 h-[2px] bg-slate-200 dark:bg-slate-700"></span>
                <div class="flex items-center gap-1.5" :class="selectedShipping ? 'text-primary' : 'text-slate-400'">
                    <span class="size-6 rounded-full flex items-center justify-center text-[11px] font-black transition-colors"
                        :class="selectedShipping ? 'bg-primary text-white shadow-sm shadow-primary/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-400'">{{ Auth::check() ? '2' : '3' }}</span>
                    <span :class="selectedShipping ? 'text-slate-900 dark:text-white' : 'text-slate-400'">Pagamento</span>
                </div>
            </div>

            {{-- Resumo Compacto / Sanfona Visível apenas no MOBILE no Topo --}}
            <div class="lg:hidden mb-6 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm"
                 x-data="{ openCartSummary: false }">
                <button type="button" @click="openCartSummary = !openCartSummary"
                    class="w-full p-4 flex items-center justify-between gap-3 text-left bg-slate-50/70 dark:bg-slate-800/40 hover:bg-slate-100/70 transition-colors">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="material-symbols-outlined text-primary text-xl flex-shrink-0">shopping_bag</span>
                        <div class="min-w-0">
                            <span class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white block truncate">
                                Ver itens da compra ({{ count($cart) }} {{ count($cart) === 1 ? 'item' : 'itens' }})
                            </span>
                            <span class="text-[10px] text-slate-400 block truncate" x-text="openCartSummary ? 'Clique para recolher' : 'Toque para conferir produtos'"></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="text-sm font-black text-slate-900 dark:text-white"
                            x-text="'R$ ' + ({{ $subtotal }} + (selectedShipping ? selectedShipping.valor : 0)).toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2})">
                            R$ {{ number_format($total, 2, ',', '.') }}
                        </span>
                        <span class="material-symbols-outlined text-slate-400 text-lg transition-transform duration-200"
                            :class="openCartSummary ? 'rotate-180' : ''">expand_more</span>
                    </div>
                </button>
                <div x-show="openCartSummary" x-transition x-cloak class="p-4 border-t border-slate-100 dark:border-slate-800 space-y-4">
                    <div class="space-y-3 max-h-[300px] overflow-y-auto pr-1 custom-scrollbar">
                        @foreach($cart as $item)
                            <div class="flex gap-3 items-center">
                                <div class="size-14 bg-slate-50 dark:bg-slate-800 rounded-xl flex-shrink-0 overflow-hidden border border-slate-100 dark:border-slate-800">
                                    <img alt="{{ $item['name'] }}" class="h-full w-full object-cover"
                                        src="{{ str_starts_with($item['image'], 'http') ? $item['image'] : asset('storage/' . $item['image']) }}" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                        {{ $item['name'] }}
                                    </h4>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        @if(isset($item['color']))
                                            <span class="size-2 rounded-full border border-slate-200" style="background-color: {{ $item['color'] }}"></span>
                                        @endif
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400">
                                            @if(!empty($item['size']))
                                                <strong class="text-slate-700 dark:text-slate-300">Tam: {{ $item['size'] }}</strong> •
                                            @endif
                                            Qtd: {{ $item['quantity'] }}
                                        </p>
                                    </div>
                                    <p class="text-xs font-black text-slate-900 dark:text-white mt-0.5">
                                        R$ {{ number_format($item['price'], 2, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 text-xs space-y-1.5">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Frete</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200"
                                :class="selectedShipping && selectedShipping.valor === 0 ? 'text-emerald-600' : ''">
                                <template x-if="!selectedShipping">— A selecionar</template>
                                <template x-if="selectedShipping && selectedShipping.valor === 0">✔ Grátis</template>
                                <template x-if="selectedShipping && selectedShipping.valor > 0">
                                    <span x-text="'R$ ' + Number(selectedShipping.valor).toLocaleString('pt-BR', {minimumFractionDigits:2})"></span>
                                </template>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
                {{-- COLUNA PRINCIPAL: Formulários de Entrega e Pagamento --}}
                <div class="flex-1 space-y-8 lg:space-y-10">

                    {{-- IDENTIFICAÇÃO DO COMPRADOR (VISITANTE / SEM LOGIN) --}}
                    @guest
                        <section class="space-y-4">
                            {{-- BANNER DE DESTAQUE: JÁ TEM CONTA? --}}
                            <div class="bg-gradient-to-r from-primary/10 via-primary/5 to-transparent border-2 border-primary/30 rounded-3xl p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
                                <div class="flex items-center gap-3.5">
                                    <div class="size-11 sm:size-12 rounded-2xl bg-primary text-white flex items-center justify-center flex-shrink-0 shadow-md shadow-primary/30">
                                        <span class="material-symbols-outlined text-2xl sm:text-3xl">account_circle</span>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs sm:text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                                                Já tem uma conta?
                                            </span>
                                            <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-full bg-primary/20 text-primary">
                                                Mais Rápido
                                            </span>
                                        </div>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                                            Faça login para carregar seus <strong>endereços e dados salvos</strong> automaticamente.
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ route('login', ['redirect' => route('checkout')]) }}"
                                   class="w-full sm:w-auto px-5 py-2.5 bg-primary hover:bg-primary/95 text-white font-black text-xs uppercase tracking-wider rounded-xl transition-all shadow-md shadow-primary/25 flex items-center justify-center gap-2 flex-shrink-0 active:scale-95">
                                    <span class="material-symbols-outlined text-base">login</span>
                                    <span>Entrar na Minha Conta</span>
                                </a>
                            </div>

                            {{-- SEÇÃO DE DADOS EXPRESSOS PARA QUEM NÃO QUER LOGAR --}}
                            <div class="flex items-center justify-between pt-1">
                                <div class="flex items-center gap-3">
                                    <span class="size-7 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-black text-xs">1</span>
                                    <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white">
                                        Ou Compre Sem Cadastro (Rápido)
                                    </h2>
                                </div>
                                <span class="text-[10px] font-black uppercase px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    Sem Senha
                                </span>
                            </div>

                            <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm rounded-3xl p-4 sm:p-6 space-y-4">
                                <div class="flex items-center gap-2 text-xs text-slate-500 pb-2 border-b border-slate-100 dark:border-slate-800">
                                    <span class="material-symbols-outlined text-emerald-500 text-base">lock</span>
                                    <span>Compra expressa e segura. Preencha apenas os dados básicos para o envio:</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                                    <div class="sm:col-span-2">
                                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5 block">Nome Completo *</label>
                                        <input name="customer_name"
                                            x-model="customerName"
                                            value="{{ old('customer_name') }}"
                                            class="w-full bg-slate-50/80 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 font-bold text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-slate-400 placeholder:font-normal"
                                            placeholder="Ex: Carlos Eduardo de Oliveira" type="text" required />
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5 block">E-mail (para confirmação e código PIX) *</label>
                                        <input name="customer_email"
                                            x-model="customerEmail"
                                            value="{{ old('customer_email') }}"
                                            class="w-full bg-slate-50/80 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 font-bold text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-slate-400 placeholder:font-normal"
                                            placeholder="seuemail@exemplo.com" type="email" inputmode="email" required />
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5 block">CPF (para Nota Fiscal e Envio) *</label>
                                        <input name="customer_cpf"
                                            x-model="customerCpf"
                                            value="{{ old('customer_cpf') }}"
                                            class="w-full bg-slate-50/80 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 font-bold text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-slate-400 placeholder:font-normal"
                                            placeholder="000.000.000-00" type="text" inputmode="numeric"
                                            x-mask="999.999.999-99" required />
                                    </div>

                                    <div>
                                        <label class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-1.5 block">WhatsApp / Telefone *</label>
                                        <input name="customer_phone"
                                            x-model="customerPhone"
                                            value="{{ old('customer_phone') }}"
                                            class="w-full bg-slate-50/80 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 font-bold text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all placeholder:text-slate-400 placeholder:font-normal"
                                            placeholder="(00) 00000-0000" type="tel" inputmode="tel"
                                            x-mask="(99) 99999-9999" required />
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endguest

                    {{-- ETAPA DE ENTREGA --}}
                    <section class="space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="size-7 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-black text-xs">{{ Auth::check() ? '1' : '2' }}</span>
                            <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white">
                                Opção de Entrega
                            </h2>
                        </div>

                        @if(isset($errors) && $errors->any())
                            <div class="p-4 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-2xl flex flex-col gap-1.5 text-xs font-bold shadow-sm">
                                @foreach($errors->all() as $error)
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-sm shrink-0">error</span>
                                        <span>{{ $error }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @auth
                            @if(!Auth::user()->cpf || !Auth::user()->phone)
                                <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 rounded-2xl p-4 sm:p-6 space-y-4">
                                    <div class="flex items-center gap-2.5">
                                        <span class="material-symbols-outlined text-amber-600 text-lg">person_edit</span>
                                        <h3 class="text-xs font-black text-amber-900 dark:text-amber-400 uppercase tracking-wider">
                                            Identificação para Nota Fiscal
                                        </h3>
                                    </div>
                                    <p class="text-xs text-amber-800 dark:text-amber-300/80 leading-relaxed">
                                        Informe seu CPF e WhatsApp para emissão da etiqueta de envio:
                                    </p>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        @if(!Auth::user()->cpf)
                                            <div>
                                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 block">CPF</label>
                                                <input name="user_cpf"
                                                    class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 font-bold text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                                                    placeholder="000.000.000-00" type="text" inputmode="numeric" x-model="userCpf"
                                                    x-mask="999.999.999-99" required />
                                            </div>
                                        @endif
                                        @if(!Auth::user()->phone)
                                            <div>
                                                <label class="text-[10px] font-bold uppercase tracking-wider text-slate-500 mb-1.5 block">WhatsApp / Telefone</label>
                                                <input name="user_phone"
                                                    class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 font-bold text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                                                    placeholder="(00) 00000-0000" type="tel" inputmode="tel" x-model="userPhone"
                                                    x-mask="(99) 99999-9999" required />
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endauth

                        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm rounded-3xl p-4 sm:p-8 space-y-6">

                            @if(App\Models\Setting::get('enable_store_pickup', '1') === '1')
                                {{-- Seletor de Modo: Entrega vs Retirada --}}
                                <div>
                                    <label class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-2.5 block">
                                        Como deseja receber seu pedido?
                                    </label>
                                    <div class="grid grid-cols-2 gap-2.5 sm:gap-4">
                                        <button type="button" @click="setDeliveryMode()"
                                            class="flex flex-col sm:flex-row items-center sm:items-start p-3 sm:p-4 rounded-2xl border-2 transition-all text-center sm:text-left gap-2 sm:gap-3.5"
                                            :class="fulfillmentType === 'delivery' ? 'border-primary bg-primary/5 shadow-sm' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 opacity-80'">
                                            <div class="size-10 sm:size-11 rounded-xl flex items-center justify-center transition-colors flex-shrink-0"
                                                :class="fulfillmentType === 'delivery' ? 'bg-primary text-white shadow-md shadow-primary/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'">
                                                <span class="material-symbols-outlined text-xl sm:text-2xl">local_shipping</span>
                                            </div>
                                            <div class="min-w-0">
                                                <span class="text-xs font-black uppercase tracking-wider block"
                                                    :class="fulfillmentType === 'delivery' ? 'text-primary' : 'text-slate-800 dark:text-white'">
                                                    Receber em Casa
                                                </span>
                                                <span class="text-[10px] text-slate-400 hidden sm:block truncate">Correios / Transportadora</span>
                                            </div>
                                        </button>

                                        <button type="button" @click="setPickupMode()"
                                            class="flex flex-col sm:flex-row items-center sm:items-start p-3 sm:p-4 rounded-2xl border-2 transition-all text-center sm:text-left gap-2 sm:gap-3.5 relative overflow-hidden"
                                            :class="fulfillmentType === 'pickup' ? 'border-emerald-500 bg-emerald-50/20 shadow-sm' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 opacity-80'">
                                            <div class="size-10 sm:size-11 rounded-xl flex items-center justify-center transition-colors flex-shrink-0"
                                                :class="fulfillmentType === 'pickup' ? 'bg-emerald-500 text-white shadow-md shadow-emerald-500/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-500'">
                                                <span class="material-symbols-outlined text-xl sm:text-2xl">storefront</span>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5 justify-center sm:justify-start">
                                                    <span class="text-xs font-black uppercase tracking-wider block"
                                                        :class="fulfillmentType === 'pickup' ? 'text-emerald-700 dark:text-emerald-400' : 'text-slate-800 dark:text-white'">
                                                        Retirar Loja
                                                    </span>
                                                    <span class="text-[9px] font-black px-1.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 uppercase">
                                                        Grátis
                                                    </span>
                                                </div>
                                                <span class="text-[10px] text-slate-400 hidden sm:block truncate">Pronto em até 2h</span>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            {{-- Informações do Ponto de Retirada na Loja --}}
                            <div x-show="fulfillmentType === 'pickup'" x-transition class="space-y-4">
                                <div class="p-4 sm:p-6 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 space-y-4">
                                    <div class="flex items-center justify-between gap-3 pb-3 border-b border-emerald-100 dark:border-emerald-800/30">
                                        <div class="flex items-center gap-2.5">
                                            <span class="material-symbols-outlined text-emerald-600 text-xl">store</span>
                                            <div>
                                                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 block">Ponto Oficial</span>
                                                <h4 class="text-sm font-black text-slate-900 dark:text-white" x-text="storePickupInfo.name"></h4>
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full bg-emerald-500 text-white text-[10px] font-black uppercase tracking-wider flex items-center gap-1">
                                            <span class="material-symbols-outlined text-xs">check</span>
                                            Grátis
                                        </span>
                                    </div>

                                    <div class="text-xs space-y-2 text-slate-600 dark:text-slate-300">
                                        <p class="font-bold text-slate-900 dark:text-white">
                                            📍 <span x-text="storePickupInfo.street"></span>, <span x-text="storePickupInfo.number"></span>
                                            — <span x-text="storePickupInfo.neighborhood"></span>, <span x-text="storePickupInfo.city"></span>/<span x-text="storePickupInfo.state"></span>
                                        </p>
                                        <p class="text-[11px] text-slate-500 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-sm text-amber-500">schedule</span>
                                            <span x-text="storePickupInfo.hours"></span>
                                        </p>
                                    </div>

                                    <div class="p-3 rounded-xl bg-white dark:bg-slate-900 text-[11px] text-slate-500 border border-emerald-100 dark:border-emerald-900/40 flex items-center gap-2">
                                        <span class="material-symbols-outlined text-emerald-600 text-base shrink-0">check_circle</span>
                                        <span>Você receberá notificação por WhatsApp/E-mail assim que estiver separado.</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Endereço e Opções de Frete para Entrega em Casa --}}
                            <div x-show="fulfillmentType === 'delivery'" x-transition class="space-y-6">
                                @auth
                                    @if($addresses->count() > 0)
                                        <div class="p-3.5 sm:p-4 bg-slate-50 dark:bg-slate-800/40 rounded-2xl border border-slate-200/60 dark:border-slate-800">
                                            <label class="text-[10px] font-black uppercase tracking-wider text-slate-400 mb-3 block">
                                                Endereços Salvos
                                            </label>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                                @foreach($addresses as $addr)
                                                    <label class="flex flex-col p-3 rounded-xl border-2 cursor-pointer transition-all bg-white dark:bg-slate-900"
                                                        :class="selectedAddressId == {{ $addr->id }} ? 'border-primary bg-primary/5' : 'border-slate-100 dark:border-slate-800'">
                                                        <input type="radio" name="saved_address_id" value="{{ $addr->id }}"
                                                            x-model="selectedAddressId" class="hidden"
                                                            @change="useSavedAddress({{ $addr->id }})">
                                                        <div class="flex items-center justify-between mb-1">
                                                            <span class="text-[10px] font-black uppercase tracking-wider {{ $addr->is_default ? 'text-primary' : 'text-slate-400' }}">
                                                                {{ $addr->label ?? 'Endereço' }} {{ $addr->is_default ? '(Principal)' : '' }}
                                                            </span>
                                                            <div class="size-3.5 rounded-full border flex items-center justify-center"
                                                                :class="selectedAddressId == {{ $addr->id }} ? 'border-primary bg-primary' : 'border-slate-300'">
                                                                <div class="size-1.5 rounded-full bg-white" x-show="selectedAddressId == {{ $addr->id }}"></div>
                                                            </div>
                                                        </div>
                                                        <p class="text-xs font-bold text-slate-800 dark:text-white truncate">{{ $addr->street }}, {{ $addr->number }}</p>
                                                        <p class="text-[10px] text-slate-400">{{ $addr->city }} - {{ $addr->state }}</p>
                                                    </label>
                                                @endforeach
                                                <button type="button"
                                                    @click="addressMode = 'manual'; selectedAddressId = null; clearForm()"
                                                    class="flex items-center justify-center p-3 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl hover:border-primary hover:text-primary transition-all text-slate-400 gap-1.5 text-xs font-bold">
                                                    <span class="material-symbols-outlined text-base">add_circle</span>
                                                    <span>Usar Outro Endereço</span>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                @endauth

                                {{-- Formulário de Endereço --}}
                                <div class="space-y-4"
                                    x-show="addressMode === 'manual' || !{{ Auth::check() ? 'true' : 'false' }}"
                                    x-transition>

                                    {{-- Campo de CEP com Botão Buscar Embutido --}}
                                    <div>
                                        <label class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-1.5 block">
                                            CEP de Entrega
                                        </label>
                                        <div class="relative flex items-center">
                                            <input
                                                class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-2xl px-4 py-3.5 font-black text-base text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all pr-24 shadow-inner"
                                                placeholder="00000-000" type="text" inputmode="numeric" x-model="formData.zip_code"
                                                x-mask="99999-999" @blur="searchCep" />
                                            <button type="button" @click="searchCep"
                                                class="absolute right-2 px-3.5 py-2 bg-primary hover:bg-primary/95 text-white text-xs font-black uppercase rounded-xl transition-all shadow-sm flex items-center gap-1"
                                                :disabled="loadingCep">
                                                <span x-show="!loadingCep">Buscar</span>
                                                <span x-show="loadingCep" class="material-symbols-outlined animate-spin text-sm">sync</span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div class="sm:col-span-2">
                                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 block">Rua / Avenida</label>
                                            <input
                                                class="w-full bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all"
                                                placeholder="Ex: Av. Brasil" type="text" x-model="formData.street" />
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 block">Número e Compl.</label>
                                            <input
                                                class="w-full bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all"
                                                placeholder="123, Apto 4" type="text" x-model="formData.number" />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                                        <div class="col-span-2 sm:col-span-1">
                                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 block">Bairro</label>
                                            <input
                                                class="w-full bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all"
                                                placeholder="Bairro" type="text" x-model="formData.neighborhood" />
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 block">Cidade</label>
                                            <input
                                                class="w-full bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all"
                                                placeholder="Cidade" type="text" x-model="formData.city" />
                                        </div>
                                        <div>
                                            <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 block">Estado</label>
                                            <input
                                                class="w-full bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all uppercase"
                                                placeholder="UF" type="text" x-model="formData.state" x-mask="aa" />
                                        </div>
                                    </div>
                                </div>

                                {{-- Opções de Frete Calculadas --}}
                                <div class="pt-4 border-t border-slate-100 dark:border-slate-800">
                                    <label class="text-[11px] font-black uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3 block flex items-center justify-between">
                                        <span>Opções de Envio Disponíveis</span>
                                        <span class="material-symbols-outlined text-sm text-primary">local_shipping</span>
                                    </label>

                                    <div class="space-y-2.5">
                                        {{-- Loading State --}}
                                        <template x-if="shippingLoading">
                                            <div class="flex items-center justify-center p-6 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-400 gap-2">
                                                <span class="material-symbols-outlined animate-spin text-primary text-base">sync</span>
                                                <span>Calculando melhor frete para seu CEP...</span>
                                            </div>
                                        </template>

                                        {{-- Error State --}}
                                        <template x-if="shippingError">
                                            <div class="p-3.5 bg-red-50 text-red-600 rounded-2xl text-xs font-bold flex items-center gap-2">
                                                <span class="material-symbols-outlined text-sm shrink-0">error</span>
                                                <span x-text="shippingError"></span>
                                            </div>
                                        </template>

                                        {{-- Shipping Cards --}}
                                        <template x-for="(opt, i) in shippingOptions" :key="i">
                                            <label
                                                class="relative flex items-center p-3.5 sm:p-4 border-2 rounded-2xl cursor-pointer hover:border-primary transition-all group gap-3"
                                                :class="selectedShipping?._type !== 'pickup' && selectedShipping?.modalidade === opt.modalidade && selectedShipping?.simulacao_id === opt.simulacao_id
                                                       ? 'border-primary bg-primary/5 shadow-sm' : 'border-slate-200 dark:border-slate-700/80 bg-white dark:bg-slate-800/40'">
                                                <input type="radio" name="shipping_method_radio"
                                                    :checked="selectedShipping?._type !== 'pickup' && selectedShipping?.modalidade === opt.modalidade && selectedShipping?.simulacao_id === opt.simulacao_id"
                                                    @change="selectShipping(opt)"
                                                    class="text-primary focus:ring-primary size-4 border-slate-300 dark:bg-slate-800" />
                                                <div class="flex-1 min-w-0">
                                                    <span class="text-xs font-bold text-slate-900 dark:text-white block uppercase tracking-wider truncate"
                                                        x-text="opt.servico"></span>
                                                    <span class="text-[11px] text-slate-400">
                                                        Chega em <strong class="text-slate-700 dark:text-slate-300" x-text="opt.prazo"></strong> dias úteis
                                                    </span>
                                                </div>
                                                <span class="text-sm font-black text-slate-900 dark:text-white"
                                                    x-text="formatMoney(opt.valor)"></span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Campos Ocultos para o Backend -->
                            <input type="hidden" name="address[zip_code]" x-model="formData.zip_code">
                            <input type="hidden" name="address[street]" x-model="formData.street">
                            <input type="hidden" name="address[number]" x-model="formData.number">
                            <input type="hidden" name="address[neighborhood]" x-model="formData.neighborhood">
                            <input type="hidden" name="address[city]" x-model="formData.city">
                            <input type="hidden" name="address[state]" x-model="formData.state">

                            <input type="hidden" name="shipping_amount"
                                x-bind:value="selectedShipping ? selectedShipping.valor : 0">
                            <input type="hidden" name="shipping_label"
                                x-bind:value="selectedShipping ? selectedShipping.modalidade : ''">
                            <input type="hidden" name="shipping_simulacao_id"
                                x-bind:value="selectedShipping ? (selectedShipping.simulacao_id ?? '') : ''">
                        </div>
                    </section>

                    {{-- ETAPA 2: PAGAMENTO --}}
                    <section class="space-y-4">
                        <div class="flex items-center gap-3">
                            <span class="size-7 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-black text-xs">{{ Auth::check() ? '2' : '3' }}</span>
                            <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 dark:text-white">
                                Forma de Pagamento
                            </h2>
                        </div>

                        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm rounded-3xl p-4 sm:p-8 space-y-6">
                            <!-- Hidden input for payment method -->
                            <input type="hidden" name="payment_method" x-model="paymentMethod">

                            {{-- Seletor de Pagamento Super Adaptado para Mobile --}}
                            <div class="grid grid-cols-3 gap-2 sm:gap-4">
                                <button type="button" @click="paymentMethod = 'pix'"
                                    class="flex flex-col items-center justify-center p-3 sm:p-5 border-2 rounded-2xl cursor-pointer transition-all gap-1.5 text-center"
                                    :class="paymentMethod === 'pix' ? 'border-primary bg-primary/5 shadow-sm' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 opacity-75'">
                                    <span class="material-symbols-outlined text-2xl sm:text-3xl text-primary">qr_code_2</span>
                                    <span class="text-[11px] sm:text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">PIX</span>
                                    <span class="text-[8px] sm:text-[9px] font-black px-1.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 uppercase whitespace-nowrap">-5% OFF</span>
                                </button>

                                <button type="button" @click="paymentMethod = 'credit_card'"
                                    class="flex flex-col items-center justify-center p-3 sm:p-5 border-2 rounded-2xl cursor-pointer transition-all gap-1.5 text-center"
                                    :class="paymentMethod === 'credit_card' ? 'border-primary bg-primary/5 shadow-sm' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 opacity-75'">
                                    <span class="material-symbols-outlined text-2xl sm:text-3xl text-slate-700 dark:text-slate-300">credit_card</span>
                                    <span class="text-[11px] sm:text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Cartão</span>
                                    <span class="text-[8px] sm:text-[9px] font-bold text-slate-400 uppercase whitespace-nowrap">Até 10x</span>
                                </button>

                                <button type="button" @click="paymentMethod = 'boleto'"
                                    class="flex flex-col items-center justify-center p-3 sm:p-5 border-2 rounded-2xl cursor-pointer transition-all gap-1.5 text-center"
                                    :class="paymentMethod === 'boleto' ? 'border-primary bg-primary/5 shadow-sm' : 'border-slate-200 dark:border-slate-800 hover:border-slate-300 opacity-75'">
                                    <span class="material-symbols-outlined text-2xl sm:text-3xl text-slate-700 dark:text-slate-300">receipt_long</span>
                                    <span class="text-[11px] sm:text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Boleto</span>
                                    <span class="text-[8px] sm:text-[9px] font-bold text-slate-400 uppercase whitespace-nowrap">À vista</span>
                                </button>
                            </div>

                            {{-- Formulário Cartão de Crédito --}}
                            <div class="space-y-4 pt-2"
                                x-show="paymentMethod === 'credit_card'" x-transition x-cloak>
                                <div>
                                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 block">Nome Impresso no Cartão</label>
                                    <input name="card_holder"
                                        class="w-full bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 text-sm font-medium text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all uppercase"
                                        placeholder="COMO ESTÁ NO CARTÃO" type="text" />
                                </div>

                                <div>
                                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 block">Número do Cartão</label>
                                    <div class="relative">
                                        <input name="card_number"
                                            class="w-full bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 text-sm font-mono text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all"
                                            placeholder="0000 0000 0000 0000" type="text" inputmode="numeric"
                                            x-mask="9999 9999 9999 9999" />
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1.5 opacity-50">
                                            <span class="material-symbols-outlined text-lg text-slate-400">credit_card</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 block">Validade (MM/AA)</label>
                                        <input name="card_expiry"
                                            class="w-full bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 text-sm font-mono text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all text-center"
                                            placeholder="MM/AA" type="text" inputmode="numeric" x-mask="99/99" />
                                    </div>
                                    <div>
                                        <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 block">CVV</label>
                                        <input name="card_cvv"
                                            class="w-full bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 text-sm font-mono text-slate-900 dark:text-white placeholder:text-slate-300 focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all text-center"
                                            placeholder="123" type="password" inputmode="numeric" x-mask="9999" />
                                    </div>
                                </div>

                                <div>
                                    <label class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1 block">Opção de Parcelamento</label>
                                    <select name="installments"
                                        class="w-full bg-slate-50/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 rounded-xl px-3.5 py-3 text-sm font-bold text-slate-900 dark:text-white focus:ring-2 focus:ring-primary/30 focus:border-primary outline-none transition-all">
                                        <option value="10">Até 10x de R$ {{ number_format($total / 10, 2, ',', '.') }} sem juros</option>
                                        <option value="1">1x de R$ {{ number_format($total, 2, ',', '.') }} à vista</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Informação PIX --}}
                            <div class="text-center py-6 sm:py-8 space-y-3" x-show="paymentMethod === 'pix'" x-transition x-cloak>
                                <div class="inline-flex items-center justify-center size-20 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 border border-emerald-100 dark:border-emerald-800/40">
                                    <span class="material-symbols-outlined text-4xl">qr_code_2</span>
                                </div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Aprovação Instantânea via PIX</h4>
                                <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                                    O QR Code e a chave Copia e Cola serão gerados na próxima tela assim que você finalizar a compra.
                                </p>
                            </div>

                            {{-- Informação Boleto --}}
                            <div class="text-center py-6 sm:py-8 space-y-3" x-show="paymentMethod === 'boleto'" x-transition x-cloak>
                                <div class="inline-flex items-center justify-center size-20 rounded-2xl bg-slate-50 dark:bg-slate-800 text-slate-600 border border-slate-100 dark:border-slate-700">
                                    <span class="material-symbols-outlined text-4xl">receipt_long</span>
                                </div>
                                <h4 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white">Boleto Bancário</h4>
                                <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                                    O boleto com código de barras será gerado logo após a conclusão. Compensação bancária em até 2 dias úteis.
                                </p>
                            </div>
                        </div>
                    </section>
                </div>

                {{-- COLUNA LATERAL (DESKTOP): Resumo do Pedido Completo --}}
                <aside class="hidden lg:block lg:w-[420px]">
                    <div class="lg:sticky lg:top-28 space-y-6">
                        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm rounded-3xl p-6 sm:p-8 space-y-6">
                            <h3 class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white flex items-center justify-between">
                                <span>Resumo do Pedido</span>
                                <span class="text-slate-400 font-bold text-[11px]">{{ count($cart) }} {{ count($cart) === 1 ? 'item' : 'itens' }}</span>
                            </h3>

                            <div class="space-y-4 max-h-[360px] overflow-y-auto pr-2 custom-scrollbar">
                                @foreach($cart as $item)
                                    <div class="flex gap-4 items-center">
                                        <div class="size-16 bg-slate-50 dark:bg-slate-800 rounded-xl flex-shrink-0 overflow-hidden border border-slate-100 dark:border-slate-800">
                                            <img alt="{{ $item['name'] }}" class="h-full w-full object-cover"
                                                src="{{ str_starts_with($item['image'], 'http') ? $item['image'] : asset('storage/' . $item['image']) }}" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-xs font-bold text-slate-900 dark:text-white truncate">
                                                {{ $item['name'] }}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                @if(isset($item['color']))
                                                    <span class="size-2 rounded-full border border-slate-200" style="background-color: {{ $item['color'] }}"></span>
                                                @endif
                                                <p class="text-[10px] text-slate-400 uppercase">
                                                    @if(!empty($item['size']))
                                                        <span class="font-bold text-slate-600 dark:text-slate-300">Tam: {{ $item['size'] }}</span> •
                                                    @endif
                                                    Qtd: {{ $item['quantity'] }}
                                                </p>
                                            </div>
                                            <p class="text-xs font-black text-slate-900 dark:text-white mt-1">R$
                                                {{ number_format($item['price'], 2, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="space-y-3 py-4 border-y border-slate-100 dark:border-slate-800 text-xs">
                                <div class="flex justify-between text-slate-500">
                                    <span>Subtotal</span>
                                    <span class="font-bold text-slate-900 dark:text-white">R$ {{ number_format($subtotal, 2, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between text-slate-500">
                                    <span>Frete</span>
                                    <span class="font-bold text-slate-900 dark:text-white"
                                        :class="selectedShipping && selectedShipping.valor === 0 ? 'text-emerald-600' : ''">
                                        <template x-if="!selectedShipping">— A selecionar</template>
                                        <template x-if="selectedShipping && selectedShipping.valor === 0">✔ Grátis</template>
                                        <template x-if="selectedShipping && selectedShipping.valor > 0">
                                            <span x-text="'R$ ' + Number(selectedShipping.valor).toLocaleString('pt-BR', {minimumFractionDigits:2})"></span>
                                        </template>
                                    </span>
                                </div>
                                <div x-show="selectedShipping" class="text-[11px] text-slate-400 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm text-primary"
                                        x-text="selectedShipping?._type === 'pickup' ? 'store' : 'local_shipping'"></span>
                                    <span x-text="selectedShipping?.modalidade"></span>
                                    <template x-if="selectedShipping && selectedShipping.prazo > 0">
                                        <span x-text="'• ' + selectedShipping.prazo + ' dias úteis'"></span>
                                    </template>
                                </div>
                            </div>

                            <div class="pt-2">
                                <div class="flex justify-between items-baseline mb-1">
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total</span>
                                    <span class="text-3xl font-black text-slate-900 dark:text-primary tracking-tight"
                                        x-text="'R$ ' + ({{ $subtotal }} + (selectedShipping ? selectedShipping.valor : 0)).toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2})">
                                        R$ {{ number_format($total, 2, ',', '.') }}
                                    </span>
                                </div>
                                <p class="text-[10px] text-slate-400 text-right uppercase tracking-wider">em até 10x sem juros</p>
                            </div>

                            <button type="submit"
                                :disabled="!selectedShipping || (needsUserInfo && (!userCpf || !userPhone))"
                                :class="(selectedShipping && (!needsUserInfo || (userCpf && userPhone))) ? 'bg-primary hover:bg-primary/95 shadow-lg shadow-primary/25 active:scale-[0.99]' : 'bg-slate-300 dark:bg-slate-700 cursor-not-allowed shadow-none opacity-60'"
                                class="w-full text-white font-black py-4 px-6 rounded-2xl transition-all uppercase text-xs tracking-wider flex items-center justify-center gap-2">
                                <span>Finalizar Pedido</span>
                                <span class="material-symbols-outlined text-base">arrow_forward</span>
                            </button>
                            <p x-show="!selectedShipping"
                                class="text-[11px] text-amber-600 dark:text-amber-400 font-bold text-center flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-xs">local_shipping</span>
                                Selecione o frete para finalizar
                            </p>
                        </div>

                        {{-- Badges de Confiança --}}
                        <div class="grid grid-cols-2 gap-3 text-center">
                            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-3.5 rounded-2xl flex flex-col items-center">
                                <span class="material-symbols-outlined text-emerald-600 text-2xl mb-1">lock</span>
                                <span class="text-[10px] font-black text-slate-900 dark:text-white uppercase">100% Seguro</span>
                                <span class="text-[9px] text-slate-400">Criptografia SSL 256 bits</span>
                            </div>
                            <div class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 p-3.5 rounded-2xl flex flex-col items-center">
                                <span class="material-symbols-outlined text-primary text-2xl mb-1">verified</span>
                                <span class="text-[10px] font-black text-slate-900 dark:text-white uppercase">Original</span>
                                <span class="text-[9px] text-slate-400">Garantia oficial Denner</span>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            {{-- BARRA FIXA FLUTUANTE INFERIOR NO MOBILE (Estilo App Nativo) --}}
            <div class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border-t border-slate-200 dark:border-slate-800 p-3.5 sm:p-4 shadow-2xl">
                <div class="flex items-center justify-between gap-3 max-w-lg mx-auto">
                    <div class="min-w-0">
                        <span class="text-[10px] text-slate-400 uppercase font-black tracking-wider block">Total a pagar</span>
                        <span class="text-lg sm:text-xl font-black text-slate-900 dark:text-primary tracking-tight truncate block"
                            x-text="'R$ ' + ({{ $subtotal }} + (selectedShipping ? selectedShipping.valor : 0)).toLocaleString('pt-BR', {minimumFractionDigits:2, maximumFractionDigits:2})">
                            R$ {{ number_format($total, 2, ',', '.') }}
                        </span>
                        <span class="text-[9px] text-slate-400 block truncate">em até 10x sem juros</span>
                    </div>
                    <button type="submit"
                        :disabled="!isFormReady"
                        :class="isFormReady ? 'bg-primary hover:bg-primary/95 shadow-lg shadow-primary/25 active:scale-[0.98]' : 'bg-slate-300 dark:bg-slate-700 cursor-not-allowed shadow-none opacity-60'"
                        class="flex-1 max-w-[210px] sm:max-w-[240px] text-white font-black py-3.5 px-4 rounded-2xl transition-all uppercase text-xs tracking-wider flex items-center justify-center gap-2">
                        <span>Finalizar</span>
                        <span class="material-symbols-outlined text-base">arrow_forward</span>
                    </button>
                </div>
                <template x-if="!isFormReady">
                    <p class="text-[10px] text-amber-600 dark:text-amber-400 font-bold text-center mt-1.5 flex items-center justify-center gap-1">
                        <span class="material-symbols-outlined text-xs">info</span>
                        <span x-text="getPendingRequirementText()"></span>
                    </p>
                </template>
            </div>
        </form>
    </main>

    <script>
        function checkoutHandler() {
            return {
                fulfillmentType: 'delivery',
                addressMode: '{{ (Auth::check() && $addresses->count() > 0) ? 'saved' : 'manual' }}',
                selectedAddressId: {{ (Auth::check() && $addresses->where('is_default', true)->first()) ? $addresses->where('is_default', true)->first()->id : ($addresses->first()?->id ?? 'null') }},
                savedAddresses: {!! json_encode($addresses->keyBy('id')) !!},
                loadingCep: false,
                paymentMethod: 'pix', // Padrão PIX (mais rápido no mobile)

                // User Info mandate
                isGuest: {{ Auth::check() ? 'false' : 'true' }},
                customerName: '{{ addslashes(old('customer_name', '')) }}',
                customerEmail: '{{ addslashes(old('customer_email', '')) }}',
                customerCpf: '{{ addslashes(old('customer_cpf', '')) }}',
                customerPhone: '{{ addslashes(old('customer_phone', '')) }}',

                needsUserInfo: {{ (Auth::check() && (!Auth::user()->cpf || !Auth::user()->phone)) ? 'true' : 'false' }},
                userCpf: '{{ Auth::user()->cpf ?? '' }}',
                userPhone: '{{ Auth::user()->phone ?? '' }}',

                // Shipping Logic
                shippingLoading: false,
                shippingOptions: [],
                shippingError: '',
                selectedShipping: null,

                storePickupInfo: {
                    name: '{{ addslashes(App\Models\Setting::get('store_pickup_name', 'DENNER Tênis - Loja Física')) }}',
                    street: '{{ addslashes(App\Models\Setting::get('store_street', 'Quadra SHN Quadra 1')) }}',
                    number: '{{ addslashes(App\Models\Setting::get('store_number', '24')) }}',
                    neighborhood: '{{ addslashes(App\Models\Setting::get('store_neighborhood', 'Asa Norte')) }}',
                    city: '{{ addslashes(App\Models\Setting::get('store_city', 'Brasília')) }}',
                    state: '{{ addslashes(App\Models\Setting::get('store_state', 'DF')) }}',
                    zip_code: '{{ addslashes(App\Models\Setting::get('store_zip', '70701-000')) }}',
                    hours: '{{ addslashes(App\Models\Setting::get('store_pickup_hours', 'Segunda a Sexta: 09h às 18h | Sábado: 09h às 13h')) }}',
                    timeframe: '{{ addslashes(App\Models\Setting::get('store_pickup_timeframe', 'Pronto em até 2 horas úteis após confirmação')) }}',
                    instructions: '{{ addslashes(App\Models\Setting::get('store_pickup_instructions', 'Apresente documento com foto e número do pedido.')) }}',
                },

                savedDeliveryAddress: null,

                init() {
                    // Check if user selected Store Pickup in Cart or before
                    const saved = localStorage.getItem('selected_shipping');
                    if (saved) {
                        try {
                            const parsed = JSON.parse(saved);
                            if (parsed && (parsed._type === 'pickup' || parsed.modalidade === 'Retirar na Loja' || parsed.modalidade === 'Retirada na Loja')) {
                                this.setPickupMode();
                                return;
                            }
                        } catch(e) {}
                    }

                    // Start by trying to calculate if we have a valid CEP for delivery
                    if (this.formData.zip_code && this.formData.zip_code.replace(/\D/g, '').length === 8) {
                        this.calculateShipping();
                    }
                },

                setPickupMode() {
                    this.fulfillmentType = 'pickup';
                    this.selectedShipping = {
                        _type: 'pickup',
                        servico: 'Retirada na Loja Física',
                        modalidade: 'Retirada na Loja',
                        prazo: 0,
                        valor: 0,
                        simulacao_id: null
                    };

                    if (this.formData.street && this.formData.street !== this.storePickupInfo.street) {
                        this.savedDeliveryAddress = { ...this.formData };
                    }

                    this.formData.street = this.storePickupInfo.street;
                    this.formData.number = this.storePickupInfo.number;
                    this.formData.neighborhood = this.storePickupInfo.neighborhood;
                    this.formData.city = this.storePickupInfo.city;
                    this.formData.state = this.storePickupInfo.state;
                    this.formData.zip_code = this.storePickupInfo.zip_code;

                    try {
                        localStorage.setItem('selected_shipping', JSON.stringify(this.selectedShipping));
                    } catch(e) {}
                },

                setDeliveryMode() {
                    this.fulfillmentType = 'delivery';
                    this.selectedShipping = null;

                    if (this.savedDeliveryAddress) {
                        this.formData = { ...this.savedDeliveryAddress };
                    } else {
                        @if(Auth::check() && $addresses->where('is_default', true)->first())
                            this.useSavedAddress({{ $addresses->where('is_default', true)->first()->id }});
                        @elseif(Auth::check() && $addresses->first())
                            this.useSavedAddress({{ $addresses->first()->id }});
                        @else
                            this.clearForm();
                        @endif
                    }

                    try {
                        localStorage.removeItem('selected_shipping');
                    } catch(e) {}

                    if (this.formData.zip_code && this.formData.zip_code.replace(/\D/g, '').length === 8) {
                        this.calculateShipping();
                    }
                },

                calculateShipping() {
                    if (this.fulfillmentType === 'pickup') return;

                    const cep = this.formData.zip_code.replace(/\D/g, '');
                    if (cep.length < 8) return;

                    this.shippingLoading = true;
                    this.shippingError = '';
                    this.shippingOptions = [];
                    this.selectedShipping = null;

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
                            if (data.cotacoes && data.cotacoes.length > 0) {
                                this.shippingOptions = data.cotacoes;

                                let cheapest = data.cotacoes[0];
                                data.cotacoes.forEach(opt => {
                                    if (opt.valor < cheapest.valor) {
                                        cheapest = opt;
                                    }
                                });
                                this.selectShipping(cheapest);
                            } else if (data.cotacoes && data.cotacoes.length === 0) {
                                this.shippingError = 'Nenhuma opção de frete disponível para este CEP.';
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
                    try {
                        localStorage.setItem('selected_shipping', JSON.stringify(option));
                    } catch(e) {}
                },

                get isDeliveryActive() {
                    if (this.isGuest) {
                        return this.customerName.trim().length > 2 && this.customerEmail.includes('@');
                    }
                    return true;
                },

                get isFormReady() {
                    if (!this.selectedShipping) return false;
                    if (this.isGuest) {
                        if (!this.customerName.trim() || !this.customerEmail.trim()) return false;
                        if (this.customerCpf.replace(/\D/g, '').length < 11) return false;
                        if (this.customerPhone.replace(/\D/g, '').length < 10) return false;
                    } else if (this.needsUserInfo) {
                        if (!this.userCpf || !this.userPhone) return false;
                        if (this.userCpf.replace(/\D/g, '').length < 11) return false;
                        if (this.userPhone.replace(/\D/g, '').length < 10) return false;
                    }
                    return true;
                },

                getPendingRequirementText() {
                    if (this.isGuest) {
                        if (!this.customerName.trim()) return 'Informe seu nome acima';
                        if (!this.customerEmail.trim()) return 'Informe seu e-mail acima';
                        if (this.customerCpf.replace(/\D/g, '').length < 11) return 'Informe seu CPF acima';
                        if (this.customerPhone.replace(/\D/g, '').length < 10) return 'Informe seu WhatsApp / telefone';
                    } else if (this.needsUserInfo) {
                        if (!this.userCpf || this.userCpf.replace(/\D/g, '').length < 11) return 'Informe seu CPF acima';
                        if (!this.userPhone || this.userPhone.replace(/\D/g, '').length < 10) return 'Informe seu WhatsApp acima';
                    }
                    if (!this.selectedShipping) return 'Selecione a entrega acima para continuar';
                    return '';
                },

                get isDeliveryActive() {
                    if (this.isGuest) {
                        return this.customerName.trim().length > 2 && this.customerEmail.includes('@');
                    }
                    return true;
                },

                get isFormReady() {
                    if (!this.selectedShipping) return false;
                    if (this.isGuest) {
                        if (!this.customerName.trim() || !this.customerEmail.trim()) return false;
                        if (this.customerCpf.replace(/\D/g, '').length < 11) return false;
                        if (this.customerPhone.replace(/\D/g, '').length < 10) return false;
                    } else if (this.needsUserInfo) {
                        if (!this.userCpf || !this.userPhone) return false;
                        if (this.userCpf.replace(/\D/g, '').length < 11) return false;
                        if (this.userPhone.replace(/\D/g, '').length < 10) return false;
                    }
                    return true;
                },

                getPendingRequirementText() {
                    if (this.isGuest) {
                        if (!this.customerName.trim()) return 'Informe seu nome acima';
                        if (!this.customerEmail.trim()) return 'Informe seu e-mail acima';
                        if (this.customerCpf.replace(/\D/g, '').length < 11) return 'Informe seu CPF acima';
                        if (this.customerPhone.replace(/\D/g, '').length < 10) return 'Informe seu WhatsApp / telefone';
                    } else if (this.needsUserInfo) {
                        if (!this.userCpf || this.userCpf.replace(/\D/g, '').length < 11) return 'Informe seu CPF acima';
                        if (!this.userPhone || this.userPhone.replace(/\D/g, '').length < 10) return 'Informe seu WhatsApp acima';
                    }
                    if (!this.selectedShipping) return 'Selecione a entrega acima para continuar';
                    return '';
                },

                isSelectedShipping(opt) {
                    if (!this.selectedShipping || this.selectedShipping._type === 'pickup') return false;
                    return this.selectedShipping === opt || (this.selectedShipping.modalidade === opt.modalidade && Number(this.selectedShipping.valor) === Number(opt.valor));
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

                useSavedAddress(addressOrId) {
                    let address = null;
                    if (typeof addressOrId === 'object' && addressOrId !== null) {
                        address = addressOrId;
                    } else if (this.savedAddresses && this.savedAddresses[addressOrId]) {
                        address = this.savedAddresses[addressOrId];
                    }
                    if (!address) return;
                    this.selectedAddressId = address.id;
                    this.addressMode = 'saved';
                    this.formData = {
                        zip_code: address.zip_code || '',
                        street: address.street || '',
                        number: address.number || '',
                        neighborhood: address.neighborhood || '',
                        city: address.city || '',
                        state: address.state || '',
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
            };
        }

        window.checkoutHandler = checkoutHandler;
        if (window.Alpine) {
            Alpine.data('checkoutHandler', checkoutHandler);
        } else {
            document.addEventListener('alpine:init', () => {
                Alpine.data('checkoutHandler', checkoutHandler);
            });
        }
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
