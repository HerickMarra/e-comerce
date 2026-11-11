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

    protected $appends = [
        'public_shipping_label_url',
    ];

    /**
     * Mutator to guarantee shipping_label_url always carries ?public=true parameter.
     */
    public function setShippingLabelUrlAttribute($value): void
    {
        if ($value && !str_contains($value, 'public=')) {
            $value = trim($value) . (str_contains($value, '?') ? '&' : '?') . 'public=true';
        }
        $this->attributes['shipping_label_url'] = $value;
    }

    /**
     * Accessor to guarantee direct download link for Melhor Envio label without login gate.
     */
    public function getPublicShippingLabelUrlAttribute(): ?string
    {
        if (empty($this->shipping_label_url)) {
            return null;
        }

        $url = trim($this->shipping_label_url);
        if (!str_contains($url, 'public=')) {
            $url .= (str_contains($url, '?') ? '&' : '?') . 'public=true';
        }

        return $url;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class)->orderBy('created_at', 'asc');
    }

    /**
     * Record an audit history event for this order.
     */
    public function recordHistory(
        string $action,
        string $title,
        ?string $description = null,
        array $metadata = [],
        ?User $user = null,
        ?string $actorName = null,
        ?\DateTimeInterface $createdAt = null
    ): OrderHistory {
        $currentUser = $user ?: (auth()->check() ? auth()->user() : null);

        $actorType = $currentUser ? 'user' : 'system';
        $resolvedActorName = $currentUser 
            ? $currentUser->name 
            : ($actorName ?: 'Sistema');

        $data = [
            'user_id' => $currentUser?->id,
            'actor_type' => $actorType,
            'actor_name' => $resolvedActorName,
            'action' => $action,
            'title' => $title,
            'description' => $description,
            'metadata' => !empty($metadata) ? $metadata : null,
        ];

        if ($createdAt) {
            $data['created_at'] = $createdAt;
            $data['updated_at'] = $createdAt;
        }

        return $this->histories()->create($data);
    }

    /**
     * Ensure historical events exist for orders created before the history system was deployed.
     */
    public function ensureInitialHistory(): void
    {
        if ($this->histories()->exists()) {
            return;
        }

        // 1. Order Creation
        $clientUser = $this->user;
        $this->recordHistory(
            action: 'order_created',
            title: 'Pedido Gerado no Sistema',
            description: "Pedido criado no valor total de R$ " . number_format($this->total_amount, 2, ',', '.') . " (" . strtoupper($this->payment_method ?? 'N/A') . ").",
            metadata: ['subtotal' => $this->subtotal, 'shipping_amount' => $this->shipping_amount, 'total' => $this->total_amount],
            user: $clientUser,
            actorName: $clientUser ? $clientUser->name : 'Cliente (Checkout)',
            createdAt: $this->created_at
        );

        // 2. Gateway Billing Generated
        if ($this->payment_id) {
            $isPix = strtolower($this->payment_method ?? '') === 'pix';
            $this->recordHistory(
                action: $isPix ? 'pix_generated' : 'credit_card_processed',
                title: $isPix ? 'Código PIX Gerado no Gateway' : 'Cobrança Gerada no Gateway',
                description: "Cobrança gerada com sucesso no Asaas. ID: {$this->payment_id}",
                metadata: ['payment_id' => $this->payment_id, 'method' => $this->payment_method],
                user: null,
                actorName: 'Gateway Asaas',
                createdAt: $this->created_at->addSeconds(2)
            );
        }

        // 3. Paid
        if (in_array($this->status, ['paid', 'shipped', 'delivered'])) {
            $this->recordHistory(
                action: 'payment_confirmed',
                title: 'Pagamento Confirmado',
                description: 'Pagamento confirmado e compensado com sucesso.',
                metadata: ['status' => 'paid'],
                user: null,
                actorName: 'Sistema (Asaas)',
                createdAt: $this->updated_at->gt($this->created_at) ? $this->updated_at : $this->created_at->addMinutes(5)
            );
        }

        // 4. Shipping Label Recruited
        if ($this->shipping_id) {
            $this->recordHistory(
                action: 'shipping_label_created',
                title: 'Etiqueta Gerada no Melhor Envio',
                description: "Frete contratado no Melhor Envio. ID #{$this->shipping_id}" . ($this->shipping_tracking_code ? " • Rastreio: {$this->shipping_tracking_code}" : ''),
                metadata: ['shipping_id' => $this->shipping_id, 'tracking' => $this->shipping_tracking_code, 'service' => $this->shipping_service_name],
                user: null,
                actorName: 'Melhor Envio API',
                createdAt: $this->updated_at->addSeconds(10)
            );
        }

        // 5. Refunded
        if ($this->status === 'refunded') {
            $this->recordHistory(
                action: 'refund_processed',
                title: 'Reembolso / Estorno Realizado',
                description: 'Valor reembolsado ao cliente.',
                metadata: ['status' => 'refunded'],
                user: null,
                actorName: 'Sistema',
                createdAt: $this->updated_at
            );
        }
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
