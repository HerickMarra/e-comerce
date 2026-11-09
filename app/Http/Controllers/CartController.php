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
            if (!$product || $product->stock <= 0) {
                $item['out_of_stock'] = true;
                $hasStockIssues = true;
            } elseif ($product->stock < $item['quantity']) {
                $item['insufficient_stock'] = true;
                $item['available_stock'] = $product->stock;
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

        return view('cart.index', compact('cart', 'total', 'recommended', 'defaultCep', 'hasStockIssues'));
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'color' => 'nullable|string',
            'color_name' => 'nullable|string',
            'size' => 'nullable|string',
        ]);

        $availableSizes = $product->availableSizes();
        if (!empty($availableSizes) && empty($request->size)) {
            return back()->withErrors(['message' => 'Por favor, selecione um tamanho de tênis antes de comprar.'])->withInput();
        }

        if ($product->colors()->exists() && empty($request->color)) {
            return back()->withErrors(['message' => 'Por favor, selecione a cor do tênis antes de comprar.'])->withInput();
        }

        if ($product->stock <= 0) {
            return back()->withErrors(['message' => 'Desculpe, este produto acabou de esgotar.']);
        }

        $cart = session()->get('cart', []);
        $id = $product->id 
            . ($request->color ? '_' . str_replace('#', '', $request->color) : '')
            . ($request->size ? '_tam' . preg_replace('/[^a-zA-Z0-9]/', '', $request->size) : '');

        $currentQty = isset($cart[$id]) ? $cart[$id]['quantity'] : 0;
        $newQty = $currentQty + $request->quantity;

        if ($newQty > $product->stock) {
            return back()->withErrors(['message' => "Desculpe, só temos {$product->stock} unidades em estoque."]);
        }

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $newQty;
        } else {
            $cart[$id] = [
                'id' => $id,
                'product_id' => $product->id,
                'name' => $product->name,
                'quantity' => $request->quantity,
                'price' => $product->price,
                'color' => $request->color,
                'color_name' => $request->color_name,
                'size' => $request->size,
                'image' => $product->images->where('is_main', true)->first()?->path ?? ($product->images->first()?->path ?? ''),
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('status', 'item-added');
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $product = Product::find($cart[$id]['product_id'] ?? null);

            if ($request->has('quantity')) {
                $requestedQty = max(1, (int) $request->quantity);
                if ($product && $product->stock > 0) {
                    $requestedQty = min($product->stock, $requestedQty);
                }
                $cart[$id]['quantity'] = $requestedQty;
            }

            if ($request->has('color') && $request->has('color_name')) {
                $item = $cart[$id];
                $newColor = $request->color;
                $newColorName = $request->color_name;

                // If color changed, compute new ID preserving size if present
                $sizePart = !empty($item['size']) ? '_tam' . preg_replace('/[^a-zA-Z0-9]/', '', $item['size']) : '';
                $newId = $item['product_id'] . ($newColor ? '_' . str_replace('#', '', $newColor) : '') . $sizePart;

                if ($newId !== $id) {
                    unset($cart[$id]);
                    if (isset($cart[$newId])) {
                        $cart[$newId]['quantity'] += $item['quantity'];
                    } else {
                        $item['id'] = $newId;
                        $item['color'] = $newColor;
                        $item['color_name'] = $newColorName;
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
            if (!$product || $product->stock <= 0) {
                $item['out_of_stock'] = true;
                $hasStockIssues = true;
            } elseif ($product->stock < $item['quantity']) {
                $item['insufficient_stock'] = true;
                $item['available_stock'] = $product->stock;
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
