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
        $products = Product::with('categories')->latest()->paginate(10);
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
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'technical_specifications' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_urls' => 'nullable|array',
            'image_urls.*' => 'url',
            'image_order' => 'required|array',
            'colors' => 'nullable|array',
            'colors.*.hex' => 'required|string|max:7',
            'colors.*.name' => 'nullable|string|max:255',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . rand(1000, 9999),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'weight' => $validated['weight'] ?? null,
            'height' => $validated['height'] ?? null,
            'width' => $validated['width'] ?? null,
            'length' => $validated['length'] ?? null,
            'technical_specifications' => $validated['technical_specifications'] ?? null,
            'is_active' => true,
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
                if (isset($colorData['hex'])) {
                    $product->colors()->create([
                        'hex_code' => $colorData['hex'],
                        'color_name' => $colorData['name'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produto criado com sucesso com sua galeria organizada!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load(['categories', 'images']);
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
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'height' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'length' => 'nullable|numeric|min:0',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'technical_specifications' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_urls' => 'nullable|array',
            'image_urls.*' => 'url',
            'image_order' => 'required|array',
            'colors' => 'nullable|array',
            'colors.*.hex' => 'required|string|max:7',
            'colors.*.name' => 'nullable|string|max:255',
        ]);

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'weight' => $validated['weight'] ?? null,
            'height' => $validated['height'] ?? null,
            'width' => $validated['width'] ?? null,
            'length' => $validated['length'] ?? null,
            'technical_specifications' => $validated['technical_specifications'] ?? null,
        ]);

        $product->categories()->sync($request->categories);

        // Sync images
        $imageOrder = $request->input('image_order', []);
        $files = $request->file('images', []);
        $urls = $request->input('image_urls', []);

        // Get current image IDs to see what was removed
        $currentImages = $product->images;
        $orderIds = array_keys($imageOrder);

        // Remove images not in the new order
        foreach ($currentImages as $existingImage) {
            if (!in_array((string) $existingImage->id, $orderIds)) {
                // If it's a local file, we could delete it from storage here
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
                // Existing image, just update position and is_main
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
                if (isset($colorData['hex'])) {
                    $product->colors()->create([
                        'hex_code' => $colorData['hex'],
                        'color_name' => $colorData['name'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Performance a soft delete (only hides the product)
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Produto removido com sucesso!');
    }
}
