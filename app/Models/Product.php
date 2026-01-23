<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\ProductImageHelper;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'discount_price',
        'stock',
        'image',
        'description',
        'category_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get product image URL with fallback to default
     */
    public function getImageUrlAttribute()
    {
        return ProductImageHelper::getProductImage($this);
    }

    /**
     * Get product image path for display
     */
    public function getImagePathAttribute()
    {
        if ($this->image) {
            $imagePath = $this->image;
            // Remove 'products/' prefix if present
            if (str_starts_with($imagePath, 'products/')) {
                $imagePath = substr($imagePath, 8);
            }
            
            // Check in storage/products first
            if (file_exists(public_path('storage/products/' . $imagePath))) {
                return 'storage/products/' . ltrim($imagePath, '/');
            }
            // Fallback to images/products
            if (file_exists(public_path('images/products/' . $imagePath))) {
                return 'images/products/' . $imagePath;
            }
        }
        
        return 'images/default/default-product.png';
    }

    /**
     * Get the effective price (discount price if available, otherwise regular price)
     */
    public function getEffectivePriceAttribute()
    {
        return $this->discount_price ?? $this->price;
    }

    /**
     * Check if product has discount
     */
    public function hasDiscount(): bool
    {
        return $this->discount_price !== null && $this->discount_price < $this->price;
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->hasDiscount()) {
            return 0;
        }

        return round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    /**
     * Get formatted effective price
     */
    public function getFormattedEffectivePriceAttribute()
    {
        return 'Rp ' . number_format($this->effective_price, 0, ',', '.');
    }

    /**
     * Get formatted regular price
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get formatted discount price
     */
    public function getFormattedDiscountPriceAttribute()
    {
        return $this->discount_price ? 'Rp ' . number_format($this->discount_price, 0, ',', '.') : null;
    }
}
