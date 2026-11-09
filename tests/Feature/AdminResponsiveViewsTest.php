<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\EmailTemplate;
use App\Models\Newsletter;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminResponsiveViewsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Product $product;
    protected Category $category;
    protected Order $order;
    protected Banner $banner;
    protected EmailTemplate $emailTemplate;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('Super Admin');
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Super Admin');

        $this->category = Category::create([
            'name' => 'Tênis Esportivos',
            'slug' => 'tenis-esportivos',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name' => 'Tênis Responsivo Pro',
            'slug' => 'tenis-responsivo-pro',
            'price' => 399.90,
            'stock' => 10,
            'is_active' => true,
            'sizes' => ['38', '39', '40', '41', '42'],
        ]);
        $this->product->categories()->attach($this->category->id);

        $color = ProductColor::create([
            'product_id' => $this->product->id,
            'color_name' => 'Preto',
            'hex_code' => '#000000',
        ]);

        ProductVariant::create([
            'product_id' => $this->product->id,
            'product_color_id' => $color->id,
            'color_name' => 'Preto',
            'color_hex' => '#000000',
            'size' => '42',
            'stock' => 10,
        ]);

        $this->order = Order::create([
            'user_id' => $this->admin->id,
            'order_number' => 'ORD-RESP-01',
            'status' => 'pending',
            'subtotal' => 399.90,
            'shipping_amount' => 20.00,
            'total_amount' => 419.90,
            'payment_method' => 'pix',
        ]);

        $this->banner = Banner::create([
            'title' => 'Banner Teste Mobile',
            'media_type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'is_active' => true,
        ]);

        $this->emailTemplate = EmailTemplate::create([
            'name' => 'Email Teste',
            'slug' => 'email-teste',
            'subject' => 'Assunto Teste',
            'content' => '<p>Olá {name}</p>',
            'type' => 'marketing',
        ]);

        Newsletter::create([
            'email' => 'cliente@teste.com',
        ]);
    }

    public function test_admin_dashboard_renders_with_responsive_layout()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('adminNav()', false);
        $response->assertSee('sidebarOpen', false);
        $response->assertSee('Desempenho de Vendas');
    }

    public function test_admin_products_views_render_successfully()
    {
        // Index
        $resIndex = $this->actingAs($this->admin)->get(route('admin.products.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Tênis Responsivo Pro');
        $resIndex->assertSee('Cadastrar Novo Tênis');

        // Create
        $resCreate = $this->actingAs($this->admin)->get(route('admin.products.create'));
        $resCreate->assertStatus(200);
        $resCreate->assertSee('Controle de Estoque por Variação');
        $resCreate->assertSee('Tamanhos Disponíveis');

        // Edit
        $resEdit = $this->actingAs($this->admin)->get(route('admin.products.edit', $this->product));
        $resEdit->assertStatus(200);
        $resEdit->assertSee('Controle de Estoque por Variação');
        $resEdit->assertSee('Tênis Responsivo Pro');
    }

    public function test_admin_categories_views_render_successfully()
    {
        // Index
        $resIndex = $this->actingAs($this->admin)->get(route('admin.categories.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Tênis Esportivos');

        // Create
        $resCreate = $this->actingAs($this->admin)->get(route('admin.categories.create'));
        $resCreate->assertStatus(200);

        // Edit
        $resEdit = $this->actingAs($this->admin)->get(route('admin.categories.edit', $this->category));
        $resEdit->assertStatus(200);
        $resEdit->assertSee('Tênis Esportivos');
    }

    public function test_admin_orders_views_render_successfully()
    {
        // Index
        $resIndex = $this->actingAs($this->admin)->get(route('admin.orders.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('ORD-RESP-01');

        // Show
        $resShow = $this->actingAs($this->admin)->get(route('admin.orders.show', $this->order));
        $resShow->assertStatus(200);
        $resShow->assertSee('ORD-RESP-01');
    }

    public function test_admin_banners_views_render_successfully()
    {
        // Index
        $resIndex = $this->actingAs($this->admin)->get(route('admin.banners.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Banner Teste Mobile');

        // Create
        $resCreate = $this->actingAs($this->admin)->get(route('admin.banners.create'));
        $resCreate->assertStatus(200);

        // Edit
        $resEdit = $this->actingAs($this->admin)->get(route('admin.banners.edit', $this->banner));
        $resEdit->assertStatus(200);
        $resEdit->assertSee('Banner Teste Mobile');
    }

    public function test_admin_users_views_render_successfully()
    {
        // Index
        $resIndex = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee($this->admin->name);

        // Create
        $resCreate = $this->actingAs($this->admin)->get(route('admin.users.create'));
        $resCreate->assertStatus(200);

        // Edit
        $resEdit = $this->actingAs($this->admin)->get(route('admin.users.edit', $this->admin));
        $resEdit->assertStatus(200);
        $resEdit->assertSee($this->admin->name);
    }

    public function test_admin_emails_newsletters_appearance_and_settings_render_successfully()
    {
        // Emails Index, Create, Edit
        $resEmails = $this->actingAs($this->admin)->get(route('admin.emails.index'));
        $resEmails->assertStatus(200);

        $resEmailCreate = $this->actingAs($this->admin)->get(route('admin.emails.create'));
        $resEmailCreate->assertStatus(200);

        $resEmailEdit = $this->actingAs($this->admin)->get(route('admin.emails.edit', $this->emailTemplate));
        $resEmailEdit->assertStatus(200);

        // Newsletters
        $resNews = $this->actingAs($this->admin)->get(route('admin.newsletters.index'));
        $resNews->assertStatus(200);
        $resNews->assertSee('cliente@teste.com');

        // Appearance
        $resApp = $this->actingAs($this->admin)->get(route('admin.appearance.index'));
        $resApp->assertStatus(200);

        // Settings
        $resSet = $this->actingAs($this->admin)->get(route('admin.settings.index'));
        $resSet->assertStatus(200);
        $resSet->assertSee('Identidade Visual');
    }
}
