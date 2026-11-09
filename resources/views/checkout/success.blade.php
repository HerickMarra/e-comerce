<x-storefront-layout :seo="$seo">
    @php
        $paymentData = $paymentData ?? session('payment_data');
        $isOrderPaid = in_array($order->status, ['paid', 'completed', 'shipped', 'delivered']);
        $isPickup = str_contains(strtolower($order->shipping_modalidade ?? ''), 'retirad') || 
                    str_contains(strtolower($order->notes ?? ''), 'retirad') ||
                    $order->shipping_amount == 0;
        $pixCopyPaste = $paymentData['pix_copy_paste'] ?? '';
        $pixQrCode = $paymentData['pix_qr_code'] ?? '';
    @endphp

    <main class="max-w-2xl mx-auto px-3.5 sm:px-6 py-6 sm:py-12 pb-36 sm:pb-24"
          x-data="pixPaymentStatus('{{ route('checkout.status', $order->id) }}', {{ $isOrderPaid ? 'true' : 'false' }}, '{{ addslashes($pixCopyPaste) }}')">

        {{-- Topo Informativo do Pedido --}}
        <div class="text-center mb-6 sm:mb-8">
            {{-- Badge de Status --}}
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider mb-3 select-none"
                 :class="isPaid ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/60 dark:text-amber-300'">
                <span class="size-2 rounded-full"
                      :class="isPaid ? 'bg-emerald-500' : 'bg-amber-500 animate-ping'"></span>
                <span x-text="isPaid ? 'Pagamento Aprovado' : 'Aguardando Pagamento'"></span>
            </div>

            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight"
                x-text="isPaid ? 'Pedido Confirmado com Sucesso!' : 'Quase lá! Conclua seu PIX'">
                {{ $isOrderPaid ? 'Pedido Confirmado com Sucesso!' : 'Quase lá! Conclua seu PIX' }}
            </h1>

            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Número do pedido: <strong class="text-slate-800 dark:text-slate-200 font-mono font-bold">#{{ $order->order_number }}</strong>
            </p>
        </div>

        {{-- Toast Flutuante de Feedback ao Copiar Código --}}
        <div x-show="toastVisible"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             x-cloak
             class="fixed top-6 left-1/2 -translate-x-1/2 z-50 bg-slate-900 text-white dark:bg-white dark:text-slate-900 px-5 py-3 rounded-2xl shadow-2xl flex items-center gap-2.5 text-xs font-bold border border-white/20 select-none">
            <span class="material-symbols-outlined text-emerald-400 text-lg">check_circle</span>
            <span>Código PIX copiado! Abra o aplicativo do seu banco para pagar.</span>
        </div>

        {{-- ESTADO 1: PAGAMENTO APROVADO / FINALIZADO --}}
        <div x-show="isPaid" x-cloak x-transition
             class="bg-white dark:bg-slate-900 border-2 border-emerald-500/30 rounded-3xl p-6 sm:p-8 text-center space-y-6 shadow-xl shadow-emerald-500/10 mb-6">
            
            <div class="size-20 bg-emerald-500 text-white rounded-full flex items-center justify-center mx-auto shadow-lg shadow-emerald-500/30">
                <span class="material-symbols-outlined text-4xl font-black">check</span>
            </div>

            <div>
                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-700 bg-emerald-100 dark:bg-emerald-950/60 dark:text-emerald-300 px-3 py-1 rounded-full">
                    Aprovação Confirmada
                </span>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-2">
                    Recebemos seu Pagamento!
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 max-w-md mx-auto mt-2 leading-relaxed">
                    Seu pedido <strong class="text-slate-900 dark:text-white">#{{ $order->order_number }}</strong> já está sendo preparado pela nossa equipe.
                </p>
            </div>

            @if($isPickup)
                <div class="p-4 sm:p-5 rounded-2xl bg-emerald-50/70 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/40 text-left space-y-2">
                    <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-300 font-black text-xs uppercase tracking-wider">
                        <span class="material-symbols-outlined text-lg">storefront</span>
                        <span>Retirada no Balcão Liberada</span>
                    </div>
                    <p class="text-xs text-slate-700 dark:text-slate-200 font-semibold">
                        📍 {{ App\Models\Setting::get('store_pickup_name', 'DENNER Tênis - Loja Física') }}
                    </p>
                    <p class="text-[11px] text-slate-500">
                        {{ App\Models\Setting::get('store_street') }}, {{ App\Models\Setting::get('store_number') }} — {{ App\Models\Setting::get('store_neighborhood') }}, {{ App\Models\Setting::get('store_city') }}/{{ App\Models\Setting::get('store_state') }}
                    </p>
                    <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold pt-1">
                        ⏱ {{ App\Models\Setting::get('store_pickup_hours', 'Segunda a Sexta: 09h às 18h | Sábado: 09h às 13h') }}
                    </p>
                </div>
            @endif

            <template x-if="trackingCode">
                <div class="p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 flex items-center justify-between gap-3 text-left">
                    <div class="min-w-0">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Código de Rastreamento</span>
                        <span class="font-mono font-black text-sm text-slate-900 dark:text-white truncate block" x-text="trackingCode"></span>
                    </div>
                    <span class="material-symbols-outlined text-primary text-2xl">local_shipping</span>
                </div>
            </template>
        </div>

        {{-- ESTADO 2: PAGAMENTO PENDENTE (HERO CARD MOBILE DO PIX) --}}
        <div x-show="!isPaid" x-cloak class="space-y-4 mb-6">
            @if($order->payment_method === 'pix')
                <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-4 sm:p-7 shadow-xl shadow-slate-900/5 relative overflow-hidden">
                    
                    {{-- Faixa de topo do Card: Valor e Tag PIX --}}
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="size-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-2xl">qr_code_2</span>
                            </div>
                            <div>
                                <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block">Pague com PIX</span>
                                <span class="text-lg sm:text-xl font-black text-slate-900 dark:text-white block">
                                    R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Temporizador Regressivo --}}
                        <div class="text-right flex flex-col items-end">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Expira em</span>
                            <span class="text-xs sm:text-sm font-mono font-black text-amber-600 dark:text-amber-400 flex items-center gap-1">
                                <span class="material-symbols-outlined text-xs">timer</span>
                                <span x-text="countdown">30:00</span>
                            </span>
                        </div>
                    </div>

                    {{-- AÇÃO #1 NO MOBILE: BOTÃO GIGANTE DE COPIAR CÓDIGO PIX --}}
                    @if(!empty($pixCopyPaste))
                        <div class="py-5 space-y-3">
                            <button type="button"
                                    @click="copyPixCode()"
                                    class="w-full py-4 px-5 rounded-2xl font-black text-sm uppercase tracking-wider transition-all duration-200 flex items-center justify-center gap-3 shadow-lg select-none active:scale-[0.98]"
                                    :class="copied ? 'bg-emerald-600 text-white shadow-emerald-600/30' : 'bg-primary hover:bg-primary/95 text-white shadow-primary/25'">
                                <span class="material-symbols-outlined text-xl" x-text="copied ? 'check_circle' : 'content_copy'"></span>
                                <span x-text="copied ? 'CÓDIGO PIX COPIADO!' : 'COPIAR CÓDIGO PIX'"></span>
                            </button>

                            {{-- Campo com o código Copia e Cola visível para caso o usuário queira selecionar manualmente --}}
                            <div class="relative flex items-center">
                                <input type="text"
                                       readonly
                                       :value="code"
                                       @click="$event.target.select()"
                                       class="w-full bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/80 rounded-xl px-3.5 py-2.5 text-[11px] font-mono text-slate-500 dark:text-slate-400 select-all outline-none truncate pr-16" />
                                <button type="button"
                                        @click="copyPixCode()"
                                        class="absolute right-1 px-3 py-1.5 text-[10px] font-bold text-primary hover:text-primary/80 uppercase">
                                    <span x-text="copied ? 'Copiado' : 'Copiar'"></span>
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- QR CODE PARA ESCANEAR (Opção Alternativa ou Segundo Aparelho) --}}
                    @if(!empty($pixQrCode))
                        <div x-data="{ showQr: true }" class="pt-3 border-t border-slate-100 dark:border-slate-800">
                            <button type="button"
                                    @click="showQr = !showQr"
                                    class="w-full flex items-center justify-between text-left py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-primary transition-colors">
                                <span class="flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-base text-primary">qr_code</span>
                                    <span>QR Code para escanear com outro celular</span>
                                </span>
                                <span class="material-symbols-outlined text-sm transition-transform duration-200"
                                      :class="showQr ? 'rotate-180' : ''">expand_more</span>
                            </button>

                            <div x-show="showQr" x-transition x-cloak class="pt-3 pb-2 text-center">
                                <div class="inline-block p-3.5 sm:p-4 bg-white rounded-3xl border-2 border-slate-100 dark:border-slate-700 shadow-md">
                                    <img src="data:image/png;base64,{{ $pixQrCode }}"
                                         alt="QR Code PIX"
                                         class="size-48 sm:size-52 mx-auto rounded-xl object-contain block" />
                                </div>
                                <p class="text-[11px] text-slate-400 mt-2 font-medium">
                                    Aponte a câmera do aplicativo do seu banco para a imagem acima
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Passo a Passo Rápido e Simples no Mobile --}}
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-800/40 -mx-4 -mb-4 p-4 rounded-b-3xl">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 block mb-2.5">
                            Como pagar pelo celular em 3 passos:
                        </span>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-left text-xs">
                            <div class="flex items-start gap-2">
                                <span class="size-5 rounded-full bg-primary/10 text-primary font-black text-[11px] flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                                <span class="text-slate-600 dark:text-slate-300 text-[11px] leading-tight">
                                    Toque em <strong>Copiar Código Pix</strong> acima.
                                </span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="size-5 rounded-full bg-primary/10 text-primary font-black text-[11px] flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                                <span class="text-slate-600 dark:text-slate-300 text-[11px] leading-tight">
                                    Abra o app do seu banco e acesse <strong>Pix</strong>.
                                </span>
                            </div>
                            <div class="flex items-start gap-2">
                                <span class="size-5 rounded-full bg-primary/10 text-primary font-black text-[11px] flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                                <span class="text-slate-600 dark:text-slate-300 text-[11px] leading-tight">
                                    Cole o código no <strong>Pix Copia e Cola</strong> e confirme!
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Indicador de Atualização em Tempo Real --}}
                    <div class="mt-5 flex items-center justify-center gap-2 text-xs text-emerald-600 dark:text-emerald-400 font-bold select-none">
                        <span class="material-symbols-outlined text-sm animate-spin">sync</span>
                        <span>Aguardando pagamento... A tela atualizará automaticamente!</span>
                    </div>
                </div>

            @elseif($order->payment_method === 'boleto' && isset($paymentData['bank_slip_url']))
                {{-- Card Boleto --}}
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 text-center space-y-4 shadow-sm">
                    <div class="size-14 bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center mx-auto text-slate-700 dark:text-slate-300">
                        <span class="material-symbols-outlined text-3xl">receipt_long</span>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 dark:text-white">Boleto Bancário Gerado</h3>
                        <p class="text-xs text-slate-500 mt-1">Pague em qualquer banco, lotérica ou pelo internet banking.</p>
                    </div>

                    <a href="{{ $paymentData['bank_slip_url'] }}" target="_blank"
                       class="w-full py-3.5 px-6 bg-slate-900 hover:bg-slate-800 dark:bg-white dark:text-slate-900 text-white font-black text-xs uppercase tracking-wider rounded-2xl transition-all shadow-md flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-base">download</span>
                        <span>Abrir / Baixar Boleto PDF</span>
                    </a>
                </div>
            @endif
        </div>

        {{-- RESUMO SANFONA DO PEDIDO (Compacto no Mobile) --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-4 sm:p-6 mb-6 shadow-sm"
             x-data="{ showSummary: false }">
            
            <button type="button"
                    @click="showSummary = !showSummary"
                    class="w-full flex items-center justify-between text-left">
                <div class="flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-primary text-xl">shopping_bag</span>
                    <div>
                        <span class="text-xs font-black uppercase tracking-wider text-slate-900 dark:text-white block">
                            Resumo da Compra ({{ $order->items->count() }} {{ $order->items->count() === 1 ? 'item' : 'itens' }})
                        </span>
                        <span class="text-[10px] text-slate-400 block" x-text="showSummary ? 'Toque para recolher' : 'Toque para ver os produtos'"></span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-xs font-black text-slate-900 dark:text-white">
                        R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                    </span>
                    <span class="material-symbols-outlined text-slate-400 text-sm transition-transform duration-200"
                          :class="showSummary ? 'rotate-180' : ''">expand_more</span>
                </div>
            </button>

            <div x-show="showSummary" x-transition x-cloak class="pt-4 mt-4 border-t border-slate-100 dark:border-slate-800 space-y-3">
                <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between gap-3 text-xs">
                            <div class="min-w-0 flex-1">
                                <span class="font-bold text-slate-800 dark:text-slate-200 truncate block">
                                    {{ $item->product->name ?? 'Tênis' }}
                                </span>
                                <span class="text-[10px] text-slate-400 block">
                                    {{ $item->quantity }}x {{ $item->color_name }} @if(!empty($item->size)) • Tam: {{ $item->size }} @endif
                                </span>
                            </div>
                            <span class="font-bold text-slate-900 dark:text-white font-mono shrink-0">
                                R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 space-y-1.5 text-xs text-slate-500">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-bold text-slate-900 dark:text-white font-mono">
                            R$ {{ number_format($order->subtotal, 2, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span>Entrega / Frete</span>
                        <span class="font-bold {{ $order->shipping_amount == 0 ? 'text-emerald-600' : 'text-slate-900 dark:text-white font-mono' }}">
                            {{ $order->shipping_amount == 0 ? '✔ Grátis' : 'R$ ' . number_format($order->shipping_amount, 2, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm font-black text-slate-900 dark:text-white pt-2 border-t border-slate-100 dark:border-slate-800">
                        <span>Total Pago</span>
                        <span class="text-primary font-mono">
                            R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- BOTÕES DE NAVEGAÇÃO --}}
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('customer.orders') }}"
               class="w-full sm:w-auto px-6 py-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-white font-black text-xs uppercase tracking-wider rounded-2xl text-center hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm">
                Ver Meus Pedidos
            </a>
            <a href="/"
               class="w-full sm:w-auto px-6 py-3.5 bg-primary text-white font-black text-xs uppercase tracking-wider rounded-2xl text-center shadow-lg shadow-primary/25 hover:bg-primary/95 transition-all">
                Continuar Comprando
            </a>
        </div>

        {{-- BARRA FIXA FLUTUANTE INFERIOR NO MOBILE (Estilo App) --}}
        @if(!empty($pixCopyPaste) && $order->payment_method === 'pix')
            <div x-show="!isPaid" x-cloak
                 class="lg:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-slate-900/95 backdrop-blur-xl border-t border-slate-200/90 dark:border-slate-800 p-3.5 shadow-2xl">
                <div class="flex items-center justify-between gap-3 max-w-lg mx-auto">
                    <div class="min-w-0">
                        <span class="text-[9px] text-slate-400 uppercase font-black tracking-wider block">Total PIX</span>
                        <span class="text-base font-black text-slate-900 dark:text-white tracking-tight truncate block">
                            R$ {{ number_format($order->total_amount, 2, ',', '.') }}
                        </span>
                    </div>
                    <button type="button"
                            @click="copyPixCode()"
                            class="flex-1 max-w-[220px] text-white font-black py-3 px-4 rounded-xl transition-all uppercase text-xs tracking-wider flex items-center justify-center gap-2 shadow-lg active:scale-95"
                            :class="copied ? 'bg-emerald-600 shadow-emerald-600/30' : 'bg-primary shadow-primary/25'">
                        <span class="material-symbols-outlined text-base" x-text="copied ? 'check' : 'content_copy'"></span>
                        <span x-text="copied ? 'Copiado!' : 'Copiar PIX'"></span>
                    </button>
                </div>
            </div>
        @endif
    </main>

    <script>
        function pixPaymentStatus(statusUrl, initialPaid = false, pixCode = '') {
            return {
                isPaid: initialPaid,
                trackingCode: '{{ $order->shipping_tracking_code ?? '' }}',
                code: pixCode,
                copied: false,
                toastVisible: false,
                interval: null,
                countdownSeconds: 1800, // 30 minutos
                countdown: '30:00',

                init() {
                    this.startCountdown();

                    if (this.isPaid) return;
                    // Checa status a cada 3.5 segundos
                    this.interval = setInterval(() => this.checkStatus(), 3500);
                },

                copyPixCode() {
                    const text = this.code;
                    if (!text) return;

                    const showSuccess = () => {
                        this.copied = true;
                        this.toastVisible = true;
                        setTimeout(() => this.copied = false, 3000);
                        setTimeout(() => this.toastVisible = false, 3500);
                    };

                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(text).then(showSuccess).catch(() => this.fallbackCopy(text, showSuccess));
                    } else {
                        this.fallbackCopy(text, showSuccess);
                    }
                },

                fallbackCopy(text, callback) {
                    const ta = document.createElement('textarea');
                    ta.value = text;
                    ta.style.position = 'fixed';
                    ta.style.opacity = '0';
                    document.body.appendChild(ta);
                    ta.focus();
                    ta.select();
                    try {
                        document.execCommand('copy');
                        callback();
                    } catch (e) {
                        prompt('Copie o código PIX manualmente:', text);
                    }
                    document.body.removeChild(ta);
                },

                startCountdown() {
                    const timer = setInterval(() => {
                        if (this.countdownSeconds <= 0 || this.isPaid) {
                            clearInterval(timer);
                            return;
                        }
                        this.countdownSeconds--;
                        const mins = Math.floor(this.countdownSeconds / 60).toString().padStart(2, '0');
                        const secs = (this.countdownSeconds % 60).toString().padStart(2, '0');
                        this.countdown = `${mins}:${secs}`;
                    }, 1000);
                },

                checkStatus() {
                    if (this.isPaid) {
                        if (this.interval) clearInterval(this.interval);
                        return;
                    }

                    fetch(statusUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.paid) {
                            this.isPaid = true;
                            if (data.tracking_code) {
                                this.trackingCode = data.tracking_code;
                            }
                            if (this.interval) clearInterval(this.interval);
                        }
                    })
                    .catch(() => {});
                }
            };
        }

        window.pixPaymentStatus = pixPaymentStatus;
        if (window.Alpine) {
            Alpine.data('pixPaymentStatus', pixPaymentStatus);
        } else {
            document.addEventListener('alpine:init', () => {
                Alpine.data('pixPaymentStatus', pixPaymentStatus);
            });
        }
    </script>
</x-storefront-layout>
