<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\DynamicEmail;
use App\Models\EmailTemplate;
use App\Services\OrderPaymentService;

class AsaasWebhookController extends Controller
{
    /**
     * Map Asaas event names → our Order status values.
     */
    protected const STATUS_MAP = [
        'PAYMENT_CONFIRMED' => 'paid',
        'PAYMENT_RECEIVED' => 'paid',
        'PAYMENT_APPROVED_BY_RISK_ANALYSIS' => 'paid',
        'PAYMENT_OVERDUE' => 'overdue',
        'PAYMENT_DELETED' => 'cancelled',
        'PAYMENT_CHARGEBACK_REQUESTED' => 'chargeback',
        'PAYMENT_CHARGEBACK_DISPUTE' => 'chargeback',
        'PAYMENT_AWAITING_CHARGEBACK_REVERSAL' => 'chargeback',
        'PAYMENT_DUNNING_RECEIVED' => 'paid',
        'PAYMENT_REFUNDED' => 'refunded',
        'PAYMENT_PARTIALLY_REFUNDED' => 'refunded',
        'PAYMENT_REPROVED_BY_RISK_ANALYSIS' => 'cancelled',
        'PAYMENT_RESTORED' => 'pending',
    ];

    public function handle(Request $request)
    {
        Log::info('Asaas Webhook Received', [
            'event' => $request->input('event'),
            'payment' => $request->input('payment.id'),
        ]);

        // Optional: validate webhook token
        $webhookToken = Setting::get('asaas_webhook_token');
        if ($webhookToken) {
            $receivedToken = $request->header('asaas-access-token')
                ?? $request->header('access-token')
                ?? $request->query('token');

            if ($receivedToken !== $webhookToken) {
                Log::warning('Asaas Webhook: invalid token');
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        $event = $request->input('event');
        $payment = $request->input('payment', []);

        if (empty($event) || empty($payment)) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $newStatus = self::STATUS_MAP[$event] ?? null;

        if (!$newStatus) {
            // Unknown event – acknowledge and ignore
            Log::info("Asaas Webhook: unhandled event [{$event}]");
            return response()->json(['status' => 'ignored']);
        }

        // Resolve the order via externalReference (our order primary key)
        $orderId = $payment['externalReference'] ?? null;
        $paymentId = $payment['id'] ?? null;

        $order = null;

        if ($orderId) {
            $order = Order::find($orderId);
        }

        // Fallback: look up by payment_id stored on order
        if (!$order && $paymentId) {
            $order = Order::where('payment_id', $paymentId)->first();
        }

        if (!$order) {
            Log::warning('Asaas Webhook: order not found', [
                'externalReference' => $orderId,
                'payment_id' => $paymentId,
            ]);
            return response()->json(['error' => 'Order not found'], 404);
        }

        $previousStatus = $order->status;

        if ($newStatus === 'paid') {
            $order->update([
                'payment_id' => $paymentId ?? $order->payment_id,
            ]);
            OrderPaymentService::finalize($order);
        } else {
            // If order was cancelled, refunded, or overdue, and was not previously cancelled, restore inventory
            if (in_array($newStatus, ['cancelled', 'refunded', 'overdue']) && !in_array($previousStatus, ['cancelled', 'refunded', 'overdue'])) {
                try {
                    $order->restoreStock();
                    Log::info("Asaas Webhook: Stock restored for cancelled/refunded order #{$order->order_number}");
                } catch (\Throwable $e) {
                    Log::error("Asaas Webhook: Failed to restore stock for order #{$order->order_number}: " . $e->getMessage());
                }
            }

            $order->update([
                'status' => $newStatus,
                'payment_id' => $paymentId ?? $order->payment_id,
            ]);

            // Record History from Webhook
            $action = match ($newStatus) {
                'refunded' => 'refund_processed',
                'cancelled' => 'status_changed',
                default => 'status_changed',
            };
            $title = match ($newStatus) {
                'refunded' => 'Estorno / Reembolso Confirmado',
                'cancelled' => 'Pedido Cancelado pelo Gateway',
                default => "Status Atualizado via Webhook ({$newStatus})",
            };
            $description = match ($newStatus) {
                'refunded' => "Estorno de pagamento processado e confirmado no Asaas via Webhook (Evento: {$event}).",
                'cancelled' => "Pedido cancelado pelo gateway Asaas (Evento: {$event}).",
                default => "Status alterado de '{$previousStatus}' para '{$newStatus}' pelo Webhook do Asaas (Evento: {$event}).",
            };

            $order->recordHistory(
                action: $action,
                title: $title,
                description: $description,
                metadata: [
                    'event' => $event,
                    'payment_id' => $paymentId,
                    'from' => $previousStatus,
                    'to' => $newStatus,
                ],
                user: null,
                actorName: 'Webhook Asaas'
            );
        }

        Log::info('Asaas Webhook: order updated', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'event' => $event,
            'from' => $previousStatus,
            'to' => $newStatus,
        ]);

        return response()->json(['status' => 'ok', 'order' => $order->order_number]);
    }
}
