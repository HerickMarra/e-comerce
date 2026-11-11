<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Added for DB facade
use App\Models\Order; // Added for Order model
use App\Gateways\AsaasGateway; // Added for AsaasGateway
use Illuminate\Support\Facades\Mail;
use App\Mail\DynamicEmail;
use App\Models\EmailTemplate;

use App\Services\OrderPaymentService;

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

        $seo = \App\Services\SeoService::forPrivate('Finalizar Compra');

        return view('checkout.index', compact('cart', 'subtotal', 'shipping', 'total', 'addresses', 'defaultCep', 'seo'));
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

        // Validate payment method and card inputs
        $request->validate([
            'payment_method' => 'required|string|in:pix,credit_card,boleto',
            'card_holder' => 'required_if:payment_method,credit_card|nullable|string|max:100',
            'card_number' => 'required_if:payment_method,credit_card|nullable|string',
            'card_expiry' => 'required_if:payment_method,credit_card|nullable|string',
            'card_cvv' => 'required_if:payment_method,credit_card|nullable|string|min:3|max:4',
        ], [
            'payment_method.required' => 'Selecione uma forma de pagamento.',
            'payment_method.in' => 'Forma de pagamento inválida.',
            'card_holder.required_if' => 'Informe o nome impresso no cartão.',
            'card_number.required_if' => 'Informe o número do cartão de crédito.',
            'card_expiry.required_if' => 'Informe a validade do cartão.',
            'card_cvv.required_if' => 'Informe o código de segurança (CVV).',
        ]);

        // Validate and save user info (Logged in or Guest)
        if (Auth::check()) {
            $user = Auth::user();
            $rules = [];
            if (!$user->cpf)
                $rules['user_cpf'] = 'required|string|min:11';
            if (!$user->phone)
                $rules['user_phone'] = 'required|string';

            if (!empty($rules)) {
                $request->validate($rules, [
                    'user_cpf.required' => 'Informe seu CPF para emissão da nota fiscal.',
                    'user_cpf.min' => 'Informe um CPF válido.',
                    'user_phone.required' => 'Informe seu WhatsApp / telefone para envio.',
                ]);

                $user->update([
                    'cpf' => $user->cpf ?: $request->user_cpf,
                    'phone' => $user->phone ?: $request->user_phone,
                ]);
            }
        } else {
            // Guest Checkout: validação dos dados básicos do cliente
            $request->validate([
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'customer_cpf' => 'required|string|min:11',
                'customer_phone' => 'required|string|min:10',
            ], [
                'customer_name.required' => 'Informe seu nome completo.',
                'customer_email.required' => 'Informe seu e-mail para receber a confirmação do pedido.',
                'customer_email.email' => 'Informe um e-mail válido.',
                'customer_cpf.required' => 'O CPF é obrigatório para emissão da nota e processamento do pagamento.',
                'customer_cpf.min' => 'Informe um CPF válido.',
                'customer_phone.required' => 'Informe seu WhatsApp / telefone para contato.',
            ]);

            $cleanEmail = strtolower(trim($request->customer_email));
            $user = \App\Models\User::where('email', $cleanEmail)->first();

            if (!$user) {
                $user = \App\Models\User::create([
                    'name' => trim($request->customer_name),
                    'email' => $cleanEmail,
                    'cpf' => $request->customer_cpf,
                    'phone' => $request->customer_phone,
                    'password' => bcrypt(\Illuminate\Support\Str::random(16)),
                ]);
                if (method_exists($user, 'assignRole')) {
                    try { $user->assignRole('Customer'); } catch (\Exception $e) {}
                }
            } else {
                $updates = [];
                if (!$user->cpf && $request->customer_cpf) $updates['cpf'] = $request->customer_cpf;
                if (!$user->phone && $request->customer_phone) $updates['phone'] = $request->customer_phone;
                if (!empty($updates)) $user->update($updates);
            }

            // Autentica o cliente automaticamente para acompanhar o pedido
            Auth::login($user);
        }

        // Start Transaction
        DB::beginTransaction();

        try {
            // Stock Validation with Pessimistic Locking to Prevent Race Conditions
            $orderItemsToCreate = [];

            foreach ($cart as $id => $item) {
                $product = \App\Models\Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception("Desculpe, o produto '{$item['name']}' não foi encontrado.");
                }

                $variant = null;
                if (!empty($item['product_variant_id'])) {
                    $variant = \App\Models\ProductVariant::where('id', $item['product_variant_id'])->lockForUpdate()->first();
                } elseif (!empty($item['size'])) {
                    $variant = \App\Models\ProductVariant::where('product_id', $product->id)
                        ->where('size', (string) $item['size'])
                        ->where(function ($q) use ($item) {
                            if (!empty($item['color_name'])) {
                                $q->where('color_name', $item['color_name']);
                            }
                            if (!empty($item['color'])) {
                                $q->orWhere('color_hex', $item['color']);
                            }
                        })
                        ->lockForUpdate()
                        ->first();
                }

                if ($variant) {
                    if ($variant->stock < $item['quantity']) {
                        $spec = "tamanho {$item['size']}" . (!empty($item['color_name']) ? " na cor {$item['color_name']}" : '');
                        throw new \Exception("Desculpe, o estoque para o {$spec} de '{$item['name']}' mudou e restam apenas {$variant->stock} unidades disponíveis.");
                    }
                } else {
                    $lockedProduct = \App\Models\Product::where('id', $product->id)->lockForUpdate()->first();
                    if ($lockedProduct->stock < $item['quantity']) {
                        throw new \Exception("Desculpe, o estoque do produto '{$item['name']}' mudou e restam apenas {$lockedProduct->stock} unidades disponíveis.");
                    }
                }

                $orderItemsToCreate[] = [
                    'product' => $product,
                    'variant' => $variant,
                    'item' => $item,
                ];
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
                'shipping_simulacao_id' => $request->shipping_simulacao_id,
                'shipping_modalidade' => $shippingLabel,
                'shipping_descricao_conteudo' => 'Calçados / Tênis', // Sneaker store description
            ]);

            // Audit history: order created by customer
            $client = Auth::user();
            $order->recordHistory(
                action: 'order_created',
                title: 'Pedido Gerado no Sistema',
                description: 'Pedido gerado pelo cliente no checkout com total de R$ ' . number_format($total, 2, ',', '.') . '.',
                metadata: [
                    'subtotal' => $subtotal,
                    'shipping_amount' => $shippingAmount,
                    'total_amount' => $total,
                    'payment_method' => $request->payment_method,
                    'ip' => $request->ip(),
                ],
                user: $client,
                actorName: $client ? $client->name : 'Cliente'
            );

            foreach ($orderItemsToCreate as $entry) {
                $product = $entry['product'];
                $variant = $entry['variant'];
                $item = $entry['item'];

                if ($variant) {
                    $variant->recordMovement(
                        'sale',
                        -(int) $item['quantity'],
                        "Saída por venda no pedido #{$order->order_number}",
                        $order->id,
                        Auth::id()
                    );
                } else {
                    $product->decrement('stock', $item['quantity']);
                }

                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $variant?->id,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'color' => $item['color'] ?? null,
                    'color_name' => $item['color_name'] ?? null,
                    'size' => $item['size'] ?? null,
                ]);
            }

            // Send New Order Email
            try {
                $template = EmailTemplate::where('slug', 'new-order')->first();
                if ($template && Auth::check()) {
                    $user = Auth::user();
                    $content = str_replace(
                        ['{name}', '{order_number}', '{status}'],
                        [$user->name, $order->order_number, $order->status],
                        $template->content
                    );
                    Mail::to($user->email)->send(new DynamicEmail($template->subject, $content));
                }
            } catch (\Exception $e) {
                \Log::error('Error sending new order email: ' . $e->getMessage());
            }

            // Process Payment
            $paymentResult = $gateway->createPayment($order, $request->all());

            if ($paymentResult['success']) {
                $order->update([
                    'payment_id' => $paymentResult['payment_id'],
                ]);

                // Audit history: gateway billing / PIX generated
                $isPix = strtolower($request->payment_method ?? '') === 'pix';
                $order->recordHistory(
                    action: $isPix ? 'pix_generated' : 'credit_card_processed',
                    title: $isPix ? 'Código PIX Gerado no Gateway' : 'Cobrança Criada no Gateway',
                    description: $isPix 
                        ? "Código PIX gerado com sucesso no gateway Asaas (ID: {$paymentResult['payment_id']})."
                        : "Cobrança de cartão criada no gateway Asaas (ID: {$paymentResult['payment_id']}).",
                    metadata: [
                        'gateway' => 'asaas',
                        'payment_id' => $paymentResult['payment_id'],
                        'status' => $paymentResult['status'] ?? null,
                    ],
                    user: null,
                    actorName: 'Gateway Asaas'
                );

                // Check if payment was approved immediately (e.g. Credit Card)
                $asaasStatus = $paymentResult['status'] ?? '';
                if (in_array($asaasStatus, ['CONFIRMED', 'RECEIVED'])) {
                    OrderPaymentService::finalize($order);
                }

                // Clear Cart and remember this order for guest access to success screen
                session()->forget('cart');
                session()->put('checkout_order_id', $order->id);

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

    public function success(Order $order, \App\Services\AsaasService $asaasService)
    {
        $isAuthorized = (Auth::check() && $order->user_id === Auth::id())
            || (session('checkout_order_id') == $order->id)
            || (Auth::check() && method_exists(Auth::user(), 'hasRole') && (Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Manager')));

        if (!$isAuthorized) {
            abort(403, 'Acesso não autorizado a este pedido.');
        }

        $paymentData = session('payment_data');

        if (!$paymentData && $order->payment_method === 'pix' && $order->payment_id) {
            try {
                $pixData = $asaasService->getPixQrCode($order->payment_id);
                if ($pixData) {
                    $paymentData = [
                        'pix_qr_code' => $pixData['encodedImage'] ?? null,
                        'pix_copy_paste' => $pixData['payload'] ?? null,
                    ];
                }
            } catch (\Exception $e) {
                \Log::warning('Could not recover PIX data for order ' . $order->id . ': ' . $e->getMessage());
            }
        }

        $seo = \App\Services\SeoService::forPrivate('Pedido Confirmado #' . $order->order_number);

        return view('checkout.success', compact('order', 'paymentData', 'seo'));
    }

    public function checkStatus(Order $order, AsaasGateway $gateway)
    {
        $isAuthorized = (Auth::check() && $order->user_id === Auth::id())
            || (session('checkout_order_id') == $order->id)
            || (Auth::check() && method_exists(Auth::user(), 'hasRole') && (Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Manager')));

        if (!$isAuthorized) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // If already finalized in database, return immediately
        if (in_array($order->status, ['paid', 'completed', 'delivered', 'shipped'])) {
            return response()->json([
                'paid' => true,
                'status' => $order->status,
                'tracking_code' => $order->shipping_tracking_code,
                'label_url' => $order->shipping_label_url,
            ]);
        }

        if (!$order->payment_id) {
            return response()->json(['paid' => false]);
        }

        $payment = $gateway->getPayment($order->payment_id);

        if ($payment && in_array($payment['status'], ['RECEIVED', 'CONFIRMED'])) {
            OrderPaymentService::finalize($order);

            $order->refresh();

            return response()->json([
                'paid' => true,
                'status' => 'paid',
                'tracking_code' => $order->shipping_tracking_code,
                'label_url' => $order->shipping_label_url,
            ]);
        }

        return response()->json(['paid' => false, 'status' => $order->status]);
    }
}
