<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class CartService
{
    /**
     * Check if cart operation is allowed (guest user handling)
     */
    public static function canAccessCart(): bool
    {
        return Auth::check();
    }

    /**
     * Get authentication response for cart operations
     */
    public static function getAuthErrorResponse()
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu.',
                'redirect' => route('login'),
                'is_guest' => true
            ], 401);
        }

        return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses keranjang.');
    }

    /**
     * Validate cart operation for authenticated users
     */
    public static function validateCartAccess()
    {
        if (!self::canAccessCart()) {
            return self::getAuthErrorResponse();
        }

        return null;
    }

    /**
     * Get user-friendly guest message
     */
    public static function getGuestMessage(): string
    {
        return 'Silakan login terlebih dahulu untuk menggunakan fitur keranjang belanja.';
    }
}