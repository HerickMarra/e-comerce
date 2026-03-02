<?php

if (!defined('ABSPATH')) {
    exit;
}

class EnviaMaisAPI
{

    private $token;
    private $url;
    private $devMode;
    private $cubage = false;
    private $url_prod = 'https://app.enviamais.com.br/api/partner/v1';
    private $url_hmg = 'https://hmg.enviamais.com.br/api/partner/v1';
    private $url_cep = 'https://viacep.com.br/ws';

    public function __construct()
    {
        $this->token =  Utils::getOption('token_0');
        $this->devMode = Utils::getOption('dev_mode_1') != null;
        $this->url = $this->devMode ?  $this->url_hmg : $this->url_prod;
    }

    public function getShipping($shipment)
    {
        try {
            $response = wp_remote_post(
                $this->url . '/simulacao',
                [
                    'body' =>  json_encode($shipment),
                    'timeout'     => '5',
                    'redirection' => '5',
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => array(
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Api-Key' =>  $this->token
                    ),
                    'cookies' => array(),
                    'data_format' => 'body',
                ]
            );

            $response = json_decode($response['body']);

            if (isset($response->error)) {
                throw new Error("Envia Mais: $response->error");
            };

            return $response;
        } catch (\Throwable $th) {
            error_log(print_r($th, true));
            $this->storeErrors($th);
        }
    }

    public function makeOrder($shipment)
    {

        error_log(print_r($shipment, true));
        try {
            $response = wp_remote_post(
                $this->url . '/pedido',
                [
                    'body' =>  json_encode($shipment),
                    'timeout'     => '5',
                    'redirection' => '5',
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => array(
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Api-Key' =>  $this->token
                    ),
                    'cookies' => array(),
                    'data_format' => 'body',
                ]
            );
            $response = json_decode($response['body']);

            if (isset($response->error)) {
                throw new Error("Envia Mais: $response->error");
            };

            return $response;
        } catch (\Throwable $th) {
            error_log(print_r($th, true));
            $this->storeErrors($th);
        }
    }

    public function getRecipientLocation($cep)
    {
        try {
            $response = wp_remote_get(
                $this->url_cep . "/{$cep}/json/",
                [
                    'timeout'     => '5',
                    'redirection' => '5',
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => array(
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ),
                    'cookies' => array(),
                    'data_format' => 'body',
                ]
            );
            return json_decode($response['body']);
        } catch (\Throwable $th) {
            error_log(print_r($th, true));
            $this->storeErrors($th);
        }
    }

    public function getUserToken()
    {
        try {
            $response = wp_remote_get(
                $this->url . '/detalhes-token',
                [
                    'timeout'     => '5',
                    'redirection' => '5',
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => array(
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Api-Key' =>  $this->token
                    ),
                    'cookies' => array(),
                    'data_format' => 'body',
                ]
            );
            return json_decode($response['body']);
        } catch (\Throwable $th) {
            error_log(print_r($th, true));
            $this->storeErrors($th);
        }
    }

    public function storeErrors($erro)
    {
        try {
            $response = wp_remote_post(
                $this->url . '/woocomerce-plugin/erro',
                [
                    'body' =>  json_encode(['erro' => print_r($erro, true)]),
                    'timeout'     => '5',
                    'redirection' => '5',
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => array(
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                        'Api-Key' =>  $this->token
                    ),
                    'cookies' => array(),
                    'data_format' => 'body',
                ]
            );
            return json_decode($response['body']);
        } catch (\Throwable $th) {
            error_log($th);
        }
    }

    public function getCurrentVersion()
    {
        try {
            $response = wp_remote_get(
                $this->url . '/woocomerce-plugin/versao',
                [
                    'timeout'     => '5',
                    'redirection' => '5',
                    'httpversion' => '1.0',
                    'blocking'    => true,
                    'headers'     => array(
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ),
                    'cookies' => array(),
                    'data_format' => 'body',
                ]
            );
            
            $response = json_decode($response['body']);

            if (isset($response->error)) {
                throw new Error("Envia Mais: $response->error");
            };

            return $response;
        } catch (\Throwable $th) {
            error_log(print_r($th, true));
            $this->storeErrors($th);
        }
    }

    private function printConsoleError($error)
    {
        echo '<script>console.error("ENVIA MAIS error: ' . $error . '")</script>';
    }
}
