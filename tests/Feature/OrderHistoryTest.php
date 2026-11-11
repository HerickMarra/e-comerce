<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class OrderHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::findOrCreate('Super Admin');
    }

    protected function createAdmin(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->assignRole('Super Admin');
        return $user;
    }

    public function test_record_history_for_system_and_user_actors(): void
    {
        $user = $this->createAdmin([
            'name' => 'Admin Fulano',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-HIST-001',
            'status' => 'pending',
            'subtotal' => 100.00,
            'shipping_amount' => 15.00,
            'total_amount' => 115.00,
            'payment_method' => 'pix',
        ]);

        // 1. System event (e.g. Asaas PIX generation or webhook)
        $systemHistory = $order->recordHistory(
            action: 'pix_generated',
            title: 'Código PIX Gerado no Gateway',
            description: 'Cobrança PIX gerada com sucesso no Asaas.',
            metadata: ['payment_id' => 'pay_123456'],
            user: null,
            actorName: 'Gateway Asaas'
        );

        $this->assertTrue($systemHistory->isSystem());
        $this->assertFalse($systemHistory->isUser());
        $this->assertEquals('system', $systemHistory->actor_type);
        $this->assertEquals('Gateway Asaas', $systemHistory->actor_name);
        $this->assertNull($systemHistory->user_id);

        // 2. User event (e.g. Admin changes status)
        $userHistory = $order->recordHistory(
            action: 'status_changed',
            title: 'Status do Pedido Alterado',
            description: 'Status alterado de Pendente para Pago.',
            metadata: ['from' => 'pending', 'to' => 'paid'],
            user: $user
        );

        $this->assertTrue($userHistory->isUser());
        $this->assertFalse($userHistory->isSystem());
        $this->assertEquals('user', $userHistory->actor_type);
        $this->assertEquals('Admin Fulano', $userHistory->actor_name);
        $this->assertEquals($user->id, $userHistory->user_id);
    }

    public function test_ensure_initial_history_backfills_events_for_legacy_orders(): void
    {
        $customer = User::factory()->create([
            'name' => 'Cliente Beltrano',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-LEGACY-002',
            'user_id' => $customer->id,
            'status' => 'paid',
            'subtotal' => 200.00,
            'shipping_amount' => 25.00,
            'total_amount' => 225.00,
            'payment_method' => 'pix',
            'payment_id' => 'pay_asaas_999',
            'shipping_id' => 'me_cart_555',
            'shipping_tracking_code' => 'BR123456789BR',
        ]);

        $this->assertEquals(0, $order->histories()->count());

        $order->ensureInitialHistory();

        $this->assertGreaterThanOrEqual(4, $order->histories()->count());

        $actions = $order->histories->pluck('action')->all();
        $this->assertContains('order_created', $actions);
        $this->assertContains('pix_generated', $actions);
        $this->assertContains('payment_confirmed', $actions);
        $this->assertContains('shipping_label_created', $actions);
    }

    public function test_admin_can_view_order_history_and_add_internal_note(): void
    {
        $admin = $this->createAdmin([
            'name' => 'Administrador Loja',
            'email' => 'admin@lojatenis.com',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-NOTE-003',
            'status' => 'pending',
            'subtotal' => 150.00,
            'shipping_amount' => 20.00,
            'total_amount' => 170.00,
            'payment_method' => 'pix',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.orders.add-note', $order), [
            'note' => 'Cliente entrou em contato solicitando prioridade no envio.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('order_histories', [
            'order_id' => $order->id,
            'user_id' => $admin->id,
            'actor_type' => 'user',
            'actor_name' => 'Administrador Loja',
            'action' => 'note_added',
            'title' => 'Anotação Interna da Equipe',
            'description' => 'Cliente entrou em contato solicitando prioridade no envio.',
        ]);

        // View admin show page and verify timeline elements
        $viewResponse = $this->actingAs($admin)->get(route('admin.orders.show', $order));
        $viewResponse->assertOk();
        $viewResponse->assertSee('Histórico do Pedido');
        $viewResponse->assertSee('Administrador Loja');
        $viewResponse->assertSee('Cliente entrou em contato solicitando prioridade no envio.');
        $viewResponse->assertSee('Usuário:');
    }

    public function test_admin_can_update_status_and_it_records_user_actor(): void
    {
        $admin = $this->createAdmin([
            'name' => 'Gerente de Vendas',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-STATUS-004',
            'status' => 'pending',
            'subtotal' => 100.00,
            'shipping_amount' => 10.00,
            'total_amount' => 110.00,
            'payment_method' => 'pix',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.orders.update', $order), [
            'status' => 'processing',
        ]);

        $response->assertRedirect();
        $this->assertEquals('processing', $order->fresh()->status);

        $this->assertDatabaseHas('order_histories', [
            'order_id' => $order->id,
            'user_id' => $admin->id,
            'actor_type' => 'user',
            'actor_name' => 'Gerente de Vendas',
            'action' => 'status_changed',
        ]);
    }

    public function test_customer_cannot_view_admin_order_audit_history(): void
    {
        $customer = User::factory()->create([
            'name' => 'Cliente Secreto',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-CUST-005',
            'user_id' => $customer->id,
            'status' => 'pending',
            'subtotal' => 100.00,
            'shipping_amount' => 10.00,
            'total_amount' => 110.00,
            'payment_method' => 'pix',
        ]);

        $order->recordHistory(
            action: 'note_added',
            title: 'Nota Confidencial da Equipe',
            description: 'Verificação interna de antifraude e risco.',
            user: null,
            actorName: 'Sistema Antifraude'
        );

        // Customer accesses their own order view
        $response = $this->actingAs($customer)->get(route('customer.orders.show', $order));
        $response->assertOk();
        $response->assertDontSee('Histórico do Pedido & Auditoria');
        $response->assertDontSee('Nota Confidencial da Equipe');
        $response->assertDontSee('Verificação interna de antifraude');

        // Customer cannot access admin routes
        $adminResponse = $this->actingAs($customer)->get(route('admin.orders.show', $order));
        $adminResponse->assertForbidden();
    }
}
