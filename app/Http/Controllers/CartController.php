<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\CartHelper;

class CartController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses keranjang.');
        }
        
        $cart = $this->getOrCreateCart();
        if (!$cart) {
            return redirect()->route('home')->with('error', 'Terjadi kesalahan saat memuat keranjang.');
        }
        
        $cart->load(['cartItems.product', 'cartItems.product.category']);
        
        // Prepare cart items for JavaScript
        $cartItemsJson = $cart->cartItems->map(function($item) {
            return [
                'id' => $item->id,
                'name' => $item->product->name ?? 'Produk Tidak Ditemukan',
                'price' => $item->product ? (float) $item->product->effective_price : 0,
                'original_price' => $item->product ? (float) $item->product->price : 0,
                'has_discount' => $item->product ? $item->product->hasDiscount() : false,
                'discount_percentage' => $item->product ? $item->product->discount_percentage : 0,
                'quantity' => $item->qty,
                'image' => $item->product ? ($item->product->image_path ?? asset('images/default/default-product.png')) : asset('images/default/default-product.png'),
                'stock' => $item->product ? $item->product->stock : 0
            ];
        })->toJson();
        
        return view('cart', compact('cart', 'cartItemsJson'));
    }
    
    public function add(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.',
                    'redirect' => route('login')
                ], 401);
            }
            
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1|max:100'
            ]);
            
            $product = Product::findOrFail($request->product_id);
            
            // Check stock availability
            if ($product->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi! Stok tersedia: ' . $product->stock
                ], 400);
            }
            
            $cart = $this->getOrCreateCart();
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal membuat keranjang belanja.'
                ], 500);
            }
            
            // Use database transaction to prevent race conditions
            \DB::beginTransaction();
            
            try {
                // Check if product already in cart (with lock to prevent race conditions)
                $existingCartItem = CartItem::where('cart_id', $cart->id)
                    ->where('product_id', $product->id)
                    ->lockForUpdate()
                    ->first();
                    
                if ($existingCartItem) {
                    // Update quantity if product already exists
                    $newQuantity = $existingCartItem->qty + $request->quantity;
                    
                    if ($newQuantity > $product->stock) {
                        \DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Stok tidak mencukupi! Stok tersedia: ' . $product->stock
                        ], 400);
                    }
                    
                    $existingCartItem->update(['qty' => $newQuantity]);
                } else {
                    // Add new item to cart
                    CartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                        'qty' => $request->quantity
                    ]);
                }
                
                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();
                \Log::error('Cart add error: ' . $e->getMessage());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menambahkan produk ke keranjang.'
                ], 500);
            }
            
            // Reload cart with items and products to get accurate totals
            $cart->refresh();
            $cart->load(['cartItems', 'cartItems.product']);
            $totalItems = $cart->item_count;
            
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang!',
                'cart_count' => $totalItems,
                'cart_total' => $cart->total
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Cart add system error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }
    
    public function update(Request $request, CartItem $cartItem)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.',
                    'redirect' => route('login')
                ], 401);
            }
            
            $request->validate([
                'quantity' => 'required|integer|min:1|max:100'
            ]);
            
            // Check if cart item belongs to current user
            if (!$cartItem->cart || $cartItem->cart->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            
            $newQuantity = $request->quantity;
            $maxStock = $cartItem->product ? $cartItem->product->stock : 0;
            
            if ($newQuantity > $maxStock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quantity melebihi stok yang tersedia! Max: ' . $maxStock
                ], 400);
            }
            
            $cartItem->update(['qty' => $newQuantity]);
            
            $cart = $cartItem->cart;
            $cart->load(['cartItems', 'cartItems.product']);
            $totalItems = $cart->item_count;
            
            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil diperbarui!',
                'cart_count' => $totalItems,
                'subtotal' => $cartItem->subtotal,
                'cart_total' => $cart->total
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Cart update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }
    
    public function remove(CartItem $cartItem)
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.',
                    'redirect' => route('login')
                ], 401);
            }
            
            // Check if cart item belongs to current user
            if (!$cartItem->cart || $cartItem->cart->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            
            $cart = $cartItem->cart;
            $cartItem->delete();
            
            $cart->load(['cartItems', 'cartItems.product']);
            $totalItems = $cart->item_count;
            
            return response()->json([
                'success' => true,
                'message' => 'Produk dihapus dari keranjang!',
                'cart_count' => $totalItems,
                'cart_total' => $cart->total ?? 0
            ]);
            
        } catch (\Exception $e) {
            Log::error('Cart remove error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }
    
    public function clear()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan login terlebih dahulu.',
                    'redirect' => route('login')
                ], 401);
            }
            
            $cart = $this->getOrCreateCart();
            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang tidak ditemukan.'
                ], 404);
            }
            
            $cart->cartItems()->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Keranjang dikosongkan!',
                'cart_count' => 0,
                'cart_total' => 0
            ]);
            
        } catch (\Exception $e) {
            Log::error('Cart clear error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }
    
    private function getOrCreateCart()
    {
        if (!Auth::check()) {
            return null;
        }
        
        try {
            $cartModel = new Cart();
            return $cartModel->getOrCreateForUser(Auth::id());
        } catch (\Exception $e) {
            Log::error('Get or create cart error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get cart count for API response
     */
    public function getCartCount()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'cart_count' => 0,
                    'cart_total' => 0
                ]);
            }
            
            $cart = $this->getOrCreateCart();
            if (!$cart) {
                return response()->json([
                    'cart_count' => 0,
                    'cart_total' => 0
                ]);
            }
            
            $cart->load(['cartItems.product']);
            $totalItems = $cart->item_count;
            $cartTotal = $cart->total;
            
            return response()->json([
                'cart_count' => $totalItems,
                'cart_total' => $cartTotal
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get cart count error: ' . $e->getMessage());
            
            return response()->json([
                'cart_count' => 0,
                'cart_total' => 0
            ]);
        }
    }
}
