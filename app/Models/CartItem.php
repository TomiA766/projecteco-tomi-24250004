<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'qty',
    ];

    protected $casts = [
        'qty' => 'integer',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute()
    {
        return $this->product->effective_price * $this->qty;
    }

    public function incrementQty($amount = 1)
    {
        $this->increment('qty', $amount);
    }

    public function decrementQty($amount = 1)
    {
        if ($this->qty > $amount) {
            $this->decrement('qty', $amount);
            return true;
        }
        return false;
    }
}
