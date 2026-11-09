<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        $hasStockIssues = false;

        foreach ($cart as $id => &$item) {
            $product = Product::with('colors')->find($item['product_id']);

            $variant = null;
            if (!empty($item['product_variant_id'])) {
                $variant = \App\Models\ProductVariant::find($item['product_variant_id']);
            } elseif ($product && !empty($item['size'])) {
                $variant = $product->findVariant($item['size'], $item['color_name'] ?? null, $item['color'] ?? null);
            }

            $availableStock = $variant ? (int) $variant->stock : ($product ? (int) $product->stock : 0);
            $item['available_stock'] = $availableStock;
            if ($variant) {
                $item['product_variant_id'] = $variant->id;
            }

            if (!$product || $availableStock <= 0) {
                $item['out_of_stock'] = true;
                $hasStockIssues = true;
            } elseif ($availableStock < $item['quantity']) {
                $item['insufficient_stock'] = true;
                $hasStockIssues = true;
            } else {
                $item['out_of_stock'] = false;
                $item['insufficient_stock'] = false;
            }
            if ($product) {
                $item['price'] = (float) $product->price;
            }
            $item['available_colors'] = $product ? $product->colors : [];
            $total += $item['price'] * $item['quantity'];
        }
        unset($item);

        session()->put('cart', $cart);

        // Mock recommended products for now
        $recommended = Product::where('is_active', true)->where('stock', '>', 0)->take(3)->get();

        $defaultCep = '';
        if (auth()->check()) {
            $defaultCep = auth()->user()->addresses()->where('is_default', true)->first()?->zip_code
                ?? auth()->user()->addresses()->first()?->zip_code
                ?? '';
        }

        $seo = \App\Services\SeoService::forPrivate('Meu Carrinho');

        return view('cart.index', compact('cart', 'total', 'recommended', 'defaultCep', 'hasStockIssues', 'seo'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'color' => 'nullable|string',
            'color_name' => 'nullable|string',
            'size' => 'nullable|string',
            'product_variant_id' => 'nullable|integer',
        ]);

        $availableSizes = $product->availableSizes();
        if (!empty($availableSizes) && empty($request->size)) {
            return back()->withErrors(['message' => 'Por favor, selecione um tamanho de tênis antes de comprar.'])->withInput();
        }

        if ($product->colors()->exists() && empty($request->color) && empty($request->color_name)) {
            return back()->withErrors(['message' => 'Por favor, selecione a cor do tênis antes de comprar.'])->withInput();
        }

        // Find exact variant
        $variant = null;
        if ($request->filled('product_variant_id')) {
            $variant = \App\Models\ProductVariant::where('product_id', $product->id)->find($request->product_variant_id);
        }
        if (!$variant && $request->filled('size')) {
            $variant = $product->findVariant($request->size, $request->color_name, $request->color);
        }

        $availableStock = $variant ? (int) $variant->stock : (int) $product->stock;

        if ($availableStock <= 0) {
            $spec = $request->size ? "o tamanho {$request->size}" : 'este produto';
            if ($request->color_name) $spec .= " na cor {$request->color_name}";
            return back()->withErrors(['message' => "Desculpe, {$spec} acabou de esgotar."]);
        }

        $cart = session()->get('cart', []);
        $id = $product->id 
            . ($variant ? '_var' . $variant->id : '')
            . ($request->color ? '_' . str_replace('#', '', $request->color) : '')
            . ($request->size ? '_tam' . preg_replace('/[^a-zA-Z0-9]/', '', $request->size) : '');

        $currentQty = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
        $newQty = $currentQty + (int) $request->quantity;

        if ($newQty > $availableStock) {
            return back()->withErrors(['message' => "Desculpe, só temos {$availableStock} unidades disponíveis desta combinação em estoque."]);
        }

        $cart[$id] = [
            'id' => $id,
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'name' => $product->name,
            'quantity' => $newQty,
            'price' => (float) $product->price,
            'color' => $request->color,
            'color_name' => $request->color_name,
            'size' => $request->size,
            'available_stock' => $availableStock,
            'image' => $product->images->where('is_main', true)->first()?->path ?? ($product->images->first()?->path ?? ''),
        ];

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'item-added');
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $product = Product::find($cart[$id]['product_id'] ?? null);

            $variant = null;
            if (!empty($cart[$id]['product_variant_id'])) {
                $variant = \App\Models\ProductVariant::find($cart[$id]['product_variant_id']);
            } elseif ($product && !empty($cart[$id]['size'])) {
                $variant = $product->findVariant($cart[$id]['size'], $cart[$id]['color_name'] ?? null, $cart[$id]['color'] ?? null);
            }

            $availableStock = $variant ? (int) $variant->stock : ($product ? (int) $product->stock : 0);

            if ($request->has('quantity')) {
                $requestedQty = max(1, (int) $request->quantity);
                if ($availableStock > 0) {
                    $requestedQty = min($availableStock, $requestedQty);
                }
                $cart[$id]['quantity'] = $requestedQty;
            }

            if ($request->has('color') && $request->has('color_name')) {
                $item = $cart[$id];
                $newColor = $request->color;
                $newColorName = $request->color_name;

                $newVariant = $product ? $product->findVariant($item['size'] ?? '', $newColorName, $newColor) : null;
                $newStock = $newVariant ? (int) $newVariant->stock : ($product ? (int) $product->stock : 0);

                $sizePart = !empty($item['size']) ? '_tam' . preg_replace('/[^a-zA-Z0-9]/', '', $item['size']) : '';
                $newId = $item['product_id'] . ($newVariant ? '_var' . $newVariant->id : '') . ($newColor ? '_' . str_replace('#', '', $newColor) : '') . $sizePart;

                if ($newId !== $id) {
                    unset($cart[$id]);
                    if (isset($cart[$newId])) {
                        $cart[$newId]['quantity'] = min($newStock, $cart[$newId]['quantity'] + $item['quantity']);
                    } else {
                        $item['id'] = $newId;
                        $item['product_variant_id'] = $newVariant?->id;
                        $item['color'] = $newColor;
                        $item['color_name'] = $newColorName;
                        $item['available_stock'] = $newStock;
                        $item['quantity'] = min($newStock, $item['quantity']);
                        $cart[$newId] = $item;
                    }
                } else {
                    $cart[$id]['color'] = $newColor;
                    $cart[$id]['color_name'] = $newColorName;
                }
            }

            session()->put('cart', $cart);
        }

        if ($request->wantsJson()) {
            return response()->json($this->getCartResponse($cart));
        }

        return redirect()->route('cart.index');
    }

    public function remove(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        if ($request->wantsJson()) {
            return response()->json($this->getCartResponse($cart));
        }

        return redirect()->route('cart.index')->with('status', 'item-removed');
    }

    private function getCartResponse($cart)
    {
        $total = 0;
        $hasStockIssues = false;

        foreach ($cart as $id => &$item) {
            $product = Product::with('colors')->find($item['product_id']);

            $variant = null;
            if (!empty($item['product_variant_id'])) {
                $variant = \App\Models\ProductVariant::find($item['product_variant_id']);
            } elseif ($product && !empty($item['size'])) {
                $variant = $product->findVariant($item['size'], $item['color_name'] ?? null, $item['color'] ?? null);
            }

            $availableStock = $variant ? (int) $variant->stock : ($product ? (int) $product->stock : 0);
            $item['available_stock'] = $availableStock;
            if ($variant) {
                $item['product_variant_id'] = $variant->id;
            }

            if (!$product || $availableStock <= 0) {
                $item['out_of_stock'] = true;
                $hasStockIssues = true;
            } elseif ($availableStock < $item['quantity']) {
                $item['insufficient_stock'] = true;
                $hasStockIssues = true;
            } else {
                $item['out_of_stock'] = false;
                $item['insufficient_stock'] = false;
            }
            if ($product) {
                $item['price'] = (float) $product->price;
            }
            $item['available_colors'] = $product ? $product->colors : [];
            $total += $item['price'] * $item['quantity'];
        }

        return [
            'success' => true,
            'cart' => $cart,
            'total' => $total,
            'count' => count($cart),
            'hasStockIssues' => $hasStockIssues
        ];
    }

    public function getCount()
    {
        return response()->json(['count' => count(session()->get('cart', []))]);
    }
}
