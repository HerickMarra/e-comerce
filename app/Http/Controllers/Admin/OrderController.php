<?php

namespace App\Http\Controllers\Admin;

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
        $order->load(['user', 'items.product']);
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string|in:pending,paid,processing,shipped,delivered,cancelled',
        ]);

        if ($request->status === 'paid' && $order->status !== 'paid') {
            OrderPaymentService::finalize($order);
        } else {
            $order->update([
                'status' => $request->status
            ]);
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

            // If order already has a shipping_id but lacks label URL, try generating/fetching label directly
            if (!empty($order->shipping_id) && empty($order->shipping_label_url)) {
                $melhorEnvio->generate([$order->shipping_id]);
                $labelUrl = $melhorEnvio->printLabel([$order->shipping_id]);
                $trackingData = $melhorEnvio->tracking([$order->shipping_id]);
                $trackingCode = $trackingData[$order->shipping_id]['tracking'] ?? null;

                if ($labelUrl) {
                    $order->update([
                        'shipping_label_url' => $labelUrl,
                        'shipping_tracking_code' => $trackingCode ?: $order->shipping_tracking_code,
                        'shipping_tracking_url' => $trackingCode ? 'https://melhorenvio.com.br/rastreio/' . $trackingCode : $order->shipping_tracking_url,
                    ]);
                    return back()->with('success', 'Etiqueta do Melhor Envio sincronizada e disponível com sucesso!');
                }
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
                    $labelUrl = $melhorEnvio->printLabel([$cartId]);
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
                    ],
                ]);

                $msg = 'Envio gerado no Melhor Envio com sucesso! ID: ' . $cartId;
                if ($labelUrl) {
                    $msg .= ' (Etiqueta disponível para impressão)';
                } else {
                    $msg .= ' (Acesse seu painel do Melhor Envio para liberar a etiqueta)';
                }

                return back()->with('success', $msg);
            }

            return back()->with('error', 'Falha ao contratar frete no Melhor Envio. Verifique os dados de endereço, o token e os logs.');
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

            return back()->with('success', 'Frete contratado com sucesso! ID: ' . $result['id']);
        }

        return back()->with('error', 'Falha ao contratar frete na EnviaMais. Verifique os logs.');
    }
}
