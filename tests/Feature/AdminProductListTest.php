<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminProductListTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $categoryRunning;
    protected Category $categoryCasual;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Super Admin');
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');

        $this->categoryRunning = Category::create(['name' => 'Corrida', 'slug' => 'corrida']);
        $this->categoryCasual = Category::create(['name' => 'Casual', 'slug' => 'casual']);
    }

    public function test_admin_can_search_and_filter_products_list()
    {
        $nike = Product::create([
            'name' => 'Nike Pegasus Turbo',
            'slug' => 'nike-pegasus-turbo',
            'brand' => 'Nike',
            'model_line' => 'Pegasus',
            'price' => 799.90,
            'stock' => 15,
            'is_active' => true,
        ]);
        $nike->categories()->attach($this->categoryRunning->id);

        $adidas = Product::create([
            'name' => 'Adidas Ultraboost Light',
            'slug' => 'adidas-ultraboost-light',
            'brand' => 'Adidas',
            'model_line' => 'Ultraboost',
            'price' => 999.90,
            'stock' => 2, // Low stock
            'is_active' => true,
        ]);
        $adidas->categories()->attach($this->categoryRunning->id);

        $vans = Product::create([
            'name' => 'Vans Old Skool Classic',
            'slug' => 'vans-old-skool-classic',
            'brand' => 'Vans',
            'model_line' => 'Old Skool',
            'price' => 349.90,
            'stock' => 0, // Out of stock
            'is_active' => false,
        ]);
        $vans->categories()->attach($this->categoryCasual->id);

        // 1. Search by brand
        $resSearch = $this->actingAs($this->admin)->get(route('admin.products.index', ['search' => 'Nike']));
        $resSearch->assertStatus(200);
        $resSearch->assertSee('Nike Pegasus Turbo');
        $resSearch->assertDontSee('Adidas Ultraboost Light');
        $resSearch->assertDontSee('Vans Old Skool Classic');

        // 2. Filter by stock: out_of_stock
        $resStock = $this->actingAs($this->admin)->get(route('admin.products.index', ['stock_status' => 'out_of_stock']));
        $resStock->assertStatus(200);
        $resStock->assertSee('Vans Old Skool Classic');
        $resStock->assertDontSee('Nike Pegasus Turbo');

        // 3. Filter by stock: low_stock
        $resLowStock = $this->actingAs($this->admin)->get(route('admin.products.index', ['stock_status' => 'low_stock']));
        $resLowStock->assertStatus(200);
        $resLowStock->assertSee('Adidas Ultraboost Light');
        $resLowStock->assertDontSee('Vans Old Skool Classic');

        // 4. Filter by status: inactive
        $resInactive = $this->actingAs($this->admin)->get(route('admin.products.index', ['status' => 'inactive']));
        $resInactive->assertStatus(200);
        $resInactive->assertSee('Vans Old Skool Classic');
        $resInactive->assertDontSee('Nike Pegasus Turbo');

        // 5. Filter by category
        $resCat = $this->actingAs($this->admin)->get(route('admin.products.index', ['category' => $this->categoryCasual->id]));
        $resCat->assertStatus(200);
        $resCat->assertSee('Vans Old Skool Classic');
        $resCat->assertDontSee('Nike Pegasus Turbo');

        // 6. Check KPI summary rendering
        $resIndex = $this->actingAs($this->admin)->get(route('admin.products.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Modelos');
        $resIndex->assertSee('Ativos na Loja');
        $resIndex->assertSee('Pares em Estoque');
        $resIndex->assertSee('Alerta Estoque');
    }
}

