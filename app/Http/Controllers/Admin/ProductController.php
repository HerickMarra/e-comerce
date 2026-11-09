<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['categories', 'images', 'colors'])->latest()->paginate(12);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'model_line' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:Masculino,Feminino,Unissex,Infantil',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sizes' => 'nullable|array',
            'material' => 'nullable|string|max:255',
            'sole_material' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'technical_specifications' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'image_urls' => 'nullable|array',
            'image_urls.*' => 'url',
            'image_order' => 'nullable|array',
            'colors' => 'nullable|array',
            'colors.*.hex' => 'required|string|max:7',
            'colors.*.name' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        // Default sneaker box dimensions if not provided (0.8kg, 34cm x 22cm x 12cm)
        $weight = !empty($validated['weight']) ? (float) $validated['weight'] : 0.8;
        $height = !empty($validated['height']) ? (float) $validated['height'] : 12.0;
        $width = !empty($validated['width']) ? (float) $validated['width'] : 22.0;
        $length = !empty($validated['length']) ? (float) $validated['length'] : 34.0;

        // Process sizes array
        $sizes = $request->input('sizes', []);
        if (is_array($sizes)) {
            // Sort sizes numerically if possible
            usort($sizes, fn($a, $b) => is_numeric($a) && is_numeric($b) ? ($a <=> $b) : strcmp($a, $b));
        }

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . rand(1000, 9999),
            'brand' => $validated['brand'] ?? null,
            'model_line' => $validated['model_line'] ?? null,
            'gender' => $validated['gender'] ?? 'Unissex',
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'compare_price' => $validated['compare_price'] ?? null,
            'stock' => $validated['stock'],
            'sizes' => $sizes,
            'material' => $validated['material'] ?? null,
            'sole_material' => $validated['sole_material'] ?? null,
            'weight' => $weight,
            'height' => $height,
            'width' => $width,
            'length' => $length,
            'technical_specifications' => $validated['technical_specifications'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $product->categories()->attach($request->categories);

        // Map images
        $imageOrder = $request->input('image_order', []);
        $files = $request->file('images', []);
        $urls = $request->input('image_urls', []);

        $fileCounter = 0;
        $urlCounter = 0;

        foreach ($imageOrder as $id => $position) {
            $path = null;

            if (str_starts_with($id, 'file_')) {
                if (isset($files[$fileCounter])) {
                    $path = $files[$fileCounter]->store('products', 'public');
                    $fileCounter++;
                }
            } elseif (str_starts_with($id, 'ext_')) {
                if (isset($urls[$urlCounter])) {
                    $path = $urls[$urlCounter];
                    $urlCounter++;
                }
            }

            if ($path) {
                $product->images()->create([
                    'path' => $path,
                    'position' => (int) $position,
                    'is_main' => (int) $position === 0,
                ]);
            }
        }

        // Save colors
        if ($request->has('colors')) {
            foreach ($request->colors as $colorData) {
                if (!empty($colorData['hex'])) {
                    $product->colors()->create([
                        'hex_code' => $colorData['hex'],
                        'color_name' => $colorData['name'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Tênis cadastrado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load(['categories', 'images', 'colors']);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'model_line' => 'nullable|string|max:100',
            'gender' => 'nullable|string|in:Masculino,Feminino,Unissex,Infantil',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'sizes' => 'nullable|array',
            'material' => 'nullable|string|max:255',
            'sole_material' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'technical_specifications' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp,avif|max:10240',
            'image_urls' => 'nullable|array',
            'image_urls.*' => 'url',
            'image_order' => 'nullable|array',
            'colors' => 'nullable|array',
            'colors.*.hex' => 'required|string|max:7',
            'colors.*.name' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $weight = !empty($validated['weight']) ? (float) $validated['weight'] : ($product->weight ?: 0.8);
        $height = !empty($validated['height']) ? (float) $validated['height'] : ($product->height ?: 12.0);
        $width = !empty($validated['width']) ? (float) $validated['width'] : ($product->width ?: 22.0);
        $length = !empty($validated['length']) ? (float) $validated['length'] : ($product->length ?: 34.0);

        $sizes = $request->input('sizes', []);
        if (is_array($sizes)) {
            usort($sizes, fn($a, $b) => is_numeric($a) && is_numeric($b) ? ($a <=> $b) : strcmp($a, $b));
        }

        $product->update([
            'name' => $validated['name'],
            'brand' => $validated['brand'] ?? null,
            'model_line' => $validated['model_line'] ?? null,
            'gender' => $validated['gender'] ?? 'Unissex',
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'compare_price' => $validated['compare_price'] ?? null,
            'stock' => $validated['stock'],
            'sizes' => $sizes,
            'material' => $validated['material'] ?? null,
            'sole_material' => $validated['sole_material'] ?? null,
            'weight' => $weight,
            'height' => $height,
            'width' => $width,
            'length' => $length,
            'technical_specifications' => $validated['technical_specifications'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        $product->categories()->sync($request->categories);

        // Sync images
        $imageOrder = $request->input('image_order', []);
        $files = $request->file('images', []);
        $urls = $request->input('image_urls', []);

        $currentImages = $product->images;
        $orderIds = array_keys($imageOrder);

        foreach ($currentImages as $existingImage) {
            if (!in_array((string) $existingImage->id, $orderIds)) {
                $existingImage->delete();
            }
        }

        $fileCounter = 0;
        $urlCounter = 0;

        foreach ($imageOrder as $id => $position) {
            if (str_starts_with($id, 'file_')) {
                if (isset($files[$fileCounter])) {
                    $path = $files[$fileCounter]->store('products', 'public');
                    $product->images()->create([
                        'path' => $path,
                        'position' => (int) $position,
                        'is_main' => (int) $position === 0,
                    ]);
                    $fileCounter++;
                }
            } elseif (str_starts_with($id, 'ext_')) {
                if (isset($urls[$urlCounter])) {
                    $product->images()->create([
                        'path' => $urls[$urlCounter],
                        'position' => (int) $position,
                        'is_main' => (int) $position === 0,
                    ]);
                    $urlCounter++;
                }
            } else {
                $img = $product->images()->find($id);
                if ($img) {
                    $img->update([
                        'position' => (int) $position,
                        'is_main' => (int) $position === 0,
                    ]);
                }
            }
        }

        // Sync colors
        $product->colors()->delete();
        if ($request->has('colors')) {
            foreach ($request->colors as $colorData) {
                if (!empty($colorData['hex'])) {
                    $product->colors()->create([
                        'hex_code' => $colorData['hex'],
                        'color_name' => $colorData['name'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Tênis atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Tênis removido com sucesso!');
    }
}
