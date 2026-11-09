<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\SeoService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::with(['images', 'categories', 'colors', 'variants'])
            ->where('slug', $slug)
            ->first();

        if (!$product) {
            abort(404, 'Produto não encontrado.');
        }

        // Handle inactive / discontinued products
        if (!$product->is_active) {
            $firstCategory = $product->categories->first();
            if ($firstCategory) {
                // 301 Permanent Redirect preserves SEO link juice and routes shopper to related items
                return redirect()->to(route('search', ['categories' => [$firstCategory->id]]), 301);
            }

            // 410 Gone explicitly tells search engines this URL was permanently removed
            abort(410, 'Este produto foi descontinuado e não está mais disponível.');
        }

        $seo = SeoService::forProduct($product);

        return view('product.show', compact('product', 'seo'));
    }
}
