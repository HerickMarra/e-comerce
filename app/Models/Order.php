<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'stock_restored',
        'subtotal',
        'shipping_amount',
        'total_amount',
        'payment_method',
        'payment_id',
        'address_info',
        'notes',
        'shipping_id',
        'shipping_service_name',
        'shipping_tracking_url',
        'shipping_simulacao_id',
        'shipping_modalidade',
        'shipping_descricao_conteudo',
        'shipping_label_url',
        'shipping_tracking_code',
        'shipping_api_response',
    ];

    protected $casts = [
        'address_info' => 'array',
        'subtotal' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipping_api_response' => 'array',
        'stock_restored' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Restore stock for all items in this order if cancelled or refunded.
     */
    public function restoreStock(): bool
    {
        if ($this->stock_restored) {
            return false;
        }

        foreach ($this->items as $item) {
            $variant = $item->variant;
            if (!$variant && $item->product && $item->size) {
                $variant = $item->product->findVariant($item->size, $item->color_name, $item->color);
            }

            if ($variant) {
                $variant->recordMovement(
                    'cancellation_restore',
                    (int) $item->quantity,
                    "Reposição por cancelamento/estorno do pedido #{$this->order_number}",
                    $this->id
                );
            } elseif ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        $this->update(['stock_restored' => true]);
        return true;
    }
}
