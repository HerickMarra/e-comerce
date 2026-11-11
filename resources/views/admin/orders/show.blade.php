<x-admin-layout>
    @section('page_title', 'Detalhes do Pedido #' . $order->order_number)

    <div class="space-y-6" x-data="{ showNoteModal: false, showRefundModal: false }">
        <!-- Back Link -->
        <a href="{{ route('admin.orders.index') }}"
            class="inline-flex items-center gap-2 text-sm font-bold text-slate-500 hover:text-primary transition-colors">
            <span class="material-symbols-outlined text-lg">arrow_back</span>
            Voltar para Pedidos
        </a>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Info & Items -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Status Card -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Pedido
                                    #{{ $order->order_number }}</h2>
                                @if($order->status === 'refunded')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-black uppercase bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300 border border-rose-200 dark:border-rose-800">
                                        Reembolsado
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-slate-500 mt-1">Realizado em
                                {{ $order->created_at->format('d/m/Y \à\s H:i') }}
                            </p>
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full sm:w-auto">
                            <form action="{{ route('admin.orders.update', $order) }}" method="POST"
                                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 sm:gap-3 w-full sm:w-auto">
                                @csrf
                                @method('PUT')
                                <select name="status"
                                    class="w-full sm:w-auto px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm font-bold">
                                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pendente
                                    </option>
                                    <option value="paid" {{ $order->status == 'paid' ? 'selected' : '' }}>Pago</option>
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>
                                        Processando</option>
                                    <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Em trânsito
                                    </option>
                                    <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Entregue
                                    </option>
                                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelado
                                    </option>
                                    <option value="refunded" {{ $order->status == 'refunded' ? 'selected' : '' }}>Reembolsado
                                    </option>
                                </select>
                                <button type="submit"
                                    class="w-full sm:w-auto justify-center bg-primary text-white font-bold px-4 py-2 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center gap-2 cursor-pointer">
                                    <span class="material-symbols-outlined text-sm">save</span>
                                    Atualizar
                                </button>
                            </form>

                            @if($order->payment_id && $order->status !== 'refunded')
                                <button type="button" @click="showRefundModal = true"
                                    class="w-full sm:w-auto justify-center bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800 font-bold px-4 py-2 rounded-xl transition-all hover:bg-rose-100 dark:hover:bg-rose-900/50 flex items-center gap-2 cursor-pointer"
                                    title="Solicitar estorno/reembolso no gateway">
                                    <span class="material-symbols-outlined text-sm">currency_exchange</span>
                                    Reembolsar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="font-bold text-lg">Produtos do Pedido</h3>
                    </div>
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left min-w-[500px]">
                            <thead>
                                <tr
                                    class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 text-xs uppercase tracking-wider">
                                    <th class="px-6 py-4 font-bold">Produto</th>
                                    <th class="px-6 py-4 font-bold text-center">Quantidade</th>
                                    <th class="px-6 py-4 font-bold text-right">Preço Un.</th>
                                    <th class="px-6 py-4 font-bold text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                @php
                                                    $imagePath = null;
                                                    if ($item->product) {
                                                        if ($item->product->images->count() > 0) {
                                                            $imagePath = $item->product->images->first()->path;
                                                        } elseif ($item->product->image) {
                                                            $imagePath = $item->product->image;
                                                        }
                                                    }
                                                @endphp

                                                @if($imagePath)
                                                    <img src="{{ str_starts_with($imagePath, 'http') ? $imagePath : asset('storage/' . $imagePath) }}"
                                                        class="w-12 h-12 rounded-xl object-cover" alt="">
                                                @else
                                                    <div
                                                        class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-xl flex items-center justify-center">
                                                        <span class="material-symbols-outlined text-slate-400">image</span>
                                                    </div>
                                                @endif
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm font-bold text-slate-900 dark:text-white">{{ $item->product->name ?? 'Produto Removido' }}</span>
                                                    @if($item->size)
                                                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-[10px] font-black text-slate-700 dark:text-slate-200 w-fit">
                                                            Tamanho: {{ $item->size }}
                                                        </span>
                                                    @endif
                                                    @if($item->color_name)
                                                        <div class="flex items-center gap-1.5 mt-0.5">
                                                            @if($item->color)
                                                                <span class="size-2.5 rounded-full border border-slate-200" style="background-color: {{ $item->color }}"></span>
                                                            @endif
                                                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                                                                Cor: {{ $item->color_name }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    @if($item->options)
                                                        <span class="text-xs text-slate-500">
                                                            @foreach($item->options as $key => $val)
                                                                {{ ucfirst($key) }}: {{ $val }}@if(!$loop->last), @endif
                                                            @endforeach
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="text-sm font-medium">{{ $item->quantity }}x</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-sm">R$ {{ number_format($item->price, 2, ',', '.') }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="text-sm font-bold">R$
                                                {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-6 bg-slate-50/50 dark:bg-slate-800/30">
                        <div class="max-w-xs ml-auto space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500 font-medium">Subtotal</span>
                                <span class="font-bold">R$ {{ number_format($order->subtotal, 2, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-slate-500 font-medium">Frete</span>
                                <span class="font-bold">R$
                                    {{ number_format($order->shipping_amount, 2, ',', '.') }}</span>
                            </div>
                            <div class="pt-3 border-t border-slate-200 dark:border-slate-700 flex justify-between">
                                <span class="text-lg font-bold text-slate-900 dark:text-white">Total</span>
                                <span class="text-lg font-bold text-primary">R$
                                    {{ number_format($order->total_amount, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Histórico do Pedido & Auditoria (Linha do Tempo) -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                        <div>
                            <div class="flex items-center gap-2.5">
                                <div class="size-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                                    <span class="material-symbols-outlined text-xl">history</span>
                                </div>
                                <h3 class="font-extrabold text-xl text-slate-900 dark:text-white">Histórico do Pedido & Auditoria</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300">
                                    {{ $order->histories->count() }} {{ Str::plural('evento', $order->histories->count()) }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Rastreabilidade auditável de eventos: checkout, cobrança gateway, emissão de frete, estornos e intervenções manuais.
                            </p>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            <button type="button" @click="showNoteModal = true"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all cursor-pointer">
                                <span class="material-symbols-outlined text-base">edit_note</span>
                                <span>Nova Nota Interna</span>
                            </button>

                            @if($order->payment_id && $order->status !== 'refunded')
                                <button type="button" @click="showRefundModal = true"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800/50 hover:bg-rose-100 dark:hover:bg-rose-900/50 transition-all cursor-pointer">
                                    <span class="material-symbols-outlined text-base">currency_exchange</span>
                                    <span>Solicitar Reembolso</span>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Timeline Content -->
                    <div class="pt-6">
                        @if($order->histories->isEmpty())
                            <div class="text-center py-10 text-slate-400">
                                <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">timeline</span>
                                <p class="text-sm">Nenhum evento registrado no histórico até o momento.</p>
                            </div>
                        @else
                            <div class="relative pl-6 sm:pl-8 space-y-6 before:content-[''] before:absolute before:left-3 sm:before:left-4 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800">
                                @foreach($order->histories as $history)
                                    <div class="relative group">
                                        <!-- Icon Bullet -->
                                        <div class="absolute -left-6 sm:-left-8 top-1 size-7 sm:size-8 rounded-full border-2 border-white dark:border-slate-900 flex items-center justify-center shadow-sm {{ $history->color_classes['bg'] }} {{ $history->color_classes['text'] }}">
                                            <span class="material-symbols-outlined text-sm sm:text-base">{{ $history->icon }}</span>
                                        </div>

                                        <!-- Event Card -->
                                        <div class="bg-slate-50/70 dark:bg-slate-800/40 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-4 sm:p-5 transition-all hover:border-slate-300 dark:hover:border-slate-700">
                                            <!-- Top Line: Title, Actor Badge, Timestamp -->
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <h4 class="font-bold text-sm sm:text-base text-slate-900 dark:text-white">
                                                        {{ $history->title }}
                                                    </h4>

                                                    <!-- Actor Badge: Sistema vs Usuário -->
                                                    @if($history->isSystem())
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-200/80 dark:bg-slate-700 text-slate-700 dark:text-slate-300">
                                                            <span class="material-symbols-outlined text-[13px] text-indigo-500">smart_toy</span>
                                                            <span>Sistema ({{ $history->actor_name }})</span>
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100/90 dark:bg-amber-950/60 text-amber-900 dark:text-amber-300 border border-amber-200 dark:border-amber-800/50">
                                                            <span class="material-symbols-outlined text-[13px] text-amber-600 dark:text-amber-400">person</span>
                                                            <span>Usuário: <strong>{{ $history->actor_name }}</strong></span>
                                                            @if($history->user)
                                                                <span class="text-[10px] opacity-75 font-normal">({{ $history->user->email }})</span>
                                                            @endif
                                                        </span>
                                                    @endif
                                                </div>

                                                <div class="text-xs text-slate-400 dark:text-slate-500 whitespace-nowrap flex items-center gap-1" title="{{ $history->created_at->format('d/m/Y H:i:s') }}">
                                                    <span class="material-symbols-outlined text-xs">schedule</span>
                                                    <span>{{ $history->created_at->format('d/m/Y H:i:s') }}</span>
                                                    <span class="text-[10px] text-slate-400">({{ $history->created_at->diffForHumans() }})</span>
                                                </div>
                                            </div>

                                            <!-- Description -->
                                            @if($history->description)
                                                <p class="text-xs sm:text-sm text-slate-600 dark:text-slate-300 leading-relaxed">
                                                    {{ $history->description }}
                                                </p>
                                            @endif

                                            <!-- Metadata details -->
                                            @if(!empty($history->metadata) && is_array($history->metadata))
                                                <div class="mt-3 flex items-center gap-2 flex-wrap text-[11px]">
                                                    @foreach($history->metadata as $key => $val)
                                                        @if(is_scalar($val) && !empty($val))
                                                            <span class="px-2 py-0.5 rounded-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-500">
                                                                <strong class="text-slate-700 dark:text-slate-300">{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $val }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Customer & Shipping sidebar -->
            <div class="space-y-6">
                <!-- Customer Info -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    <h3 class="font-bold text-lg mb-4">Informações do Cliente</h3>
                    <div class="flex items-center gap-4 mb-4">
                        <div
                            class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary font-bold text-lg">
                            {{ strtoupper(substr($order->user?->name ?? ($order->address_info['recipient_name'] ?? 'CL'), 0, 2)) }}
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="text-sm font-bold text-slate-900 dark:text-white">{{ $order->user?->name ?? ($order->address_info['recipient_name'] ?? 'Cliente') }}</span>
                            <span class="text-xs text-slate-500">{{ $order->user?->email ?? ($order->address_info['email'] ?? 'Não informado') }}</span>
                        </div>
                    </div>
                    <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        @php
                            $customerPhone = $order->user?->phone ?? ($order->address_info['phone'] ?? null);
                            $customerCpf = $order->user?->cpf ?? ($order->address_info['cpf'] ?? null);
                        @endphp
                        @if($customerPhone)
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-slate-400 text-lg">phone</span>
                                <div class="flex flex-col">
                                    <span
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Telefone</span>
                                    <span class="text-sm">{{ $customerPhone }}</span>
                                </div>
                            </div>
                        @endif
                        @if($customerCpf)
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-slate-400 text-lg">tag</span>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">CPF</span>
                                    <span class="text-sm">{{ $customerCpf }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Shipping Address -->
                @php
                    $isPickup = str_contains(strtolower($order->shipping_modalidade ?? ''), 'retirad') || 
                                str_contains(strtolower($order->notes ?? ''), 'retirad') ||
                                str_contains(strtolower($order->shipping_service_name ?? ''), 'retirad');
                @endphp
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-lg">{{ $isPickup ? 'Retirada na Loja' : 'Endereço de Entrega' }}</h3>
                        @if($isPickup)
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300">
                                Presencial • Grátis
                            </span>
                        @endif
                    </div>
                    @if($isPickup)
                        <div class="p-4 rounded-2xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-100 dark:border-emerald-800/30 space-y-2">
                            <div class="flex items-center gap-2 text-emerald-800 dark:text-emerald-300 font-bold text-xs">
                                <span class="material-symbols-outlined text-base">storefront</span>
                                <span>{{ App\Models\Setting::get('store_pickup_name', 'DENNER Tênis - Loja Física') }}</span>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300">
                                {{ App\Models\Setting::get('store_street') }}, {{ App\Models\Setting::get('store_number') }} - {{ App\Models\Setting::get('store_neighborhood') }}<br>
                                {{ App\Models\Setting::get('store_city') }} - {{ App\Models\Setting::get('store_state') }} • CEP: {{ App\Models\Setting::get('store_zip') }}
                            </p>
                            <p class="text-[10px] text-slate-400 mt-2">
                                <strong>Horário de Retirada:</strong> {{ App\Models\Setting::get('store_pickup_hours', 'Segunda a Sexta: 09h às 18h | Sábado: 09h às 13h') }}
                            </p>
                        </div>
                    @elseif($order->address_info)
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-slate-400 text-lg">location_on</span>
                                <div class="flex flex-col">
                                    <span
                                        class="text-sm font-bold text-slate-900 dark:text-white">{{ $order->address_info['street'] ?? '' }},
                                        {{ $order->address_info['number'] ?? '' }}</span>
                                    <span
                                        class="text-xs text-slate-500">{{ $order->address_info['complement'] ?? '' }}</span>
                                    <span class="text-sm text-slate-700 dark:text-slate-300 mt-1">
                                        {{ $order->address_info['neighborhood'] ?? '' }}<br>
                                        {{ $order->address_info['city'] ?? '' }} -
                                        {{ $order->address_info['state'] ?? '' }}<br>
                                        CEP: {{ $order->address_info['zip'] ?? $order->address_info['zip_code'] ?? '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-slate-400 italic">Informações de endereço não encontradas.</p>
                    @endif
                </div>

                <!-- Shipping Status & Recruitment -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-lg">Modalidade de Envio</h3>
                        @if($isPickup)
                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300">
                                <span class="material-symbols-outlined text-sm">storefront</span>
                                <span>Retirada no Balcão</span>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                <span class="material-symbols-outlined text-sm text-primary">local_shipping</span>
                                <span>Melhor Envio</span>
                            </div>
                        @endif
                    </div>
                    
                    @if($isPickup)
                        <div class="p-6 rounded-3xl bg-emerald-50/40 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="size-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center shadow-md shadow-emerald-500/20">
                                    <span class="material-symbols-outlined text-2xl">check_circle</span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-700 dark:text-emerald-300 block">Frete Grátis • R$ 0,00</span>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">Retirada Presencial na Loja</h4>
                                </div>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                                Este pedido não requer envio postal ou transportadora. O cliente escolheu retirar diretamente no balcão da loja física.
                            </p>
                            <div class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-emerald-100 dark:border-emerald-800/30 text-[11px] text-slate-500">
                                <strong class="text-slate-800 dark:text-slate-200">Prazo de separação:</strong> {{ App\Models\Setting::get('store_pickup_timeframe', 'Pronto em até 2 horas úteis após confirmação do pagamento') }}
                            </div>
                        </div>
                    @elseif($order->shipping_id)
                        <div class="p-6 rounded-3xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                            <div class="flex items-center justify-between gap-3 mb-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                                        <span class="material-symbols-outlined text-emerald-600">local_shipping</span>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Status</span>
                                        <span class="text-sm font-bold text-emerald-700 dark:text-emerald-400">Contratado</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Código / ID</span>
                                    <span class="text-sm md:text-base font-black text-slate-600 dark:text-slate-300 tracking-tight leading-none break-all">#{{ $order->shipping_id }}</span>
                                </div>
                            </div>

                            <div class="space-y-2 mb-6">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500">Serviço</span>
                                    <span class="font-bold text-slate-900 dark:text-white">{{ $order->shipping_service_name }}</span>
                                </div>
                                @if($order->shipping_tracking_code)
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-slate-500">Rastreio</span>
                                        <span class="font-bold text-primary selection:bg-primary/10">{{ $order->shipping_tracking_code }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2">
                                @if($order->shipping_label_url)
                                    <a href="{{ $order->public_shipping_label_url }}" target="_blank"
                                        class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-emerald-600 text-white rounded-2xl text-sm font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/20 active:scale-95 cursor-pointer">
                                        <span class="material-symbols-outlined text-xl">download</span>
                                        <span>Baixar Etiqueta Oficial (PDF)</span>
                                    </a>

                                    <a href="{{ $order->public_shipping_label_url }}" target="_blank"
                                        title="Abrir no Painel do Melhor Envio sem login"
                                        class="inline-flex items-center justify-center gap-1.5 px-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-2xl hover:border-emerald-500 hover:text-emerald-600 transition-all text-xs">
                                        <span class="material-symbols-outlined text-base text-emerald-600">open_in_new</span>
                                        <span>Abrir no Navegador</span>
                                    </a>

                                    <button type="button" @click="generateLabel()"
                                        title="Gerar via PDF local da loja"
                                        class="inline-flex items-center justify-center gap-1.5 px-3 py-3.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-medium rounded-2xl hover:bg-slate-200 dark:hover:bg-slate-700 transition-all text-xs cursor-pointer">
                                        <span class="material-symbols-outlined text-base">print</span>
                                        <span>PDF Alternativo</span>
                                    </button>
                                @else
                                    <button type="button" @click="generateLabel()"
                                        class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-emerald-600 text-white rounded-2xl text-sm font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/20 active:scale-95 cursor-pointer">
                                        <span class="material-symbols-outlined text-xl">download</span>
                                        <span>Baixar PDF da Etiqueta</span>
                                    </button>

                                    <form action="{{ route('admin.orders.recruit-shipping', $order) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="inline-flex items-center justify-center gap-1.5 px-4 py-3.5 bg-white dark:bg-slate-800 border-2 border-primary/30 text-primary font-bold rounded-2xl hover:bg-primary/5 transition-all text-xs cursor-pointer">
                                            <span class="material-symbols-outlined text-base">sync</span>
                                            <span>Sincronizar Etiqueta</span>
                                        </button>
                                    </form>
                                @endif

                                @if($order->shipping_tracking_url)
                                    <a href="{{ $order->shipping_tracking_url }}" target="_blank"
                                        title="Rastrear Envio"
                                        class="inline-flex items-center justify-center w-12 h-12 bg-white dark:bg-slate-800 border-2 border-slate-100 dark:border-slate-800 text-slate-400 hover:text-primary hover:border-primary/20 rounded-2xl transition-all">
                                        <span class="material-symbols-outlined text-lg">monitoring</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @else
                        @if(!empty($order->shipping_api_response['error']))
                            <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-950/30 border-2 border-red-200 dark:border-red-800/40 text-red-900 dark:text-red-200 mb-3 space-y-2">
                                <div class="flex items-start gap-2.5">
                                    <span class="material-symbols-outlined text-red-600 dark:text-red-400 text-xl shrink-0 mt-0.5">error</span>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="text-xs font-bold uppercase tracking-wider text-red-700 dark:text-red-300">Falha na API ({{ strtoupper(($order->shipping_api_response['provider'] ?? '') === 'melhorenvio' ? 'Melhor Envio' : ($order->shipping_api_response['provider'] ?? 'Transportadora')) }})</p>
                                            @if(!empty($order->shipping_api_response['status']))
                                                <span class="px-2 py-0.5 text-[10px] font-mono font-bold rounded bg-red-200/60 dark:bg-red-900/60 text-red-800 dark:text-red-200">HTTP {{ $order->shipping_api_response['status'] }}</span>
                                            @endif
                                        </div>
                                        <p class="text-xs font-semibold mt-1 text-red-800 dark:text-red-200 leading-snug">{{ $order->shipping_api_response['error'] }}</p>
                                        @if(!empty($order->shipping_api_response['errors']) && is_array($order->shipping_api_response['errors']))
                                            <ul class="mt-2 text-[11px] list-disc list-inside space-y-0.5 text-red-700 dark:text-red-300 font-mono bg-red-100/50 dark:bg-red-900/20 p-2.5 rounded-xl">
                                                @foreach($order->shipping_api_response['errors'] as $fld => $errList)
                                                    <li><strong>{{ $fld }}:</strong> {{ is_array($errList) ? implode(', ', $errList) : $errList }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        <p class="text-[10px] text-red-600 dark:text-red-400 mt-2">Corrija o dado indicado e clique em <strong>Gerar Envio no Melhor Envio</strong> abaixo para tentar novamente.</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/20">
                            <p class="text-xs text-amber-800 dark:text-amber-500 font-medium mb-3">
                                Frete ainda não foi gerado/contratado na transportadora para este pedido.
                            </p>
                            <div class="flex flex-col gap-2">
                                <form action="{{ route('admin.orders.recruit-shipping', $order) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                        class="w-full py-3 bg-white dark:bg-slate-800 border-2 border-amber-200 dark:border-amber-800/50 hover:bg-amber-50 dark:hover:bg-amber-900/20 text-amber-800 dark:text-amber-500 font-bold rounded-2xl transition-all text-sm flex items-center justify-center gap-2">
                                        <span class="material-symbols-outlined text-lg">local_shipping</span>
                                        Gerar Envio no Melhor Envio
                                    </button>
                                </form>
                                <button @click="generateLabel()"
                                    class="w-full py-2.5 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium rounded-xl transition-all text-xs flex items-center justify-center gap-1.5">
                                    <span class="material-symbols-outlined text-base">print</span>
                                    Imprimir Declaração / Etiqueta da Loja
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Payment Info -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    <h3 class="font-bold text-lg mb-4">Pagamento</h3>
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50">
                        <span class="material-symbols-outlined text-primary">account_balance_wallet</span>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Método</span>
                            <span class="text-sm font-bold">{{ strtoupper($order->payment_method ?? 'N/A') }}</span>
                        </div>
                    </div>
                    @if($order->payment_id)
                        <p class="text-[10px] text-slate-400 mt-4 break-all uppercase tracking-widest">ID:
                            {{ $order->payment_id }}
                        </p>
                    @endif
                </div>

                @if($order->notes)
                    <div
                        class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/20 rounded-3xl p-6">
                        <h3
                            class="font-bold text-amber-800 dark:text-amber-500 text-sm uppercase tracking-widest mb-3 flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">sticky_note_2</span>
                            Observações
                        </h3>
                        <p class="text-sm text-amber-900 dark:text-amber-400 leading-relaxed">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Modal: Adicionar Nota Interna -->
        <div x-show="showNoteModal" 
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-lg w-full p-6 sm:p-8"
                @click.away="showNoteModal = false">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-xl">edit_note</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-base text-slate-900 dark:text-white">Adicionar Anotação Interna</h4>
                            <p class="text-xs text-slate-500">Registrado no histórico auditável com seu usuário.</p>
                        </div>
                    </div>
                    <button type="button" @click="showNoteModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route('admin.orders.add-note', $order) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Mensagem / Detalhes da Ocorrência
                        </label>
                        <textarea name="note" rows="4" required
                            placeholder="Ex: Cliente solicitou entrega urgente por WhatsApp ou alteração de instrução..."
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showNoteModal = false"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-primary text-white rounded-xl text-xs font-bold hover:opacity-90 shadow-lg shadow-primary/20 transition-all flex items-center gap-1.5 cursor-pointer">
                            <span class="material-symbols-outlined text-sm">save</span>
                            Salvar no Histórico
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Solicitar Reembolso -->
        <div x-show="showRefundModal" 
            style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-2xl max-w-lg w-full p-6 sm:p-8"
                @click.away="showRefundModal = false">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <div class="size-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 flex items-center justify-center">
                            <span class="material-symbols-outlined text-xl">currency_exchange</span>
                        </div>
                        <div>
                            <h4 class="font-bold text-base text-slate-900 dark:text-white">Solicitar Reembolso / Estorno</h4>
                            <p class="text-xs text-slate-500">Integração oficial Asaas Gateway</p>
                        </div>
                    </div>
                    <button type="button" @click="showRefundModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 cursor-pointer">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="mt-4 p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-800/40 text-rose-800 dark:text-rose-200 text-xs space-y-1">
                    <p class="font-bold flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-sm">warning</span>
                        Atenção: Ação Irreversível
                    </p>
                    <p>
                        Esta ação solicitará o estorno de <strong>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</strong> no Asaas. O status do pedido passará para <strong>Reembolsado</strong> e os itens retornarão ao estoque.
                    </p>
                </div>

                <form action="{{ route('admin.orders.refund', $order) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                            Motivo do Reembolso (obrigatório)
                        </label>
                        <textarea name="reason" rows="3" required
                            placeholder="Ex: Desistência do comprador antes do envio / Produto com defeito..."
                            class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 outline-none transition-all text-sm"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button type="button" @click="showRefundModal = false"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-rose-600 text-white rounded-xl text-xs font-bold hover:bg-rose-700 shadow-lg shadow-rose-600/20 transition-all flex items-center gap-1.5 cursor-pointer">
                            <span class="material-symbols-outlined text-sm">check_circle</span>
                            Confirmar Reembolso
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Printable Label Template (Hidden from screen, used to compile direct PDF) --}}
    <div id="pdf-template" style="display: none; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #111; padding: 20px; background: #fff; max-width: 800px; margin: 0 auto;">
        <div style="border: 2px solid #000; padding: 24px;">
            {{-- Header --}}
            <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #000; padding-bottom: 16px; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="background: #111; color: #fff; font-weight: 900; font-size: 14px; padding: 6px 12px; border-radius: 4px; letter-spacing: 1px;">
                        MELHOR ENVIO
                    </div>
                    <div>
                        <h1 style="margin: 0; font-size: 20px; font-weight: 800; text-transform: uppercase;">Etiqueta de Envio & Declaração</h1>
                        <p style="margin: 2px 0 0; font-size: 12px; color: #555;">Pedido: <strong>#{{ $order->order_number }}</strong> • Data: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                <div style="text-align: right;">
                    <span style="display: inline-block; background: #e0f2fe; color: #0369a1; font-weight: 800; font-size: 13px; padding: 6px 12px; border-radius: 6px; border: 1px solid #bae6fd;">
                        {{ $order->shipping_service_name ?? 'SEDEX (Melhor Envio)' }}
                    </span>
                    <p style="margin: 4px 0 0; font-size: 11px; color: #666;">ID Envio: <strong>#{{ $order->shipping_id }}</strong></p>
                </div>
            </div>

            {{-- Tracking Barcode Section --}}
            <div style="background: #f8fafc; border: 1.5px dashed #cbd5e1; border-radius: 8px; padding: 16px; margin-bottom: 24px; text-align: center;">
                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #64748b; letter-spacing: 1px; margin-bottom: 6px;">
                    Código de Rastreamento / Postagem
                </div>
                <div style="display: flex; justify-content: center; margin: 4px 0;">
                    <svg id="barcode-tracking"></svg>
                </div>
                <div style="font-size: 11px; color: #475569; margin-top: 4px;">
                    Envio registrado via <strong>Melhor Envio</strong> para entrega no destinatário abaixo
                </div>
            </div>

            {{-- Sender & Recipient Box --}}
            <div style="display: flex; gap: 20px; margin-bottom: 24px;">
                {{-- Recipient (Destinatário) --}}
                <div style="flex: 1.2; border: 2px solid #000; border-radius: 8px; padding: 16px; background: #fff;">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1.5px solid #000; padding-bottom: 8px; margin-bottom: 12px;">
                        <span style="font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px;">DESTINATÁRIO</span>
                        <span style="font-size: 10px; background: #111; color: #fff; padding: 2px 6px; border-radius: 3px; font-weight: 700;">ENTREGA</span>
                    </div>
                    <p style="margin: 0 0 6px; font-size: 15px; font-weight: 800; color: #000;">{{ $order->user?->name ?? ($order->address_info['recipient_name'] ?? 'Destinatário') }}</p>
                    <p style="margin: 0 0 4px; font-size: 13px; color: #222;">
                        {{ $order->address_info['street'] ?? '' }}, {{ $order->address_info['number'] ?? 'S/N' }}
                        @if(!empty($order->address_info['complement']))
                            - {{ $order->address_info['complement'] }}
                        @endif
                    </p>
                    <p style="margin: 0 0 4px; font-size: 13px; color: #222;">
                        Bairro: <strong>{{ $order->address_info['neighborhood'] ?? '' }}</strong>
                    </p>
                    <p style="margin: 0 0 10px; font-size: 13px; color: #222;">
                        {{ $order->address_info['city'] ?? '' }} / {{ $order->address_info['state'] ?? '' }}
                    </p>
                    
                    <div style="background: #f1f5f9; padding: 10px; border-radius: 6px; margin: 8px 0 10px;">
                        <span style="font-size: 11px; color: #64748b; display: block; text-transform: uppercase; font-weight: 700;">CEP</span>
                        <span style="font-size: 18px; font-weight: 900; letter-spacing: 1px; color: #0f172a;">
                            {{ $order->address_info['zip'] ?? $order->address_info['zip_code'] ?? '' }}
                        </span>
                    </div>

                    <div style="display: flex; justify-content: center; margin: 6px 0;">
                        <svg id="barcode-cep"></svg>
                    </div>

                    <div style="border-top: 1px solid #e2e8f0; padding-top: 8px; margin-top: 8px; font-size: 11px; color: #475569;">
                        <div>CPF: <strong>{{ $order->user?->cpf ?? ($order->address_info['cpf'] ?? '---') }}</strong></div>
                        <div>Telefone: <strong>{{ $order->user?->phone ?? ($order->address_info['phone'] ?? '---') }}</strong></div>
                    </div>
                </div>

                {{-- Sender (Remetente) --}}
                <div style="flex: 0.8; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 16px; background: #fafafa;">
                    <div style="border-bottom: 1.5px solid #cbd5e1; padding-bottom: 8px; margin-bottom: 12px;">
                        <span style="font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: #475569;">REMETENTE</span>
                    </div>
                    <p style="margin: 0 0 4px; font-size: 13px; font-weight: 800; color: #0f172a;">
                        {{ App\Models\Setting::get('store_name', config('app.name', 'Denner Tênis')) }}
                    </p>
                    <p style="margin: 0 0 3px; font-size: 11px; color: #475569;">
                        {{ App\Models\Setting::get('store_street', 'Praça Doutor Pedro Ludovico Teixeira') }}, {{ App\Models\Setting::get('store_number', '24') }}
                    </p>
                    <p style="margin: 0 0 3px; font-size: 11px; color: #475569;">
                        Bairro: {{ App\Models\Setting::get('store_neighborhood', 'Setor Central') }}
                    </p>
                    <p style="margin: 0 0 3px; font-size: 11px; color: #475569;">
                        {{ App\Models\Setting::get('store_city', 'Goiânia') }} / {{ App\Models\Setting::get('store_state', 'GO') }}
                    </p>
                    <p style="margin: 0 0 8px; font-size: 12px; font-weight: 700; color: #0f172a;">
                        CEP: {{ App\Models\Setting::get('store_zip', '74003-010') }}
                    </p>
                    <div style="border-top: 1px solid #e2e8f0; padding-top: 6px; margin-top: 6px; font-size: 10px; color: #64748b;">
                        <div>Documento: {{ App\Models\Setting::get('store_document', '083.517.231-77') }}</div>
                        <div>Telefone: {{ App\Models\Setting::get('store_phone', '(61) 99688-5273') }}</div>
                    </div>

                    <div style="margin-top: 20px; background: #fff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px;">
                        <span style="font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: 700; display: block;">Peso Total Estimado</span>
                        <span style="font-size: 14px; font-weight: 800; color: #0f172a;">{{ max(0.5, array_sum(array_column($order->items->toArray(), 'quantity')) * 0.5) }} kg</span>
                    </div>
                </div>
            </div>

            {{-- Contents Declaration Table --}}
            <div style="border-top: 2px solid #000; padding-top: 16px; margin-top: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <h3 style="margin: 0; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                        Declaração de Conteúdo dos Itens
                    </h3>
                    <span style="font-size: 11px; color: #64748b;">Total de Volumes: 1</span>
                </div>

                <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                    <thead>
                        <tr style="background: #f1f5f9; border-top: 1px solid #cbd5e1; border-bottom: 1.5px solid #000;">
                            <th style="padding: 6px 8px; text-align: left; font-weight: 700;">#</th>
                            <th style="padding: 6px 8px; text-align: left; font-weight: 700;">Item / Descrição</th>
                            <th style="padding: 6px 8px; text-align: center; font-weight: 700; width: 70px;">Qtd</th>
                            <th style="padding: 6px 8px; text-align: right; font-weight: 700; width: 90px;">Valor Unit.</th>
                            <th style="padding: 6px 8px; text-align: right; font-weight: 700; width: 100px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $idx => $item)
                        <tr style="border-bottom: 1px solid #e2e8f0;">
                            <td style="padding: 8px; color: #64748b;">{{ $idx + 1 }}</td>
                            <td style="padding: 8px; font-weight: 600;">
                                {{ $item->product->name ?? 'Produto' }}
                                @if($item->size)
                                    <span style="color: #64748b;">[Tam: {{ $item->size }}]</span>
                                @endif
                                @if($item->color_name)
                                    <span style="color: #64748b;">- {{ $item->color_name }}</span>
                                @endif
                            </td>
                            <td style="padding: 8px; text-align: center; font-weight: 700;">{{ $item->quantity }}</td>
                            <td style="padding: 8px; text-align: right;">R$ {{ number_format($item->price, 2, ',', '.') }}</td>
                            <td style="padding: 8px; text-align: right; font-weight: 700;">R$ {{ number_format($item->price * $item->quantity, 2, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr style="background: #f8fafc; font-weight: 800; border-top: 1.5px solid #000;">
                            <td colspan="4" style="padding: 8px; text-align: right; font-size: 12px;">Total Declarado:</td>
                            <td style="padding: 8px; text-align: right; font-size: 12px; color: #0f172a;">R$ {{ number_format($order->total_amount, 2, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Declaration Footer --}}
            <div style="margin-top: 24px; padding-top: 12px; border-top: 1px dashed #cbd5e1; font-size: 10px; color: #64748b; line-height: 1.5;">
                <p style="margin: 0 0 16px;">
                    Declaro que não me enquadro no conceito de contribuinte previsto no art. 4º da Lei Complementar nº 87/1996, e que este envio não constitui ato de mercancia comercial ilegal, responsabilizando-me perante a legislação vigente pelo conteúdo transportado.
                </p>
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 24px;">
                    <div>
                        ___________________________________________________<br>
                        <strong>Assinatura do Remetente / Declarante</strong>
                    </div>
                    <div style="text-align: right;">
                        Data: {{ date('d/m/Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function generateLabel() {
            const btn = (window.event && window.event.currentTarget) ? window.event.currentTarget : document.querySelector('[onclick*="generateLabel"], [\\@click*="generateLabel"]');
            let originalHtml = '';
            if (btn) {
                originalHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">progress_activity</span> <span>Gerando PDF...</span>';
            }

            // Generate Barcodes
            try {
                if (typeof JsBarcode === 'function') {
                    const cepRaw = "{{ preg_replace('/\D/', '', $order->address_info['zip'] ?? $order->address_info['zip_code'] ?? '70000000') }}";
                    if (cepRaw) {
                        JsBarcode("#barcode-cep", cepRaw, {
                            format: "CODE128",
                            width: 1.8,
                            height: 35,
                            displayValue: true,
                            fontSize: 12,
                            margin: 0
                        });
                    }

                    const trackingRaw = "{{ $order->shipping_tracking_code ?: ($order->shipping_id ?: $order->order_number) }}";
                    if (trackingRaw) {
                        JsBarcode("#barcode-tracking", trackingRaw, {
                            format: "CODE128",
                            width: 2.0,
                            height: 48,
                            displayValue: true,
                            fontSize: 13,
                            margin: 0
                        });
                    }
                }
            } catch (err) {
                console.warn('Barcode generation notice:', err);
            }

            const element = document.getElementById('pdf-template');
            element.style.display = 'block';

            const opt = {
                margin: [6, 6, 6, 6],
                filename: 'etiqueta-pedido-{{ $order->order_number }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, logging: false },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                element.style.display = 'none';
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            }).catch((err) => {
                element.style.display = 'none';
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
                console.error('PDF error:', err);
            });
        }
    </script>
</x-admin-layout>
