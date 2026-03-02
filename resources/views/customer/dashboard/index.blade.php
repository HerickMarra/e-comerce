<x-customer-layout title="Painel" active="dashboard">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white leading-tight">Olá, {{ Auth::user()->name }}!</h1>
        <p class="text-slate-500 mt-2 font-medium">Bem-vindo ao seu painel. Aqui você pode acompanhar seus pedidos e
            gerenciar sua conta.</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                <span class="material-symbols-outlined">shopping_bag</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">
                {{ str_pad($totalOrders, 2, '0', STR_PAD_LEFT) }}
            </div>
            <div class="text-sm font-bold text-slate-400 uppercase tracking-wider">Pedidos realizados</div>
        </div>
        <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4">
                <span class="material-symbols-outlined">local_shipping</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">
                {{ str_pad($inTransit, 2, '0', STR_PAD_LEFT) }}
            </div>
            <div class="text-sm font-bold text-slate-400 uppercase tracking-wider">Em processamento</div>
        </div>
        <a href="{{ route('customer.addresses') }}"
            class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition-all group">
            <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-4">
                <span class="material-symbols-outlined">location_on</span>
            </div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">
                <span
                    class="material-symbols-outlined text-3xl group-hover:translate-x-1 transition-transform">arrow_forward</span>
            </div>
            <div class="text-sm font-bold text-slate-400 uppercase tracking-wider">Meus Endereços</div>
        </a>
    </div>

    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white">Pedidos Recentes</h2>
        <a href="{{ route('customer.orders') }}" class="text-sm font-bold text-primary hover:underline">Ver todos</a>
    </div>

    @if($recentOrders->isNotEmpty())
        <div class="space-y-4">
            @foreach($recentOrders as $order)
                @php
                    $statusMap = [
                        'pending' => ['label' => 'Aguardando Pagamento', 'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'],
                        'paid' => ['label' => 'Pago', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'],
                        'processing' => ['label' => 'Em processamento', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'],
                        'shipped' => ['label' => 'Enviado', 'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'],
                        'delivered' => ['label' => 'Entregue', 'class' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'],
                        'cancelled' => ['label' => 'Cancelado', 'class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'],
                    ];
                    $st = $statusMap[$order->status] ?? ['label' => ucfirst($order->status), 'class' => 'bg-slate-100 text-slate-600'];
                    $firstItem = $order->items->first();
                    $img = $firstItem?->product?->images?->where('is_main', true)->first()?->path
                        ?? $firstItem?->product?->images?->first()?->path;
                @endphp
                <div
                    class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-all">
                    <div class="p-4 flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-50 border border-slate-100 shrink-0">
                                @if($img)
                                    <img alt="Produto" class="w-full h-full object-cover"
                                        src="{{ str_starts_with($img, 'http') ? $img : asset('storage/' . $img) }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-slate-300 text-2xl">inventory_2</span>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Pedido</div>
                                <div class="font-bold text-slate-900 dark:text-white text-base">{{ $order->order_number }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $order->created_at->format('d M, Y') }}</div>
                            </div>
                        </div>
                        <div class="hidden sm:block">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status</div>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wide {{ $st['class'] }}">
                                {{ $st['label'] }}
                            </span>
                        </div>
                        <div class="hidden sm:block">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Total</div>
                            <div class="font-black text-slate-900 dark:text-white text-base">R$
                                {{ number_format($order->total_amount, 2, ',', '.') }}
                            </div>
                        </div>
                        <a href="{{ route('customer.orders.show', $order) }}"
                            class="px-5 py-2.5 bg-slate-900 text-white dark:bg-white dark:text-slate-900 text-xs font-black rounded-xl hover:scale-105 transition-all">
                            Ver
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div
            class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-16 text-center shadow-sm">
            <span class="material-symbols-outlined text-6xl text-slate-200 mb-4 block">shopping_bag</span>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Nenhum pedido ainda</h3>
            <p class="text-slate-500 text-sm mb-6">Que tal explorar nossa coleção de móveis?</p>
            <a href="/"
                class="inline-flex items-center gap-2 px-8 py-3 bg-primary text-white font-bold rounded-2xl hover:scale-105 transition-all shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined">explore</span>
                Explorar Produtos
            </a>
        </div>
    @endif
</x-customer-layout>