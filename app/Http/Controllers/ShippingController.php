<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Setting;
use App\Services\EnviaMaisService;
use App\Services\MelhorEnvioService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function calculate(Request $request)
    {
        $request->validate([
            'cep' => 'required|string|min:8|max:9',
        ]);

        $cep = preg_replace('/\D/', '', $request->cep);

        $productsList = [];
        $volumes = [];
        $totalValue = 0;

        if ($request->has('product_id')) {
            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json(['error' => 'Produto não encontrado.'], 404);
            }

            $quantity = max(1, (int) $request->get('quantity', 1));
            $price = (float) $product->price;
            $totalValue = $price * $quantity;

            $weight = (float) ($product->weight ?: 0.3);
            $height = (float) ($product->height ?: 4);
            $width = (float) ($product->width ?: 11);
            $length = (float) ($product->length ?: 16);

            $productsList[] = [
                'id' => $product->id,
                'weight' => $weight,
                'height' => $height,
                'width' => $width,
                'length' => $length,
                'price' => $price,
                'quantity' => $quantity,
            ];

            for ($i = 0; $i < $quantity; $i++) {
                $volumes[] = [
                    'peso_carga' => $weight,
                    'altura_carga' => $height,
                    'largura_carga' => $width,
                    'comprimento_carga' => $length,
                ];
            }
        } else {
            $cart = session('cart', []);

            if (empty($cart)) {
                return response()->json(['error' => 'Carrinho vazio.'], 422);
            }

            foreach ($cart as $item) {
                $product = Product::find($item['product_id'] ?? $item['id'] ?? null);

                if (!$product) {
                    continue;
                }

                $qty = max(1, (int) ($item['quantity'] ?? 1));
                $price = (float) ($item['price'] ?? $product->price);
                $totalValue += $price * $qty;

                $weight = (float) ($product->weight ?: 0.3);
                $height = (float) ($product->height ?: 4);
                $width = (float) ($product->width ?: 11);
                $length = (float) ($product->length ?: 16);

                $productsList[] = [
                    'id' => $product->id,
                    'weight' => $weight,
                    'height' => $height,
                    'width' => $width,
                    'length' => $length,
                    'price' => $price,
                    'quantity' => $qty,
                ];

                for ($i = 0; $i < $qty; $i++) {
                    $volumes[] = [
                        'peso_carga' => (int) ceil($weight),
                        'altura_carga' => (int) ceil($height),
                        'largura_carga' => (int) ceil($width),
                        'comprimento_carga' => (int) ceil($length),
                    ];
                }
            }
        }

        if (empty($productsList)) {
            return response()->json([
                'error' => 'Nenhum produto válido encontrado para o cálculo de frete.',
            ], 422);
        }

        $provider = Setting::get('shipping_provider', 'melhorenvio');
        $hasMelhorEnvioToken = !empty(Setting::get('melhorenvio_token'));
        $cotacoes = [];

        // Primary: Melhor Envio
        if ($provider === 'melhorenvio' || $hasMelhorEnvioToken) {
            $melhorEnvio = new MelhorEnvioService();
            $cotacoes = $melhorEnvio->calculate($cep, $productsList, $totalValue);
        }

        // Secondary / Fallback: EnviaMais
        if (empty($cotacoes) && !empty(Setting::get('enviamais_api_key'))) {
            $enviaMais = new EnviaMaisService();
            $rawCotacoes = $enviaMais->getShipping($cep, $totalValue, $volumes);

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

            foreach ($rawCotacoes as $c) {
                $cotacoes[] = [
                    'modalidade' => (string) ($c['modalidade'] ?? ''),
                    'servico' => $modalidadeMap[$c['modalidade']] ?? ($c['servico'] ?? ('Opção #' . ($c['modalidade'] ?? ''))),
                    'prazo' => (int) ($c['prazo'] ?? 5),
                    'valor' => (float) ($c['valor'] ?? 0),
                    'simulacao_id' => $c['simulacao_id'] ?? null,
                ];
            }
        }

        if (empty($cotacoes)) {
            return response()->json([
                'error' => 'Não foi possível calcular o frete para este CEP. Verifique se o CEP está correto ou entre em contato.',
            ], 422);
        }

        // Sort by price
        usort($cotacoes, fn($a, $b) => ($a['valor'] ?? 0) <=> ($b['valor'] ?? 0));

        return response()->json(['cotacoes' => $cotacoes]);
    }
}
