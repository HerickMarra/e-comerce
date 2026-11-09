<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'product_color_id',
        'color_name',
        'color_hex',
        'size',
        'stock',
        'sku',
        'price',
    ];

    protected $casts = [
        'stock' => 'integer',
        'price' => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(ProductColor::class, 'product_color_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_variant_id');
    }

    /**
     * Record a stock movement and synchronize product aggregate stock.
     */
    public function recordMovement(string $type, int $delta, ?string $reason = null, ?int $orderId = null, ?int $userId = null): StockMovement
    {
        $before = (int) $this->stock;
        $after = max(0, $before + $delta);

        $this->update(['stock' => $after]);

        $movement = StockMovement::create([
            'product_id' => $this->product_id,
            'product_variant_id' => $this->id,
            'order_id' => $orderId,
            'user_id' => $userId,
            'type' => $type,
            'quantity_before' => $before,
            'quantity_delta' => $delta,
            'quantity_after' => $after,
            'size' => $this->size,
            'color_name' => $this->color_name,
            'reason' => $reason,
        ]);

        $this->product?->syncStockFromVariants();

        return $movement;
    }

    /**
     * Adjust stock directly (e.g. from admin matrix).
     */
    public function setStock(int $newStock, ?string $reason = 'Ajuste manual de estoque', ?int $userId = null): void
    {
        $newStock = max(0, $newStock);
        $delta = $newStock - (int) $this->stock;

        if ($delta !== 0) {
            $this->recordMovement('manual_adjustment', $delta, $reason, null, $userId);
        }
    }
}
