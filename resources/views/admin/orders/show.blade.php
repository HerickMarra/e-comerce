<x-admin-layout>
    @section('page_title', 'Detalhes do Pedido #' . $order->order_number)

    <div class="space-y-6">
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
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Pedido
                                #{{ $order->order_number }}</h2>
                            <p class="text-sm text-slate-500 mt-1">Realizado em
                                {{ $order->created_at->format('d/m/Y \à\s H:i') }}
                            </p>
                        </div>

                        <form action="{{ route('admin.orders.update', $order) }}" method="POST"
                            class="flex items-center gap-3">
                            @csrf
                            @method('PUT')
                            <select name="status"
                                class="px-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all text-sm font-bold">
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
                            </select>
                            <button type="submit"
                                class="bg-primary text-white font-bold px-4 py-2 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center gap-2">
                                <span class="material-symbols-outlined text-sm">save</span>
                                Atualizar
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Items Table -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                        <h3 class="font-bold text-lg">Produtos do Pedido</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
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
                                                @if($item->product && $item->product->images->count() > 0)
                                                    <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}"
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
                            {{ strtoupper(substr($order->user->name, 0, 2)) }}
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="text-sm font-bold text-slate-900 dark:text-white">{{ $order->user->name }}</span>
                            <span class="text-xs text-slate-500">{{ $order->user->email }}</span>
                        </div>
                    </div>
                    <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        @if($order->user->phone)
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-slate-400 text-lg">phone</span>
                                <div class="flex flex-col">
                                    <span
                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Telefone</span>
                                    <span class="text-sm">{{ $order->user->phone }}</span>
                                </div>
                            </div>
                        @endif
                        @if($order->user->cpf)
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-slate-400 text-lg">tag</span>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">CPF</span>
                                    <span class="text-sm">{{ $order->user->cpf }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Shipping Address -->
                <div
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    <h3 class="font-bold text-lg mb-4">Endereço de Entrega</h3>
                    @if($order->address_info)
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
                        <h3 class="font-bold text-lg">Frete de Envio</h3>
                        <img src="{{ asset('img/logoEnviaMais.png') }}" class="h-6 object-contain" alt="EnviaMais">
                    </div>
                    
                    @if($order->shipping_id)
                        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-800/20">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="material-symbols-outlined text-emerald-600">local_shipping</span>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest">Status</span>
                                    <span class="text-sm font-bold text-emerald-700 dark:text-emerald-400">Contratado</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-600 dark:text-slate-400 mb-2">Serviço: <strong>{{ $order->shipping_service_name }}</strong></p>
                            
                            @if($order->shipping_tracking_code)
                                <p class="text-xs text-slate-600 dark:text-slate-400 mb-2">Código de Rastreio: <strong class="text-primary">{{ $order->shipping_tracking_code }}</strong></p>
                            @endif

                            <div class="flex flex-wrap gap-3 mt-4">
                                @if($order->shipping_id)
                                    <button @click="generateLabel()"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-xs font-bold hover:bg-primary-dark transition-all shadow-sm shadow-primary/20">
                                        <span class="material-symbols-outlined text-sm">print</span>
                                        Imprimir Etiqueta
                                    </button>
                                @endif

                                @if($order->shipping_tracking_url)
                                    <a href="{{ $order->shipping_tracking_url }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg text-xs font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
                                        <span class="material-symbols-outlined text-sm">monitoring</span>
                                        Rastrear Pedido
                                    </a>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/20">
                            <p class="text-xs text-amber-800 dark:text-amber-500 font-medium mb-3">
                                Frete ainda não foi contratado na EnviaMais para este pedido.
                            </p>
                            <form action="{{ route('admin.orders.recruit-shipping', $order) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                    class="w-full py-3 bg-white dark:bg-slate-800 border-2 border-amber-200 dark:border-amber-800/50 hover:bg-amber-50 dark:hover:bg-amber-900/20 text-amber-800 dark:text-amber-500 font-bold rounded-2xl transition-all text-sm flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-lg">smart_button</span>
                                    Contratar Frete Manualmente
                                </button>
                            </form>
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
    </div>

    {{-- Printable Label Template (Hidden from screen) --}}
    <div id="pdf-template" style="display: none; font-family: 'Helvetica', sans-serif; color: #000; padding: 40px; background: #fff;">
        <div style="border: 2px solid #000; padding: 20px;">
            {{-- Header --}}
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 20px;">
                <div>
                    <h1 style="margin: 0; font-size: 24px; text-transform: uppercase;">Etiqueta de Envio</h1>
                    <p style="margin: 5px 0; font-size: 14px;">Pedido: <strong>#{{ $order->order_number }}</strong></p>
                    <p style="margin: 0; font-size: 12px; color: #666;">Data: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div style="text-align: right;">
                    <h2 style="margin: 0; font-size: 18px;">{{ config('app.name') }}</h2>
                    <p style="margin: 5px 0; font-size: 12px;">Comprovante de Postagem</p>
                </div>
            </div>

            {{-- Addresses --}}
            <div style="display: grid; grid-template-cols: 1fr 1fr; gap: 40px; margin-bottom: 30px;">
                <div>
                    <h3 style="font-size: 10px; text-transform: uppercase; color: #666; margin-bottom: 10px; border-bottom: 1px solid #ccc;">Destinatário</h3>
                    <p style="margin: 0; font-size: 14px; font-weight: bold;">{{ $order->user->name }}</p>
                    <p style="margin: 5px 0; font-size: 12px;">{{ $order->address_info['street'] ?? '' }}, {{ $order->address_info['number'] ?? '' }}</p>
                    @if($order->address_info['complement'] ?? '')
                        <p style="margin: 5px 0; font-size: 12px;">{{ $order->address_info['complement'] }}</p>
                    @endif
                    <p style="margin: 5px 0; font-size: 12px;">{{ $order->address_info['neighborhood'] ?? '' }}</p>
                    <p style="margin: 5px 0; font-size: 12px;">{{ $order->address_info['city'] ?? '' }} - {{ $order->address_info['state'] ?? '' }}</p>
                    <p style="margin: 10px 0; font-size: 14px; font-weight: bold;">CEP: {{ $order->address_info['zip'] ?? $order->address_info['zip_code'] ?? '' }}</p>
                    <p style="margin: 5px 0; font-size: 11px;">CPF: {{ $order->user->cpf }}</p>
                    <p style="margin: 5px 0; font-size: 11px;">Tel: {{ $order->user->phone }}</p>
                </div>
                <div style="background: #f9f9f9; padding: 15px; border: 1px dashed #ccc;">
                    <div style="text-align: center; margin-bottom: 10px;">
                        @php
                            $logoPath = public_path('img/logoEnviaMais.png');
                            $logoData = '';
                            if (file_exists($logoPath)) {
                                $logoData = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
                            }
                        @endphp
                        @if($logoData)
                            <img src="{{ $logoData }}" style="height: 30px; object-fit: contain;" alt="EnviaMais">
                        @else
                            <span style="font-size: 10px; font-weight: bold; color: #666;">ENVIAMAIS</span>
                        @endif
                    </div>
                    <h3 style="font-size: 10px; text-transform: uppercase; color: #666; margin-bottom: 10px; border-bottom: 1px solid #ccc;">Informações de Frete</h3>
                    <div style="margin-bottom: 15px;">
                        <span style="font-size: 10px; text-transform: uppercase; color: #666; display: block;">Status</span>
                        <span style="font-size: 16px; font-weight: bold; color: #10b981;">Frete Contratado</span>
                        <span style="font-size: 20px; font-weight: 900; display: block; margin-top: 5px;">COD. ENVIAMAIS: {{ $order->shipping_id }}</span>
                    </div>
                    <p style="margin: 5px 0; font-size: 12px;">Serviço: <strong>{{ $order->shipping_service_name }}</strong></p>
                    @if($order->shipping_tracking_code)
                        <div style="margin-top: 20px; text-align: center; border: 1px solid #000; padding: 10px;">
                            <span style="font-size: 10px; text-transform: uppercase; display: block;">Código de Rastreamento</span>
                            <span style="font-size: 18px; font-weight: bold; font-family: monospace;">{{ $order->shipping_tracking_code }}</span>
                        </div>
                    @endif
                    
                    {{-- Full API Details --}}
                    @if($order->shipping_api_response)
                        <div style="margin-top: 15px; font-size: 9px; line-height: 1.4; border-top: 1px dashed #ccc; padding-top: 10px;">
                            <h4 style="margin: 0 0 5px 0; font-size: 9px; text-transform: uppercase; color: #666;">Detalhes da Remessa</h4>
                            @foreach($order->shipping_api_response as $key => $value)
                                @if(!is_array($value) && !in_array($key, ['id', 'url_etiqueta', 'rastreamento', 'codigo_rastreio', 'rastreamento_url']) && $value)
                                    <div style="display: flex; justify-content: space-between;">
                                        <span style="font-weight: bold;">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                                        <span>{{ $value }}</span>
                                    </div>
                                @endif
                            @endforeach
                            
                            {{-- Specific nested info if exists --}}
                            @if(isset($order->shipping_api_response['cotacao']))
                                <div style="margin-top: 5px; border-top: 1px dotted #eee; padding-top: 5px;">
                                    <strong>Serviço:</strong> {{ $order->shipping_api_response['cotacao']['servico_nome'] ?? 'N/A' }}<br>
                                    <strong>Prazo:</strong> {{ $order->shipping_api_response['cotacao']['prazo'] ?? 'N/A' }} dias
                                </div>
                            @endif
                        </div>
                    @endif

                    <div style="margin-top: 20px; font-size: 10px; text-align: center;">
                        <p style="margin: 0;">Peso Total: {{ array_sum(array_column($order->items->toArray(), 'quantity')) * 0.5 }}kg</p>
                    </div>
                </div>
            </div>

            {{-- Items Summary --}}
            <div style="margin-top: 30px;">
                <h3 style="font-size: 10px; text-transform: uppercase; color: #666; margin-bottom: 10px; border-bottom: 1px solid #ccc;">Resumo do Pedido</h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                    <thead>
                        <tr style="background: #eee;">
                            <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Item</th>
                            <th style="padding: 8px; text-align: center; border: 1px solid #ddd; width: 60px;">Qtd</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd;">{{ $item->product->name ?? 'Produto' }} {{ $item->color_name ? '- ' . $item->color_name : '' }}</td>
                            <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ $item->quantity }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Footer --}}
            <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 20px;">
                <p>Este é um documento de identificação para logística. Gerado automaticamente pelo sistema de gestão.</p>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function generateLabel() {
            const element = document.getElementById('pdf-template');
            element.style.display = 'block'; // Temporarily show for capture
            
            const opt = {
                margin: 0,
                filename: 'etiqueta-pedido-{{ $order->order_number }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                element.style.display = 'none'; // Hide again
            });
        }
    </script>
</x-admin-layout>
