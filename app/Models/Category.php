<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\ProductImageHelper;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productsCount()
    {
        return $this->products()->count();
    }

    /**
     * Get category image URL with fallback to default
     */
    public function getImageUrlAttribute()
    {
        return ProductImageHelper::getCategoryImage($this);
    }

    /**
     * Get category image path for display
     */
    public function getImagePathAttribute()
    {
        if ($this->image) {
            return 'images/categories/' . $this->image;
        }
        
        return 'images/default/default-category.svg';
    }
}
