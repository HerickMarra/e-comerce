<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Services\SeoService;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /**
     * Generate dynamic sitemap.xml compliant with Sitemaps protocol and Google Image extensions.
     */
    public function sitemap(): Response
    {
        $products = Product::where('is_active', true)
            ->with(['images', 'categories'])
            ->latest('updated_at')
            ->get();

        $categories = Category::all();

        $latestProductDate = $products->first()?->updated_at?->toIso8601String() ?? now()->toIso8601String();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ' . "\n";
        $xml .= '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";

        // 1. Home Page
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars(url('/')) . "</loc>\n";
        $xml .= "    <lastmod>{$latestProductDate}</lastmod>\n";
        $xml .= "    <changefreq>daily</changefreq>\n";
        $xml .= "    <priority>1.0</priority>\n";
        $xml .= "  </url>\n";

        // 2. Categories Index
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars(route('categories.index')) . "</loc>\n";
        $xml .= "    <lastmod>" . now()->toIso8601String() . "</lastmod>\n";
        $xml .= "    <changefreq>weekly</changefreq>\n";
        $xml .= "    <priority>0.8</priority>\n";
        $xml .= "  </url>\n";

        // 3. Institutional: Nossa História
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars(route('home.family')) . "</loc>\n";
        $xml .= "    <lastmod>" . now()->subDays(7)->toIso8601String() . "</lastmod>\n";
        $xml .= "    <changefreq>monthly</changefreq>\n";
        $xml .= "    <priority>0.7</priority>\n";
        $xml .= "  </url>\n";

        // 4. Categories Landing / Filters
        foreach ($categories as $category) {
            $catUrl = route('search', ['categories' => [$category->id]]);
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($catUrl) . "</loc>\n";
            $xml .= "    <lastmod>" . ($category->updated_at ? $category->updated_at->toIso8601String() : now()->toIso8601String()) . "</lastmod>\n";
            $xml .= "    <changefreq>weekly</changefreq>\n";
            $xml .= "    <priority>0.8</priority>\n";
            if ($category->image_path) {
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>" . htmlspecialchars(SeoService::resolveAbsoluteImageUrl($category->image_path)) . "</image:loc>\n";
                $xml .= "      <image:title>" . htmlspecialchars($category->name) . "</image:title>\n";
                $xml .= "    </image:image>\n";
            }
            $xml .= "  </url>\n";
        }

        // 5. Active Products with Images
        foreach ($products as $product) {
            $productUrl = route('product.show', $product->slug);
            $lastmod = $product->updated_at ? $product->updated_at->toIso8601String() : now()->toIso8601String();

            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($productUrl) . "</loc>\n";
            $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
            $xml .= "    <changefreq>daily</changefreq>\n";
            $xml .= "    <priority>0.9</priority>\n";

            foreach ($product->images as $image) {
                $imgUrl = SeoService::resolveAbsoluteImageUrl($image->path);
                $xml .= "    <image:image>\n";
                $xml .= "      <image:loc>" . htmlspecialchars($imgUrl) . "</image:loc>\n";
                $xml .= "      <image:title>" . htmlspecialchars($product->name . ($product->brand ? ' ' . $product->brand : '')) . "</image:title>\n";
                $xml .= "    </image:image>\n";
            }

            $xml .= "  </url>\n";
        }

        $xml .= "</urlset>\n";

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'X-Robots-Tag' => 'noindex', // Sitemap file itself should not appear as a search result
        ]);
    }

    /**
     * Dynamic robots.txt output.
     */
    public function robots(): Response
    {
        $sitemapUrl = url('/sitemap.xml');

        $content = "User-agent: *\n";
        $content .= "Allow: /\n\n";

        // Protect private routes from search engine crawlers
        $content .= "# Sensitive and dynamic routes\n";
        $content .= "Disallow: /admin\n";
        $content .= "Disallow: /admin/\n";
        $content .= "Disallow: /checkout\n";
        $content .= "Disallow: /checkout/\n";
        $content .= "Disallow: /carrinho\n";
        $content .= "Disallow: /minha-conta\n";
        $content .= "Disallow: /minha-conta/\n";
        $content .= "Disallow: /webhooks\n";
        $content .= "Disallow: /webhooks/\n";
        $content .= "Disallow: /dashboard\n";
        $content .= "Disallow: /profile\n";
        $content .= "Disallow: /password\n\n";

        // Sitemap location
        $content .= "Sitemap: {$sitemapUrl}\n";

        return response($content, 200, [
            'Content-Type' => 'text/plain; charset=utf-8',
        ]);
    }

    /**
     * Generate Google Merchant Center RSS 2.0 XML feed.
     */
    public function googleShoppingFeed(): Response
    {
        $storeName = Setting::get('store_name', config('app.name', 'Denner Tênis'));
        $storeUrl = rtrim(config('app.url', url('/')), '/');
        $tagline = Setting::get('store_tagline', 'Tênis Esportivos e Casuais');

        $products = Product::where('is_active', true)
            ->with(['images', 'categories'])
            ->latest('updated_at')
            ->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n";
        $xml .= "  <channel>\n";
        $xml .= "    <title>" . htmlspecialchars($storeName) . "</title>\n";
        $xml .= "    <link>" . htmlspecialchars($storeUrl) . "</link>\n";
        $xml .= "    <description>" . htmlspecialchars("Feed de produtos do Google Shopping para {$storeName} - {$tagline}") . "</description>\n";

        foreach ($products as $product) {
            $mainImg = $product->images->where('is_main', true)->first() ?? $product->images->first();
            $mainImgUrl = $mainImg ? SeoService::resolveAbsoluteImageUrl($mainImg->path) : SeoService::defaultShareImage();
            $productUrl = route('product.show', $product->slug);
            $cleanDesc = htmlspecialchars(strip_tags($product->description ?: "Tênis {$product->name} {$product->brand} com garantia de conforto e estilo."));
            $category = $product->categories->first()?->name ?: 'Calçados';
            $sku = $product->sku ?: ('PROD-' . $product->id);
            $price = number_format((float) $product->price, 2, '.', '') . ' BRL';
            $availability = $product->stock > 0 ? 'in_stock' : 'out_of_stock';

            $xml .= "    <item>\n";
            $xml .= "      <g:id>" . htmlspecialchars($sku) . "</g:id>\n";
            $xml .= "      <g:title>" . htmlspecialchars($product->name . ($product->brand ? ' - ' . $product->brand : '')) . "</g:title>\n";
            $xml .= "      <g:description>{$cleanDesc}</g:description>\n";
            $xml .= "      <g:link>" . htmlspecialchars($productUrl) . "</g:link>\n";
            $xml .= "      <g:image_link>" . htmlspecialchars($mainImgUrl) . "</g:image_link>\n";

            // Additional images
            $additionalImages = $product->images->where('id', '!=', $mainImg?->id)->take(9);
            foreach ($additionalImages as $addImg) {
                $addUrl = SeoService::resolveAbsoluteImageUrl($addImg->path);
                $xml .= "      <g:additional_image_link>" . htmlspecialchars($addUrl) . "</g:additional_image_link>\n";
            }

            $xml .= "      <g:condition>new</g:condition>\n";
            $xml .= "      <g:availability>{$availability}</g:availability>\n";
            $xml .= "      <g:price>{$price}</g:price>\n";
            $xml .= "      <g:brand>" . htmlspecialchars($product->brand ?: $storeName) . "</g:brand>\n";
            $xml .= "      <g:google_product_category>187</g:google_product_category>\n"; // 187 = Apparel & Accessories > Shoes
            $xml .= "      <g:product_type>" . htmlspecialchars("Calçados > {$category}") . "</g:product_type>\n";
            $xml .= "      <g:identifier_exists>" . ($product->sku ? 'yes' : 'no') . "</g:identifier_exists>\n";
            if ($product->sku) {
                $xml .= "      <g:mpn>" . htmlspecialchars($product->sku) . "</g:mpn>\n";
            }

            $xml .= "    </item>\n";
        }

        $xml .= "  </channel>\n";
        $xml .= "</rss>\n";

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
        ]);
    }
}
