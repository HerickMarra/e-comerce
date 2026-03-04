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
        'description',
        'price',
        'stock',
        'image',
        'is_active',
        'weight',
        'height',
        'width',
        'length',
        'technical_specifications',
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

    public function inStock()
    {
        return $this->stock > 0;
    }

    public function hasStock($quantity)
    {
        return $this->stock >= $quantity;
    }
}
