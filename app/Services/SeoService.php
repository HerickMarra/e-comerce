<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Str;

class SeoService
{
    /**
     * Build SEO metadata array for any page.
     */
    public static function build(array $overrides = []): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'Denner Tênis'));
        $tagline = Setting::get('store_tagline', 'Tênis Esportivos, Casuais e Alta Performance');
        $storeUrl = rtrim(config('app.url', url('/')), '/');

        // Resolve default store logo for sharing
        $logoPath = Setting::get('store_logo');
        $defaultImage = $logoPath 
            ? (str_starts_with($logoPath, 'http') ? $logoPath : asset('storage/' . $logoPath))
            : asset('images/logo.png');

        $defaults = [
            'title' => "{$storeName} | {$tagline}",
            'meta_title' => "{$storeName} | {$tagline}",
            'description' => "Compre os melhores tênis na {$storeName}. Encontre modelos exclusivos Nike, Adidas, Olympikus e muito mais com entrega rápida e frete seguro.",
            'canonical' => url()->current(),
            'robots' => 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
            'og_type' => 'website',
            'og_title' => "{$storeName} | {$tagline}",
            'og_description' => "Compre os melhores tênis na {$storeName}. Encontre modelos exclusivos com frete seguro e pagamento facilitado no PIX e Cartão.",
            'og_image' => $defaultImage,
            'og_image_width' => 1200,
            'og_image_height' => 630,
            'og_image_alt' => $storeName,
            'og_url' => url()->current(),
            'twitter_card' => 'summary_large_image',
            'twitter_title' => "{$storeName} | {$tagline}",
            'twitter_description' => "Compre os melhores tênis na {$storeName}. Modelos exclusivos com frete seguro e entrega rápida.",
            'twitter_image' => $defaultImage,
            'schema' => [
                self::organizationSchema(),
                self::websiteSchema(),
            ],
        ];

        $merged = array_merge($defaults, $overrides);

        // Keep Open Graph & Twitter in sync with title/description if not explicitly set in overrides
        if (isset($overrides['title']) && !isset($overrides['og_title'])) {
            $merged['og_title'] = $overrides['title'];
            $merged['twitter_title'] = $overrides['title'];
        }
        if (isset($overrides['description']) && !isset($overrides['og_description'])) {
            $merged['og_description'] = $overrides['description'];
            $merged['twitter_description'] = $overrides['description'];
        }
        if (isset($overrides['image']) && !isset($overrides['og_image'])) {
            $merged['og_image'] = $overrides['image'];
            $merged['twitter_image'] = $overrides['image'];
        }

        return $merged;
    }

    /**
     * Build SEO metadata specifically for a Product.
     */
    public static function forProduct(Product $product): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'Denner Tênis'));
        $brand = $product->brand ?: 'Original';
        $categoryName = $product->categories->first()?->name ?: 'Calçados';
        $priceFormatted = number_format((float) $product->price, 2, ',', '.');

        // Dynamic, high-CTR Title
        $title = "{$product->name} - {$brand} | {$storeName}";

        // Clean description excerpt (max ~155 chars)
        $cleanDesc = Str::limit(
            strip_tags($product->description ?: "Compre {$product->name} {$brand} na {$storeName} por apenas R$ {$priceFormatted}. Tênis confortável de alta performance com frete rápido e seguro."),
            155
        );

        // Main Image URL (guaranteed absolute)
        $mainImageModel = $product->images->where('is_main', true)->first() ?? $product->images->first();
        $imageUrl = $mainImageModel ? self::resolveAbsoluteImageUrl($mainImageModel->path) : self::defaultShareImage();

        $productUrl = route('product.show', $product->slug);

        return self::build([
            'title' => $title,
            'meta_title' => $title,
            'description' => $cleanDesc,
            'canonical' => $productUrl,
            'robots' => $product->is_active 
                ? 'index, follow, max-image-preview:large, max-snippet:-1' 
                : 'noindex, nofollow',
            'og_type' => 'product',
            'og_title' => $title,
            'og_description' => $cleanDesc,
            'og_image' => $imageUrl,
            'og_image_width' => 1000,
            'og_image_height' => 1000,
            'og_image_alt' => "{$product->name} {$brand}",
            'og_url' => $productUrl,
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $title,
            'twitter_description' => $cleanDesc,
            'twitter_image' => $imageUrl,
            'product_price' => (float) $product->price,
            'product_currency' => 'BRL',
            'product_availability' => $product->stock > 0 ? 'instock' : 'outofstock',
            'schema' => [
                self::organizationSchema(),
                self::productSchema($product, $imageUrl, $productUrl),
                self::breadcrumbSchema([
                    ['name' => 'Início', 'url' => url('/')],
                    ['name' => 'Categorias', 'url' => route('categories.index')],
                    ['name' => $categoryName, 'url' => route('search', ['categories' => [$product->categories->first()?->id]])],
                    ['name' => $product->name, 'url' => $productUrl],
                ]),
            ],
        ]);
    }

    /**
     * Build SEO metadata for Categories page.
     */
    public static function forCategoriesIndex(): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'Denner Tênis'));
        $title = "Categorias & Marcas de Tênis | {$storeName}";
        $description = "Explore todas as categorias e marcas de tênis disponíveis na {$storeName}. Encontre tênis de corrida, caminhada, treino e moda casual com os melhores preços.";

        return self::build([
            'title' => $title,
            'meta_title' => $title,
            'description' => $description,
            'canonical' => route('categories.index'),
            'og_title' => $title,
            'og_description' => $description,
            'schema' => [
                self::organizationSchema(),
                self::websiteSchema(),
                self::breadcrumbSchema([
                    ['name' => 'Início', 'url' => url('/')],
                    ['name' => 'Categorias', 'url' => route('categories.index')],
                ]),
            ],
        ]);
    }

    /**
     * Build SEO metadata for a specific Category search/listing.
     */
    public static function forCategory(Category $category): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'Denner Tênis'));
        $title = "Tênis {$category->name} | Catálogo {$storeName}";
        $description = "Confira a seleção exclusiva de tênis {$category->name} na {$storeName}. Diversos modelos, numerações e cores com entrega garantida para todo o Brasil.";
        $canonical = route('search', ['categories' => [$category->id]]);
        $imageUrl = $category->image_path ? self::resolveAbsoluteImageUrl($category->image_path) : self::defaultShareImage();

        return self::build([
            'title' => $title,
            'meta_title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => $imageUrl,
            'schema' => [
                self::organizationSchema(),
                self::breadcrumbSchema([
                    ['name' => 'Início', 'url' => url('/')],
                    ['name' => 'Categorias', 'url' => route('categories.index')],
                    ['name' => $category->name, 'url' => $canonical],
                ]),
            ],
        ]);
    }

    /**
     * Build SEO metadata for Home page.
     */
    public static function forHome(): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'Denner Tênis'));
        $tagline = Setting::get('store_tagline', 'Tênis Exclusivos & Alta Performance');
        $title = "{$storeName} - Loja Oficial de Tênis | {$tagline}";
        $description = "Bem-vindo à {$storeName}. O melhor catálogo de tênis esportivos, casuais e de corrida das maiores marcas mundiais com frete expresso e pagamento seguro no PIX.";

        return self::build([
            'title' => $title,
            'meta_title' => $title,
            'description' => $description,
            'canonical' => url('/'),
            'schema' => [
                self::organizationSchema(),
                self::websiteSchema(),
            ],
        ]);
    }

    /**
     * Build SEO metadata for Explore page.
     */
    public static function forExplore(): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'Denner Tênis'));
        $title = "Explorar Tênis | Descubra seu Próximo Tênis Favorito | {$storeName}";
        $description = "Descubra todos os modelos de tênis masculinos e femininos em um feed dinâmico de exploração contínua. Encontre novidades, lançamentos e ofertas exclusivas na {$storeName}.";

        return self::build([
            'title' => $title,
            'meta_title' => $title,
            'description' => $description,
            'canonical' => route('explore'),
            'og_title' => $title,
            'og_description' => $description,
            'schema' => [
                self::organizationSchema(),
                self::websiteSchema(),
                self::breadcrumbSchema([
                    ['name' => 'Início', 'url' => url('/')],
                    ['name' => 'Explorar', 'url' => route('explore')],
                ]),
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'CollectionPage',
                    'name' => "Explorar Tênis - {$storeName}",
                    'description' => $description,
                    'url' => route('explore'),
                ],
            ],
        ]);
    }

    /**
     * Build SEO metadata for Institutional pages (e.g. Nossa História).
     */
    public static function forInstitutional(string $pageTitle, string $description, string $url): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'Denner Tênis'));
        $title = "{$pageTitle} | {$storeName}";

        return self::build([
            'title' => $title,
            'meta_title' => $title,
            'description' => $description,
            'canonical' => $url,
            'og_title' => $title,
            'og_description' => $description,
            'schema' => [
                self::organizationSchema(),
                self::breadcrumbSchema([
                    ['name' => 'Início', 'url' => url('/')],
                    ['name' => $pageTitle, 'url' => $url],
                ]),
            ],
        ]);
    }

    /**
     * Build SEO metadata for Search / Catalog pages.
     */
    public static function forSearch(?string $query = null, ?Category $category = null): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'Denner Tênis'));

        if (!empty($query)) {
            return self::build([
                'title' => "Busca por \"{$query}\" | {$storeName}",
                'meta_title' => "Busca por \"{$query}\" | {$storeName}",
                'description' => "Confira os resultados de tênis para \"{$query}\" na {$storeName}. Encontre modelos exclusivos com frete seguro para todo o Brasil.",
                'canonical' => route('search', ['q' => $query]),
                'robots' => 'noindex, follow', // Do not index dynamic search pages to avoid thin content & crawler traps, but follow product links
            ]);
        }

        if ($category) {
            return self::forCategory($category);
        }

        return self::build([
            'title' => "Catálogo de Tênis & Coleções | {$storeName}",
            'meta_title' => "Catálogo de Tênis & Coleções | {$storeName}",
            'description' => "Explore o catálogo completo de tênis esportivos, casuais e de corrida da {$storeName}. Modelos autênticos com o melhor custo-benefício e parcelamento facilitado.",
            'canonical' => route('search'),
            'robots' => 'index, follow',
            'schema' => [
                self::organizationSchema(),
                self::websiteSchema(),
                self::breadcrumbSchema([
                    ['name' => 'Início', 'url' => url('/')],
                    ['name' => 'Catálogo', 'url' => route('search')],
                ]),
            ],
        ]);
    }

    /**
     * Build SEO metadata for Private or Dynamic Search pages (noindex, follow).
     */
    public static function forPrivate(string $pageTitle): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'Denner Tênis'));

        return self::build([
            'title' => "{$pageTitle} | {$storeName}",
            'robots' => 'noindex, nofollow, noarchive',
            'canonical' => url()->current(),
        ]);
    }

    /**
     * Build Schema.org Organization + OnlineStore JSON-LD.
     */
    public static function organizationSchema(): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'Denner Tênis'));
        $storeUrl = rtrim(config('app.url', url('/')), '/');
        $phone = Setting::get('store_phone', '61996885273');
        $email = Setting::get('mail_from_address', 'contato@dennertenis.com.br');
        $document = Setting::get('store_document', '08351723177');

        $logoPath = Setting::get('store_logo');
        $logoUrl = $logoPath 
            ? (str_starts_with($logoPath, 'http') ? $logoPath : asset('storage/' . $logoPath))
            : asset('images/logo.png');

        return [
            '@context' => 'https://schema.org',
            '@type' => ['Organization', 'OnlineStore'],
            '@id' => "{$storeUrl}/#organization",
            'name' => $storeName,
            'url' => $storeUrl,
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $logoUrl,
            ],
            'image' => $logoUrl,
            'description' => Setting::get('store_tagline', 'Loja especializada em calçados e tênis de alta performance.'),
            'telephone' => $phone,
            'email' => $email,
            'taxID' => $document,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => Setting::get('store_street', 'Praça Doutor Pedro Ludovico Teixeira') . ', ' . Setting::get('store_number', '24'),
                'addressLocality' => Setting::get('store_city', 'Goiânia'),
                'addressRegion' => Setting::get('store_state', 'GO'),
                'postalCode' => Setting::get('store_zip', '74003-010'),
                'addressCountry' => 'BR',
            ],
            'priceRange' => 'R$ 5,00 - R$ 2.500,00',
            'currenciesAccepted' => 'BRL',
            'paymentAccepted' => 'PIX, Cartão de Crédito, Boleto Bancário',
        ];
    }

    /**
     * Build Schema.org WebSite JSON-LD with Sitelinks Searchbox.
     */
    public static function websiteSchema(): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'Denner Tênis'));
        $storeUrl = rtrim(config('app.url', url('/')), '/');

        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => "{$storeUrl}/#website",
            'url' => $storeUrl,
            'name' => $storeName,
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => "{$storeUrl}/busca?q={search_term_string}",
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /**
     * Build Schema.org Product JSON-LD with Offer details.
     */
    public static function productSchema(Product $product, string $imageUrl, string $productUrl): array
    {
        $storeName = Setting::get('store_name', config('app.name', 'Denner Tênis'));
        $storeUrl = rtrim(config('app.url', url('/')), '/');
        $brandName = $product->brand ?: 'Denner Tênis';

        $allImages = $product->images->map(fn($img) => self::resolveAbsoluteImageUrl($img->path))->toArray();
        if (empty($allImages)) {
            $allImages = [$imageUrl];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            '@id' => "{$productUrl}/#product",
            'name' => $product->name,
            'url' => $productUrl,
            'image' => $allImages,
            'description' => strip_tags($product->description ?: $product->name),
            'sku' => (string) ($product->sku ?: ('PROD-' . $product->id)),
            'mpn' => (string) ($product->sku ?: ('PROD-' . $product->id)),
            'brand' => [
                '@type' => 'Brand',
                'name' => $brandName,
            ],
            'category' => $product->categories->pluck('name')->implode(', ') ?: 'Calçados',
            'offers' => [
                '@type' => 'Offer',
                '@id' => "{$productUrl}/#offer",
                'url' => $productUrl,
                'priceCurrency' => 'BRL',
                'price' => number_format((float) $product->price, 2, '.', ''),
                'priceValidUntil' => now()->addYear()->format('Y-12-31'),
                'itemCondition' => 'https://schema.org/NewCondition',
                'availability' => $product->stock > 0 
                    ? 'https://schema.org/InStock' 
                    : 'https://schema.org/OutOfStock',
                'seller' => [
                    '@type' => 'Organization',
                    'name' => $storeName,
                    'url' => $storeUrl,
                ],
                'hasMerchantReturnPolicy' => [
                    '@type' => 'MerchantReturnPolicy',
                    'applicableCountry' => 'BR',
                    'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                    'merchantReturnDays' => 7,
                    'returnMethod' => 'https://schema.org/ReturnByMail',
                    'returnFees' => 'https://schema.org/FreeReturn',
                ],
                'shippingDetails' => [
                    '@type' => 'OfferShippingDetails',
                    'shippingDestination' => [
                        '@type' => 'DefinedRegion',
                        'addressCountry' => 'BR',
                    ],
                    'deliveryTime' => [
                        '@type' => 'ShippingDeliveryTime',
                        'handlingTime' => [
                            '@type' => 'QuantitativeValue',
                            'minValue' => 0,
                            'maxValue' => 2,
                            'unitCode' => 'd',
                        ],
                        'transitTime' => [
                            '@type' => 'QuantitativeValue',
                            'minValue' => 2,
                            'maxValue' => 7,
                            'unitCode' => 'd',
                        ],
                    ],
                ],
            ],
        ];

        // Include product weight/dimensions if present
        if (!empty($product->weight)) {
            $schema['weight'] = [
                '@type' => 'QuantitativeValue',
                'value' => (float) $product->weight,
                'unitCode' => 'KGM',
            ];
        }

        if (!empty($product->gtin)) {
            $schema['gtin'] = (string) $product->gtin;
        }

        return $schema;
    }

    /**
     * Build Schema.org BreadcrumbList JSON-LD.
     */
    public static function breadcrumbSchema(array $items): array
    {
        $elements = [];
        $position = 1;

        foreach ($items as $item) {
            $elements[] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];
    }

    /**
     * Resolve absolute public URL for an image.
     */
    public static function resolveAbsoluteImageUrl(?string $path): string
    {
        if (empty($path)) {
            return self::defaultShareImage();
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    /**
     * Default fallback share image.
     */
    public static function defaultShareImage(): string
    {
        $logoPath = Setting::get('store_logo');
        if ($logoPath) {
            return self::resolveAbsoluteImageUrl($logoPath);
        }

        return asset('images/logo.png');
    }
}
