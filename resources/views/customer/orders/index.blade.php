<x-customer-layout title="Meus Pedidos" active="orders">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white">Meus Pedidos</h1>
        <p class="text-slate-500 mt-1">Acompanhe o status e detalhes de suas compras.</p>
    </div>

    @php
        $statusMap = [
            'pending' => ['label' => 'Aguardando Pagamento', 'class' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400'],
            'paid' => ['label' => 'Pago', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'],
            'processing' => ['label' => 'Em processamento', 'class' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400'],
            'shipped' => ['label' => 'Enviado', 'class' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'],
            'delivered' => ['label' => 'Entregue', 'class' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400'],
            'cancelled' => ['label' => 'Cancelado', 'class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400'],
        ];
    @endphp

    {{-- Filters --}}
    <div
        class="mb-8 bg-white dark:bg-slate-900 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <form action="{{ route('customer.orders') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px] relative">
                <span
                    class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por nº do pedido..."
                    class="w-full pl-10 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm focus:ring-2 focus:ring-primary/20 transition-all">
            </div>

            <select name="status" onchange="this.form.submit()"
                class="bg-slate-50 dark:bg-slate-800 border-none rounded-xl text-sm px-4 py-2 pr-10 focus:ring-2 focus:ring-primary/20 transition-all">
                <option value="">Todos os status</option>
                @foreach($statusMap as $key => $val)
                    <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                        {{ $val['label'] }}
                    </option>
                @endforeach
            </select>

            @if(request('q') || request('status'))
                <a href="{{ route('customer.orders') }}"
                    class="text-xs font-bold text-slate-400 hover:text-red-500 uppercase tracking-wider transition-colors">
                    Limpar Filtros
                </a>
            @endif

            <button type="submit" class="hidden">Filtrar</button>
        </form>
    </div>

    @if($orders->count() > 0)
        <div class="space-y-4">
            @foreach($orders as $order)
                @php
                    $st = $statusMap[$order->status] ?? ['label' => ucfirst($order->status), 'class' => 'bg-slate-100 text-slate-600'];
                    $firstItem = $order->items->first();
                    $img = $firstItem?->product?->images?->where('is_main', true)->first()?->path
                        ?? $firstItem?->product?->images?->first()?->path;
                    $extraCount = $order->items->count() - 1;
                @endphp
                <div
                    class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow relative">
                    <div class="p-5 flex flex-wrap items-center justify-between gap-6">
                        {{-- Imagem + Número --}}
                        <div class="flex items-center gap-5">
                            <div
                                class="w-20 h-20 rounded-xl overflow-hidden bg-slate-50 border border-slate-100 relative shrink-0">
                                @if($img)
                                    <img alt="Produto" class="w-full h-full object-cover"
                                        src="{{ str_starts_with($img, 'http') ? $img : asset('storage/' . $img) }}">
                                    @if($extraCount > 0)
                                        <div class="absolute inset-0 bg-slate-900/50 flex items-center justify-center">
                                            <span class="text-white text-xs font-black">+{{ $extraCount }}</span>
                                        </div>
                                    @endif
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <span class="material-symbols-outlined text-slate-300 text-3xl">inventory_2</span>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Pedido</div>
                                <div class="font-bold text-slate-900 dark:text-white">{{ $order->order_number }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $order->created_at->format('d M, Y') }}</div>
                            </div>
                        </div>

                        {{-- Data --}}
                        <div class="hidden sm:block">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Itens</div>
                            <div class="font-semibold text-slate-700 dark:text-slate-300">
                                {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                            </div>
                        </div>

                        {{-- Total --}}
                        <div class="hidden sm:block">
                            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total</div>
                            <div class="font-bold text-primary">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</div>
                        </div>

                        {{-- Status --}}
                        <div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $st['class'] }}">
                                {{ $st['label'] }}
                            </span>
                        </div>

                        {{-- Ação --}}
                        <div>
                            <a href="{{ route('customer.orders.show', $order->id) }}"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-bold hover:bg-primary hover:text-white transition-all">
                                Ver Detalhes
                                <span class="material-symbols-outlined text-sm">arrow_forward</span>
                            </a>
                        </div>
                    </div>

                    {{-- Items summary bar --}}
                    @if($order->items->count() > 0)
                        <div class="px-5 pb-4 border-t border-slate-50 dark:border-slate-800 pt-3">
                            <p class="text-xs text-slate-400 font-medium truncate">
                                {{ $order->items->map(fn($i) => ($i->quantity > 1 ? $i->quantity . '× ' : '') . ($i->product?->name ?? 'Produto') . ($i->color_name ? ' (' . $i->color_name . ')' : ''))->join(', ') }}
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Paginação --}}
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @else
        <div
            class="bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-3xl p-20 text-center shadow-sm">
            <span class="material-symbols-outlined text-6xl text-slate-200 mb-4 block">shopping_bag</span>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">Nenhum pedido encontrado</h3>
            <p class="text-slate-500 text-sm mb-6">Você ainda não realizou nenhuma compra.</p>
            <a href="/"
                class="inline-flex items-center gap-2 px-8 py-3 bg-primary text-white font-bold rounded-2xl hover:scale-105 transition-all shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined">explore</span>
                Explorar Produtos
            </a>
        </div>
    @endif
</x-customer-layout>