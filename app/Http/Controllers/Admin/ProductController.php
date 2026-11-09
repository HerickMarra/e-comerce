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
            'matrix' => 'nullable|array',
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

        // Sync colors & variation inventory matrix
        $this->syncVariants($product, $sizes, $request->input('colors', []), $request->input('matrix', []), (int) $validated['stock']);

        return redirect()->route('admin.products.index')->with('success', 'Tênis cadastrado com sucesso!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->load(['categories', 'images', 'colors', 'variants']);
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
            'matrix' => 'nullable|array',
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

        // Sync colors & variation inventory matrix
        $this->syncVariants($product, $sizes, $request->input('colors', []), $request->input('matrix', []), (int) $validated['stock']);

        return redirect()->route('admin.products.index')->with('success', 'Tênis atualizado com sucesso!');
    }

    /**
     * Synchronize product colors and variation inventory matrix.
     */
    protected function syncVariants(Product $product, array $sizes, array $submittedColors, array $matrixData, int $fallbackStock = 0): void
    {
        $existingColors = $product->colors()->get();
        $savedColors = [];
        $keepColorIds = [];

        // 1. Process and synchronize colors without blind deletion to maintain referential integrity
        foreach ($submittedColors as $index => $colorData) {
            if (empty($colorData['hex'])) {
                continue;
            }

            $name = trim($colorData['name'] ?? '') ?: 'Cor ' . ($index + 1);
            $hex = $colorData['hex'];

            $colorModel = $existingColors->first(function ($c) use ($name, $hex) {
                return $c->color_name === $name || $c->hex_code === $hex;
            });

            if ($colorModel) {
                $colorModel->update(['color_name' => $name, 'hex_code' => $hex]);
            } else {
                $colorModel = $product->colors()->create(['color_name' => $name, 'hex_code' => $hex]);
            }

            $keepColorIds[] = $colorModel->id;
            $savedColors[$index] = $colorModel;
        }

        // Delete colors removed by admin
        $product->colors()->whereNotIn('id', $keepColorIds)->delete();

        // 2. Synchronize variations matrix
        $validVariantIds = [];
        $totalStock = 0;

        if (empty($savedColors)) {
            $defaultColor = $product->colors()->create([
                'hex_code' => '#000000',
                'color_name' => 'Padrão',
            ]);
            $savedColors[0] = $defaultColor;
        }

        if (empty($sizes)) {
            $sizes = ['38', '39', '40', '41', '42', '43'];
        }

        foreach ($savedColors as $colorIndex => $colorModel) {
            foreach ($sizes as $size) {
                $size = (string) $size;
                $colorName = $colorModel->color_name ?: $colorModel->hex_code;

                $hasSubmittedQty = isset($matrixData[$colorIndex][$size]) || isset($matrixData[$colorName][$size]);
                $qty = null;
                if (isset($matrixData[$colorIndex][$size])) {
                    $qty = max(0, (int) $matrixData[$colorIndex][$size]);
                } elseif (isset($matrixData[$colorName][$size])) {
                    $qty = max(0, (int) $matrixData[$colorName][$size]);
                }

                $variant = $product->variants()
                    ->where('size', $size)
                    ->where(function ($q) use ($colorModel, $colorName) {
                        $q->where('product_color_id', $colorModel->id)
                          ->orWhere('color_name', $colorName);
                    })
                    ->first();

                if ($variant) {
                    $targetQty = $hasSubmittedQty ? $qty : (int) $variant->stock;
                    if ($hasSubmittedQty && (int) $variant->stock !== $targetQty) {
                        $variant->setStock($targetQty, 'Ajuste manual via painel administrativo', auth()->id());
                    }
                    $variant->update([
                        'product_color_id' => $colorModel->id,
                        'color_name' => $colorName,
                        'color_hex' => $colorModel->hex_code,
                    ]);
                } else {
                    $initialStock = $hasSubmittedQty ? $qty : 0;
                    $variant = $product->variants()->create([
                        'product_color_id' => $colorModel->id,
                        'color_name' => $colorName,
                        'color_hex' => $colorModel->hex_code,
                        'size' => $size,
                        'stock' => 0,
                        'sku' => ($product->sku ? "{$product->sku}-{$size}-" . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $colorName), 0, 3)) : null),
                    ]);

                    if ($initialStock > 0) {
                        $variant->recordMovement('initial_entry', $initialStock, 'Estoque inicial da variação cadastrado no painel', null, auth()->id());
                    }
                }

                $totalStock += (int) $variant->stock;
                $validVariantIds[] = $variant->id;
            }
        }

        // Clean up or zero out variants no longer in active sizes/colors
        $orphanedVariants = $product->variants()->whereNotIn('id', $validVariantIds)->get();
        foreach ($orphanedVariants as $orphan) {
            $hasOrders = \App\Models\OrderItem::where('product_variant_id', $orphan->id)->exists();
            if ($hasOrders) {
                if ($orphan->stock > 0) {
                    $orphan->setStock(0, 'Variação desativada do produto pelo administrador', auth()->id());
                }
            } else {
                $orphan->delete();
            }
        }

        // 3. Keep total product stock aligned
        $product->update(['stock' => $totalStock]);
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
