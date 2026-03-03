<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use App\Services\EnviaMaisService;
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

        $order->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Status do pedido atualizado com sucesso!');
    }

    public function recruitShipping(Order $order, EnviaMaisService $shippingService)
    {
        if (!$order->address_info) {
            return back()->with('error', 'Endereço de entrega não disponível para este pedido.');
        }

        // Prepare products/volumes for EnviaMais
        $volumes = [];
        foreach ($order->items as $item) {
            $product = $item->product;
            // Fallback dimensions if not set
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
            'cep_origem' => Setting::get('store_zip_code', '01310100'),
            'valor_carga' => (float) $order->total_amount,
            'volumes' => $volumes,
            'destinatario' => [
                'nome' => $order->user->name,
                'cnpjCpf' => preg_replace('/\D/', '', $order->user->cpf ?? ''),
                'email' => $order->user->email,
                'phone' => preg_replace('/\D/', '', $order->user->phone ?? ''), // Changed from telefone to phone
                'telefone' => preg_replace('/\D/', '', $order->user->phone ?? ''), // Keep for compatibility
                'endereco' => $addr['street'] ?? '',
                'numero' => $addr['number'] ?? '',
                'bairro' => $addr['neighborhood'] ?? '',
                'cidade' => $addr['city'] ?? '',
                'uf' => $addr['state'] ?? '',
                'cep' => preg_replace('/\D/', '', $addr['zip'] ?? $addr['zip_code'] ?? ''),
                'complemento' => $addr['complement'] ?? '',
                'complement' => $addr['complement'] ?? '', // Added complement alias
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
