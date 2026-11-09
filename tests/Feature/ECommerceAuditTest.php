<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ECommerceAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_shipping_calculation_accepts_formatted_brazilian_cep()
    {
        $product = Product::create([
            'name' => 'Tênis Teste',
            'slug' => 'tenis-teste',
            'price' => 199.90,
            'stock' => 10,
            'is_active' => true,
        ]);

        // Standard masked CEP (8 digits with dash: 10 chars)
        $response = $this->postJson(route('shipping.calculate'), [
            'cep' => '74003-010',
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // It should pass validation (status 200 or 422 with api message, NOT validation failure for max length)
        $this->assertNotEquals(422, $response->status() === 422 && isset($response->json()['errors']['cep']));
    }

    public function test_checkout_unauthorized_user_cannot_view_others_success_page()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $order = Order::create([
            'user_id' => $user1->id,
            'order_number' => 'ORD-TEST1234',
            'status' => 'pending',
            'subtotal' => 100,
            'shipping_amount' => 20,
            'total_amount' => 120,
            'payment_method' => 'pix',
        ]);

        // Attempt as unauthenticated user without session
        $resGuest = $this->get(route('checkout.success', $order));
        $resGuest->assertStatus(403);

        // Attempt as user2 (not owner)
        $resOtherUser = $this->actingAs($user2)->get(route('checkout.success', $order));
        $resOtherUser->assertStatus(403);

        // Access as owner
        $resOwner = $this->actingAs($user1)->get(route('checkout.success', $order));
        $resOwner->assertStatus(200);
    }

    public function test_checkout_status_api_is_protected_against_idor()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $order = Order::create([
            'user_id' => $user1->id,
            'order_number' => 'ORD-STATUS-TEST',
            'status' => 'pending',
            'subtotal' => 100,
            'shipping_amount' => 20,
            'total_amount' => 120,
            'payment_method' => 'pix',
        ]);

        $resOther = $this->actingAs($user2)->getJson(route('checkout.status', $order));
        $resOther->assertStatus(403);

        $resOwner = $this->actingAs($user1)->getJson(route('checkout.status', $order));
        $resOwner->assertStatus(200);
    }

    public function test_order_restore_stock_restores_product_quantity()
    {
        $product = Product::create([
            'name' => 'Tênis Restauração',
            'slug' => 'tenis-restauracao',
            'price' => 250.00,
            'stock' => 5,
            'is_active' => true,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-RESTORE',
            'status' => 'pending',
            'subtotal' => 500,
            'shipping_amount' => 0,
            'total_amount' => 500,
            'payment_method' => 'pix',
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 250.00,
        ]);

        $product->decrement('stock', 2);
        $this->assertEquals(3, $product->fresh()->stock);

        // Restore
        $order->restoreStock();
        $this->assertEquals(5, $product->fresh()->stock);
    }
}
