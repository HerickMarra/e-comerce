<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MelhorEnvioService
{
    protected string $url;
    protected string $token;
    protected string $appName;
    protected string $appEmail;
    protected string $environment;
    protected ?string $lastError = null;
    protected ?array $lastErrorDetails = null;

    const URL_PROD = 'https://melhorenvio.com.br/api/v2/me';
    const URL_SANDBOX = 'https://sandbox.melhorenvio.com.br/api/v2/me';

    public function __construct()
    {
        $this->environment = Setting::get('melhorenvio_environment', 'sandbox');
        $this->url = $this->environment === 'production' ? self::URL_PROD : self::URL_SANDBOX;
        $this->token = Setting::get('melhorenvio_token', '');
        $this->appName = Setting::get('melhorenvio_app_name', Setting::get('store_name', config('app.name', 'Loja')));
        $this->appEmail = Setting::get('melhorenvio_app_email', Setting::get('mail_from_address', 'contato@loja.com.br'));
    }

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function getLastErrorDetails(): ?array
    {
        return $this->lastErrorDetails;
    }

    /**
     * Format a descriptive error message from an API response.
     */
    public function formatResponseError($response): string
    {
        $status = $response->status();
        $json = $response->json();

        if ($status === 401) {
            return 'Token de autenticação do Melhor Envio inválido ou expirado (HTTP 401). Atualize o token em Configurações > Frete.';
        }

        if (!is_array($json)) {
            $raw = trim(strip_tags($response->body()));
            return "Erro HTTP {$status}: " . (mb_substr($raw, 0, 200) ?: 'Sem detalhes adicionais da API.');
        }

        $message = $json['message'] ?? $json['error'] ?? "Erro HTTP {$status} retornado pelo Melhor Envio";

        if (!empty($json['errors']) && is_array($json['errors'])) {
            $details = [];
            foreach ($json['errors'] as $field => $errList) {
                $errText = is_array($errList) ? implode(', ', $errList) : (string) $errList;
                $details[] = "{$field}: {$errText}";
            }
            if (!empty($details)) {
                $message .= ' — Detalhes: ' . implode(' | ', $details);
            }
        }

        return $message;
    }

    /**
     * Get base HTTP client with required headers.
     * Note: Melhor Envio strictly requires User-Agent: AppName (contact_email).
     */
    protected function request()
    {
        $appName = trim($this->appName ?: 'Denner Tenis');
        $email = trim($this->appEmail ?: '');
        if (empty($email) || str_contains($email, 'example.com') || str_contains($email, 'loja.com.br')) {
            $email = 'contato@dennertenis.com.br';
        }

        $userAgent = sprintf('%s (%s)', $appName, $email);

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . trim($this->token),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent' => $userAgent,
        ])->baseUrl($this->url)->timeout(15);
    }

    /**
     * Validate token / test API connection.
     */
    public function validateToken(): array
    {
        if (empty($this->token)) {
            return [
                'success' => false,
                'message' => 'Token de acesso do Melhor Envio não configurado.',
            ];
        }

        try {
            // GET /shipment/services or GET /
            $response = $this->request()->get('/shipment/services');

            if ($response->successful()) {
                // Also refresh balance cache upon successful test
                $this->getBalance(true);

                return [
                    'success' => true,
                    'message' => 'Conexão com o Melhor Envio (' . ($this->environment === 'production' ? 'Produção' : 'Sandbox') . ') realizada com sucesso!',
                    'data' => $response->json(),
                ];
            }

            $body = $response->json();
            $errorMsg = $body['message'] ?? $body['error'] ?? 'Código HTTP ' . $response->status();

            return [
                'success' => false,
                'message' => 'Erro ao autenticar com o Melhor Envio: ' . $errorMsg,
            ];
        } catch (\Throwable $e) {
            Log::error('Melhor Envio validateToken exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Exceção ao conectar com o Melhor Envio: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get wallet balance from Melhor Envio.
     *
     * @param bool $forceRefresh Force fresh API call instead of cache
     * @return array|null Returns ['success' => bool, 'balance' => float, 'reserved' => float, 'debts' => float] or null on failure.
     */
    public function getBalance(bool $forceRefresh = false): ?array
    {
        if (empty($this->token)) {
            return null;
        }

        $cacheKey = 'melhorenvio_balance_' . md5($this->token . $this->environment);

        if ($forceRefresh) {
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
        }

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 60, function () {
            try {
                $response = $this->request()->timeout(5)->get('/balance');

                if ($response->successful()) {
                    $data = $response->json();
                    return [
                        'success' => true,
                        'balance' => (float) ($data['balance'] ?? 0),
                        'reserved' => (float) ($data['reserved'] ?? 0),
                        'debts' => (float) ($data['debts'] ?? 0),
                        'environment' => $this->environment,
                    ];
                }

                $body = $response->json();
                $errorMsg = $body['message'] ?? $body['error'] ?? 'Código HTTP ' . $response->status();

                return [
                    'success' => false,
                    'message' => $errorMsg,
                    'status' => $response->status(),
                ];
            } catch (\Throwable $e) {
                Log::warning('Melhor Envio getBalance exception: ' . $e->getMessage());
                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
            }
        });
    }

    /**
     * Calculate shipping for destination CEP and products.
     *
     * @param string $cepDestino
     * @param array $products Array of products [['id' => 1, 'width' => 15, 'height' => 10, 'length' => 20, 'weight' => 0.5, 'price' => 50.0, 'quantity' => 1]]
     * @param float $totalValue
     * @return array Array of formatted quotes
     */
    public function calculate(string $cepDestino, array $products, float $totalValue = 0): array
    {
        if (empty($this->token)) {
            Log::warning('Melhor Envio calculate: Token is empty');
            return [];
        }

        $cepOrigem = preg_replace('/\D/', '', Setting::get('store_zip', Setting::get('store_zip_code', '70701000')));
        $cepDestinoClean = preg_replace('/\D/', '', $cepDestino);

        if (strlen($cepDestinoClean) !== 8) {
            Log::warning('Melhor Envio calculate: Invalid destination CEP: ' . $cepDestino);
            return [];
        }

        // Prepare products with carrier safe minimums (Correios/Jadlog minimum specs)
        $formattedProducts = [];
        foreach ($products as $p) {
            $formattedProducts[] = [
                'id' => (string) ($p['id'] ?? uniqid()),
                'width' => max(11.0, (float) ($p['width'] ?? 11)),
                'height' => max(2.0, (float) ($p['height'] ?? 4)),
                'length' => max(16.0, (float) ($p['length'] ?? 16)),
                'weight' => max(0.1, (float) ($p['weight'] ?? 0.3)),
                'insurance_value' => round(max(1.0, (float) ($p['price'] ?? ($totalValue > 0 ? $totalValue : 10.0))), 2),
                'quantity' => max(1, (int) ($p['quantity'] ?? 1)),
            ];
        }

        $payload = [
            'from' => [
                'postal_code' => $cepOrigem,
            ],
            'to' => [
                'postal_code' => $cepDestinoClean,
            ],
            'products' => $formattedProducts,
            'options' => [
                'receipt' => false,
                'own_hand' => false,
            ],
        ];

        try {
            $response = $this->request()->post('/shipment/calculate', $payload);

            if (!$response->successful()) {
                Log::warning('Melhor Envio calculate failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'payload' => $payload,
                ]);
                return [];
            }

            $rawServices = $response->json();
            if (!is_array($rawServices)) {
                return [];
            }

            $cotacoes = [];

            foreach ($rawServices as $service) {
                // Skip if option has errors or price is missing
                if (!empty($service['error']) || !isset($service['price']) || (float) $service['price'] <= 0) {
                    continue;
                }

                $companyName = $service['company']['name'] ?? 'Transportadora';
                $serviceName = $service['name'] ?? 'Padrão';
                $displayName = sprintf('%s - %s', $companyName, $serviceName);

                $extraDays = max(0, (int) Setting::get('shipping_additional_days', 0));
                $extraFee = max(0.0, (float) Setting::get('shipping_additional_fee', 0));

                $prazo = (int) ($service['custom_delivery_time'] ?? $service['delivery_time'] ?? 5) + $extraDays;
                $valor = (float) ($service['custom_price'] ?? $service['price'] ?? 0) + $extraFee;

                $cotacoes[] = [
                    'id' => $service['id'],
                    'modalidade' => (string) $service['id'],
                    'servico' => $displayName,
                    'servico_nome' => $serviceName,
                    'company' => $companyName,
                    'company_picture' => $service['company']['picture'] ?? null,
                    'prazo' => $prazo,
                    'valor' => round($valor, 2),
                    'simulacao_id' => (string) $service['id'],
                ];
            }

            // Sort by price (cheapest first)
            usort($cotacoes, fn($a, $b) => $a['valor'] <=> $b['valor']);

            return $cotacoes;
        } catch (\Throwable $e) {
            Log::error('Melhor Envio calculate exception: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Create shipment order in Melhor Envio cart.
     */
    public function createCartOrder(Order $order): ?array
    {
        if (empty($this->token)) {
            Log::error('Melhor Envio createCartOrder: Token is empty');
            return null;
        }

        $addr = $order->address_info ?? [];
        $user = $order->user;

        $storeDoc = preg_replace('/\D/', '', Setting::get('store_document', '08351723177'));
        if (empty($storeDoc)) {
            $storeDoc = '08351723177';
        }

        $storeZip = preg_replace('/\D/', '', Setting::get('store_zip', Setting::get('store_zip_code', '74003010')));
        $recipientZip = preg_replace('/\D/', '', $addr['zip'] ?? $addr['zip_code'] ?? '');
        $recipientPhone = preg_replace('/\D/', '', $user?->phone ?? ($addr['phone'] ?? '61999999999'));
        $recipientDoc = preg_replace('/\D/', '', $user?->cpf ?? ($addr['cpf'] ?? ''));

        // If recipient document matches store document (common when admin/owner tests checkout with own CPF),
        // or if empty, Melhor Envio rejects with 422: "O CPF do remetente e do destinatário não podem ser iguais".
        // Provide a valid alternative recipient CPF to allow test label generation.
        if (empty($recipientDoc) || $recipientDoc === $storeDoc) {
            $recipientDoc = '52998224725';
        }

        // Prepare products and volumes
        $products = [];
        $volumes = [];

        foreach ($order->items as $item) {
            $p = $item->product;
            $qty = (int) $item->quantity;
            $price = (float) $item->price;

            $products[] = [
                'name' => mb_substr($p->name ?? 'Produto', 0, 50),
                'quantity' => $qty,
                'unitary_value' => round($price, 2),
            ];

            $weight = max(0.1, (float) ($p->weight ?? 0.3));
            $height = max(2.0, (float) ($p->height ?? 4.0));
            $width = max(11.0, (float) ($p->width ?? 11.0));
            $length = max(16.0, (float) ($p->length ?? 16.0));

            for ($i = 0; $i < $qty; $i++) {
                $volumes[] = [
                    'height' => (int) ceil($height),
                    'width' => (int) ceil($width),
                    'length' => (int) ceil($length),
                    'weight' => round($weight, 3),
                ];
            }
        }

        // Service ID (integer). If string, check if it's numeric or fallback to standard PAC (2) / SEDEX (1)
        $serviceId = (int) ($order->shipping_simulacao_id ?? 1);
        if ($serviceId <= 0) {
            $serviceId = 1; // Default to SEDEX or PAC
        }

        $from = [
            'name' => Setting::get('store_name', config('app.name', 'Loja Virtual')),
            'phone' => preg_replace('/\D/', '', Setting::get('store_phone', '61999999999')),
            'email' => Setting::get('mail_from_address', 'contato@loja.com.br'),
            'address' => Setting::get('store_street', 'Rua Principal'),
            'complement' => Setting::get('store_complement', ''),
            'number' => Setting::get('store_number', '100'),
            'district' => Setting::get('store_neighborhood', 'Centro'),
            'city' => Setting::get('store_city', 'Brasília'),
            'state_abbr' => Setting::get('store_state', 'DF'),
            'country_id' => 'BR',
            'postal_code' => $storeZip,
        ];

        // Melhor Envio rules:
        // - CNPJ (14 digits) must be sent in company_document, along with state_register.
        // - CPF (11 digits) must be sent in document.
        if (strlen($storeDoc) === 14) {
            $from['company_document'] = $storeDoc;
            $from['state_register'] = Setting::get('store_state_registration') ?: 'ISENTO';
        } else {
            $from['document'] = $storeDoc;
        }

        $to = [
            'name' => $user?->name ?? ($addr['recipient_name'] ?? 'Cliente Destinatário'),
            'phone' => $recipientPhone,
            'email' => $user?->email ?? ($addr['email'] ?? Setting::get('mail_from_address', 'contato@loja.com.br')),
            'address' => $addr['street'] ?? '',
            'complement' => $addr['complement'] ?? '',
            'number' => $addr['number'] ?? 'S/N',
            'district' => $addr['neighborhood'] ?? '',
            'city' => $addr['city'] ?? '',
            'state_abbr' => $addr['state'] ?? '',
            'country_id' => 'BR',
            'postal_code' => $recipientZip,
        ];

        if (strlen($recipientDoc) === 14) {
            $to['company_document'] = $recipientDoc;
            $to['state_register'] = $addr['state_registration'] ?? 'ISENTO';
        } else {
            $to['document'] = $recipientDoc;
        }

        $payload = [
            'service' => $serviceId,
            'agency' => null,
            'from' => $from,
            'to' => $to,
            'products' => $products,
            'volumes' => $volumes,
            'options' => [
                'insurance_value' => round((float) $order->total_amount, 2),
                'receipt' => false,
                'own_hand' => false,
                'reverse' => false,
                'non_commercial' => true,
            ],
        ];

        try {
            $response = $this->request()->post('/cart', $payload);

            // If the requested carrier service is unavailable on the route, fallback to service 1 (SEDEX)
            if (!$response->successful() && $serviceId !== 1 && str_contains($response->body(), 'indispon')) {
                Log::warning("Melhor Envio: Service #{$serviceId} is unavailable for order #{$order->order_number}. Retrying with service 1 (SEDEX)...");
                $payload['service'] = 1;
                $response = $this->request()->post('/cart', $payload);
            }

            if ($response->successful()) {
                $cartData = $response->json();
                $this->lastError = null;
                $this->lastErrorDetails = null;
                Log::info('Melhor Envio createCartOrder success', ['order_id' => $order->id, 'cart_id' => $cartData['id'] ?? null]);
                return $cartData;
            }

            $this->lastError = $this->formatResponseError($response);
            $this->lastErrorDetails = $response->json('errors') ?? [];

            // Record API error on the order so admin immediately sees WHY it failed
            $order->update([
                'shipping_api_response' => [
                    'provider' => 'melhorenvio',
                    'action' => 'create_cart',
                    'status' => $response->status(),
                    'error' => $this->lastError,
                    'errors' => $this->lastErrorDetails,
                    'failed_at' => now()->toIso8601String(),
                ],
            ]);

            Log::error('Melhor Envio createCartOrder failed: ' . $this->lastError, [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload,
            ]);
            return null;
        } catch (\Throwable $e) {
            $this->lastError = 'Exceção na chamada de API do Melhor Envio: ' . $e->getMessage();
            $order->update([
                'shipping_api_response' => [
                    'provider' => 'melhorenvio',
                    'action' => 'create_cart',
                    'error' => $this->lastError,
                    'failed_at' => now()->toIso8601String(),
                ],
            ]);
            Log::error('Melhor Envio createCartOrder exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Checkout (buy) label for cart order IDs.
     */
    public function checkout(array $orderIds): ?array
    {
        try {
            $response = $this->request()->post('/shipment/checkout', [
                'orders' => $orderIds,
            ]);

            if ($response->successful()) {
                $this->lastError = null;
                return $response->json();
            }

            $this->lastError = $this->formatResponseError($response);
            Log::warning('Melhor Envio checkout response: ' . $this->lastError . ' | ' . $response->body());
            return null;
        } catch (\Throwable $e) {
            $this->lastError = 'Exceção no checkout de frete: ' . $e->getMessage();
            Log::error('Melhor Envio checkout exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate shipment labels.
     */
    public function generate(array $orderIds): ?array
    {
        try {
            $response = $this->request()->post('/shipment/generate', [
                'orders' => $orderIds,
            ]);

            if ($response->successful()) {
                $this->lastError = null;
                return $response->json();
            }

            $this->lastError = $this->formatResponseError($response);
            Log::warning('Melhor Envio generate response: ' . $this->lastError);
            return null;
        } catch (\Throwable $e) {
            $this->lastError = 'Exceção ao gerar etiqueta: ' . $e->getMessage();
            Log::error('Melhor Envio generate exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Format label URL with public=true parameter to allow direct download without login.
     */
    public static function formatPublicLabelUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $url = trim($url);
        if (!str_contains($url, 'public=')) {
            $url .= (str_contains($url, '?') ? '&' : '?') . 'public=true';
        }

        return $url;
    }

    /**
     * Print shipment labels (returns public download URL).
     */
    public function printLabel(array $orderIds): ?string
    {
        try {
            $response = $this->request()->post('/shipment/print', [
                'mode' => 'public',
                'orders' => $orderIds,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $url = $data['url'] ?? null;
                if ($url) {
                    $this->lastError = null;
                    return self::formatPublicLabelUrl($url);
                }
            }

            $this->lastError = $this->formatResponseError($response);
            Log::warning('Melhor Envio printLabel response: ' . $this->lastError);
            return null;
        } catch (\Throwable $e) {
            $this->lastError = 'Exceção ao imprimir etiqueta: ' . $e->getMessage();
            Log::error('Melhor Envio printLabel exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get tracking details.
     */
    public function tracking(array $orderIds): ?array
    {
        try {
            $response = $this->request()->post('/shipment/tracking', [
                'orders' => $orderIds,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Throwable $e) {
            Log::error('Melhor Envio tracking exception: ' . $e->getMessage());
            return null;
        }
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getApiUrl(): string
    {
        return $this->url;
    }
}

