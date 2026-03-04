<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\EnviaMaisService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function calculate(Request $request)
    {
        $request->validate([
            'cep' => 'required|string|min:8|max:9',
        ]);

        $cep = preg_replace('/\D/', '', $request->cep);

        // Build volumes from the cart session or single product
        $volumes = [];
        $totalValue = 0;

        if ($request->has('product_id')) {
            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json(['error' => 'Produto não encontrado.'], 404);
            }

            $quantity = (int) $request->get('quantity', 1);
            $totalValue = $product->price * $quantity;

            if (!$product->weight || !$product->height || !$product->width || !$product->length) {
                return response()->json([
                    'error' => 'Este produto não possui dimensões cadastradas para o cálculo de frete.',
                ], 422);
            }

            for ($i = 0; $i < $quantity; $i++) {
                $volumes[] = [
                    'peso_carga' => (float) $product->weight,
                    'altura_carga' => (float) $product->height,
                    'largura_carga' => (float) $product->width,
                    'comprimento_carga' => (float) $product->length,
                ];
            }
        } else {
            $cart = session('cart', []);

            if (empty($cart)) {
                return response()->json(['error' => 'Carrinho vazio.'], 422);
            }

            foreach ($cart as $item) {
                $product = Product::find($item['product_id'] ?? $item['id'] ?? null);

                if (!$product)
                    continue;

                $totalValue += $product->price * ($item['quantity'] ?? 1);

                // Skip if product has no dimensions
                if (!$product->weight || !$product->height || !$product->width || !$product->length) {
                    continue;
                }

                // One volume entry per quantity
                for ($i = 0; $i < ($item['quantity'] ?? 1); $i++) {
                    $volumes[] = [
                        'peso_carga' => (int) $product->weight,
                        'altura_carga' => (int) $product->height,
                        'largura_carga' => (int) $product->width,
                        'comprimento_carga' => (int) $product->length,
                    ];
                }
            }
        }

        if (empty($volumes)) {
            return response()->json([
                'error' => 'Não foi possível identificar volumes para cálculo. Verifique as dimensões dos produtos.',
            ], 422);
        }

        $service = new EnviaMaisService();
        $cotacoes = $service->getShipping($cep, $totalValue, $volumes);

        if (empty($cotacoes)) {
            return response()->json([
                'error' => 'Não foi possível calcular o frete para este CEP. Tente novamente ou entre em contato.',
            ], 422);
        }

        // Map modalidade IDs to names (EnviaMais common values)
        $modalidadeMap = [
            4 => 'PAC (Econômico)',
            12 => 'SEDEX (Expresso)',
            1 => 'Normal',
            2 => 'SEDEX 10',
            3 => 'SEDEX 12',
            5 => 'PAC Mini',
            6 => 'Impresso Normal',
            7 => 'Impresso Urgente',
            8 => 'Carta Simples',
            9 => 'Carta Registrada',
            10 => 'SEDEX Hoje',
            11 => 'SEDEX (Contrato)',
            13 => 'PAC (Contrato)',
            201 => 'Jadlog Package',
            202 => 'Jadlog .Com',
            301 => 'Azul Cargo Amanhã',
            302 => 'Azul Cargo E-commerce',
        ];

        if (count($cotacoes) > 0) {
            // Sort by price to find "Melhor Preço"
            usort($cotacoes, fn($a, $b) => ($a['valor'] ?? 9999) <=> ($b['valor'] ?? 9999));
            $cheapestIndex = 0;

            // Sort by time to find "Melhor Prazo"
            // We create a temporary copy to find the fastest without losing price sort order yet
            $tempFastest = $cotacoes;
            usort($tempFastest, fn($a, $b) => ($a['prazo'] ?? 999) <=> ($b['prazo'] ?? 999));
            $fastestModalidade = $tempFastest[0]['modalidade'] ?? null;

            foreach ($cotacoes as $i => &$c) {
                if ($i === $cheapestIndex) {
                    $c['servico'] = 'Melhor Preço';
                } elseif ($c['modalidade'] === $fastestModalidade) {
                    $c['servico'] = 'Melhor Prazo';
                } else {
                    $c['servico'] = $modalidadeMap[$c['modalidade']] ?? "Entrega #" . $c['modalidade'];
                }
            }

            // Final Sort: Mejor Precio (index 0) stays first, Mejor Prazo stays last if there's more than one
            if (count($cotacoes) > 1) {
                // To guarantee "Melhor Prazo" is last if it's different from "Melhor Preço"
                usort($cotacoes, function ($a, $b) {
                    if (($a['servico'] ?? '') === 'Melhor Preço')
                        return -1;
                    if (($b['servico'] ?? '') === 'Melhor Preço')
                        return 1;
                    if (($a['servico'] ?? '') === 'Melhor Prazo')
                        return 1;
                    if (($b['servico'] ?? '') === 'Melhor Prazo')
                        return -1;
                    return 0;
                });
            }
        } else {
            // Safety for empty results
            $cotacoes = [];
        }

        return response()->json(['cotacoes' => $cotacoes]);
    }
}
