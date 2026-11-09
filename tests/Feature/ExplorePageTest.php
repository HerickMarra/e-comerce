<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Services\ExploreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExplorePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        app(ExploreService::class)->clearCache();
    }

    public function test_explore_page_is_accessible_and_renders_ui_elements(): void
    {
        Product::create([
            'name' => 'Tênis Nike Zoom Fly 5',
            'slug' => 'tenis-nike-zoom-fly-5',
            'brand' => 'Nike',
            'price' => 799.90,
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this->get('/explorar');

        $response->assertStatus(200);
        $response->assertSee('Explorar');
        $response->assertSee('Descubra seu próximo tênis favorito');
        $response->assertSee('grid-cols-2');
        $response->assertSee('Tênis Nike Zoom Fly 5');
        $response->assertSee('R$ 799,90');
    }

    public function test_explore_page_excludes_inactive_products(): void
    {
        Product::create([
            'name' => 'Tênis Ativo Visível',
            'slug' => 'tenis-ativo-visivel',
            'price' => 299.90,
            'stock' => 5,
            'is_active' => true,
        ]);

        Product::create([
            'name' => 'Tênis Inativo Oculto',
            'slug' => 'tenis-inativo-oculto',
            'price' => 399.90,
            'stock' => 5,
            'is_active' => false,
        ]);

        $response = $this->get('/explorar');

        $response->assertStatus(200);
        $response->assertSee('Tênis Ativo Visível');
        $response->assertDontSee('Tênis Inativo Oculto');
    }

    public function test_explore_navigation_links_present_in_storefront(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee(route('explore'));
        $response->assertSee('Explorar');
    }

    public function test_explore_ajax_request_returns_valid_json_with_html_cards(): void
    {
        Product::create([
            'name' => 'Tênis Adidas Ultraboost Light',
            'slug' => 'tenis-adidas-ultraboost-light',
            'brand' => 'Adidas',
            'price' => 999.90,
            'stock' => 8,
            'is_active' => true,
        ]);

        $response = $this->getJson('/explorar?ajax=1&page=1&seed=123456');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'html',
            'page',
            'next_page',
            'has_more',
            'cycle',
            'count',
            'total',
            'seed',
        ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals(1, $response->json('page'));
        $this->assertEquals(2, $response->json('next_page'));
        $this->assertStringContainsString('Tênis Adidas Ultraboost Light', $response->json('html'));
    }

    public function test_explore_seed_provides_deterministic_order_without_duplicates(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Product::create([
                'name' => "Tênis Modelo {$i}",
                'slug' => "tenis-modelo-{$i}",
                'price' => 100 + $i,
                'stock' => 5,
                'is_active' => true,
            ]);
        }

        $service = app(ExploreService::class);
        $service->clearCache();

        $seed = 777888;
        $batch1 = $service->getProducts($seed, 1, 5);
        $batch2 = $service->getProducts($seed, 2, 5);

        // Calling page 1 again with same seed produces identical order
        $batch1Repeat = $service->getProducts($seed, 1, 5);
        $ids1 = $batch1['products']->pluck('id')->all();
        $ids1Repeat = $batch1Repeat['products']->pluck('id')->all();
        $this->assertEquals($ids1, $ids1Repeat);

        // Batch 1 and Batch 2 must not overlap within the same cycle
        $ids2 = $batch2['products']->pluck('id')->all();
        $intersection = array_intersect($ids1, $ids2);
        $this->assertEmpty($intersection, 'Products within the same cycle must not duplicate across pages');
        $this->assertCount(5, $ids1);
        $this->assertCount(5, $ids2);
    }

    public function test_explore_cycles_continuously_when_page_exceeds_total_products(): void
    {
        for ($i = 1; $i <= 6; $i++) {
            Product::create([
                'name' => "Tênis Ciclo {$i}",
                'slug' => "tenis-ciclo-{$i}",
                'price' => 200,
                'stock' => 5,
                'is_active' => true,
            ]);
        }

        $service = app(ExploreService::class);
        $service->clearCache();

        // Total 6 products with perPage=3 -> 2 pages per cycle
        // Page 1 -> Cycle 1
        // Page 2 -> Cycle 1
        // Page 3 -> Cycle 2!
        $seed = 444555;
        $page1 = $service->getProducts($seed, 1, 3);
        $page2 = $service->getProducts($seed, 2, 3);
        $page3 = $service->getProducts($seed, 3, 3);

        $this->assertEquals(1, $page1['cycle']);
        $this->assertEquals(1, $page2['cycle']);
        $this->assertEquals(2, $page3['cycle']);
        $this->assertTrue($page3['has_more']);
        $this->assertCount(3, $page3['products']);
    }

    public function test_explore_empty_state_when_no_active_products(): void
    {
        $response = $this->get('/explorar');

        $response->assertStatus(200);
        $response->assertSee('Nenhum tênis disponível no momento');
    }

    public function test_sitemap_includes_explore_route(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertSee(route('explore'));
    }
}
