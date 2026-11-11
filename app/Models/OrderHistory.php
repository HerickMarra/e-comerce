<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderHistory extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'actor_type',
        'actor_name',
        'action',
        'title',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSystem(): bool
    {
        return $this->actor_type === 'system';
    }

    public function isUser(): bool
    {
        return $this->actor_type === 'user';
    }

    /**
     * Get appropriate icon for the action.
     */
    public function getIconAttribute(): string
    {
        return match ($this->action) {
            'order_created' => 'shopping_bag',
            'pix_generated' => 'qr_code_2',
            'credit_card_authorized', 'credit_card_processed' => 'credit_card',
            'payment_confirmed' => 'verified',
            'shipping_label_created' => 'local_shipping',
            'shipping_label_failed' => 'warning',
            'refund_requested', 'refund_processed' => 'currency_exchange',
            'status_changed' => 'published_with_changes',
            'note_added' => 'edit_note',
            default => 'info',
        };
    }

    /**
     * Get style color classes for timeline indicators.
     */
    public function getColorClassesAttribute(): array
    {
        return match ($this->action) {
            'order_created' => [
                'bg' => 'bg-indigo-50 dark:bg-indigo-950/40',
                'text' => 'text-indigo-600 dark:text-indigo-400',
                'border' => 'border-indigo-200 dark:border-indigo-800/40',
                'badge' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300',
            ],
            'pix_generated' => [
                'bg' => 'bg-teal-50 dark:bg-teal-950/40',
                'text' => 'text-teal-600 dark:text-teal-400',
                'border' => 'border-teal-200 dark:border-teal-800/40',
                'badge' => 'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300',
            ],
            'credit_card_authorized', 'credit_card_processed' => [
                'bg' => 'bg-blue-50 dark:bg-blue-950/40',
                'text' => 'text-blue-600 dark:text-blue-400',
                'border' => 'border-blue-200 dark:border-blue-800/40',
                'badge' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
            ],
            'payment_confirmed' => [
                'bg' => 'bg-emerald-50 dark:bg-emerald-950/40',
                'text' => 'text-emerald-600 dark:text-emerald-400',
                'border' => 'border-emerald-200 dark:border-emerald-800/40',
                'badge' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
            ],
            'shipping_label_created' => [
                'bg' => 'bg-cyan-50 dark:bg-cyan-950/40',
                'text' => 'text-cyan-600 dark:text-cyan-400',
                'border' => 'border-cyan-200 dark:border-cyan-800/40',
                'badge' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300',
            ],
            'shipping_label_failed' => [
                'bg' => 'bg-red-50 dark:bg-red-950/40',
                'text' => 'text-red-600 dark:text-red-400',
                'border' => 'border-red-200 dark:border-red-800/40',
                'badge' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
            ],
            'refund_requested', 'refund_processed' => [
                'bg' => 'bg-rose-50 dark:bg-rose-950/40',
                'text' => 'text-rose-600 dark:text-rose-400',
                'border' => 'border-rose-200 dark:border-rose-800/40',
                'badge' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
            ],
            'status_changed' => [
                'bg' => 'bg-amber-50 dark:bg-amber-950/40',
                'text' => 'text-amber-600 dark:text-amber-400',
                'border' => 'border-amber-200 dark:border-amber-800/40',
                'badge' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
            ],
            default => [
                'bg' => 'bg-slate-50 dark:bg-slate-900/50',
                'text' => 'text-slate-600 dark:text-slate-400',
                'border' => 'border-slate-200 dark:border-slate-800',
                'badge' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
            ],
        };
    }
}
