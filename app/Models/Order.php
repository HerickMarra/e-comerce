<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'status',
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
    ];

    protected $casts = [
        'address_info' => 'array',
        'subtotal' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
