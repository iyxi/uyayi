<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['user_id', 'order_number', 'status', 'shipping_address'];

    protected $appends = ['computed_total', 'computed_subtotal'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getComputedSubtotalAttribute(): float
    {
        $items = $this->relationLoaded('items') ? $this->items : $this->items()->get();

        return round((float) $items->sum(function ($item) {
            if (isset($item->subtotal)) {
                return (float) $item->subtotal;
            }

            return ((float) ($item->unit_price ?? 0)) * ((int) ($item->quantity ?? 0));
        }), 2);
    }

    public function getComputedTotalAttribute(): float
    {
        if ($this->relationLoaded('payment') && $this->payment) {
            return round((float) $this->payment->amount, 2);
        }

        if ($this->payment()->exists()) {
            return round((float) ($this->payment()->value('amount') ?? 0), 2);
        }

        return $this->computed_subtotal;
    }

    public function getTotalAttribute(): float
    {
        return $this->computed_total;
    }
}
