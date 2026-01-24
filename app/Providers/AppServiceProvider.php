<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Cart;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Auto-migrate on production deployment
        if (app()->environment('production')) {
            try {
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            } catch (\Exception $e) {
                // Log error but don't break the app
                \Log::error('Migration failed: ' . $e->getMessage());
            }
        }

        // Share cart data to all views
        View::composer('*', function ($view) {
            $cartCount = 0;
            $cartTotal = 0;
            
            if (Auth::check()) {
                $cart = Cart::where('user_id', Auth::id())->first();
                if ($cart) {
                    $cart->load(['cartItems.product']);
                    $cartCount = $cart->cartItems->sum('qty');
                    $cartTotal = $cart->total;
                    
                    error_log('Cart data shared to view - User ID: ' . Auth::id() . ', Cart ID: ' . $cart->id . ', Count: ' . $cartCount . ', Total: ' . $cartTotal);
                } else {
                    error_log('No cart found for user - User ID: ' . Auth::id());
                }
            }
            
            $view->with('cartCount', $cartCount);
            $view->with('cartTotal', $cartTotal);
        });
    }
}
