<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Services\SeoService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $rawQuery = $request->input('q');
        $query = is_string($rawQuery) ? trim($rawQuery) : null;
        $selectedCategories = (array) $request->input('categories', []);
        $minPrice = max(0, (float) $request->input('min_price', 0));
        $maxPrice = max($minPrice, (float) $request->input('max_price', 50000));
        $selectedColors = (array) $request->input('colors', []);
        $inStockOnly = $request->has('in_stock');
        $sort = $request->input('sort', 'relevance');

        $baseQuery = Product::where('is_active', true);

        if ($query) {
            $escapedQuery = addcslashes($query, '%_');
            $baseQuery->where(function ($q) use ($escapedQuery) {
                $q->where('name', 'like', "%{$escapedQuery}%")
                    ->orWhere('brand', 'like', "%{$escapedQuery}%")
                    ->orWhere('model_line', 'like', "%{$escapedQuery}%")
                    ->orWhere('description', 'like', "%{$escapedQuery}%");
            });
        }

        if (!empty($selectedCategories)) {
            $baseQuery->whereHas('categories', function ($q) use ($selectedCategories) {
                $q->whereIn('categories.id', $selectedCategories);
            });
        }

        if ($minPrice > 0 || $maxPrice < 50000) {
            $baseQuery->whereBetween('price', [$minPrice, $maxPrice]);
        }

        if ($inStockOnly) {
            $baseQuery->where('stock', '>', 0);
        }

        // As cores disponíveis na busca agora são estritamente as cores dos produtos filtrados
        // e agrupadas por hex_code único para não exibir bolinhas repetidas
        $availableColors = ProductColor::whereIn('product_id', (clone $baseQuery)->select('id'))
            ->select('hex_code', \Illuminate\Support\Facades\DB::raw('MIN(color_name) as color_name'))
            ->groupBy('hex_code')
            ->orderBy('color_name')
            ->get();

        $productsQuery = clone $baseQuery;

        if (!empty($selectedColors)) {
            $productsQuery->whereHas('colors', function ($q) use ($selectedColors) {
                $q->whereIn('hex_code', $selectedColors);
            });
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

        $products = $productsQuery->with(['colors', 'images'])->paginate(12)->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('search._product_list', compact('products', 'selectedColors'))->render(),
                'colors_html' => view('search._color_filter', compact('availableColors', 'selectedColors'))->render(),
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

        $matchedCategory = (count($selectedCategories) === 1 && empty($query))
            ? $categories->firstWhere('id', (int) $selectedCategories[0])
            : null;

        $seo = SeoService::forSearch($query, $matchedCategory);

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
            'sort',
            'seo'
        ));
    }

    /**
     * Retorna sugestões rápidas de busca via AJAX para a barra de pesquisa da Home.
     */
    public function suggestions(Request $request)
    {
        $rawQuery = $request->input('q');
        $query = is_string($rawQuery) ? trim($rawQuery) : '';

        if (mb_strlen($query) < 2) {
            return response()->json([
                'products' => [],
                'categories' => [],
                'total' => 0,
            ]);
        }

        $escapedQuery = addcslashes($query, '%_');

        $products = Product::where('is_active', true)
            ->where(function ($q) use ($escapedQuery) {
                $q->where('name', 'like', "%{$escapedQuery}%")
                    ->orWhere('brand', 'like', "%{$escapedQuery}%")
                    ->orWhere('model_line', 'like', "%{$escapedQuery}%");
            })
            ->with(['images', 'categories'])
            ->take(5)
            ->get()
            ->map(function ($product) {
                $firstImage = $product->images->first();
                $imageSrc = $firstImage ? (str_starts_with($firstImage->image_path, 'http') ? $firstImage->image_path : asset('storage/' . $firstImage->image_path)) : asset('images/placeholder.png');
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'brand' => $product->brand,
                    'price_formatted' => 'R$ ' . number_format($product->price, 2, ',', '.'),
                    'url' => route('product.show', $product->slug),
                    'image' => $imageSrc,
                    'category' => $product->categories->first()?->name,
                ];
            });

        $categories = Category::where('name', 'like', "%{$escapedQuery}%")
            ->take(3)
            ->get(['id', 'name', 'slug'])
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'name' => $cat->name,
                    'url' => route('search', ['categories' => [$cat->id]]),
                ];
            });

        $total = Product::where('is_active', true)
            ->where(function ($q) use ($escapedQuery) {
                $q->where('name', 'like', "%{$escapedQuery}%")
                    ->orWhere('brand', 'like', "%{$escapedQuery}%")
                    ->orWhere('model_line', 'like', "%{$escapedQuery}%");
            })
            ->count();

        return response()->json([
            'products' => $products,
            'categories' => $categories,
            'total' => $total,
        ]);
    }
}
