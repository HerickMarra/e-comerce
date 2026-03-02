<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show(string $slug)
    {
        $product = Product::with(['images', 'categories'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('product.show', compact('product'));
    }
}
