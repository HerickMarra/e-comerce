<?php

namespace App\Gateways;

use App\Interfaces\PaymentGatewayInterface;
use App\Models\Order;
use App\Services\AsaasService;

class AsaasGateway implements PaymentGatewayInterface
{
    protected $service;

    public function __construct(AsaasService $service)
    {
        $this->service = $service;
    }

    public function createPayment(Order $order, array $paymentData = [])
    {
        $billingType = match ($order->payment_method) {
            'pix' => 'PIX',
            'boleto' => 'BOLETO',
            'credit_card' => 'CREDIT_CARD',
            default => throw new \Exception('Método de pagamento inválido.'),
        };

        $creditCardInfo = null;
        if ($billingType === 'CREDIT_CARD') {
            $rawExpiry = trim($paymentData['card_expiry'] ?? '');
            $expiryParts = preg_split('/[\/\s-]+/', $rawExpiry);
            $month = str_pad($expiryParts[0] ?? '12', 2, '0', STR_PAD_LEFT);
            $yearRaw = $expiryParts[1] ?? date('y');
            $year = strlen($yearRaw) === 2 ? '20' . $yearRaw : $yearRaw;

            $creditCardInfo = [
                'holderName' => $paymentData['card_holder'] ?? ($order->user?->name ?? 'Cliente'),
                'number' => preg_replace('/\D/', '', $paymentData['card_number'] ?? ''),
                'expiryMonth' => $month,
                'expiryYear' => $year,
                'ccv' => $paymentData['card_cvv'] ?? '',
                'addressInfo' => $order->address_info
            ];
        }

        $response = $this->service->createPayment($order, $billingType, $creditCardInfo);

        // If PIX, we might need the QR code immediately
        if ($billingType === 'PIX' && isset($response['id'])) {
            $pixData = $this->service->getPixQrCode($response['id']);
            $response['pix_qr_code'] = $pixData['encodedImage'] ?? null;
            $response['pix_copy_paste'] = $pixData['payload'] ?? null;
        }

        return [
            'success' => true,
            'payment_id' => $response['id'],
            'status' => $response['status'],
            'invoice_url' => $response['invoiceUrl'] ?? null,
            'pix_qr_code' => $response['pix_qr_code'] ?? null,
            'pix_copy_paste' => $response['pix_copy_paste'] ?? null,
            'bank_slip_url' => $response['bankSlipUrl'] ?? null,
            'raw_response' => $response
        ];
    }

    public function getPayment($paymentId)
    {
        return $this->service->getPayment($paymentId);
    }

    public function refundPayment($paymentId, $amount, $reason)
    {
        return $this->service->refundPayment($paymentId, $amount, $reason);
    }
}
