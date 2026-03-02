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
            'cep_origem' => Setting::get('store_zip_code', '01310100'), // Fallback
            'cep_destino' => preg_replace('/\D/', '', $addr['zip'] ?? $addr['zip_code'] ?? ''),
            'valor_carga' => (float) $order->total_amount,
            'volumes' => $volumes,
            'destinatario' => [
                'nome' => $order->user->name,
                'cpf_cnpj' => $order->user->cpf ?? '',
                'email' => $order->user->email,
                'telefone' => $order->user->phone ?? '',
                'endereco' => $addr['street'] ?? '',
                'numero' => $addr['number'] ?? '',
                'bairro' => $addr['neighborhood'] ?? '',
                'cidade' => $addr['city'] ?? '',
                'uf' => $addr['state'] ?? '',
                'complemento' => $addr['complement'] ?? '',
            ]
        ];

        $result = $shippingService->makeOrder($payload);

        if ($result && isset($result['id'])) {
            $order->update([
                'shipping_id' => $result['id'],
                'shipping_service_name' => $result['cotacao']['servico_nome'] ?? 'Frete Contratado',
                'shipping_tracking_url' => $result['url_etiqueta'] ?? null,
            ]);

            return back()->with('success', 'Frete contratado com sucesso! ID: ' . $result['id']);
        }

        return back()->with('error', 'Falha ao contratar frete na EnviaMais. Verifique os logs.');
    }
}
