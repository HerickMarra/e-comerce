<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchSuggestionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_suggestions_endpoint_returns_matching_products_and_categories()
    {
        $category = Category::create([
            'name' => 'Armas de Airsoft',
            'slug' => 'armas-de-airsoft',
        ]);

        $product = Product::create([
            'name' => 'Rifle Airsoft M4A1 Full Metal',
            'slug' => 'rifle-airsoft-m4a1-full-metal',
            'brand' => 'Cyma',
            'price' => 1899.90,
            'stock' => 10,
            'is_active' => true,
        ]);
        $product->categories()->attach($category->id);

        $response = $this->getJson(route('search.suggestions', ['q' => 'M4A1']));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'products' => [
                '*' => ['id', 'name', 'brand', 'price_formatted', 'url', 'image', 'category']
            ],
            'categories',
            'total',
        ]);
        $this->assertEquals(1, $response->json('total'));
        $this->assertEquals('Rifle Airsoft M4A1 Full Metal', $response->json('products.0.name'));
    }

    public function test_suggestions_ignores_queries_shorter_than_two_characters()
    {
        $response = $this->getJson(route('search.suggestions', ['q' => 'a']));

        $response->assertStatus(200);
        $this->assertEquals(0, $response->json('total'));
        $this->assertEmpty($response->json('products'));
    }

    public function test_welcome_page_renders_enhanced_search_bar_and_pills()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee(route('search.suggestions'));
        $response->assertSee('Em alta:', false);
        $response->assertSee('Rifle M4A1');
        $response->assertSee('Pistola Glock G17');
        $response->assertDontSee('Nike, Adidas, Corre 3');
    }
}
