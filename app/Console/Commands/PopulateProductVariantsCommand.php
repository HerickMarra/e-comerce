<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PopulateProductVariantsCommand extends Command
{
    protected $signature = 'products:populate-variants';
    protected $description = 'Populate product variants matrix (sizes x colors) for existing products';

    public function handle(): int
    {
        $products = Product::with('colors')->get();
        $this->info("Found {$products->count()} products to process.");

        foreach ($products as $product) {
            $sizes = $product->availableSizes();
            $colors = $product->colors;

            if (empty($sizes)) {
                $sizes = ['38', '39', '40', '41', '42', '43'];
            }

            // If no colors defined, create a default color
            if ($colors->isEmpty()) {
                $defaultColor = $product->colors()->create([
                    'hex_code' => '#000000',
                    'color_name' => 'Padrão',
                ]);
                $colors = collect([$defaultColor]);
            }

            $combinationsCount = count($sizes) * $colors->count();
            $currentStock = (int) $product->stock;

            // Distribute current stock among combinations
            $baseStock = $combinationsCount > 0 ? (int) floor($currentStock / $combinationsCount) : 0;
            $remainder = $combinationsCount > 0 ? ($currentStock % $combinationsCount) : 0;

            foreach ($colors as $colorIndex => $color) {
                foreach ($sizes as $sizeIndex => $size) {
                    $stockToAssign = $baseStock;
                    if ($remainder > 0) {
                        $stockToAssign++;
                        $remainder--;
                    }

                    $variant = ProductVariant::firstOrCreate(
                        [
                            'product_id' => $product->id,
                            'size' => (string) $size,
                            'color_name' => $color->color_name ?: $color->hex_code,
                        ],
                        [
                            'product_color_id' => $color->id,
                            'color_hex' => $color->hex_code,
                            'stock' => $stockToAssign,
                            'sku' => ($product->sku ? "{$product->sku}-{$size}-" . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $color->color_name ?: 'COL'), 0, 3)) : null),
                        ]
                    );

                    // If newly created and has stock, record initial stock movement
                    if ($variant->wasRecentlyCreated && $variant->stock > 0) {
                        $variant->recordMovement('initial_entry', $variant->stock, 'Estoque inicial da variação migrado do produto');
                    }
                }
            }

            $product->syncStockFromVariants();
        }

        $this->info('Product variants successfully populated!');
        return 0;
    }
}
