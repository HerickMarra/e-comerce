<?php

namespace Tests\Feature;

use App\Gateways\AsaasGateway;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class VariationInventoryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Super Admin');
        $this->admin = User::factory()->create([
            'cpf' => '11122233344',
            'phone' => '11999998888',
        ]);
        $this->admin->assignRole('Super Admin');

        $this->customer = User::factory()->create([
            'cpf' => '12345678900',
            'phone' => '11987654321',
        ]);

        $this->category = Category::create([
            'name' => 'Tênis Corrida',
            'slug' => 'tenis-corrida',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_create_product_with_variation_matrix_and_stock()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Tênis Matrix Teste',
            'slug' => 'tenis-matrix-teste',
            'price' => 299.90,
            'stock' => 19,
            'categories' => [$this->category->id],
            'is_active' => 1,
            'sizes' => ['38', '39', '42'],
            'colors' => [
                ['name' => 'Vermelho', 'hex' => '#FF0000'],
                ['name' => 'Preto', 'hex' => '#000000'],
            ],
            'matrix' => [
                'Vermelho' => ['38' => 5, '39' => 2, '42' => 3],
                'Preto' => ['38' => 0, '39' => 8, '42' => 1],
            ],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Tênis Matrix Teste')->first();
        $this->assertNotNull($product);

        // Sum of matrix: 5 + 2 + 3 + 0 + 8 + 1 = 19
        $this->assertEquals(19, $product->stock);

        // Check specific variants
        $v42Red = $product->findVariant('42', 'Vermelho');
        $this->assertNotNull($v42Red);
        $this->assertEquals(3, $v42Red->stock);

        $v42Black = $product->findVariant('42', 'Preto');
        $this->assertNotNull($v42Black);
        $this->assertEquals(1, $v42Black->stock);

        $v38Black = $product->findVariant('38', 'Preto');
        $this->assertNotNull($v38Black);
        $this->assertEquals(0, $v38Black->stock);

        // Check movements recorded
        $movementsCount = StockMovement::where('product_id', $product->id)->count();
        $this->assertGreaterThan(0, $movementsCount);
    }

    public function test_independent_stock_per_size_and_color_combination()
    {
        $product = Product::create([
            'name' => 'Nike Air Max Test',
            'slug' => 'nike-air-max-test',
            'price' => 499.90,
            'stock' => 0,
            'category_id' => $this->category->id,
            'sizes' => ['38', '39', '40', '41', '42', '43'],
            'is_active' => true,
        ]);

        $red = ProductColor::create(['product_id' => $product->id, 'color_name' => 'Vermelho', 'hex_code' => '#FF0000']);
        $black = ProductColor::create(['product_id' => $product->id, 'color_name' => 'Preto', 'hex_code' => '#000000']);

        // Combinations:
        // 38 Vermelho: 5 | 39 Vermelho: 2 | 40 Vermelho: 0 | 42 Vermelho: 3
        // 41 Preto: 8 | 42 Preto: 1 | 43 Preto: 0
        ProductVariant::create(['product_id' => $product->id, 'product_color_id' => $red->id, 'color_name' => 'Vermelho', 'color_hex' => '#FF0000', 'size' => '38', 'stock' => 5]);
        ProductVariant::create(['product_id' => $product->id, 'product_color_id' => $red->id, 'color_name' => 'Vermelho', 'color_hex' => '#FF0000', 'size' => '39', 'stock' => 2]);
        ProductVariant::create(['product_id' => $product->id, 'product_color_id' => $red->id, 'color_name' => 'Vermelho', 'color_hex' => '#FF0000', 'size' => '40', 'stock' => 0]);
        ProductVariant::create(['product_id' => $product->id, 'product_color_id' => $red->id, 'color_name' => 'Vermelho', 'color_hex' => '#FF0000', 'size' => '42', 'stock' => 3]);

        ProductVariant::create(['product_id' => $product->id, 'product_color_id' => $black->id, 'color_name' => 'Preto', 'color_hex' => '#000000', 'size' => '41', 'stock' => 8]);
        ProductVariant::create(['product_id' => $product->id, 'product_color_id' => $black->id, 'color_name' => 'Preto', 'color_hex' => '#000000', 'size' => '42', 'stock' => 1]);
        ProductVariant::create(['product_id' => $product->id, 'product_color_id' => $black->id, 'color_name' => 'Preto', 'color_hex' => '#000000', 'size' => '43', 'stock' => 0]);

        $product->syncStockFromVariants();

        // 5 + 2 + 0 + 3 + 8 + 1 + 0 = 19
        $this->assertEquals(19, $product->stock);

        // Size 42 Red has 3 units
        $this->assertEquals(3, $product->getVariantStock('42', 'Vermelho'));
        $this->assertTrue($product->isVariantInStock('42', 'Vermelho'));

        // Size 42 Black has 1 unit
        $this->assertEquals(1, $product->getVariantStock('42', 'Preto'));
        $this->assertTrue($product->isVariantInStock('42', 'Preto'));

        // Size 40 Red is out of stock (0)
        $this->assertEquals(0, $product->getVariantStock('40', 'Vermelho'));
        $this->assertFalse($product->isVariantInStock('40', 'Vermelho'));

        // Size 43 Black is out of stock (0)
        $this->assertEquals(0, $product->getVariantStock('43', 'Preto'));
        $this->assertFalse($product->isVariantInStock('43', 'Preto'));
    }

    public function test_updating_one_variation_does_not_affect_other_variations()
    {
        $product = Product::create([
            'name' => 'Test Isolated Update',
            'slug' => 'test-isolated-update',
            'price' => 199.90,
            'stock' => 0,
            'category_id' => $this->category->id,
            'sizes' => ['41', '42'],
            'is_active' => true,
        ]);

        $product->categories()->attach($this->category->id);

        $red = ProductColor::create(['product_id' => $product->id, 'color_name' => 'Vermelho', 'hex_code' => '#FF0000']);
        $black = ProductColor::create(['product_id' => $product->id, 'color_name' => 'Preto', 'hex_code' => '#000000']);

        $v42Red = ProductVariant::create(['product_id' => $product->id, 'product_color_id' => $red->id, 'color_name' => 'Vermelho', 'color_hex' => '#FF0000', 'size' => '42', 'stock' => 3]);
        $v42Black = ProductVariant::create(['product_id' => $product->id, 'product_color_id' => $black->id, 'color_name' => 'Preto', 'color_hex' => '#000000', 'size' => '42', 'stock' => 1]);

        $product->syncStockFromVariants();
        $this->assertEquals(4, $product->stock);

        // Admin updates via controller
        $this->actingAs($this->admin)->put(route('admin.products.update', $product), [
            'name' => 'Test Isolated Update',
            'slug' => 'test-isolated-update',
            'price' => 199.90,
            'stock' => 8,
            'categories' => [$this->category->id],
            'is_active' => 1,
            'sizes' => ['41', '42'],
            'colors' => [
                ['name' => 'Vermelho', 'hex' => '#FF0000'],
                ['name' => 'Preto', 'hex' => '#000000'],
            ],
            'matrix' => [
                'Vermelho' => ['41' => 0, '42' => 7], // 42 Red changed from 3 to 7
                'Preto' => ['41' => 0, '42' => 1],    // 42 Black unchanged
            ],
        ]);

        $v42Red->refresh();
        $v42Black->refresh();
        $product->refresh();

        $this->assertEquals(7, $v42Red->stock);
        $this->assertEquals(1, $v42Black->stock);
        $this->assertEquals(8, $product->stock);
    }

    public function test_cart_validates_variant_stock_and_prevents_overselling()
    {
        $product = Product::create([
            'name' => 'Tênis Cart Test',
            'slug' => 'tenis-cart-test',
            'price' => 250.00,
            'stock' => 2,
            'category_id' => $this->category->id,
            'sizes' => ['40', '42'],
            'is_active' => true,
        ]);

        $color = ProductColor::create(['product_id' => $product->id, 'color_name' => 'Azul', 'hex_code' => '#0000FF']);

        $vAvailable = ProductVariant::create([
            'product_id' => $product->id,
            'product_color_id' => $color->id,
            'color_name' => 'Azul',
            'color_hex' => '#0000FF',
            'size' => '42',
            'stock' => 2,
        ]);

        $vOutOfStock = ProductVariant::create([
            'product_id' => $product->id,
            'product_color_id' => $color->id,
            'color_name' => 'Azul',
            'color_hex' => '#0000FF',
            'size' => '40',
            'stock' => 0,
        ]);

        // Attempt to add out-of-stock combination (Size 40 Azul)
        $resOutOfStock = $this->post(route('cart.add', $product), [
            'size' => '40',
            'color_name' => 'Azul',
            'color' => '#0000FF',
            'quantity' => 1,
        ]);
        $resOutOfStock->assertSessionHasErrors(['message']);

        // Attempt to add more than available (3 units when stock is 2)
        $resExceed = $this->post(route('cart.add', $product), [
            'size' => '42',
            'color_name' => 'Azul',
            'color' => '#0000FF',
            'quantity' => 3,
        ]);
        $resExceed->assertSessionHasErrors(['message']);

        // Add valid quantity (2 units)
        $resValid = $this->post(route('cart.add', $product), [
            'size' => '42',
            'color_name' => 'Azul',
            'color' => '#0000FF',
            'quantity' => 2,
        ]);
        $resValid->assertRedirect(route('cart.index'));

        $cart = session()->get('cart');
        $this->assertNotEmpty($cart);
        $cartItem = collect($cart)->first();
        $this->assertEquals($vAvailable->id, $cartItem['product_variant_id']);
        $this->assertEquals(2, $cartItem['quantity']);
    }

    public function test_checkout_deducts_variant_stock_and_records_audit_movement()
    {
        $mockGateway = $this->mock(AsaasGateway::class);
        $mockGateway->shouldReceive('createPayment')->andReturn([
            'success' => true,
            'payment_id' => 'pay_test_123',
            'status' => 'PENDING',
        ]);

        $product = Product::create([
            'name' => 'Tênis Checkout Test',
            'slug' => 'tenis-checkout-test',
            'price' => 300.00,
            'stock' => 5,
            'category_id' => $this->category->id,
            'sizes' => ['41'],
            'is_active' => true,
        ]);

        $color = ProductColor::create(['product_id' => $product->id, 'color_name' => 'Branco', 'hex_code' => '#FFFFFF']);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'product_color_id' => $color->id,
            'color_name' => 'Branco',
            'color_hex' => '#FFFFFF',
            'size' => '41',
            'stock' => 5,
        ]);

        // Put item into cart session with product_variant_id
        $cartKey = $product->id . '-41-Branco';
        session()->put('cart', [
            $cartKey => [
                'name' => $product->name,
                'price' => 300.00,
                'image' => null,
                'size' => '41',
                'color' => '#FFFFFF',
                'color_name' => 'Branco',
                'quantity' => 2,
                'product_id' => $product->id,
                'product_variant_id' => $variant->id,
            ],
        ]);

        $response = $this->actingAs($this->customer)->post(route('checkout.process'), [
            'name' => 'Comprador Teste',
            'email' => 'comprador@teste.com',
            'user_cpf' => '123.456.789-00',
            'user_phone' => '(11) 98765-4321',
            'postal_code' => '01001-000',
            'address' => 'Praça da Sé, 100',
            'number' => '100',
            'neighborhood' => 'Sé',
            'city' => 'São Paulo',
            'state' => 'SP',
            'shipping_method' => 'sedex',
            'shipping_amount' => 25.00,
            'payment_method' => 'pix',
        ]);

        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('checkout.success', $order));

        // Variant stock should be decremented from 5 to 3
        $variant->refresh();
        $this->assertEquals(3, $variant->stock);

        // Product total stock should be 3
        $product->refresh();
        $this->assertEquals(3, $product->stock);

        // Order item should reference variant
        $orderItem = OrderItem::where('order_id', $order->id)->first();
        $this->assertNotNull($orderItem);
        $this->assertEquals($variant->id, $orderItem->product_variant_id);

        // Stock movement should be recorded
        $movement = StockMovement::where('product_variant_id', $variant->id)
            ->where('type', 'sale')
            ->first();
        $this->assertNotNull($movement);
        $this->assertEquals(-2, $movement->quantity_delta);
        $this->assertEquals(5, $movement->quantity_before);
        $this->assertEquals(3, $movement->quantity_after);
    }

    public function test_order_cancellation_restores_exact_variant_stock_safely_without_duplication()
    {
        $product = Product::create([
            'name' => 'Tênis Cancelamento Test',
            'slug' => 'tenis-cancelamento-test',
            'price' => 350.00,
            'stock' => 1,
            'category_id' => $this->category->id,
            'sizes' => ['42'],
            'is_active' => true,
        ]);

        $color = ProductColor::create(['product_id' => $product->id, 'color_name' => 'Preto', 'hex_code' => '#000000']);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'product_color_id' => $color->id,
            'color_name' => 'Preto',
            'color_hex' => '#000000',
            'size' => '42',
            'stock' => 1,
        ]);

        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_number' => 'ORD-VAR-TEST-01',
            'status' => 'pending',
            'subtotal' => 350.00,
            'shipping_amount' => 20.00,
            'total_amount' => 370.00,
            'payment_method' => 'pix',
            'stock_restored' => false,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant->id,
            'quantity' => 1,
            'price' => 350.00,
            'color' => '#000000',
            'color_name' => 'Preto',
            'size' => '42',
        ]);

        // First cancellation: stock should restore from 1 to 2
        $restoredFirst = $order->restoreStock('Cancelamento pelo cliente');
        $this->assertTrue($restoredFirst);

        $variant->refresh();
        $this->assertEquals(2, $variant->stock);

        $order->refresh();
        $this->assertTrue($order->stock_restored);

        $restoreMovement = StockMovement::where('product_variant_id', $variant->id)
            ->where('type', 'cancellation_restore')
            ->first();
        $this->assertNotNull($restoreMovement);
        $this->assertEquals(1, $restoreMovement->quantity_delta);

        // Second cancellation attempt: must NOT duplicate restore
        $restoredSecond = $order->restoreStock('Tentativa duplicada');
        $this->assertFalse($restoredSecond);

        $variant->refresh();
        $this->assertEquals(2, $variant->stock); // Remains 2, not 3!
    }

    public function test_storefront_product_page_reflects_stock_matrix_and_availability()
    {
        $product = Product::create([
            'name' => 'Tênis Storefront Test',
            'slug' => 'tenis-storefront-test',
            'price' => 299.90,
            'stock' => 3,
            'category_id' => $this->category->id,
            'sizes' => ['40', '42'],
            'is_active' => true,
        ]);

        $product->categories()->attach($this->category->id);

        $color = ProductColor::create([
            'product_id' => $product->id,
            'color_name' => 'Vermelho',
            'hex_code' => '#FF0000',
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'product_color_id' => $color->id,
            'color_name' => 'Vermelho',
            'color_hex' => '#FF0000',
            'size' => '42',
            'stock' => 3,
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'product_color_id' => $color->id,
            'color_name' => 'Vermelho',
            'color_hex' => '#FF0000',
            'size' => '40',
            'stock' => 0,
        ]);

        $response = $this->get(route('product.show', $product->slug));
        $response->assertStatus(200);
        $response->assertSee('Tênis Storefront Test');
        $response->assertSee('x-data="productDetailComponent()"', false);
        $response->assertDontSee('{ const matchSize = String(v.size)', false);

        // Verify stock matrix helper
        $matrix = $product->getStockMatrix();
        $this->assertArrayHasKey('Vermelho', $matrix);
        $this->assertEquals(3, $matrix['Vermelho']['42']);
        $this->assertEquals(0, $matrix['Vermelho']['40']);

        $this->assertTrue($product->isVariantInStock('42', 'Vermelho'));
        $this->assertFalse($product->isVariantInStock('40', 'Vermelho'));
    }
}
