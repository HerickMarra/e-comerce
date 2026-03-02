<x-storefront-layout>
    <main class="max-w-3xl mx-auto px-4 py-16 text-center">
        @php
            $paymentData = session('payment_data');
        @endphp

        <div class="mb-10">
            <div
                class="size-20 bg-emerald-50 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="material-symbols-outlined text-emerald-500 text-4xl">check_circle</span>
            </div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Pedido Realizado com Sucesso!</h1>
            <p class="text-slate-500">Obrigado pela sua compra. O número do seu pedido é <span
                    class="font-bold">#{{ $order->order_number }}</span></p>
        </div>

        <div
            class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-8 mb-10 shadow-sm text-left">
            <h2 class="text-sm font-bold uppercase tracking-widest text-slate-400 mb-6">Resumo da Compra</h2>

            <div class="space-y-4 mb-8">
                @foreach($order->items as $item)
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-600 dark:text-slate-300">{{ $item->quantity }}x {{ $item->product->name }}
                            ({{ $item->color_name }})</span>
                        <span class="font-bold text-slate-900 dark:text-white">R$
                            {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <div class="border-t border-slate-50 dark:border-slate-800 pt-6 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Frete</span>
                    <span class="text-slate-900 dark:text-white">R$
                        {{ number_format($order->shipping_amount, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-xl font-bold pt-2">
                    <span class="text-slate-900 dark:text-white">Total</span>
                    <span class="text-primary">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        @if($paymentData)
            @if($order->payment_method === 'pix')
                <div
                    class="bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-900/30 rounded-3xl p-8 mb-10">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6">Finalize seu pagamento via PIX</h3>

                    @if(isset($paymentData['pix_qr_code']))
                        <div class="bg-white p-4 rounded-2xl inline-block mb-6 border border-emerald-100 dark:border-slate-800">
                            <img src="data:image/png;base64,{{ $paymentData['pix_qr_code'] }}" alt="QR Code PIX" class="size-48">
                        </div>
                    @endif

                    @if(isset($paymentData['pix_copy_paste']))
                        <div class="max-w-md mx-auto" x-data="{ copied: false, code: '{{ $paymentData['pix_copy_paste'] }}' }">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Código Copia e Cola</p>
                            <div class="flex gap-2">
                                <input readonly type="text" :value="code"
                                    class="flex-1 bg-white dark:bg-slate-800 border-emerald-100 dark:border-slate-700 rounded-xl px-4 py-3 text-xs font-mono truncate outline-none">
                                <button
                                    @click="navigator.clipboard.writeText(code); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="bg-emerald-500 text-white px-6 rounded-xl font-bold text-xs uppercase transition-all hover:bg-emerald-600">
                                    <span x-show="!copied">Copiar</span>
                                    <span x-show="copied">Copiado!</span>
                                </button>
                            </div>
                        </div>
                    @endif

                    <div
                        class="mt-8 flex items-center justify-center gap-2 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                        <span class="material-symbols-outlined text-sm">schedule</span>
                        Este código expira em 30 minutos
                    </div>
                </div>
            @elseif($order->payment_method === 'boleto' && isset($paymentData['bank_slip_url']))
                <div class="bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-8 mb-10">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Seu boleto está pronto</h3>
                    <p class="text-sm text-slate-500 mb-8">Você pode visualizar o boleto abaixo ou utilizar o botão para abrir
                        em tela cheia.</p>

                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl overflow-hidden border border-slate-100 dark:border-slate-700 mb-8 shadow-inner">
                        <iframe src="{{ $paymentData['bank_slip_url'] }}" class="w-full h-[600px]" frameborder="0"></iframe>
                    </div>

                    <a href="{{ $paymentData['bank_slip_url'] }}" target="_blank"
                        class="inline-flex items-center justify-center px-10 py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold rounded-2xl transition-all shadow-xl hover:scale-105 gap-3">
                        <span class="material-symbols-outlined">description</span>
                        Imprimir Boleto / PDF
                    </a>
                </div>
            @endif
        @endif

        <div class="flex flex-col sm:flex-row gap-4 justify-center mt-10">
            <a href="{{ route('customer.orders') }}"
                class="px-8 py-4 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white font-bold rounded-2xl hover:bg-slate-50 transition-all">
                Ver Meus Pedidos
            </a>
            <a href="/"
                class="px-8 py-4 bg-primary text-white font-bold rounded-2xl shadow-xl shadow-primary/20 hover:scale-105 transition-all">
                Continuar Comprando
            </a>
        </div>
    </main>
</x-storefront-layout>