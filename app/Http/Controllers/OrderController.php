<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = Order::with(['orderItems.product', 'orderItems.product.category'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('orders.index', compact('orders'));
    }
    
    public function checkout()
    {
        $user = Auth::user();
        $cart = $user->cart;
        
        if (!$cart || $cart->cartItems->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong! Silakan tambahkan produk terlebih dahulu sebelum checkout.');
        }
        
        $cart->load(['cartItems.product', 'cartItems.product.category']);
        
        return view('checkout.index', compact('cart'));
    }
    
    public function place(Request $request)
    {
        $user = Auth::user();
        $cart = $user->cart;
        
        if (!$cart || $cart->cartItems->count() === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang Anda kosong!'
            ], 400);
        }
        
        $request->validate([
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:100',
            'shipping_phone' => 'required|string|max:20',
            'payment_method' => 'required|in:transfer,cod,ewallet'
        ]);
        
        try {
            DB::beginTransaction();
            
            // Calculate total
            $subtotal = $cart->cartItems->sum(function ($item) {
                return $item->product->price * $item->qty;
            });
            $shippingCost = 15000; // Flat shipping rate
            $total = $subtotal + $shippingCost;
            
            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'status' => 'pending'
            ]);
            
            // Create order items
            foreach ($cart->cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'qty' => $cartItem->qty,
                    'price' => $cartItem->product->price // Save price at time of purchase
                ]);
                
                // Reduce product stock
                $cartItem->product->decrement('stock', $cartItem->qty);
            }
            
            // Clear cart
            $cart->cartItems()->delete();
            
                // Store shipping info in session
            session([
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_phone' => $request->shipping_phone,
                'payment_method' => $request->payment_method,
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'total' => $total
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'order_id' => $order->id,
                'redirect' => route('orders.success', $order->id)
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.'
            ], 500);
        }
    }
    
    public function success($orderId)
    {
        $user = Auth::user();
        $order = Order::with(['orderItems.product', 'orderItems.product.category'])
            ->where('user_id', $user->id)
            ->where('id', $orderId)
            ->first();
            
        if (!$order) {
            return redirect()->route('home')->with('error', 'Pesanan tidak ditemukan!');
        }
        
        // Get shipping info from session
        $shippingInfo = session()->only([
            'shipping_address', 'shipping_city', 'shipping_phone', 'payment_method', 'shipping_cost'
        ]);
        
        return view('orders.success', compact('order', 'shippingInfo'));
    }
    
    public function show(Order $order)
    {
        $user = Auth::user();
        
        if (!$order || $order->user_id !== $user->id) {
            return redirect()->route('home')->with('error', 'Unauthorized');
        }
        
        $order->load(['orderItems.product', 'orderItems.product.category']);
        
        // Ensure order ID is properly cast to integer for str_pad
        $order->id = (int) $order->id;
        
        return view('orders.show', compact('order'));
    }
    
    public function update(Request $request, Order $order)
    {
        $user = Auth::user();
        
        if (!$order || $order->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak dapat diubah'], 400);
        }
        
        $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1|max:99'
        ]);
        
        try {
            DB::beginTransaction();
            
            $subtotal = 0;
            
            foreach ($request->quantities as $itemId => $quantity) {
                $orderItem = $order->orderItems()->where('id', $itemId)->first();
                
                if ($orderItem) {
                    $oldQty = $orderItem->qty;
                    $priceDiff = $orderItem->product->price * ($quantity - $oldQty);
                    
                    $orderItem->update(['qty' => $quantity]);
                    $subtotal += $orderItem->product->price * $quantity;
                    
                    // Update product stock
                    if ($quantity > $oldQty) {
                        $orderItem->product->decrement('stock', $quantity - $oldQty);
                    } else {
                        $orderItem->product->increment('stock', $oldQty - $quantity);
                    }
                }
            }
            
            $tax = round($subtotal * 0.1);
            $total = $subtotal + $tax;
            
            $order->update([
                'subtotal' => $subtotal,
                'total' => $total
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil diperbarui!'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui pesanan.'
            ], 500);
        }
    }
    
    public function cancel(Order $order)
    {
        $user = Auth::user();
        
        if (!$order || $order->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        if ($order->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak dapat dibatalkan'], 400);
        }
        
        try {
            DB::beginTransaction();
            
            // Restore product stock
            foreach ($order->orderItems as $orderItem) {
                $orderItem->product->increment('stock', $orderItem->qty);
            }
            
            $order->update(['status' => 'cancelled']);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan!'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancel error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membatalkan pesanan.'
            ], 500);
        }
    }
}
