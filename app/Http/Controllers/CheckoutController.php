<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Added for DB facade
use App\Models\Order; // Added for Order model
use App\Gateways\AsaasGateway; // Added for AsaasGateway

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('status', 'cart-empty');
        }

        $subtotal = array_reduce($cart, fn($t, $i) => $t + ($i['price'] * $i['quantity']), 0);
        // Shipping will be determined client-side from EnviaMais / store pickup selection
        $shipping = 0;
        $total = $subtotal + $shipping;

        $addresses = Auth::check() ? Auth::user()->addresses : collect();
        $defaultCep = '';
        if (Auth::check()) {
            $defaultCep = Auth::user()->addresses()->where('is_default', true)->first()?->zip_code
                ?? Auth::user()->addresses()->first()?->zip_code
                ?? '';
        }

        return view('checkout.index', compact('cart', 'subtotal', 'shipping', 'total', 'addresses', 'defaultCep'));
    }

    public function process(Request $request, AsaasGateway $gateway)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        $subtotal = array_reduce($cart, fn($t, $i) => $t + ($i['price'] * $i['quantity']), 0);

        // Read shipping from form (selected in cart via EnviaMais or store pickup)
        $shippingAmount = (float) $request->input('shipping_amount', 0);
        $shippingLabel = $request->input('shipping_label', 'Retirada na Loja');
        $total = $subtotal + $shippingAmount;

        // Start Transaction
        DB::beginTransaction();

        try {
            // Stock Validation
            foreach ($cart as $id => $item) {
                $product = \App\Models\Product::find($item['product_id']);
                if (!$product || $product->stock < $item['quantity']) {
                    throw new \Exception("Desculpe, o estoque do produto '{$item['name']}' mudou e não está mais disponível na quantidade desejada.");
                }

                // Decrement stock
                $product->decrement('stock', $item['quantity']);
            }

            // Automatic Address Saving
            if (Auth::check()) {
                $addrData = $request->input('address');
                if ($addrData && isset($addrData['zip_code'], $addrData['street'])) {
                    $user = Auth::user();

                    // Check if this address already exists for the user
                    $exists = $user->addresses()
                        ->where('zip_code', $addrData['zip_code'])
                        ->where('street', $addrData['street'])
                        ->where('number', $addrData['number'] ?? '')
                        ->exists();

                    if (!$exists) {
                        $isFirst = $user->addresses()->count() === 0;
                        $user->addresses()->create([
                            'label' => 'Endereço Checkout',
                            'recipient_name' => $user->name,
                            'zip_code' => $addrData['zip_code'],
                            'street' => $addrData['street'],
                            'number' => $addrData['number'] ?? '',
                            'neighborhood' => $addrData['neighborhood'] ?? '',
                            'city' => $addrData['city'] ?? '',
                            'state' => $addrData['state'] ?? '',
                            'is_default' => $isFirst,
                        ]);
                    }
                }
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_amount' => $shippingAmount,
                'total_amount' => $total,
                'payment_method' => $request->payment_method,
                'address_info' => $request->address,
                'notes' => trim(($request->notes ?? '') . ' | Frete: ' . $shippingLabel),
            ]);

            foreach ($cart as $id => $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'color' => $item['color'],
                    'color_name' => $item['color_name'],
                ]);
            }

            // Process Payment
            $paymentResult = $gateway->createPayment($order, $request->all());

            if ($paymentResult['success']) {
                $order->update([
                    'payment_id' => $paymentResult['payment_id'],
                ]);

                // Clear Cart
                session()->forget('cart');

                DB::commit();

                return redirect()->route('checkout.success', ['order' => $order->id])
                    ->with('payment_data', $paymentResult);
            }

            throw new \Exception('Erro no processamento do pagamento.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function success(Order $order)
    {
        return view('checkout.success', compact('order'));
    }

    public function checkStatus(Order $order, AsaasGateway $gateway)
    {
        if (!$order->payment_id) {
            return response()->json(['paid' => false]);
        }

        $payment = $gateway->getPayment($order->payment_id);

        if ($payment && in_array($payment['status'], ['RECEIVED', 'CONFIRMED'])) {
            if ($order->status !== 'paid' && $order->status !== 'completed') {
                $order->update(['status' => 'paid']);
            }
            return response()->json(['paid' => true]);
        }

        return response()->json(['paid' => false]);
    }
}
