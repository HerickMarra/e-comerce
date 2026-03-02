<?php

if (!defined('ABSPATH')) {
    exit;
}

class Recipient
{
    public function __construct($order)
    {
        $order_helper = new OrderHelper($order);
        $this->nome     = $order->get_formatted_shipping_full_name();
        $this->cnpjCpf  = Recipient::only_digits($order_helper->get_cpf_or_cnpj());
        $this->ie       = $order_helper->get_billing_ie();
        $this->endereco = $order->get_shipping_address_1();
        $this->numero   = $order_helper->get_shipping_number();
        $this->complemento    = $order->get_shipping_address_2();
        $this->bairro   = $order_helper->get_shipping_neighborhood();
        $this->cidade   = $order->get_shipping_city();
        $this->uf       = $order->get_shipping_state();
        $this->cep      = Recipient::only_digits($order->get_shipping_postcode());
        $this->telefone = Recipient::only_digits($order->get_billing_phone());
        $this->celular  = Recipient::only_digits($order_helper->get_billing_cellphone());
        $this->email    = $order->get_billing_email();
        $this->contato  = $order->get_formatted_billing_full_name();
    }

    public static function only_digits($string)
    {
        return preg_replace('/[^0-9]/', '', $string);
    }
}
