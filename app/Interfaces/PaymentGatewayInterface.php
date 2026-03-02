<?php

namespace App\Interfaces;

use App\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Create a payment for the given order.
     * 
     * @param Order $order
     * @param array $paymentData (credit card info, etc.)
     * @return array Response from the gateway
     */
    public function createPayment(Order $order, array $paymentData = []);

    /**
     * Get payment details from the gateway.
     * 
     * @param string $paymentId
     * @return array
     */
    public function getPayment($paymentId);

    /**
     * Refund a payment.
     * 
     * @param string $paymentId
     * @param float $amount
     * @param string $reason
     * @return array
     */
    public function refundPayment($paymentId, $amount, $reason);
}
