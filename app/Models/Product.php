<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'gtin',
        'brand',
        'model_line',
        'gender',
        'description',
        'price',
        'compare_price',
        'stock',
        'sizes',
        'material',
        'sole_material',
        'image',
        'is_active',
        'weight',
        'height',
        'width',
        'length',
        'technical_specifications',
    ];

    protected $casts = [
        'sizes' => 'array',
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    public function mainImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_main', true);
    }

    public function colors()
    {
        return $this->hasMany(ProductColor::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Synchronize total product stock from the sum of its variations.
     */
    public function syncStockFromVariants(): void
    {
        if ($this->variants()->exists()) {
            $total = (int) $this->variants()->sum('stock');
            $this->update(['stock' => $total]);
        }
    }

    /**
     * Locate a specific variant by size and color (name or hex).
     */
    public function findVariant(string $size, ?string $colorName = null, ?string $colorHex = null): ?ProductVariant
    {
        $query = $this->variants()->where('size', (string) $size);

        if ($colorName || $colorHex) {
            $query->where(function ($q) use ($colorName, $colorHex) {
                if ($colorName) {
                    $q->where('color_name', $colorName);
                }
                if ($colorHex) {
                    $q->orWhere('color_hex', $colorHex);
                }
            });
        }

        return $query->first();
    }

    /**
     * Get stock for a specific size and color combination.
     */
    public function getVariantStock(string $size, ?string $colorName = null, ?string $colorHex = null): int
    {
        $variant = $this->findVariant($size, $colorName, $colorHex);
        return $variant ? (int) $variant->stock : 0;
    }

    /**
     * Check if a specific variation is in stock.
     */
    public function isVariantInStock(string $size, ?string $colorName = null, ?string $colorHex = null): bool
    {
        return $this->getVariantStock($size, $colorName, $colorHex) > 0;
    }

    /**
     * Generate an associative matrix of variations: [color_name => [size => stock]].
     */
    public function getStockMatrix(): array
    {
        $matrix = [];
        $variants = $this->variants;

        foreach ($variants as $v) {
            $colorKey = $v->color_name ?: ($v->color_hex ?: 'Padrão');
            if (!isset($matrix[$colorKey])) {
                $matrix[$colorKey] = [];
            }
            $matrix[$colorKey][$v->size] = (int) $v->stock;
        }

        return $matrix;
    }

    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    public function hasStock($quantity): bool
    {
        return $this->stock >= $quantity;
    }

    public function hasDiscount(): bool
    {
        return !empty($this->compare_price) && $this->compare_price > $this->price;
    }

    public function discountPercentage(): int
    {
        if (!$this->hasDiscount() || $this->compare_price <= 0) {
            return 0;
        }

        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    public function availableSizes(): array
    {
        if (empty($this->sizes)) {
            // Default sneaker sizes if none specifically assigned
            return ['38', '39', '40', '41', '42', '43'];
        }

        return is_array($this->sizes) ? $this->sizes : [];
    }
}
