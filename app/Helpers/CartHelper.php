<?php

namespace App\Helpers;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartHelper
{
    /**
     * Get current cart item count (safely handles guests)
     */
    public static function getCartCount()
    {
        try {
            if (!Auth::check()) {
                return 0;
            }
            
            $cart = self::getCurrentCart();
            
            if (!$cart) {
                return 0;
            }
            
            return $cart->item_count;
        } catch (\Exception $e) {
            Log::error('CartHelper getCartCount error: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get current cart total (safely handles guests)
     */
    public static function getCartTotal()
    {
        try {
            if (!Auth::check()) {
                return 0;
            }
            
            $cart = self::getCurrentCart();
            
            if (!$cart) {
                return 0;
            }
            
            return $cart->total;
        } catch (\Exception $e) {
            Log::error('CartHelper getCartTotal error: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get current cart for authenticated user (safe)
     */
    private static function getCurrentCart()
    {
        try {
            if (!Auth::check()) {
                return null;
            }
            
            return Cart::with(['cartItems', 'cartItems.product'])->where('user_id', Auth::id())->first();
        } catch (\Exception $e) {
            Log::error('CartHelper getCurrentCart error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get or create cart for authenticated user (safe)
     */
    public static function getOrCreateCart()
    {
        try {
            if (!Auth::check()) {
                return null;
            }
            
            $cart = Cart::getOrCreateForUser(Auth::id());
            return $cart;
        } catch (\Exception $e) {
            Log::error('CartHelper getOrCreateCart error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Check if user has items in cart (safe)
     */
    public static function hasItemsInCart(): bool
    {
        return self::getCartCount() > 0;
    }
    
    /**
     * Get cart summary for API (safe)
     */
    public static function getCartSummary(): array
    {
        try {
            return [
                'count' => self::getCartCount(),
                'total' => self::getCartTotal(),
                'has_items' => self::hasItemsInCart(),
                'is_guest' => !Auth::check()
            ];
        } catch (\Exception $e) {
            Log::error('CartHelper getCartSummary error: ' . $e->getMessage());
            return [
                'count' => 0,
                'total' => 0,
                'has_items' => false,
                'is_guest' => true
            ];
        }
    }
}