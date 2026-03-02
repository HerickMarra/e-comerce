<x-customer-layout title="Pedido #{{ $order->order_number }}" active="orders">
    <div class="mb-6">
        <a class="inline-flex items-center gap-1 text-slate-500 hover:text-primary text-sm font-medium transition-colors mb-6" href="{{ route('customer.orders') }}">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Voltar para Meus Pedidos
        </a>
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 dark:text-white">Pedido #{{ $order->order_number }}</h1>
                <p class="text-slate-500 mt-2">Realizado em {{ $order->created_at->translatedFormat('d \d\e F, Y') }} • {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</p>
            </div>
            @if($order->status === 'delivered')
                <button class="px-6 py-2 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                    Baixar NF-e
                </button>
            @endif
        </div>
    </div>

    @php
        $statusProgression = [
            'pending' => 1,
            'paid' => 2,
            'processing' => 3,
            'shipped' => 4,
            'delivered' => 5,
            'cancelled' => 0, // Special case
        ];

        $currentStep = $statusProgression[$order->status] ?? 1;
        
        $steps = [
            ['label' => 'Pagamento pendente', 'icon' => 'payments', 'status' => 'pending'],
            ['label' => 'Processamento', 'icon' => 'inventory', 'status' => 'paid'],
            ['label' => 'Pedido sendo preparado', 'icon' => 'package_2', 'status' => 'processing'],
            ['label' => 'Enviado', 'icon' => 'local_shipping', 'status' => 'shipped'],
            ['label' => 'Entregue', 'icon' => 'check_circle', 'status' => 'delivered'],
        ];
    @endphp

    @if($order->status !== 'cancelled')
        <section class="bg-white dark:bg-slate-900 rounded-3xl p-8 border border-slate-100 dark:border-slate-800 mb-8 shadow-sm overflow-hidden">
            <h2 class="text-xl font-bold mb-10">Status do Pedido</h2>
            <div class="relative px-4">
                {{-- Progress Bar Background --}}
                <div class="absolute top-5 left-8 right-8 h-0.5 bg-slate-100 dark:bg-slate-800 -z-0"></div>
                
                {{-- Dynamic Progress Bar Filler --}}
                <div class="absolute top-5 left-8 h-0.5 bg-primary transition-all duration-1000 -z-0" 
                     style="width: {{ max(0, ($currentStep - 1) * 25) }}%"></div>

                <div class="relative z-10 flex justify-between">
                    @foreach($steps as $index => $step)
                        @php
                            $isCompleted = ($index + 1) <= $currentStep;
                            $isCurrent = ($index + 1) === $currentStep;
                        @endphp
                        <div class="flex flex-col items-center gap-3 w-1/5">
                            <div @class([
                                'w-10 h-10 rounded-full flex items-center justify-center transition-all duration-500',
                                'bg-primary text-white shadow-lg shadow-primary/20 scale-110' => $isCompleted,
                                'bg-slate-100 text-slate-400 dark:bg-slate-800' => !$isCompleted
                            ])>
                                <span class="material-symbols-outlined text-xl">
                                    {{ $isCompleted ? 'check' : $step['icon'] }}
                                </span>
                            </div>
                            <div class="text-center px-1">
                                <p @class([
                                    'font-bold text-[10px] md:text-xs uppercase tracking-tight',
                                    'text-slate-900 dark:text-white' => $isCompleted,
                                    'text-slate-400' => !$isCompleted
                                ])>
                                    {{ $step['label'] }}
                                </p>
                                @if($isCurrent)
                                    <p class="text-[9px] text-primary font-bold mt-0.5 animate-pulse">ATUAL</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @else
        <div class="mb-8 p-6 bg-red-50 border border-red-100 dark:bg-red-900/10 dark:border-red-800/20 rounded-3xl flex items-center gap-4 text-red-700 dark:text-red-400">
            <span class="material-symbols-outlined text-3xl">cancel</span>
            <div>
                <h3 class="font-bold text-lg">Pedido Cancelado</h3>
                <p class="text-sm">Este pedido foi cancelado e não seguirá no processo de entrega.</p>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Items List --}}
        <div class="lg:col-span-2 space-y-6">
            <section class="bg-white dark:bg-slate-900 rounded-3xl p-8 border border-slate-100 dark:border-slate-800 shadow-sm">
                <h2 class="text-xl font-bold mb-6">Itens do Pedido</h2>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($order->items as $item)
                        <div class="py-6 flex gap-6 first:pt-0 last:pb-0">
                            <div class="w-24 h-32 bg-slate-50 dark:bg-slate-800 rounded-xl overflow-hidden shrink-0 border border-slate-100 dark:border-slate-800">
                                @php
                                    $img = $item->product?->images->where('is_main', true)->first()?->path 
                                        ?? $item->product?->images->first()?->path;
                                @endphp
                                @if($img)
                                    <img alt="{{ $item->product->name ?? 'Produto' }}" class="w-full h-full object-cover" 
                                         src="{{ str_starts_with($img, 'http') ? $img : asset('storage/' . $img) }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-slate-300 text-3xl">inventory_2</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 flex flex-col justify-center">
                                <h3 class="font-bold text-lg text-slate-900 dark:text-white uppercase tracking-tight">
                                    {{ $item->product->name ?? 'Produto Removido' }}
                                </h3>
                                @if($item->color_name)
                                    <div class="flex items-center gap-2 mt-1">
                                        @if($item->color)
                                            <span class="size-3 rounded-full border border-slate-200" style="background-color: {{ $item->color }}"></span>
                                        @endif
                                        <p class="text-sm text-slate-500 font-medium">Cor: {{ $item->color_name }}</p>
                                    </div>
                                @endif
                                <div class="flex justify-between items-end mt-4">
                                    <span class="text-slate-600 dark:text-slate-400 text-sm font-bold uppercase tracking-widest text-[10px]">
                                        Quantidade: {{ $item->quantity }}
                                    </span>
                                    <span class="font-bold text-primary text-lg">
                                        R$ {{ number_format($item->price, 2, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- Shipping Address --}}
            <section class="bg-white dark:bg-slate-900 rounded-3xl p-8 border border-slate-100 dark:border-slate-800 shadow-sm">
                <div class="flex items-center gap-2 mb-6 text-primary">
                    <span class="material-symbols-outlined">location_on</span>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Informações de Entrega</h2>
                </div>
                <div class="text-slate-600 dark:text-slate-400 space-y-2">
                    <p class="font-bold text-slate-900 dark:text-white text-lg">{{ $order->user->name }}</p>
                    @php $addr = $order->address_info; @endphp
                    @if(isset($addr['street']))
                        <p class="text-sm">{{ $addr['street'] }}, {{ $addr['number'] ?? '' }}</p>
                        @if($addr['complement'] ?? null) <p class="text-sm">{{ $addr['complement'] }}</p> @endif
                        <p class="text-sm">{{ $addr['neighborhood'] ?? '' }}</p>
                        <p class="text-sm">{{ $addr['city'] ?? '' }} - {{ $addr['state'] ?? '' }}</p>
                        <p class="text-sm font-mono mt-1">CEP: {{ $addr['zip_code'] ?? $addr['zip'] ?? '' }}</p>
                    @else
                        <p class="text-sm italic text-slate-400">Informações de endereço não registradas no pedido.</p>
                    @endif
                    
                    @if($order->user->phone)
                        <p class="pt-4 flex items-center gap-2 text-sm font-bold text-slate-900 dark:text-white uppercase tracking-widest text-[10px]">
                            <span class="material-symbols-outlined text-base">call</span>
                            {{ $order->user->phone }}
                        </p>
                    @endif
                </div>
            </section>
        </div>

        {{-- Payment Summary --}}
        <div class="space-y-6">
            <section class="bg-white dark:bg-slate-900 rounded-3xl p-8 border border-slate-100 dark:border-slate-800 shadow-sm sticky top-24">
                <div class="flex items-center gap-2 mb-6 text-primary">
                    <span class="material-symbols-outlined">payments</span>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">Pagamento</h2>
                </div>
                <div @class([
                    'mb-8 p-4 rounded-2xl flex items-center gap-4 border',
                    'bg-slate-50 border-slate-100 dark:bg-slate-800/50 dark:border-slate-700'
                ])>
                    <div class="size-10 bg-white dark:bg-slate-900 rounded-xl flex items-center justify-center text-slate-400 border border-slate-100 dark:border-slate-800">
                        <span class="material-symbols-outlined">
                            @if($order->payment_method === 'credit_card') credit_card
                            @elseif($order->payment_method === 'pix') qr_code_2
                            @else receipt_long @endif
                        </span>
                    </div>
                    <div class="text-sm flex-1">
                        <p class="font-bold text-slate-900 dark:text-white uppercase tracking-tight">
                            @if($order->payment_method === 'credit_card') Cartão de Crédito
                            @elseif($order->payment_method === 'pix') PIX
                            @else Boleto Bancário @endif
                        </p>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">
                            {{ $order->status === 'pending' ? 'Aguardando' : 'Confirmado' }}
                        </p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-slate-400">
                        <span>Subtotal</span>
                        <span class="text-slate-900 dark:text-white">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-slate-400">
                        <span>Entrega</span>
                        <span class="{{ $order->shipping_amount > 0 ? 'text-slate-900 dark:text-white' : 'text-emerald-500' }}">
                            {{ $order->shipping_amount > 0 ? 'R$ ' . number_format($order->shipping_amount, 2, ',', '.') : 'Grátis' }}
                        </span>
                    </div>
                    <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <span class="font-black text-xs uppercase tracking-[0.2em] text-slate-400">Total</span>
                        <span class="text-3xl font-black text-primary">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                    </div>
                </div>

                @if($order->notes)
                    <div class="mt-8 pt-8 border-t border-slate-100 dark:border-slate-800">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-3">Observações</h4>
                        <div class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-2xl">
                            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed italic">{{ $order->notes }}</p>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-customer-layout>
