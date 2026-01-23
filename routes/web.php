<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::prefix('produk')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    
    // Register
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // Profile placeholder
    Route::get('/profil', function () {
        return view('profile');
    })->name('profile');
    
    // Orders placeholder
    Route::get('/pesanan', function () {
        return view('orders');
    })->name('orders.index');
    
    // Cart
    Route::prefix('/keranjang')->name('cart.')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::post('/add', [CartController::class, 'add'])->name('add');
        Route::get('/count', [CartController::class, 'getCartCount'])->name('count');
        Route::put('/update/{cartItem}', [CartController::class, 'update'])->name('update');
        Route::delete('/remove/{cartItem}', [CartController::class, 'remove'])->name('remove');
        Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    });
    
    // Orders
    Route::prefix('/pesanan')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
        Route::post('/place', [OrderController::class, 'place'])->name('place');
        Route::get('/success/{order}', [OrderController::class, 'success'])->name('success');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::post('/{order}/update', [OrderController::class, 'update'])->name('update');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    });
    
    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        
        // Categories
        Route::prefix('categories')->group(function () {
            Route::get('/', [AdminController::class, 'categories'])->name('categories.index');
            Route::get('/create', [AdminController::class, 'categoryCreate'])->name('categories.create');
            Route::post('/', [AdminController::class, 'categoryStore'])->name('categories.store');
            Route::get('/{category}/edit', [AdminController::class, 'categoryEdit'])->name('categories.edit');
            Route::put('/{category}', [AdminController::class, 'categoryUpdate'])->name('categories.update');
            Route::delete('/{category}', [AdminController::class, 'categoryDestroy'])->name('categories.destroy');
        });
        
        // Products
        Route::prefix('products')->group(function () {
            Route::get('/', [AdminController::class, 'products'])->name('products.index');
            Route::get('/create', [AdminController::class, 'productCreate'])->name('products.create');
            Route::post('/', [AdminController::class, 'productStore'])->name('products.store');
            Route::get('/{product}/edit', [AdminController::class, 'productEdit'])->name('products.edit');
            Route::put('/{product}', [AdminController::class, 'productUpdate'])->name('products.update');
            Route::delete('/{product}', [AdminController::class, 'productDestroy'])->name('products.destroy');
        });
        
        // Orders
        Route::prefix('orders')->group(function () {
            Route::get('/', [AdminController::class, 'orders'])->name('orders.index');
            Route::get('/{order}', [AdminController::class, 'orderShow'])->name('orders.show');
            Route::put('/{order}/status', [AdminController::class, 'orderUpdateStatus'])->name('orders.update.status');
            Route::delete('/{order}', [AdminController::class, 'orderDelete'])->name('orders.destroy');
        });
    });
});
