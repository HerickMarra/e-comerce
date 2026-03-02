<?php

if (!defined('ABSPATH')) {
    exit;
}


if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    function envia_mais_shipping_init()
    {
        if (!class_exists('EnviaMaisShippingMethod')) {
            class EnviaMaisShippingMethod extends WC_Shipping_Method
            {
                public function __construct()
                {
                    $this->id = 'env_mais_ship_meth';
                    $this->title = __('Envia Mais', $this->id);
                    $this->method_title = __('Envia Mais', $this->id);
                    $this->init();

                    $this->enabled = $this->enablePlugin();
                    $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Envia Mais', $this->id);
                }

                private function enablePlugin()
                {
                    $api = new EnviaMaisAPI();
                    $request = $api->getUserToken();


                    if (isset($request->error)) {
                        return 'no';
                    }

                    $user = isset($request->data) ? $request->data : null;


                    if (!$user || !$user->complete) {
                        return 'no';
                    }

                    return 'yes';
                }

                public function init()
                {
                    // $this->init_form_fields();
                    $this->init_settings();

                    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
                }

                public function init_form_fields()
                {
                    $this->form_fields  = [
                        'enabled' => [
                            'title' => __('Ativar Cotação Envia Mais'),
                            'type'  => 'checkbox',
                            'label'  => __('Ativar a Extensão Envia Mais (Apenas ativará se seu token estiver válido e seu Usuário estiver com o cadastro completo)'),
                            'default' => 'no',
                        ],

                    ];

                    $this->enabled = $this->get_option('enabled');
                }

                public function calculate_shipping($packages = [])
                {
                    try {
                        global $woocommerce;
                        $api = new EnviaMaisAPI();

                        $postcode = $woocommerce->customer->get_shipping_postcode();
                        
                        if (empty($postcode) || empty($packages)) {
                            return;
                        
                        }
                        
                        $postcode = str_replace('-', '', $postcode);

                        $price = 0.0;
                        $volumes = [];

                        foreach ($packages['contents'] as $content) {
                            $item = $content['data'];
                            
                            for ($i = 0; $i < $content['quantity']; $i++) {
                                try {
                                    $price = num_format($price + (float) $item->get_price(), 2, 2);
                                    $volumes[] = [
                                        'peso_carga' => num_format((float) $item->get_weight(), 3, 2),
                                        'altura_carga' => num_format((float)$item->get_height(), 3, 2),
                                        'largura_carga'=> num_format((float) $item->get_width(), 3, 2),
                                        'comprimento_carga' => num_format((float)$item->get_length(), 3, 2)
                                    ];
                                } catch (\Throwable $th) {
                                    error_log($th);
                                    $api->storeErrors($th);
                                }
                            }
                        }

                        $data = $api->getShipping([
                            'cep_destino' => $postcode,
                            'valor_carga' => $price,
                            'volumes' => $volumes,
                        ]);

                        $cotacoes = $data->cotacoes;

                        if (!$cotacoes) {
                            error_log('A Api Envia Mais não trouxe nenhum retorno');
                            $api->storeErrors('A Api Envia Mais não trouxe nenhum retorno');
                            return;
                        }

                        foreach ($cotacoes as $key => $cotacao) {
                            $shipmentData = $data;
                            $shipmentData->{'cotacao'} = $cotacao;
                            $destaque = $cotacao->destaque  == 'preco' ? 'Preço' : 'Prazo';

                            $rate = [
                                'id'       =>  $this->id . "[$key]",
                                'label'    => 'Envia Mais ('.$cotacao->prazo.' Dias Úteis)',
                                'cost'     =>  $cotacao->valor,
                                'calc_tax' => 'per_order',
                                'meta_data' => [
                                    'simulation' => $shipmentData,
                                    'cotation' => $cotacao
                                ]
                            ];

                            $this->add_rate($rate);
                        }
                    } catch (\Throwable $th) {
                        error_log($th);
                        $api->storeErrors($th);
                    }
                }
            }
        }
    }



    add_action('woocommerce_shipping_init', 'envia_mais_shipping_init');

    function add_envia_mais_shipment($methods)
    {
        $methods['env_mais_ship'] = 'EnviaMaisShippingMethod';
        return $methods;
    }

    function num_format($numVal, $afterPoint = 2, $minAfterPoint = 0, $thousandSep = ",", $decPoint = ".")
    {
        $ret = number_format($numVal, $afterPoint, $decPoint, $thousandSep);
        if ($afterPoint != $minAfterPoint) {
            while (($afterPoint > $minAfterPoint) && (substr($ret, -1) == "0")) {
                $ret = substr($ret, 0, -1);
                $afterPoint = $afterPoint - 1;
            }
        }
        if (substr($ret, -1) == $decPoint) {
            $ret = substr($ret, 0, -1);
        }
        return $ret;
    }

    add_filter('woocommerce_shipping_methods', 'add_envia_mais_shipment');
}
