@extends('layouts.app')

@section('title', $product->name . ' - ECHO-SHOP')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Beranda</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('products.index') }}">Produk</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>
    
    <!-- Product Detail -->
    <div class="row">
        <!-- Product Image -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card">
                <img src="{{ asset($product->image_path ?? 'images/default/default-product.svg') }}" 
                     class="card-img-top img-fluid" 
                     alt="{{ $product->name }}"
                     style="max-height: 600px; object-fit: contain; background: #f8f9fa;">
            </div>
        </div>
        
        <!-- Product Info -->
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <!-- Title & Category -->
                    <h1 class="card-title h3 mb-2">{{ $product->name }}</h1>
                    <div class="mb-3">
                        <span class="badge bg-primary">
                            <i class="bi bi-tag"></i> {{ $product->category->name }}
                        </span>
                        @if($product->stock < 5)
                            <span class="badge bg-danger">
                                <i class="bi bi-exclamation-triangle"></i> Stok Terbatas
                            </span>
                        @endif
                    </div>
                    
                    <!-- Price & Stock -->
                    <div class="mb-4">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <div>
                                @if($product->hasDiscount())
                                    <div class="mb-2">
                                        <span class="badge bg-danger mb-2">
                                            <i class="bi bi-percent"></i> Diskon {{ $product->discount_percentage }}%
                                        </span>
                                        <div class="text-decoration-line-through text-muted small">
                                            <s>{{ $product->formatted_price }}</s>
                                        </div>
                                        <h2 class="text-danger mb-0">{{ $product->formatted_effective_price }}</h2>
                                    </div>
                                @else
                                    <h2 class="text-primary mb-0">{{ $product->formatted_price }}</h2>
                                @endif
                            </div>
                            <span class="badge {{ $product->stock > 10 ? 'bg-success' : ($product->stock > 0 ? 'bg-warning' : 'bg-danger') }}">
                                Stok: {{ $product->stock }} unit
                            </span>
                        </div>
                        
                        @if($product->stock == 0)
                            <div class="alert alert-danger mb-3">
                                <i class="bi bi-exclamation-circle"></i>
                                <strong>Stok Habis!</strong> Produk ini sedang tidak tersedia.
                            </div>
                        @endif
                    </div>
                    
                    <!-- Description -->
                    <div class="mb-4">
                        <h5 class="mb-3">Deskripsi Produk</h5>
                        <div class="description-content">
                            {{ $product->description ?? 'Tidak ada deskripsi tersedia untuk produk ini.' }}
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 mb-4">
                        <div class="d-flex align-items-center">
                            <label for="qty" class="form-label me-2 mb-0">Jumlah:</label>
                            <input type="number" id="qty" class="form-control me-2" value="1" min="1" max="{{ $product->stock }}" {{ $product->stock == 0 ? 'disabled' : '' }} style="width: 100px;">
                            <span class="text-muted">/ {{ $product->stock }} tersedia</span>
                        </div>
                        
                        <button type="button" class="btn btn-primary btn-lg add-to-cart-btn w-100" 
                                id="add-to-cart-btn"
                                data-product-id="{{ $product->id }}" 
                                data-product-name="{{ $product->name }}"
                                data-product-price="{{ $product->effective_price }}"
                                data-stock="{{ $product->stock }}"
                                {{ $product->stock == 0 ? 'disabled' : '' }}>
                            <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                        </button>
                        
                        <button class="btn btn-outline-secondary w-100">
                            <i class="bi bi-heart"></i> Tambah ke Wishlist
                        </button>
                    </div>
                    
                    <!-- Product Info Grid -->
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="bi bi-truck display-6 text-primary mb-2"></i>
                                    <h6 class="card-title">Pengiriman</h6>
                                    <p class="card-text small mb-0">1-3 hari kerja</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="bi bi-shield-check display-6 text-success mb-2"></i>
                                    <h6 class="card-title">Garansi</h6>
                                    <p class="card-text small mb-0">Garansi Resmi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mt-5">
            <h3 class="mb-4">Produk Terkait</h3>
            <div class="row">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100 product-card">
                            <img src="{{ $relatedProduct->image_url }}" 
                                 class="card-img-top" 
                                 alt="{{ $relatedProduct->name }}"
                                 style="height: 150px; object-fit: cover;">
                            <div class="card-body">
                                <h6 class="card-title">{{ Str::limit($relatedProduct->name, 30) }}</h6>
                                <div class="mb-3">
                                    @if($relatedProduct->hasDiscount())
                                        <div class="position-relative">
                                            <span class="badge bg-danger position-absolute top-0 start-0" style="font-size: 0.65rem;">
                                                -{{ $relatedProduct->discount_percentage }}%
                                            </span>
                                            <div class="text-decoration-line-through text-muted small">
                                                {{ $relatedProduct->formatted_price }}
                                            </div>
                                            <div class="text-danger fw-bold">
                                                {{ $relatedProduct->formatted_effective_price }}
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-primary fw-bold mb-0">
                                            {{ $relatedProduct->formatted_price }}
                                        </p>
                                    @endif
                                </div>
                                <a href="{{ route('products.show', $relatedProduct->id) }}" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.breadcrumb {
    background-color: #f8f9fa;
    padding: 1rem 0;
    margin-bottom: 2rem;
    border-radius: 0.375rem;
}

.description-content {
    line-height: 1.6;
    min-height: 100px;
}

.product-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.d-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
}

@media (max-width: 576px) {
    .d-grid {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
let isLoading = false;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart script loaded');
    
    const addToCartBtn = document.querySelector('.add-to-cart-btn');
    const qtyInput = document.getElementById('qty');
    
    console.log('Add to cart button:', addToCartBtn);
    console.log('Qty input:', qtyInput);
    
    if (addToCartBtn && qtyInput) {
        addToCartBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Button clicked');
            
            if (this.disabled || isLoading) {
                console.log('Button disabled or loading');
                return;
            }
            
            const productId = this.dataset.productId;
            const quantity = parseInt(qtyInput.value);
            const maxStock = parseInt(this.dataset.stock);
            
            console.log('Product ID:', productId, 'Quantity:', quantity, 'Max Stock:', maxStock);
            
            // Validate quantity
            if (quantity < 1 || quantity > maxStock) {
                showNotification(`Jumlah tidak valid! (1-${maxStock} tersedia)`, 'warning');
                return;
            }
            
            handleAddToCart(productId, quantity);
        });
        
        // Validate quantity input
        qtyInput.addEventListener('input', function() {
            const max = parseInt(this.getAttribute('max'));
            const value = parseInt(this.value);
            
            if (value > max) {
                this.value = max;
                showNotification('Quantity melebihi stok yang tersedia', 'warning');
            }
            if (value < 1) {
                this.value = 1;
            }
        });
    }
});

function handleAddToCart(productId, quantity) {
    console.log('handleAddToCart called with:', productId, quantity);
    
    if (isLoading) {
        console.log('Already loading');
        return;
    }
    
    isLoading = true;
    const btn = document.querySelector('.add-to-cart-btn');
    if (btn) {
        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menambahkan...';
        console.log('Button updated to loading state');
    }
    
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
        console.log('Response received:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            updateGlobalCartCount(data.cart_count);
            updateGlobalCartTotal(data.cart_total);
            showNotification(data.message, 'success');
            
            if (btn) {
                btn.innerHTML = '<i class="bi bi-check-circle"></i> Ditambahkan!';
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-success');
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-primary');
                    btn.disabled = false;
                    isLoading = false;
                }, 1500);
            }
        } else {
            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }
            showNotification(data.message, 'error');
            resetButton(btn);
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        showNotification('Terjadi kesalahan. Silakan coba lagi.', 'error');
        resetButton(btn);
    });
}

function updateGlobalCartCount(count) {
    console.log('Updating cart count to:', count);
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        cartCount.textContent = count;
        cartCount.classList.add('badge-animate');
        setTimeout(() => {
            cartCount.classList.remove('badge-animate');
        }, 300);
    } else {
        console.log('Cart count element not found');
    }
}

function updateGlobalCartTotal(total) {
    console.log('Updating cart total to:', total);
    const navCartTotal = document.getElementById('nav-cart-total');
    if (navCartTotal) {
        navCartTotal.textContent = numberFormat(total);
        navCartTotal.classList.add('total-animate');
        setTimeout(() => {
            navCartTotal.classList.remove('total-animate');
        }, 500);
    } else {
        console.log('Cart total element not found');
    }
}

function resetButton(btn) {
    if (btn) {
        btn.disabled = false;
        isLoading = false;
    }
}

function numberFormat(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

function showNotification(message, type = 'info') {
    console.log('Showing notification:', message, type);
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