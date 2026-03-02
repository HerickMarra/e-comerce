<?php

function envia_mais_custom_meta_box()
{

  global $woocommerce, $post;

  $order = wc_get_order($post->ID);
 
  if(!$order){
    return;
  }

  $order_data = $order->get_meta('envia_mais');

  if(!$order_data){
    return;
  }

  add_meta_box(
    'woocommerce-order-envia-mais',
    __('Envia Mais'),
    function () use ($order,$order_data) {
      echo envia_mais_generate_metabox($order,$order_data);
    },
    'shop_order',
    'side',
    'default'
  );

}

function envia_mais_generate_metabox($order,$order_data)
{
  
  $vars = [
    '$id' => $order_data['shipment']->id,
    '$value' => $order->get_shipping_total(),
    '$url' => Utils::getOption('dev_mode_1') != null ?   'https://hmg.enviamais.com.br/cotation/shipment/' . $order_data['shipment']->id : 'https://app.enviamais.com.br/cotation/shipment/' . $order_data['shipment']->id,
    '$type' => $order_data['cotation']->cotacao->destaque ==  'prazo' ? 'Prazo': 'Preço'
  ];

  $template = '
    <ul class="order_actions">
      <li class="wide">
        <p>Código: <b>#$id</b></p>
        <p>Tipo: <b>Melhor $type</b></p>
        <p>Valor: <b>R$: $value</b></p>
      </li>
      <li class="wide" style="border-bottom: 0px solid #ddd">
        <button 
          class="button save_order button-primary" 
          onclick="window.open(`$url`, `_blank`).focus();"
          style="background:#75CD25 !important;border: 1px solid #75CD25 !important"
        >Abrir Cotação</button>
      </li>      
    </ul>
  ';

  return strtr($template, $vars);
}

add_action('add_meta_boxes', 'envia_mais_custom_meta_box');
