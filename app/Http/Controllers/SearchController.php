<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        $selectedCategories = $request->input('categories', []);
        $minPrice = $request->input('min_price', 0);
        $maxPrice = $request->input('max_price', 50000);
        $selectedColors = $request->input('colors', []);
        $inStockOnly = $request->has('in_stock');
        $sort = $request->input('sort', 'relevance');

        $productsQuery = Product::where('is_active', true);

        if ($query) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });
        }

        if (!empty($selectedCategories)) {
            $productsQuery->whereHas('categories', function ($q) use ($selectedCategories) {
                $q->whereIn('categories.id', $selectedCategories);
            });
        }

        if ($minPrice > 0 || $maxPrice < 50000) {
            $productsQuery->whereBetween('price', [$minPrice, $maxPrice]);
        }

        if (!empty($selectedColors)) {
            $productsQuery->whereHas('colors', function ($q) use ($selectedColors) {
                $q->whereIn('hex_code', $selectedColors);
            });
        }

        if ($inStockOnly) {
            $productsQuery->where('stock', '>', 0);
        }

        switch ($sort) {
            case 'price_low':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'price_high':
                $productsQuery->orderBy('price', 'desc');
                break;
            case 'newest':
                $productsQuery->orderBy('created_at', 'desc');
                break;
            default:
                $productsQuery->orderBy('created_at', 'desc'); // Fallback to newest for relevance for now
                break;
        }

        $products = $productsQuery->paginate(12)->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('search._product_list', compact('products'))->render(),
                'pagination' => (string) $products->links(),
                'total' => $products->total(),
                'url' => $request->fullUrl()
            ]);
        }

        $categories = Category::withCount([
            'products' => function ($query) {
                $query->where('is_active', true);
            }
        ])->get();

        $availableColors = ProductColor::whereHas('product', function ($query) {
            $query->where('is_active', true);
        })
            ->select('hex_code', 'color_name')
            ->distinct()
            ->get();

        return view('search.index', compact(
            'products',
            'categories',
            'availableColors',
            'query',
            'selectedCategories',
            'minPrice',
            'maxPrice',
            'selectedColors',
            'inStockOnly',
            'sort'
        ));
    }
}
