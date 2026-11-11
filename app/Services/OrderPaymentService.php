<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use App\Models\EmailTemplate;
use App\Mail\DynamicEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderPaymentService
{
    /**
     * Finalize order payment: update status, send confirmation email, and auto-recruit shipping.
     *
     * @param Order $order
     * @return bool
     */
    public static function finalize(Order $order): bool
    {
        // If already completed or paid, skip duplicate notification
        $wasAlreadyPaid = in_array($order->status, ['paid', 'completed', 'delivered', 'shipped']);

        if (!$wasAlreadyPaid) {
            $order->update([
                'status' => 'paid',
            ]);

            $currentUser = auth()->check() ? auth()->user() : null;
            $order->recordHistory(
                action: 'payment_confirmed',
                title: 'Pagamento Confirmado',
                description: 'Pagamento recebido e confirmado com sucesso no valor de R$ ' . number_format($order->total_amount, 2, ',', '.') . '.',
                metadata: [
                    'payment_id' => $order->payment_id,
                    'payment_method' => $order->payment_method,
                ],
                user: $currentUser,
                actorName: $currentUser ? $currentUser->name : 'Sistema (Gateway Asaas)'
            );

            Log::info("OrderPaymentService: Order #{$order->order_number} marked as PAID.");

            // Send Payment Confirmed Email
            try {
                $template = EmailTemplate::where('slug', 'payment-confirmed')->first();
                if ($template && $order->user) {
                    $user = $order->user;
                    $content = str_replace(
                        ['{name}', '{order_number}', '{status}'],
                        [$user->name, $order->order_number, 'paid'],
                        $template->content
                    );
                    Mail::to($user->email)->send(new DynamicEmail($template->subject, $content));
                    Log::info("OrderPaymentService: Payment confirmation email sent to {$user->email}.");
                }
            } catch (\Throwable $e) {
                Log::error("OrderPaymentService: Error sending payment confirmation email: " . $e->getMessage());
            }
        }

        // Automatically Recruit Shipping (Melhor Envio) if not pickup and not already recruited
        self::autoRecruitShipping($order);

        return true;
    }

    /**
     * Automatically recruit shipping on Melhor Envio when order is paid.
     *
     * @param Order $order
     * @return void
     */
    public static function autoRecruitShipping(Order $order): void
    {
        // Check if pickup at store
        $isPickup = str_contains(strtolower($order->shipping_modalidade ?? ''), 'retirad') || 
                    str_contains(strtolower($order->notes ?? ''), 'retirad') ||
                    ($order->shipping_amount == 0 && empty($order->shipping_simulacao_id));

        if ($isPickup) {
            Log::info("OrderPaymentService: Order #{$order->order_number} is store pickup. Skipping carrier label generation.");
            return;
        }

        if (empty($order->address_info)) {
            Log::warning("OrderPaymentService: Order #{$order->order_number} has no address_info. Cannot recruit shipping.");
            return;
        }

        $provider = Setting::get('shipping_provider', 'melhorenvio');
        $hasMelhorEnvioToken = !empty(Setting::get('melhorenvio_token'));

        if (!$hasMelhorEnvioToken && $provider !== 'melhorenvio') {
            return;
        }

        try {
            $melhorEnvio = new MelhorEnvioService();

            // If order already has shipping_id
            if (!empty($order->shipping_id)) {
                // If it already has label, ensure URL has ?public=true
                if (!empty($order->shipping_label_url)) {
                    if (!str_contains($order->shipping_label_url, 'public=')) {
                        $order->update([
                            'shipping_label_url' => MelhorEnvioService::formatPublicLabelUrl($order->shipping_label_url),
                        ]);
                    }
                    return;
                }

                // If no label yet, attempt checkout, generate, and fetch print URL
                $melhorEnvio->checkout([$order->shipping_id]);
                $melhorEnvio->generate([$order->shipping_id]);
                usleep(1500000); // 1.5s pause for async generation

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
                    Log::info("OrderPaymentService: Label successfully fetched for existing shipping_id #{$order->shipping_id}");
                }
                return;
            }

            // Create new cart order on Melhor Envio
            $cartResult = $melhorEnvio->createCartOrder($order);

            if ($cartResult && isset($cartResult['id'])) {
                $cartId = $cartResult['id'];
                Log::info("OrderPaymentService: Melhor Envio cart order created #{$cartId} for order #{$order->order_number}");

                // Automatic checkout (debits balance from Melhor Envio wallet)
                $checkoutResult = $melhorEnvio->checkout([$cartId]);
                $labelUrl = null;
                $trackingCode = null;

                if ($checkoutResult) {
                    $melhorEnvio->generate([$cartId]);
                    usleep(1500000); // 1.5s wait for asynchronous generation
                    $labelUrl = $melhorEnvio->printLabel([$cartId]);
                    if (!$labelUrl) {
                        usleep(2000000); // 2s retry
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
                    ],
                ]);

                Log::info("OrderPaymentService: Melhor Envio shipping recruited successfully for order #{$order->order_number}", [
                    'shipping_id' => $cartId,
                    'tracking' => $trackingCode,
                    'label_url' => $labelUrl,
                ]);

                $order->recordHistory(
                    action: 'shipping_label_created',
                    title: 'Etiqueta Contratada no Melhor Envio',
                    description: "Envio contratado no Melhor Envio (ID #{$cartId})" . ($trackingCode ? " • Rastreio: {$trackingCode}" : '') . ($labelUrl ? ' • Etiqueta disponível para impressão.' : ''),
                    metadata: [
                        'shipping_id' => $cartId,
                        'service' => $serviceName,
                        'tracking_code' => $trackingCode,
                        'label_url' => $labelUrl,
                    ],
                    user: auth()->check() ? auth()->user() : null,
                    actorName: auth()->check() ? auth()->user()->name : 'Sistema Automático'
                );
            } else {
                $err = $melhorEnvio->getLastError() ?: 'Falha na resposta da API do Melhor Envio';
                Log::warning("OrderPaymentService: Failed to create Melhor Envio cart order for order #{$order->order_number}: {$err}");

                $order->recordHistory(
                    action: 'shipping_label_failed',
                    title: 'Falha na Geração da Etiqueta',
                    description: "Erro retornado pela API do Melhor Envio: {$err}",
                    metadata: ['error' => $err],
                    user: null,
                    actorName: 'Sistema Automático'
                );
            }
        } catch (\Throwable $e) {
            Log::error("OrderPaymentService: Exception while auto-recruiting Melhor Envio shipping: " . $e->getMessage());
        }
    }
}

