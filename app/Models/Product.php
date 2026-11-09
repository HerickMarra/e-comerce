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
