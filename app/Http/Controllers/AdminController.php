<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;
use Exception;
use Log;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\OrderRequest;
use App\Helpers\ProductImageHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Helper method to check admin authorization
     */
    private function authorizeAdmin()
    {
        try {
            $user = Auth::user();
            if (!$user || !$user->isAdmin()) {
                abort(403, 'Unauthorized access');
            }
            return $user;
        } catch (Exception $e) {
            Log::error('Admin authorization error: ' . $e->getMessage());
            abort(403, 'Unauthorized access');
        }
    }

    public function dashboard()
    {
        try {
            $this->authorizeAdmin();
            
            $stats = [
                'total_products' => Product::count(),
                'total_orders' => Order::count(),
                'total_revenue' => Order::sum('total'),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'processing_orders' => Order::where('status', 'processing')->count(),
                'completed_orders' => Order::where('status', 'delivered')->count(),
                'low_stock_products' => Product::where('stock', '<', 5)->count(),
                'categories_count' => Category::count(),
                'recent_orders' => Order::with('user')->latest()->take(5)->get()
            ];
            
            return view('admin.dashboard', compact('stats'));
        } catch (Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Terjadi kesalahan. Silakan login kembali.');
        }
    }
    
    public function categories()
    {
        try {
            $this->authorizeAdmin();
            
            $categories = Category::withCount('products')->orderBy('name')->paginate(10);
            
            return view('admin.categories.index', compact('categories'));
        } catch (Exception $e) {
            Log::error('Categories index error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat kategori: ' . $e->getMessage());
        }
    }
    
    public function categoryCreate()
    {
        try {
            $this->authorizeAdmin();
            
            return view('admin.categories.create');
        } catch (Exception $e) {
            Log::error('Category create error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuka form kategori: ' . $e->getMessage());
        }
    }
    
    public function categoryStore(CategoryRequest $request)
    {
        try {
            $this->authorizeAdmin();
            
            $validated = $request->validated();
            
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = ProductImageHelper::storeCategoryImage($image);
                
                if ($filename) {
                    $validated['image'] = $filename;
                }
            }
            
            Category::create($validated);
            
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('Category store database error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan kategori. Nama mungkin sudah digunakan.');
        } catch (Exception $e) {
            Log::error('Category store error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menambahkan kategori: ' . $e->getMessage());
        }
    }
    
    public function categoryEdit(Category $category)
    {
        try {
            $this->authorizeAdmin();
            
            if (!$category) {
                abort(404, 'Kategori tidak ditemukan');
            }
            
            return view('admin.categories.edit', compact('category'));
        } catch (ModelNotFoundException $e) {
            abort(404, 'Kategori tidak ditemukan');
        } catch (Exception $e) {
            Log::error('Category edit error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuka form edit: ' . $e->getMessage());
        }
    }
    
    public function categoryUpdate(Category $category, CategoryRequest $request)
    {
        try {
            $this->authorizeAdmin();
            
            if (!$category) {
                abort(404, 'Kategori tidak ditemukan');
            }
            
            $validated = $request->validated();
            
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($category->image) {
                    ProductImageHelper::deleteCategoryImage($category->image);
                }
                
                $filename = ProductImageHelper::storeCategoryImage($request->file('image'));
                
                if ($filename) {
                    $validated['image'] = $filename;
                }
            }
            
            $category->update($validated);
            
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            abort(404, 'Kategori tidak ditemukan');
        } catch (QueryException $e) {
            Log::error('Category update database error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui kategori. Nama mungkin sudah digunakan.');
        } catch (Exception $e) {
            Log::error('Category update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui kategori: ' . $e->getMessage());
        }
    }
    
    public function categoryDestroy(Category $category)
    {
        try {
            $this->authorizeAdmin();
            
            if (!$category) {
                abort(404, 'Kategori tidak ditemukan');
            }
            
            // Check if category has products
            if ($category->products()->count() > 0) {
                return back()->with('error', 'Tidak dapat menghapus kategori yang memiliki produk. Hapus produk terlebih dahulu.');
            }
            
            // Delete image if exists
            if ($category->image) {
                ProductImageHelper::deleteCategoryImage($category->image);
            }
            
            $category->delete();
            
            return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus!');
        } catch (ModelNotFoundException $e) {
            abort(404, 'Kategori tidak ditemukan');
        } catch (QueryException $e) {
            Log::error('Category delete database error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus kategori. Mungkin masih ada produk terkait.');
        } catch (Exception $e) {
            Log::error('Category delete error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }
    
    public function products()
    {
        try {
            $this->authorizeAdmin();
            
            $products = Product::with('category')->orderBy('created_at', 'desc')->paginate(12);
            $categories = Category::orderBy('name')->get();
            
            return view('admin.products.index', compact('products', 'categories'));
        } catch (Exception $e) {
            Log::error('Products index error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat produk: ' . $e->getMessage());
        }
    }
    
    public function productCreate()
    {
        try {
            $this->authorizeAdmin();
            
            $categories = Category::orderBy('name')->get();
            
            return view('admin.products.create', compact('categories'));
        } catch (Exception $e) {
            Log::error('Product create error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuka form produk: ' . $e->getMessage());
        }
    }
    
    public function productStore(ProductRequest $request)
    {
        try {
            $this->authorizeAdmin();
            
            $validated = $request->validated();
            
            if ($request->hasFile('image')) {
                $filename = ProductImageHelper::storeProductImage($request->file('image'));
                
                if ($filename) {
                    $validated['image'] = $filename;
                }
            }
            
            Product::create($validated);
            
            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            Log::error('Product store database error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan produk. Periksa data input.');
        } catch (Exception $e) {
            Log::error('Product store error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage());
        }
    }
    
    public function productEdit(Product $product)
    {
        try {
            $this->authorizeAdmin();
            
            if (!$product) {
                abort(404, 'Produk tidak ditemukan');
            }
            
            $categories = Category::orderBy('name')->get();
            
            return view('admin.products.edit', compact('product', 'categories'));
        } catch (ModelNotFoundException $e) {
            abort(404, 'Produk tidak ditemukan');
        } catch (Exception $e) {
            Log::error('Product edit error: ' . $e->getMessage());
            return back()->with('error', 'Gagal membuka form edit: ' . $e->getMessage());
        }
    }
    
    public function productUpdate(Product $product, ProductRequest $request)
    {
        try {
            $this->authorizeAdmin();
            
            if (!$product) {
                abort(404, 'Produk tidak ditemukan');
            }
            
            $validated = $request->validated();
            
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($product->image) {
                    ProductImageHelper::deleteProductImage($product->image);
                }
                
                $filename = ProductImageHelper::storeProductImage($request->file('image'));
                
                if ($filename) {
                    $validated['image'] = $filename;
                }
            }
            
            $product->update($validated);
            
            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (ModelNotFoundException $e) {
            abort(404, 'Produk tidak ditemukan');
        } catch (QueryException $e) {
            Log::error('Product update database error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui produk. Periksa data input.');
        } catch (Exception $e) {
            Log::error('Product update error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage());
        }
    }
    
    public function productDestroy(Product $product)
    {
        try {
            $this->authorizeAdmin();
            
            if (!$product) {
                abort(404, 'Produk tidak ditemukan');
            }
            
            // Check if product has order items
            if ($product->orderItems()->count() > 0) {
                return back()->with('error', 'Tidak dapat menghapus produk yang ada dalam pesanan. Batalkan pesanan terlebih dahulu.');
            }
            
            // Delete image if exists
            if ($product->image) {
                ProductImageHelper::deleteProductImage($product->image);
            }
            
            // Delete order items first
            $product->orderItems()->delete();
            
            $product->delete();
            
            return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus!');
        } catch (ModelNotFoundException $e) {
            abort(404, 'Produk tidak ditemukan');
        } catch (QueryException $e) {
            Log::error('Product delete database error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus produk. Mungkin masih ada data terkait.');
        } catch (Exception $e) {
            Log::error('Product delete error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }
    }
    
    public function orders()
    {
        try {
            $this->authorizeAdmin();
            
            $orders = Order::with(['user', 'orderItems.product', 'orderItems.product.category'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
            
            return view('admin.orders.index', compact('orders'));
        } catch (Exception $e) {
            Log::error('Orders index error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memuat pesanan: ' . $e->getMessage());
        }
    }
    
    public function orderShow(Order $order)
    {
        try {
            $this->authorizeAdmin();
            
            if (!$order) {
                abort(404, 'Pesanan tidak ditemukan');
            }
            
            $order->load(['user', 'orderItems.product', 'orderItems.product.category']);
            
            return view('admin.orders.show', compact('order'));
        } catch (ModelNotFoundException $e) {
            abort(404, 'Pesanan tidak ditemukan');
        } catch (Exception $e) {
            Log::error('Order show error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menampilkan detail pesanan: ' . $e->getMessage());
        }
    }
    
    public function orderUpdateStatus(Order $order, Request $request)
    {
        try {
            $this->authorizeAdmin();
            
            if (!$order) {
                abort(404, 'Pesanan tidak ditemukan');
            }
            
            $request->validate([
                'status' => 'required|in:pending,processing,shipped,delivered,cancelled'
            ], [
                'status.required' => 'Status pesanan harus dipilih.',
                'status.in' => 'Status yang dipilih tidak valid.'
            ]);
            
            $order->update(['status' => $request->status]);
            
            return redirect()->route('admin.orders.show', $order->id)->with('success', 'Status pesanan berhasil diperbarui!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (ModelNotFoundException $e) {
            abort(404, 'Pesanan tidak ditemukan');
        } catch (QueryException $e) {
            Log::error('Order update status database error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui status pesanan.');
        } catch (Exception $e) {
            Log::error('Order update status error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui status pesanan: ' . $e->getMessage());
        }
    }
    
    public function orderDelete(Order $order)
    {
        try {
            $this->authorizeAdmin();
            
            if (!$order) {
                abort(404, 'Pesanan tidak ditemukan');
            }
            
            // Restore stock for deleted order items
            $order->load('orderItems.product');
            foreach ($order->orderItems as $item) {
                if ($item->product) {
                    $item->product->increment('stock', $item->qty);
                }
            }
            
            // Delete order and order items
            $order->delete();
            
            return redirect()->route('admin.orders.index')->with('success', 'Pesanan berhasil dihapus!');
        } catch (ModelNotFoundException $e) {
            abort(404, 'Pesanan tidak ditemukan');
        } catch (QueryException $e) {
            Log::error('Order delete database error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus pesanan. Terjadi kesalahan database.');
        } catch (Exception $e) {
            Log::error('Order delete error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus pesanan: ' . $e->getMessage());
        }
    }
}