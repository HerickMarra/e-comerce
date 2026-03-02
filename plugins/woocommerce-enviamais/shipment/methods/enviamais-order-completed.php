<?php

if (!defined('ABSPATH')) {
    exit;
}

function truncate($text, $chars = 80) {
    if (strlen($text) <= $chars) {
        return $text;
    }
    $text = $text." ";
    $text = substr($text,0,$chars);
    $text = substr($text,0,strrpos($text,' '));
    $text = $text."...";
    return $text;
}

function enviamais_make_order($order_id)
{
    $api = new EnviaMaisAPI();
    $order = new WC_Order($order_id);
    $recipient = new Recipient($order);
    $shipmentMetaData = null;

    if (!$recipient->bairro) {
        $location = $api->getRecipientLocation($recipient->cep);
        $recipient->bairro = $location->bairro;
    }

    $itemNames = "";
    $selectedShippingName = $order->get_shipping_method();

    if (strpos($selectedShippingName, 'Envia Mais') === false) {
        return;
    }

    foreach ($order->get_items() as $item) {
        $itemData = $item->get_data();

        if ($itemData['name']) {
            $itemNames = $itemNames . $itemData['name'] . ',';
        }
    }

    $shipmentMetaData = null;
    $paymentDate = null;

    foreach ($order->get_items('shipping') as $item_id => $item) {
        $meta_data = $item->get_meta_data();
        $shipmentOrderData = $meta_data[0]->get_data();
        $shipmentMetaData = $shipmentOrderData['value'];
        $paymentDate = $order->get_date_paid();
    }
    
    $cotation = $item->get_meta_data()[1]->get_data()['value'];
     

    $data = [
        "simulacao_id" => $shipmentMetaData->id,
        "modalidade" => $cotation->modalidade,
        "declaracao_conteudo" => 1,
        "descricao_conteudo" => truncate($itemNames,60),
        "valor_pagamento" => $order->get_shipping_total(),
        "data_pagamento" => $paymentDate ? $paymentDate->format('Y-m-d H:i:s') : null,
        "forma_pagamento" => 2,
        "destinatario" => [
            "bairro" => $recipient->bairro,
            "cep" =>  $recipient->cep,
            "cidade" =>  $recipient->cidade,
            "cnpjCpf" => $recipient->cnpjCpf,
            "email" => $recipient->email,
            "endereco" => $recipient->endereco,
            "telefone" => $recipient->telefone,
            "nome" => $recipient->nome,
            "uf" => $recipient->uf,
            "inscricao" => $recipient->ie,
            "celular" => $recipient->celular,
            "complemento" => $recipient->complemento,
            "numero" => $recipient->numero,
        ],
    ];

    try {
        $response = $api->makeOrder($data);

        if (isset($response->errors)) {
            error_log(print_r($response->errors, true));
            $response = $api->storeErrors($response->errors);
            return;
        }

        $order->update_meta_data('envia_mais', ['shipment' => $response, 'cotation' => $shipmentMetaData]);
        $order->save();
    } catch (\Throwable $th) {
        error_log(print_r($th, true));
        $response = $api->storeErrors($th);
    }
}


add_action("woocommerce_order_status_processing", 'enviamais_make_order');
