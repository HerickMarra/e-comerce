<x-admin-layout>
    @section('page_title', 'Visão Geral')

    <div class="space-y-8" x-data="{
        days: {{ json_encode($days) }},
        ordersMadeCounts: {{ json_encode($ordersMadeCounts) }},
        ordersPaidCounts: {{ json_encode($ordersPaidCounts) }},
        revenueData: {{ json_encode($revenueData) }},
        primaryColor: '{{ $appSettings['primary_color'] ?? '#10b981' }}'
    }" x-init="
        const ctx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: days,
                datasets: [
                    {
                        label: 'Pedidos Realizados',
                        data: ordersMadeCounts,
                        borderColor: '#6366f1',
                        backgroundColor: 'transparent',
                        fill: false,
                        tension: 0.4,
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointRadius: 0,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Pedidos Pagos',
                        data: ordersPaidCounts,
                        borderColor: primaryColor,
                        backgroundColor: primaryColor + '10',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        padding: 12,
                        backgroundColor: '#1e293b',
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 },
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { font: { size: 11 }, color: '#94a3b8' }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        border: { display: false },
                        ticks: { font: { size: 11 }, color: '#94a3b8' }
                    }
                }
            }
        });
    ">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div
                class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-primary/5 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-all duration-300">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                </div>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Vendas Totais</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">R$
                    {{ number_format($totalRevenue, 2, ',', '.') }}
                </h3>
            </div>

            <div
                class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-indigo/5 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-500 group-hover:bg-indigo-500 group-hover:text-white transition-all duration-300">
                        <span class="material-symbols-outlined">shopping_cart</span>
                    </div>
                </div>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Pedidos Totais</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $totalOrders }}</h3>
            </div>

            <div
                class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-amber/5 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-all duration-300">
                        <span class="material-symbols-outlined">group</span>
                    </div>
                </div>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Clientes Ativos</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $totalCustomers }}</h3>
            </div>

            <div
                class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-rose/5 transition-all group">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-12 h-12 bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-500 group-hover:bg-rose-500 group-hover:text-white transition-all duration-300">
                        <span class="material-symbols-outlined">trending_up</span>
                    </div>
                </div>
                <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Ticket Médio</p>
                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    R$ {{ $totalOrders > 0 ? number_format($totalRevenue / $totalOrders, 2, ',', '.') : '0,00' }}
                </h3>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Chart -->
            <div class="lg:col-span-2 space-y-8">
                <div
                    class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="font-bold text-lg">Desempenho de Vendas</h3>
                            <p class="text-xs text-slate-500">Pedidos e Faturamento nos últimos 30 dias</p>
                        </div>
                        <div class="flex gap-4">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
                                <span
                                    class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Realizados</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-primary"></span>
                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Pagos</span>
                            </div>
                        </div>
                    </div>
                    <div class="h-[300px] w-full">
                        <canvas id="ordersChart"></canvas>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <h3 class="font-bold text-lg">Pedidos Recentes</h3>
                        <a href="{{ route('admin.orders.index') }}"
                            class="text-sm font-bold text-primary hover:underline">Ver todos</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr
                                    class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-4 font-bold">Pedido</th>
                                    <th class="px-6 py-4 font-bold">Cliente</th>
                                    <th class="px-6 py-4 font-bold">Status</th>
                                    <th class="px-6 py-4 font-bold text-right">Valor</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <span
                                                class="text-sm font-bold text-slate-900 dark:text-white">#{{ $order->order_number }}</span>
                                            <p class="text-xs text-slate-500 mt-0.5">
                                                {{ $order->created_at->diffForHumans() }}
                                            </p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                                    {{ strtoupper(substr($order->user->name, 0, 2)) }}
                                                </div>
                                                <span class="text-sm font-medium">{{ $order->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
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
                                                class="px-2.5 py-1 rounded-lg {{ $statusColors[$order->status] ?? 'bg-slate-100' }} text-[10px] font-bold uppercase tracking-wider">
                                                {{ $statusLabels[$order->status] ?? $order->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-sm font-bold">R$
                                                {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">
                                            Nenhum pedido realizado ainda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="space-y-8">
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    <h3 class="font-bold text-lg mb-6">Ações Rápidas</h3>
                    <div class="space-y-4">
                        <a href="{{ route('admin.products.create') }}"
                            class="w-full flex items-center gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 hover:bg-primary/5 border border-transparent hover:border-primary/20 transition-all group text-left">
                            <div
                                class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center text-primary group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined">add_box</span>
                            </div>
                            <div>
                                <p class="font-bold text-sm">Adicionar Produto</p>
                                <p class="text-xs text-slate-500">Cadastre um novo item</p>
                            </div>
                        </a>
                        <a href="{{ route('admin.categories.create') }}"
                            class="w-full flex items-center gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 hover:bg-indigo-500/5 border border-transparent hover:border-indigo-500/20 transition-all group text-left">
                            <div
                                class="w-10 h-10 bg-indigo-500/10 rounded-xl flex items-center justify-center text-indigo-500 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined">category</span>
                            </div>
                            <div>
                                <p class="font-bold text-sm">Adicionar Categoria</p>
                                <p class="text-xs text-slate-500">Organize seus produtos</p>
                            </div>
                        </a>
                        <a href="{{ route('admin.settings.index') }}"
                            class="w-full flex items-center gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 hover:bg-amber-500/5 border border-transparent hover:border-amber-500/20 transition-all group text-left">
                            <div
                                class="w-10 h-10 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-500 group-hover:scale-110 transition-transform">
                                <span class="material-symbols-outlined">settings</span>
                            </div>
                            <div>
                                <p class="font-bold text-sm">Configurar Loja</p>
                                <p class="text-xs text-slate-500">Identidade e Gateway</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div
                    class="bg-indigo-600 rounded-3xl p-8 text-white relative overflow-hidden shadow-xl shadow-indigo-500/20">
                    <div class="absolute top-0 right-0 size-32 bg-white/10 rounded-full"
                        style="transform: translate(30%, -30%)"></div>
                    <div class="relative z-10">
                        <span class="material-symbols-outlined text-4xl mb-4">star</span>
                        <h4 class="text-xl font-bold leading-tight">Mantenha sua vitrine sempre atualizada</h4>
                        <p class="text-xs text-white/70 mt-4 leading-relaxed">Produtos novos e bem descritos aumentam a
                            conversão em até 40%.</p>
                        <a href="{{ route('admin.products.index') }}"
                            class="inline-flex mt-6 px-4 py-2 bg-white text-indigo-600 rounded-xl text-xs font-bold hover:bg-indigo-50 shadow-lg">Ir
                            para Produtos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>