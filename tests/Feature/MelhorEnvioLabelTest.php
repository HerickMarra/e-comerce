<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Setting;
use App\Models\User;
use App\Services\MelhorEnvioService;
use App\Services\OrderPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MelhorEnvioLabelTest extends TestCase
{
    use RefreshDatabase;

    public function test_format_public_label_url_appends_parameter_correctly()
    {
        $rawUrl = 'https://sandbox.melhorenvio.com.br/portal/imprimir/rGdJWYAylMDW';
        $formatted = MelhorEnvioService::formatPublicLabelUrl($rawUrl);
        $this->assertEquals('https://sandbox.melhorenvio.com.br/portal/imprimir/rGdJWYAylMDW?public=true', $formatted);

        $withQuery = 'https://sandbox.melhorenvio.com.br/portal/imprimir/rGdJWYAylMDW?layout=default';
        $formattedWithQuery = MelhorEnvioService::formatPublicLabelUrl($withQuery);
        $this->assertEquals('https://sandbox.melhorenvio.com.br/portal/imprimir/rGdJWYAylMDW?layout=default&public=true', $formattedWithQuery);

        $alreadyPublic = 'https://sandbox.melhorenvio.com.br/portal/imprimir/rGdJWYAylMDW?public=true';
        $formattedAlready = MelhorEnvioService::formatPublicLabelUrl($alreadyPublic);
        $this->assertEquals('https://sandbox.melhorenvio.com.br/portal/imprimir/rGdJWYAylMDW?public=true', $formattedAlready);

        $this->assertNull(MelhorEnvioService::formatPublicLabelUrl(null));
        $this->assertNull(MelhorEnvioService::formatPublicLabelUrl(''));
    }

    public function test_order_model_mutator_and_accessor_ensure_public_parameter()
    {
        $order = Order::create([
            'order_number' => 'ORD-TEST-1234',
            'status' => 'pending',
            'subtotal' => 100.00,
            'shipping_amount' => 20.00,
            'total_amount' => 120.00,
            'shipping_label_url' => 'https://sandbox.melhorenvio.com.br/portal/imprimir/rGdJWYAylMDW',
        ]);

        $this->assertEquals(
            'https://sandbox.melhorenvio.com.br/portal/imprimir/rGdJWYAylMDW?public=true',
            $order->shipping_label_url
        );

        $this->assertEquals(
            'https://sandbox.melhorenvio.com.br/portal/imprimir/rGdJWYAylMDW?public=true',
            $order->public_shipping_label_url
        );
    }

    public function test_admin_order_show_view_renders_public_label_button()
    {
        \Spatie\Permission\Models\Role::findOrCreate('Super Admin');
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');

        $order = Order::create([
            'order_number' => 'ORD-ADMIN-SHOW',
            'status' => 'paid',
            'subtotal' => 150.00,
            'shipping_amount' => 25.00,
            'total_amount' => 175.00,
            'shipping_id' => 'cart-melhor-envio-123',
            'shipping_service_name' => 'SEDEX',
            'shipping_label_url' => 'https://sandbox.melhorenvio.com.br/portal/imprimir/rGdJWYAylMDW',
            'address_info' => [
                'recipient_name' => 'Fulano de Tal',
                'street' => 'Rua das Flores',
                'number' => '123',
                'neighborhood' => 'Centro',
                'city' => 'Goiânia',
                'state' => 'GO',
                'zip' => '74000-000',
            ],
        ]);

        $response = $this->actingAs($admin)->get(route('admin.orders.show', $order));

        $response->assertStatus(200);
        $response->assertSee('https://sandbox.melhorenvio.com.br/portal/imprimir/rGdJWYAylMDW?public=true');
        $response->assertSee('Baixar Etiqueta Oficial (PDF)');
    }

    public function test_order_payment_service_finalize_triggers_melhor_envio_generation()
    {
        Setting::set('shipping_provider', 'melhorenvio');
        Setting::set('melhorenvio_token', 'fake-test-token');

        Http::fake([
            '*/cart' => Http::response(['id' => 'cart-me-999', 'service' => ['name' => 'PAC']], 200),
            '*/shipment/checkout' => Http::response(['status' => 'success'], 200),
            '*/shipment/generate' => Http::response(['status' => 'success'], 200),
            '*/shipment/print' => Http::response(['url' => 'https://sandbox.melhorenvio.com.br/portal/imprimir/fakeToken123'], 200),
            '*/shipment/tracking' => Http::response(['cart-me-999' => ['tracking' => 'BR123456789BR']], 200),
        ]);

        $order = Order::create([
            'order_number' => 'ORD-AUTO-RECRUIT',
            'status' => 'pending',
            'subtotal' => 200.00,
            'shipping_amount' => 30.00,
            'total_amount' => 230.00,
            'shipping_simulacao_id' => '1',
            'shipping_modalidade' => 'SEDEX',
            'address_info' => [
                'recipient_name' => 'Consumidor Final',
                'street' => 'Av. Paulista',
                'number' => '1000',
                'neighborhood' => 'Bela Vista',
                'city' => 'São Paulo',
                'state' => 'SP',
                'zip' => '01310-100',
                'phone' => '11999999999',
            ],
        ]);

        OrderPaymentService::finalize($order);

        $order->refresh();

        $this->assertEquals('paid', $order->status);
        $this->assertEquals('cart-me-999', $order->shipping_id);
        $this->assertStringContainsString('public=true', $order->shipping_label_url);
        $this->assertEquals('BR123456789BR', $order->shipping_tracking_code);
    }

    public function test_create_cart_order_uses_company_document_when_store_document_is_cnpj()
    {
        Setting::set('melhorenvio_token', 'test-token');
        Setting::set('store_document', '69.772.203/0001-00'); // 14 digits (CNPJ)
        Setting::set('store_state_registration', 'ISENTO');

        Http::fake([
            '*/cart' => function (\Illuminate\Http\Client\Request $request) {
                $payload = $request->data();
                $this->assertArrayHasKey('company_document', $payload['from']);
                $this->assertEquals('69772203000100', $payload['from']['company_document']);
                $this->assertEquals('ISENTO', $payload['from']['state_register']);
                $this->assertArrayNotHasKey('document', $payload['from']);

                return Http::response(['id' => 'cart-cnpj-test'], 201);
            },
        ]);

        $order = Order::create([
            'order_number' => 'ORD-CNPJ-TEST',
            'status' => 'paid',
            'subtotal' => 100,
            'shipping_amount' => 20,
            'total_amount' => 120,
            'address_info' => [
                'recipient_name' => 'Cliente Teste',
                'street' => 'Rua Teste',
                'number' => '1',
                'city' => 'Brasília',
                'state' => 'DF',
                'zip' => '70000000',
            ],
        ]);

        $service = new MelhorEnvioService();
        $result = $service->createCartOrder($order);

        $this->assertNotNull($result);
        $this->assertEquals('cart-cnpj-test', $result['id']);
    }

    public function test_failed_cart_order_stores_error_in_shipping_api_response_and_admin_sees_error()
    {
        \Spatie\Permission\Models\Role::findOrCreate('Super Admin');
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');

        Setting::set('melhorenvio_token', 'test-token');

        Http::fake([
            '*/cart' => Http::response([
                'message' => 'O campo from.document deve ter um CPF válido',
                'errors' => [
                    'from.document' => ['O campo from.document deve ter um CPF válido'],
                ],
            ], 422),
        ]);

        $order = Order::create([
            'order_number' => 'ORD-ERR-TEST',
            'status' => 'paid',
            'subtotal' => 100,
            'shipping_amount' => 20,
            'total_amount' => 120,
            'address_info' => [
                'recipient_name' => 'Cliente Teste',
                'street' => 'Rua Teste',
                'number' => '1',
                'city' => 'Brasília',
                'state' => 'DF',
                'zip' => '70000000',
            ],
        ]);

        $service = new MelhorEnvioService();
        $result = $service->createCartOrder($order);

        $this->assertNull($result);
        $this->assertStringContainsString('O campo from.document deve ter um CPF válido', $service->getLastError());

        $order->refresh();
        $this->assertNotNull($order->shipping_api_response);
        $this->assertStringContainsString('from.document', $order->shipping_api_response['error']);

        // Check that admin order view shows the error alert
        $response = $this->actingAs($admin)->get(route('admin.orders.show', $order));
        $response->assertStatus(200);
        $response->assertSee('Falha na API (MELHOR ENVIO)');
        $response->assertSee('O campo from.document deve ter um CPF válido');
    }
}
