@props([
    'seo' => []
])

@php
    $storeName = \App\Models\Setting::get('store_name', config('app.name', 'Denner Tênis'));
    $seo = array_merge(\App\Services\SeoService::build(), $seo);
@endphp

{{-- Standard Meta Tags --}}
<title>{{ $seo['title'] }}</title>
<meta name="description" content="{{ $seo['description'] }}" />
<meta name="robots" content="{{ $seo['robots'] }}" />
<link rel="canonical" href="{{ $seo['canonical'] }}" />

{{-- Open Graph / WhatsApp / Facebook --}}
<meta property="og:locale" content="pt_BR" />
<meta property="og:site_name" content="{{ $storeName }}" />
<meta property="og:type" content="{{ $seo['og_type'] }}" />
<meta property="og:title" content="{{ $seo['og_title'] }}" />
<meta property="og:description" content="{{ $seo['og_description'] }}" />
<meta property="og:url" content="{{ $seo['og_url'] }}" />
<meta property="og:image" content="{{ $seo['og_image'] }}" />
<meta property="og:image:secure_url" content="{{ $seo['og_image'] }}" />
<meta property="og:image:width" content="{{ $seo['og_image_width'] ?? 1200 }}" />
<meta property="og:image:height" content="{{ $seo['og_image_height'] ?? 630 }}" />
@if(!empty($seo['og_image_alt']))
    <meta property="og:image:alt" content="{{ $seo['og_image_alt'] }}" />
@endif

{{-- Product Open Graph Extensions --}}
@if($seo['og_type'] === 'product' && !empty($seo['product_price']))
    <meta property="product:price:amount" content="{{ number_format($seo['product_price'], 2, '.', '') }}" />
    <meta property="product:price:currency" content="{{ $seo['product_currency'] ?? 'BRL' }}" />
    <meta property="product:availability" content="{{ $seo['product_availability'] ?? 'instock' }}" />
    <meta property="product:condition" content="new" />
@endif

{{-- Twitter Cards --}}
<meta name="twitter:card" content="{{ $seo['twitter_card'] ?? 'summary_large_image' }}" />
<meta name="twitter:title" content="{{ $seo['twitter_title'] ?? $seo['og_title'] }}" />
<meta name="twitter:description" content="{{ $seo['twitter_description'] ?? $seo['og_description'] }}" />
<meta name="twitter:image" content="{{ $seo['twitter_image'] ?? $seo['og_image'] }}" />
<meta name="twitter:url" content="{{ $seo['og_url'] }}" />

{{-- WhatsApp / Schema Microdata fallback --}}
<meta itemprop="name" content="{{ $seo['og_title'] }}" />
<meta itemprop="description" content="{{ $seo['og_description'] }}" />
<meta itemprop="image" content="{{ $seo['og_image'] }}" />

{{-- JSON-LD Structured Data (Schema.org) --}}
@if(!empty($seo['schema']))
    @foreach((array) $seo['schema'] as $schemaItem)
        <script type="application/ld+json">
{!! json_encode($schemaItem, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
        </script>
    @endforeach
@endif

