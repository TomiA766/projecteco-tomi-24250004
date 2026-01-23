<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ProductImageHelper
{
    /**
     * Get product image URL with fallback to default
     */
    public static function getProductImage($product)
    {
        if ($product->image) {
            $imagePath = $product->image;
            // Remove 'products/' prefix if present
            if (str_starts_with($imagePath, 'products/')) {
                $imagePath = substr($imagePath, 8);
            }
            
            // Check in storage/products first
            if (file_exists(public_path('storage/products/' . $imagePath))) {
                return asset('storage/products/' . ltrim($imagePath, '/'));
            }
            // Fallback to images/products
            if (file_exists(public_path('images/products/' . $imagePath))) {
                return asset('images/products/' . $imagePath);
            }
        }
        
        return asset('images/default/default-product.png');
    }
    
    /**
     * Store product image to public directory
     */
    public static function storeProductImage($image, $filename = null)
    {
        if (!$image) {
            return null;
        }
        
        if (!$filename) {
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        }
        
        // Move to public/images/products directory
        $image->move(public_path('images/products'), $filename);
        
        return $filename;
    }
    
    /**
     * Delete product image from public directory
     */
    public static function deleteProductImage($filename)
    {
        if (!$filename) {
            return false;
        }
        
        $imagePath = public_path('images/products/' . $filename);
        
        if (file_exists($imagePath)) {
            unlink($imagePath);
            return true;
        }
        
        return false;
    }
    
    /**
     * Get category image URL with fallback
     */
    public static function getCategoryImage($category)
    {
        if ($category->image) {
            return asset('images/categories/' . $category->image);
        }
        
        return asset('images/default/default-category.svg');
    }
    
    /**
     * Store category image to public directory
     */
    public static function storeCategoryImage($image, $filename = null)
    {
        if (!$image) {
            return null;
        }
        
        if (!$filename) {
            $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
        }
        
        // Move to public/images/categories directory
        $image->move(public_path('images/categories'), $filename);
        
        return $filename;
    }
    
    /**
     * Delete category image from public directory
     */
    public static function deleteCategoryImage($filename)
    {
        if (!$filename) {
            return false;
        }
        
        $imagePath = public_path('images/categories/' . $filename);
        
        if (file_exists($imagePath)) {
            unlink($imagePath);
            return true;
        }
        
        return false;
    }
}