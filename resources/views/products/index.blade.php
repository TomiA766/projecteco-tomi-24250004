@extends('layouts.app')

@section('title', 'Produk - ECHO-SHOP')

@section('content')
<div class="container py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Produk Kami</h1>
        <div class="d-flex align-items-center">
            <span class="me-2">{{ $products->total() }} produk ditemukan</span>
        </div>
    </div>
    
    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-4 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-funnel"></i> Filter</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('products.index') }}">
                        <!-- Categories -->
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select class="form-select" name="category" onchange="this.form.submit()">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Sort -->
                        <div class="mb-3">
                            <label class="form-label">Urutkan</label>
                            <select class="form-select" name="sort" onchange="this.form.submit()">
                                <option value="">Terbaru</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama (A-Z)</option>
                                <option value="price-low" {{ request('sort') == 'price-low' ? 'selected' : '' }}>Harga (Rendah ke Tinggi)</option>
                                <option value="price-high" {{ request('sort') == 'price-high' ? 'selected' : '' }}>Harga (Tinggi ke Rendah)</option>
                            </select>
                        </div>
                        
                        <!-- Search -->
                        <div class="mb-3">
                            <label class="form-label">Cari</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Cari produk...">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                        
                        @if(request()->filled('search') || request()->filled('category') || request()->filled('sort'))
                            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary w-100">
                                <i class="bi bi-x-circle"></i> Reset Filter
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-9 col-md-8">
            <!-- Active Filters Display -->
            @if(request()->filled('search') || request()->filled('category') || request()->filled('sort'))
                <div class="alert alert-info d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Filter Aktif:</strong>
                        @if(request()->filled('search'))
                            <span class="badge bg-secondary me-1">Cari: "{{ request('search') }}"</span>
                        @endif
                        @if(request()->filled('category'))
                            <?php $category = $categories->where('slug', request('category'))->first(); ?>
                            <span class="badge bg-secondary me-1">Kategori: {{ $category->name ?? request('category') }}</span>
                        @endif
                        @if(request()->filled('sort'))
                            <?php 
                            $sortLabels = [
                                'name' => 'Nama (A-Z)',
                                'price-low' => 'Harga (Rendah ke Tinggi)',
                                'price-high' => 'Harga (Tinggi ke Rendah)'
                            ]; 
                            ?>
                            <span class="badge bg-secondary">{{ $sortLabels[request('sort')] ?? request('sort') }}</span>
                        @endif
                    </div>
                </div>
            @endif
            
            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 product-card">
                                <div class="position-relative">
                                    <img src="{{ $product->image_url }}" 
                                         class="card-img-top" 
                                         alt="{{ $product->name }}"
                                         style="height: 200px; object-fit: cover;">
                                    @if($product->stock < 5)
                                        <span class="position-absolute top-0 end-0 badge bg-danger m-2">
                                            Stok Terbatas
                                        </span>
                                    @endif
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ Str::limit($product->name, 30) }}</h5>
                                    <p class="text-muted small mb-2">{{ $product->category->name }}</p>
                                    <p class="card-text flex-grow-1">
                                        {{ Str::limit($product->description ?? 'Tidak ada deskripsi', 80) }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            @if($product->hasDiscount())
                                                <div class="position-relative">
                                                    <span class="badge bg-danger position-absolute top-0 start-0" style="font-size: 0.65rem; z-index: 1;">
                                                        Diskon {{ $product->discount_percentage }}%
                                                    </span>
                                                    <div class="text-decoration-line-through text-muted small">
                                                        {{ $product->formatted_price }}
                                                    </div>
                                                    <div class="fw-bold text-danger h5 mb-0">
                                                        {{ $product->formatted_effective_price }}
                                                    </div>
                                                </div>
                                            @else
                                                <span class="fw-bold text-primary h5 mb-0">
                                                    {{ $product->formatted_price }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="badge {{ $product->stock > 10 ? 'bg-success' : ($product->stock > 0 ? 'bg-warning' : 'bg-danger') }} ms-2">
                                            Stok: {{ $product->stock }}
                                        </span>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary btn-sm">
                                            <i class="bi bi-eye"></i> Lihat Detail
                                        </a>
                                        <button type="button" class="btn btn-outline-primary btn-sm add-to-cart" 
                                                data-product-id="{{ $product->id }}"
                                                data-product-name="{{ $product->name }}"
                                                data-product-price="{{ $product->effective_price }}"
                                                {{ $product->stock == 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-cart-plus"></i> + Keranjang
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <h4 class="mt-3">Produk tidak ditemukan</h4>
                    <p class="text-muted">
                        Coba ubah filter atau kata kunci pencarian Anda.
                    </p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise"></i> Reset Filter
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.product-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #dee2e6;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.card-img-top {
    border-bottom: 1px solid #dee2e6;
}

.add-to-cart:hover:not(:disabled) {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.d-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
}

.discount-badge {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    z-index: 10;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

@media (max-width: 576px) {
    .d-grid {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .btn-sm {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Product list cart script loaded');
    
    // Add to cart functionality for product list
    const addButtons = document.querySelectorAll('.add-to-cart');
    console.log('Found add to cart buttons:', addButtons.length);
    
    addButtons.forEach(button => {
        if (!button.hasAttribute('data-initialized')) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Product list button clicked');
                
                if (this.disabled) {
                    console.log('Button disabled');
                    return;
                }
                
                const productId = this.dataset.productId;
                console.log('Product ID from list:', productId);
                
                handleAddToCartToList(productId, 1);
            });
            
            button.setAttribute('data-initialized', 'true');
        }
    });
});

function handleAddToCartToList(productId, quantity) {
    console.log('handleAddToCartToList called:', productId, quantity);
    
    const btn = document.querySelector(`[data-product-id="${productId}"]`);
    if (!btn) {
        console.log('Button not found for product:', productId);
        return;
    }
    
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menambahkan...';
    console.log('Button set to loading');
    
    fetch('/keranjang/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            updateGlobalCartCount(data.cart_count);
            updateGlobalCartTotal(data.cart_total);
            showNotification(data.message, 'success');
            
            btn.innerHTML = '<i class="bi bi-check"></i> Ditambahkan!';
            btn.classList.remove('btn-outline-primary');
            btn.classList.add('btn-success');
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-outline-primary');
                btn.disabled = false;
            }, 1500);
        } else {
            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }
            showNotification(data.message, 'error');
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

function updateGlobalCartCount(count) {
    console.log('Updating cart count from list to:', count);
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        cartCount.textContent = count;
        cartCount.classList.add('badge-animate');
        setTimeout(() => {
            cartCount.classList.remove('badge-animate');
        }, 300);
    } else {
        console.log('Cart count element not found from list');
    }
}

function updateGlobalCartTotal(total) {
    console.log('Updating cart total from list to:', total);
    const navCartTotal = document.getElementById('nav-cart-total');
    if (navCartTotal) {
        navCartTotal.textContent = numberFormat(total);
        navCartTotal.classList.add('total-animate');
        setTimeout(() => {
            navCartTotal.classList.remove('total-animate');
        }, 500);
    } else {
        console.log('Cart total element not found from list');
    }
}

function numberFormat(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

function showNotification(message, type = 'info') {
    console.log('Showing notification from list:', message, type);
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'warning'} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;';
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi ${type === 'success' ? 'bi-check-circle-fill' : (type === 'error' ? 'bi-exclamation-circle-fill' : 'bi-info-circle-fill')} me-2"></i>
            <div class="flex-grow-1">${message}</div>
            <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}
</script>
@endpush