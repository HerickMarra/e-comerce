<?php

namespace App\Http\Controllers\Admin;

use App\Gateways\AsaasGateway;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Services\EnviaMaisService;
use App\Services\MelhorEnvioService;
use App\Services\OrderPaymentService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where('order_number', 'like', '%' . $request->search . '%')
                ->orWhereHas('user', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%');
                });
        }

        $orders = $query->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->ensureInitialHistory();
        $order->load(['user', 'items.product', 'histories.user']);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,paid,processing,shipped,delivered,cancelled,refunded',
        ]);

        $previousStatus = $order->status;
        $newStatus = $request->status;

        if ($newStatus !== $previousStatus) {
            $statusLabels = [
                'pending' => 'Pendente',
                'paid' => 'Pago',
                'processing' => 'Em Processamento',
                'shipped' => 'Enviado',
                'delivered' => 'Entregue',
                'cancelled' => 'Cancelado',
                'refunded' => 'Reembolsado',
            ];
            $oldLabel = $statusLabels[$previousStatus] ?? $previousStatus;
            $newLabel = $statusLabels[$newStatus] ?? $newStatus;

            if ($newStatus === 'paid') {
                OrderPaymentService::finalize($order);
            } else {
                // Restore inventory when cancelling or refunding order
                if (in_array($newStatus, ['cancelled', 'refunded']) && !in_array($previousStatus, ['cancelled', 'refunded'])) {
                    $order->restoreStock();
                }

                $order->update([
                    'status' => $newStatus
                ]);
            }

            $order->recordHistory(
                action: $newStatus === 'refunded' ? 'refund_processed' : 'status_changed',
                title: $newStatus === 'refunded' ? 'Pedido Marcado como Reembolsado' : 'Status do Pedido Alterado',
                description: "Status alterado de '{$oldLabel}' para '{$newLabel}'.",
                metadata: ['from' => $previousStatus, 'to' => $newStatus],
                user: auth()->user()
            );
        }

        return back()->with('success', 'Status do pedido atualizado com sucesso!');
    }

    public function recruitShipping(Order $order)
    {
        if (!$order->address_info) {
            return back()->with('error', 'Endereço de entrega não disponível para este pedido.');
        }

        $provider = Setting::get('shipping_provider', 'melhorenvio');
        $hasMelhorEnvioToken = !empty(Setting::get('melhorenvio_token'));

        // Handle Melhor Envio
        if ($provider === 'melhorenvio' || $hasMelhorEnvioToken) {
            $melhorEnvio = new MelhorEnvioService();

            // If order already has a shipping_id, attempt checkout, generate, and fetch label
            if (!empty($order->shipping_id)) {
                $melhorEnvio->checkout([$order->shipping_id]);
                $melhorEnvio->generate([$order->shipping_id]);
                usleep(1500000);
                $labelUrl = $melhorEnvio->printLabel([$order->shipping_id]);
                if (!$labelUrl) {
                    usleep(2000000);
                    $labelUrl = $melhorEnvio->printLabel([$order->shipping_id]);
                }
                $trackingData = $melhorEnvio->tracking([$order->shipping_id]);
                $trackingCode = $trackingData[$order->shipping_id]['tracking'] ?? null;

                if ($labelUrl || $trackingCode) {
                    $order->update([
                        'shipping_label_url' => $labelUrl ?: $order->shipping_label_url,
                        'shipping_tracking_code' => $trackingCode ?: $order->shipping_tracking_code,
                        'shipping_tracking_url' => $trackingCode ? 'https://melhorenvio.com.br/rastreio/' . $trackingCode : $order->shipping_tracking_url,
                    ]);
                    return back()->with('success', 'Etiqueta do Melhor Envio sincronizada e disponível para impressão!');
                }

                $syncError = $melhorEnvio->getLastError();
                return back()->with('warning', 'Etiqueta ainda não liberada no Melhor Envio. ' . ($syncError ? "Detalhe da API: {$syncError}. " : '') . 'Verifique se o frete foi pago na carteira do Melhor Envio.');
            }

            $cartResult = $melhorEnvio->createCartOrder($order);

            if ($cartResult && isset($cartResult['id'])) {
                $cartId = $cartResult['id'];
                \Log::info('Melhor Envio createCartOrder success response:', $cartResult);

                // Attempt automatic checkout (pay with wallet balance)
                $checkoutResult = $melhorEnvio->checkout([$cartId]);
                $labelUrl = null;
                $trackingCode = null;

                if ($checkoutResult) {
                    $melhorEnvio->generate([$cartId]);
                    usleep(1500000);
                    $labelUrl = $melhorEnvio->printLabel([$cartId]);
                    if (!$labelUrl) {
                        usleep(2000000);
                        $labelUrl = $melhorEnvio->printLabel([$cartId]);
                    }
                    $trackingData = $melhorEnvio->tracking([$cartId]);
                    $trackingCode = $trackingData[$cartId]['tracking'] ?? null;
                }

                $serviceName = $order->shipping_modalidade ?: ($cartResult['service']['name'] ?? 'Melhor Envio');

                $order->update([
                    'shipping_id' => $cartId,
                    'shipping_service_name' => $serviceName,
                    'shipping_tracking_url' => $trackingCode ? 'https://melhorenvio.com.br/rastreio/' . $trackingCode : null,
                    'shipping_label_url' => $labelUrl,
                    'shipping_tracking_code' => $trackingCode,
                    'shipping_api_response' => [
                        'provider' => 'melhorenvio',
                        'cart' => $cartResult,
                        'checkout' => $checkoutResult,
                        'checkout_error' => !$checkoutResult ? $melhorEnvio->getLastError() : null,
                    ],
                ]);

                $order->recordHistory(
                    action: 'shipping_label_created',
                    title: 'Etiqueta Gerada no Melhor Envio',
                    description: "Frete contratado no Melhor Envio pelo administrador. ID: {$cartId}" . ($trackingCode ? " • Rastreio: {$trackingCode}" : ''),
                    metadata: ['cart_id' => $cartId, 'tracking' => $trackingCode, 'service' => $serviceName],
                    user: auth()->user()
                );

                $msg = 'Envio gerado no Melhor Envio com sucesso! ID: ' . $cartId;
                if ($labelUrl) {
                    $msg .= ' (Etiqueta disponível para impressão)';
                } elseif (!$checkoutResult) {
                    $msg .= ' (Atenção: Não foi possível pagar o frete automaticamente. ' . ($melhorEnvio->getLastError() ? 'API: ' . $melhorEnvio->getLastError() : 'Verifique se há saldo na carteira') . ')';
                } else {
                    $msg .= ' (Acesse seu painel do Melhor Envio para liberar a etiqueta)';
                }

                return back()->with('success', $msg);
            }

            $errorMsg = $melhorEnvio->getLastError() ?: 'Falha ao contratar frete no Melhor Envio. Verifique os dados de endereço, o token e os logs.';

            $order->recordHistory(
                action: 'shipping_label_failed',
                title: 'Falha na Emissão de Etiqueta (Melhor Envio)',
                description: $errorMsg,
                metadata: ['error' => $errorMsg],
                user: auth()->user()
            );

            return back()->with('error', 'Melhor Envio: ' . $errorMsg);
        }

        // Fallback: EnviaMais
        $shippingService = new EnviaMaisService();
        $volumes = [];
        foreach ($order->items as $item) {
            $product = $item->product;
            $weight = ($product->weight ?? 0.5) * $item->quantity;
            $height = $product->height ?? 10;
            $width = $product->width ?? 10;
            $length = $product->length ?? 10;

            $volumes[] = [
                'peso_carga' => $weight,
                'altura_carga' => $height,
                'largura_carga' => $width,
                'comprimento_carga' => $length,
            ];
        }

        $addr = $order->address_info;

        $payload = [
            'simulacao_id' => $order->shipping_simulacao_id,
            'modalidade' => $order->shipping_modalidade,
            'declaracao_conteudo' => 1,
            'descricao_conteudo' => $order->shipping_descricao_conteudo ?? 'Artigos de Decoração',
            'cep_origem' => Setting::get('store_zip', Setting::get('store_zip_code', '70701000')),
            'valor_carga' => (float) $order->total_amount,
            'volumes' => $volumes,
            'destinatario' => [
                'nome' => $order->user->name,
                'cnpjCpf' => preg_replace('/\D/', '', $order->user->cpf ?? ''),
                'email' => $order->user->email,
                'phone' => preg_replace('/\D/', '', $order->user->phone ?? ''),
                'telefone' => preg_replace('/\D/', '', $order->user->phone ?? ''),
                'endereco' => $addr['street'] ?? '',
                'numero' => $addr['number'] ?? '',
                'bairro' => $addr['neighborhood'] ?? '',
                'cidade' => $addr['city'] ?? '',
                'uf' => $addr['state'] ?? '',
                'cep' => preg_replace('/\D/', '', $addr['zip'] ?? $addr['zip_code'] ?? ''),
                'complemento' => $addr['complement'] ?? '',
                'complement' => $addr['complement'] ?? '',
            ]
        ];

        $result = $shippingService->makeOrder($payload);

        if ($result && isset($result['id'])) {
            \Log::info('EnviaMais makeOrder success response:', $result);

            $order->update([
                'shipping_id' => $result['id'],
                'shipping_service_name' => $result['cotacao']['servico_nome'] ?? 'Frete Contratado',
                'shipping_tracking_url' => $result['rastreamento_url'] ?? $result['url_etiqueta'] ?? null,
                'shipping_label_url' => $result['url_etiqueta'] ?? null,
                'shipping_tracking_code' => $result['rastreamento'] ?? $result['codigo_rastreio'] ?? null,
                'shipping_api_response' => $result,
            ]);

            $order->recordHistory(
                action: 'shipping_label_created',
                title: 'Etiqueta Gerada na EnviaMais',
                description: "Frete contratado na EnviaMais. ID: {$result['id']}",
                metadata: ['shipping_id' => $result['id']],
                user: auth()->user()
            );

            return back()->with('success', 'Frete contratado com sucesso! ID: ' . $result['id']);
        }

        $order->recordHistory(
            action: 'shipping_label_failed',
            title: 'Falha na Emissão de Etiqueta (EnviaMais)',
            description: 'Falha ao contratar frete na EnviaMais.',
            metadata: ['provider' => 'enviamais'],
            user: auth()->user()
        );

        return back()->with('error', 'Falha ao contratar frete na EnviaMais. Verifique os logs.');
    }

    public function refund(Request $request, Order $order, AsaasGateway $gateway)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (!$order->payment_id) {
            return back()->with('error', 'Este pedido não possui identificador de pagamento no gateway para estorno.');
        }

        try {
            $gateway->refundPayment(
                paymentId: $order->payment_id,
                amount: (float) $order->total_amount,
                reason: $request->reason
            );

            $order->restoreStock();
            $order->update(['status' => 'refunded']);

            $order->recordHistory(
                action: 'refund_requested',
                title: 'Reembolso Solicitado e Processado',
                description: "Reembolso no valor de R$ " . number_format($order->total_amount, 2, ',', '.') . " processado no gateway. Motivo: {$request->reason}",
                metadata: [
                    'amount' => $order->total_amount,
                    'reason' => $request->reason,
                    'payment_id' => $order->payment_id,
                ],
                user: auth()->user()
            );

            return back()->with('success', 'Reembolso solicitado e processado com sucesso no gateway!');
        } catch (\Throwable $e) {
            \Log::error('Erro ao estornar pedido no Asaas: ' . $e->getMessage(), ['order_id' => $order->id]);

            $order->recordHistory(
                action: 'refund_failed',
                title: 'Falha na Solicitação de Reembolso',
                description: 'Tentativa de estorno no gateway falhou: ' . $e->getMessage(),
                metadata: ['error' => $e->getMessage()],
                user: auth()->user()
            );

            return back()->with('error', 'Erro ao processar estorno no gateway: ' . $e->getMessage());
        }
    }

    public function addNote(Request $request, Order $order)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $order->recordHistory(
            action: 'note_added',
            title: 'Anotação Interna da Equipe',
            description: $request->note,
            metadata: ['note' => $request->note],
            user: auth()->user()
        );

        return back()->with('success', 'Anotação adicionada ao histórico com sucesso!');
    }
}
