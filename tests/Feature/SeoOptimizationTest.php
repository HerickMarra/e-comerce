<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoOptimizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_contains_technical_seo_and_json_ld()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $content = $response->getContent();

        // Title and description
        $this->assertStringContainsString('<title>', $content);
        $this->assertStringContainsString('name="description"', $content);

        // Canonical URL
        $this->assertStringContainsString('rel="canonical"', $content);

        // Robots directive
        $this->assertStringContainsString('name="robots" content="index, follow', $content);

        // Open Graph & Twitter Cards
        $this->assertStringContainsString('property="og:title"', $content);
        $this->assertStringContainsString('property="og:image"', $content);
        $this->assertStringContainsString('name="twitter:card"', $content);

        // Schema.org JSON-LD
        $this->assertStringContainsString('application/ld+json', $content);
        $this->assertStringContainsString('"Organization"', $content);
        $this->assertStringContainsString('"OnlineStore"', $content);
        $this->assertStringContainsString('"@type": "WebSite"', $content);
    }

    public function test_product_page_contains_optimized_seo_metadata_and_product_schema()
    {
        $category = Category::create([
            'name' => 'Corrida',
            'slug' => 'corrida',
        ]);

        $product = Product::create([
            'name' => 'Nike Air Zoom Pegasus',
            'slug' => 'nike-air-zoom-pegasus',
            'brand' => 'Nike',
            'price' => 799.90,
            'stock' => 15,
            'is_active' => true,
            'description' => 'Tênis de corrida super confortável com amortecimento responsivo.',
            'sku' => 'NIKE-PEGASUS-40',
        ]);

        $product->categories()->attach($category->id);

        ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/nike-air-zoom.jpg',
            'is_main' => true,
        ]);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $content = $response->getContent();

        // Dynamic Title with Product & Brand
        $this->assertStringContainsString('Nike Air Zoom Pegasus - Nike', $content);

        // Meta Description contains excerpt
        $this->assertStringContainsString('Tênis de corrida super confortável', $content);

        // Canonical URL
        $this->assertStringContainsString('rel="canonical" href="' . route('product.show', $product->slug) . '"', $content);

        // Open Graph Product tags & Microdata
        $this->assertStringContainsString('property="og:type" content="product"', $content);
        $this->assertStringContainsString('property="product:price:amount" content="799.90"', $content);
        $this->assertStringContainsString('property="product:price:currency" content="BRL"', $content);

        // Schema.org Product, Offer, Breadcrumbs
        $this->assertStringContainsString('"@type": "Product"', $content);
        $this->assertStringContainsString('"@type": "Offer"', $content);
        $this->assertStringContainsString('"@type": "BreadcrumbList"', $content);
        $this->assertStringContainsString('"sku": "NIKE-PEGASUS-40"', $content);
        $this->assertStringContainsString('"price": "799.90"', $content);
        $this->assertStringContainsString('https://schema.org/InStock', $content);

        // Performance / Core Web Vitals: LCP fetchpriority
        $this->assertStringContainsString('fetchpriority="high"', $content);
    }

    public function test_inactive_product_redirects_301_to_category_or_aborts_410()
    {
        $category = Category::create([
            'name' => 'Casual',
            'slug' => 'casual',
        ]);

        $inactiveProductWithCategory = Product::create([
            'name' => 'Tênis Descontinuado Com Categoria',
            'slug' => 'tenis-descontinuado-cat',
            'price' => 199.90,
            'stock' => 0,
            'is_active' => false,
        ]);
        $inactiveProductWithCategory->categories()->attach($category->id);

        // 1. Inactive with category -> 301 Redirect to category listing
        $response1 = $this->get(route('product.show', $inactiveProductWithCategory->slug));
        $response1->assertStatus(301);
        $response1->assertRedirect(route('search', ['categories' => [$category->id]]));

        // 2. Inactive without category -> 410 Gone
        $inactiveProductNoCat = Product::create([
            'name' => 'Tênis Descontinuado Sem Categoria',
            'slug' => 'tenis-descontinuado-nocat',
            'price' => 199.90,
            'stock' => 0,
            'is_active' => false,
        ]);

        $response2 = $this->get(route('product.show', $inactiveProductNoCat->slug));
        $response2->assertStatus(410);

        // 3. Non-existent product -> 404 Not Found
        $response3 = $this->get(route('product.show', 'slug-que-nunca-existiu'));
        $response3->assertStatus(404);
    }

    public function test_categories_index_has_seo_and_breadcrumbs()
    {
        $response = $this->get(route('categories.index'));

        $response->assertStatus(200);
        $content = $response->getContent();

        $this->assertStringContainsString('rel="canonical" href="' . route('categories.index') . '"', $content);
        $this->assertStringContainsString('"@type": "BreadcrumbList"', $content);
    }

    public function test_search_results_have_noindex_follow_to_prevent_duplicate_indexing()
    {
        $response = $this->get(route('search', ['q' => 'tenis preto']));

        $response->assertStatus(200);
        $content = $response->getContent();

        // Must instruct crawlers not to index dynamic queries, but follow product links
        $this->assertStringContainsString('name="robots" content="noindex, follow"', $content);
    }

    public function test_cart_and_customer_areas_have_noindex_nofollow()
    {
        // 1. Cart
        $cartResponse = $this->get(route('cart.index'));
        $cartResponse->assertStatus(200);
        $this->assertStringContainsString('name="robots" content="noindex, nofollow', $cartResponse->getContent());

        // 2. Customer Dashboard
        $user = User::factory()->create();
        $dashResponse = $this->actingAs($user)->get(route('customer.dashboard'));
        $dashResponse->assertStatus(200);
        $this->assertStringContainsString('name="robots" content="noindex, nofollow', $dashResponse->getContent());
    }

    public function test_sitemap_xml_returns_valid_xml_with_products_and_images()
    {
        $product = Product::create([
            'name' => 'Adidas Ultraboost Light',
            'slug' => 'adidas-ultraboost-light',
            'price' => 999.90,
            'stock' => 8,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/ultraboost.jpg',
            'is_main' => true,
        ]);

        $response = $this->get(route('sitemap'));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));

        $content = $response->getContent();

        // Sitemaps XML structure
        $this->assertStringContainsString('<urlset', $content);
        $this->assertStringContainsString(htmlspecialchars(url('/')), $content);
        $this->assertStringContainsString(htmlspecialchars(route('product.show', $product->slug)), $content);
        $this->assertStringContainsString('<image:image>', $content);
        $this->assertStringContainsString('ultraboost.jpg', $content);
    }

    public function test_robots_txt_protects_admin_and_private_routes_and_references_sitemap()
    {
        $response = $this->get(route('robots'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/plain', $response->headers->get('Content-Type'));

        $content = $response->getContent();

        $this->assertStringContainsString('User-agent: *', $content);
        $this->assertStringContainsString('Allow: /', $content);
        $this->assertStringContainsString('Disallow: /admin', $content);
        $this->assertStringContainsString('Disallow: /checkout', $content);
        $this->assertStringContainsString('Disallow: /carrinho', $content);
        $this->assertStringContainsString('Disallow: /minha-conta', $content);
        $this->assertStringContainsString('Disallow: /webhooks', $content);
        $this->assertStringContainsString('Sitemap: ' . url('/sitemap.xml'), $content);
    }

    public function test_google_shopping_xml_feed_conforms_to_merchant_center_specs()
    {
        $product = Product::create([
            'name' => 'Olympikus Corre 3',
            'slug' => 'olympikus-corre-3',
            'brand' => 'Olympikus',
            'price' => 499.90,
            'stock' => 20,
            'is_active' => true,
            'description' => 'O melhor tênis brasileiro para corredores.',
            'sku' => 'OLYMP-CORRE-3',
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/corre3.jpg',
            'is_main' => true,
        ]);

        $response = $this->get(route('feed.google-shopping'));

        $response->assertStatus(200);
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));

        $content = $response->getContent();

        // RSS 2.0 with Google namespace
        $this->assertStringContainsString('<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">', $content);
        $this->assertStringContainsString('<g:id>OLYMP-CORRE-3</g:id>', $content);
        $this->assertStringContainsString('<g:title>Olympikus Corre 3 - Olympikus</g:title>', $content);
        $this->assertStringContainsString('<g:price>499.90 BRL</g:price>', $content);
        $this->assertStringContainsString('<g:availability>in_stock</g:availability>', $content);
        $this->assertStringContainsString('<g:condition>new</g:condition>', $content);
        $this->assertStringContainsString('<g:google_product_category>187</g:google_product_category>', $content);
    }
}
