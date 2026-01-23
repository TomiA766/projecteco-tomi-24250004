<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalAttribute()
    {
        if (!$this->relationLoaded('cartItems')) {
            $this->load(['cartItems', 'cartItems.product']);
        }
        
        return $this->cartItems->sum(function ($item) {
            return $item->product ? $item->product->effective_price * $item->qty : 0;
        });
    }

    public function getItemCountAttribute()
    {
        if (!$this->relationLoaded('cartItems')) {
            $this->load('cartItems');
        }
        
        return $this->cartItems->sum('qty');
    }

    public function getCurrentForUser($userId)
    {
        return static::with('cartItems.product')->where('user_id', $userId)->first();
    }

    public static function getOrCreateForUser($userId)
    {
        // Use firstOrCreate with proper error handling to prevent duplicates
        $cart = static::firstOrCreate(
            ['user_id' => $userId],
            ['user_id' => $userId]
        );
        
        // Ensure relationships are loaded for calculations
        $cart->load(['cartItems', 'cartItems.product']);
        return $cart;
    }
}
