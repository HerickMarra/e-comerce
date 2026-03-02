<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EnviaMaisService
{
    protected string $url;
    protected string $key;

    const URL_PROD = 'https://api.enviamais.com.br/api/partner/v1';
    const URL_HMG = 'https://hmg.enviamais.com.br/api/partner/v1';

    public function __construct()
    {
        $devMode = Setting::get('enviamais_dev_mode', 'production') === 'sandbox';
        $this->url = $devMode ? self::URL_HMG : self::URL_PROD;
        $this->key = Setting::get('enviamais_api_key', '');
    }

    /**
     * Simulate shipping – returns array of freight options (cotacoes).
     *
     * @param string $cepDestino  e.g. '01310100'
     * @param float  $totalValue  value of the goods
     * @param array  $volumes     [['peso_carga','altura_carga','largura_carga','comprimento_carga'], ...]
     */
    public function getShipping(string $cepDestino, float $totalValue, array $volumes): array
    {
        try {
            $payload = [
                'cep_destino' => preg_replace('/\D/', '', $cepDestino),
                'valor_carga' => round($totalValue, 2),
                'volumes' => $volumes,
            ];


            $response = $this->request()->post('/simulacao', $payload);
            if ($response->successful()) {
                $body = $response->json();
                $cotacoes = $body['cotacoes'] ?? [];

                // Attach the simulation ID to each option so we can use it when ordering
                return array_map(fn($c) => array_merge($c, ['simulacao_id' => $body['id'] ?? null]), $cotacoes);
            }

            Log::warning('EnviaMais getShipping failed', ['status' => $response->status(), 'body' => $response->body()]);
            return [];
        } catch (\Throwable $e) {
            Log::error('EnviaMais getShipping exception', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Create a shipment order in EnviaMais (called after payment confirmed).
     */
    public function makeOrder(array $data): ?array
    {
        try {
            $response = $this->request()->post('/pedido', $data);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('EnviaMais makeOrder failed', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('EnviaMais makeOrder exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Validate the token (used to confirm config is working).
     */
    public function validateToken(): bool
    {
        try {
            $response = $this->request()->get('/detalhes-token');
            $body = $response->json();
            return $response->successful() && !isset($body['error']) && ($body['data']['complete'] ?? false);
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function request()
    {
        return Http::withHeaders([
            'Api-Key' => $this->key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->baseUrl($this->url)->timeout(10);
    }
}
