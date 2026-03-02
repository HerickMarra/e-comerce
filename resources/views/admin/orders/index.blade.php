<x-admin-layout>
    @section('page_title', 'Pedidos')

    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-2xl font-bold">Gerenciar Pedidos</h2>

            <form action="{{ route('admin.orders.index') }}" method="GET"
                class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <div class="relative flex-1 sm:w-64">
                    <span
                        class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Buscar por nº, cliente ou email..."
                        class="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm">
                </div>

                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm">
                    <option value="">Todos os Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendentes</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Pagos</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Em Processamento
                    </option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Enviados</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregues</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelados</option>
                </select>
            </form>
        </div>

        <!-- Orders Table -->
        <div
            class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4 font-bold">Número</th>
                            <th class="px-6 py-4 font-bold">Data</th>
                            <th class="px-6 py-4 font-bold">Cliente</th>
                            <th class="px-6 py-4 font-bold">Pagamento</th>
                            <th class="px-6 py-4 font-bold text-center">Status</th>
                            <th class="px-6 py-4 font-bold text-right">Total</th>
                            <th class="px-6 py-4 font-bold text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($orders as $order)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors group">
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-bold text-slate-900 dark:text-white">#{{ $order->order_number }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm text-slate-600 dark:text-slate-400">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-medium text-slate-900 dark:text-white">{{ $order->user->name }}</span>
                                        <span class="text-xs text-slate-500">{{ $order->user->email }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="text-xs font-semibold uppercase text-slate-500">{{ $order->payment_method ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-amber-100 text-amber-600',
                                            'paid' => 'bg-emerald-100 text-emerald-600',
                                            'processing' => 'bg-blue-100 text-blue-600',
                                            'shipped' => 'bg-indigo-100 text-indigo-600',
                                            'delivered' => 'bg-slate-100 text-slate-600',
                                            'cancelled' => 'bg-rose-100 text-rose-600',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'Pendente',
                                            'paid' => 'Pago',
                                            'processing' => 'Processando',
                                            'shipped' => 'Em trânsito',
                                            'delivered' => 'Entregue',
                                            'cancelled' => 'Cancelado',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-lg {{ $statusColors[$order->status] ?? 'bg-slate-100' }} text-[10px] font-bold uppercase tracking-wider">
                                        {{ $statusLabels[$order->status] ?? $order->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-slate-900 dark:text-white">R$
                                        {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            class="p-2 text-slate-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-all"
                                            title="Ver Detalhes">
                                            <span class="material-symbols-outlined text-xl">visibility</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic">
                                    Nenhum pedido encontrado para os filtros selecionados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>