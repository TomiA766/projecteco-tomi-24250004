<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Exception;
use Log;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Product::with('category');
            
            // Filter by category
            if ($request->filled('category')) {
                $query->whereHas('category', function($q) use ($request) {
                    $q->where('slug', $request->category);
                });
            }
            
            // Search functionality
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('description', 'like', '%' . $searchTerm . '%');
                });
            }
            
            // Sort by price
            if ($request->filled('sort')) {
                switch ($request->sort) {
                    case 'price-low':
                        $query->orderBy('price', 'asc');
                        break;
                    case 'price-high':
                        $query->orderBy('price', 'desc');
                        break;
                    case 'name':
                        $query->orderBy('name', 'asc');
                        break;
                    default:
                        $query->latest();
                }
            } else {
                $query->latest();
            }
            
            $products = $query->paginate(12);
            $categories = Category::orderBy('name')->get();
            
            return view('products.index', compact('products', 'categories'));
        } catch (QueryException $e) {
            Log::error('Product index database error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat produk. Silakan coba lagi.');
        } catch (Exception $e) {
            Log::error('Product index error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat produk.');
        }
    }
    
    public function show(Product $product)
    {
        try {
            if (!$product) {
                abort(404, 'Produk tidak ditemukan');
            }
            
            $product->load('category');
            
            // Get related products
            $relatedProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->inRandomOrder()
                ->take(4)
                ->get();
            
            // Check if product is in cart
            $inCart = false;
            if (auth()->check()) {
                $cart = auth()->user()->cart;
                if ($cart) {
                    $inCart = $cart->cartItems()->where('product_id', $product->id)->exists();
                }
            }
            
            return view('products.show', compact('product', 'relatedProducts', 'inCart'));
        } catch (ModelNotFoundException $e) {
            abort(404, 'Produk tidak ditemukan');
        } catch (QueryException $e) {
            Log::error('Product show database error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat detail produk. Silakan coba lagi.');
        } catch (Exception $e) {
            Log::error('Product show error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat detail produk.');
        }
    }
}