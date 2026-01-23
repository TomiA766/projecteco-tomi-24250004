@extends('layouts.app')

@section('title', 'Keranjang - ECHO-SHOP')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Beranda</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Keranjang</li>
        </ol>
    </nav>
    
    <!-- Cart Content -->
    <div class="row">
        <!-- Cart Items -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-cart3"></i> Keranjang Belanja</h5>
                </div>
                <div class="card-body">
                    <!-- Cart is Empty -->
                    <div id="cart-empty" class="text-center py-5">
                        <i class="bi bi-cart-x display-1 text-muted mb-3"></i>
                        <h4>Keranjang Kosong</h4>
                        <p class="text-muted">Keranjang belanja Anda masih kosong. Ayo mulai berbelanja!</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-right"></i> Lanjut Belanja
                        </a>
                    </div>
                    
                    <!-- Cart Items (Hidden initially, will be shown with JS) -->
                    <div id="cart-items" style="display: none;">
                        <!-- Items will be dynamically added here -->
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cart Summary -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="bi bi-calculator"></i> Ringkasan</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div>
                            <div class="mb-2">
                                <span>Subtotal:</span>
                                <strong id="subtotal">Rp 0</strong>
                            </div>
                            <div class="mb-2">
                                <span>Pengiriman:</span>
                                <strong id="shipping">Rp 0</strong>
                            </div>
                            <div class="mb-2">
                                <span>Pajak:</span>
                                <strong id="tax">Rp 0</strong>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <span>Total:</span>
                                <strong class="text-primary h5" id="total">Rp 0</strong>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Promo Code -->
                    <div class="mb-3">
                        <label class="form-label">Kode Promo</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="promo-code" placeholder="Masukkan kode promo">
                            <button class="btn btn-outline-secondary" onclick="applyPromo()">Gunakan</button>
                        </div>
                    </div>
                    
                    <!-- Checkout Button -->
                    <button class="btn btn-success btn-lg w-100" onclick="proceedToCheckout()">
                        <i class="bi bi-credit-card"></i> Checkout
                    </button>
                    
                    <!-- Continue Shopping -->
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="bi bi-arrow-left"></i> Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let cartItems = {!! $cartItemsJson !!};

document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart page loaded with items:', cartItems.length);
    updateCartDisplay();
    updateCartCountLocal();
});

function updateCartDisplay() {
    const cartEmpty = document.getElementById('cart-empty');
    const cartItemsContainer = document.getElementById('cart-items');
    
    if (!cartEmpty || !cartItemsContainer) {
        console.error('Cart display elements not found');
        return;
    }
    
    if (cartItems.length === 0) {
        cartEmpty.style.display = 'block';
        cartItemsContainer.style.display = 'none';
        updateSummary(0, 0);
    } else {
        cartEmpty.style.display = 'none';
        cartItemsContainer.style.display = 'block';
        
        let itemsHtml = '';
        for (let index = 0; index < cartItems.length; index++) {
            const item = cartItems[index];
            
            const discountHtml = item.has_discount ? 
                '<div class="position-relative">' +
                    '<span class="badge bg-danger position-absolute" style="top: -20px; left: 0; font-size: 0.65rem;">-' + item.discount_percentage + '%</span>' +
                    '<div class="text-decoration-line-through text-muted small"><s>Rp ' + numberFormat(item.original_price) + '</s></div>' +
                    '<div class="text-danger fw-bold">Rp ' + numberFormat(item.price) + '</div>' +
                '</div>' :
                '<span class="text-primary fw-bold">Rp ' + numberFormat(item.price) + '</span>';
            
            itemsHtml += '<div class="cart-item mb-3 p-3 border-bottom" data-item-id="' + item.id + '">' +
                '<img src="' + item.image + '" alt="' + item.name + '" style="width: 80px; height: 80px; object-fit: cover;" class="me-3 rounded">' +
                '<div class="cart-item-content">' +
                    '<h6 class="mb-1">' + item.name + '</h6>' +
                    '<div class="mb-3">' +
                        '<div>' + discountHtml + '</div>' +
                    '</div>' +
                    '<div class="cart-item-controls">' +
                        '<button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(' + index + ', -1)"><i class="bi bi-dash"></i></button>' +
                        '<input type="number" value="' + item.quantity + '" min="1" max="' + item.stock + '" style="width: 60px;" class="form-control form-control-sm mx-2 text-center" onchange="setQuantity(' + index + ', this.value)">' +
                        '<button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(' + index + ', 1)"><i class="bi bi-plus"></i></button>' +
                        '<button class="btn btn-sm btn-outline-danger ms-2" onclick="removeItem(' + index + ')"><i class="bi bi-trash"></i></button>' +
                    '</div>' +
                '</div>' +
                '</div>';
        }
        
        cartItemsContainer.innerHTML = itemsHtml;
        
        // Update summary
        const subtotal = cartItems.reduce(function(sum, item) {
            return sum + (item.price * item.quantity);
        }, 0);
        const shipping = subtotal > 0 ? 15000 : 0; // Flat shipping rate
        const total = subtotal + shipping;
        
        updateSummary(subtotal, shipping, total);
    }
}

function updateSummary(subtotal, shipping, total) {
    if (total === undefined) {
        total = subtotal + shipping;
    }
    
    const tax = Math.round(total * 0.11); // 11% tax
    
    const subtotalEl = document.getElementById('subtotal');
    const shippingEl = document.getElementById('shipping');
    const taxEl = document.getElementById('tax');
    const totalEl = document.getElementById('total');
    
    if (subtotalEl) subtotalEl.textContent = 'Rp ' + numberFormat(subtotal);
    if (shippingEl) shippingEl.textContent = 'Rp ' + numberFormat(shipping);
    if (taxEl) taxEl.textContent = 'Rp ' + numberFormat(tax);
    if (totalEl) totalEl.textContent = 'Rp ' + numberFormat(total + tax);
}

function updateQuantity(index, change) {
    const newQuantity = cartItems[index].quantity + change;
    if (newQuantity < 1 || newQuantity > cartItems[index].stock) return;
    
    const itemId = cartItems[index].id;
    
    fetch('/keranjang/update/' + itemId, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            quantity: newQuantity
        })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            cartItems[index].quantity = newQuantity;
            updateCartDisplay();
            updateCartCountLocal();
            updateGlobalCartCount(data.cart_count);
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(function(error) {
        console.error('Error updating quantity:', error);
        showNotification('Terjadi kesalahan saat memperbarui kuantitas', 'error');
    });
}

function setQuantity(index, value) {
    const quantity = parseInt(value);
    if (quantity < 1 || quantity > cartItems[index].stock) {
        showNotification('Kuantitas harus antara 1 dan ' + cartItems[index].stock, 'error');
        return;
    }
    
    const itemId = cartItems[index].id;
    
    fetch('/keranjang/update/' + itemId, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            quantity: quantity
        })
    })
    .then(function(response) { return response.json(); })
    .then(function(data) {
        if (data.success) {
            cartItems[index].quantity = quantity;
            updateCartDisplay();
            updateCartCountLocal();
            updateGlobalCartCount(data.cart_count);
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(function(error) {
        console.error('Error setting quantity:', error);
        showNotification('Terjadi kesalahan saat memperbarui kuantitas', 'error');
    });
}

function removeItem(index) {
    if (confirm('Apakah Anda yakin ingin menghapus item ini dari keranjang?')) {
        const itemId = cartItems[index].id;
        
        fetch('/keranjang/remove/' + itemId, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                cartItems.splice(index, 1);
                updateCartDisplay();
                updateCartCountLocal();
                updateGlobalCartCount(data.cart_count);
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(function(error) {
            console.error('Error removing item:', error);
            showNotification('Terjadi kesalahan saat menghapus item', 'error');
        });
    }
}

function updateCartCountLocal() {
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        const totalItems = cartItems.reduce(function(sum, item) {
            return sum + item.quantity;
        }, 0);
        cartCount.textContent = totalItems;
    }
}

function updateGlobalCartCount(count) {
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        cartCount.textContent = count;
    }
}

function numberFormat(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = 'alert alert-' + (type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info') + ' alert-dismissible fade show position-fixed';
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = message + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
    
    document.body.appendChild(notification);
    
    setTimeout(function() {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

function applyPromo() {
    const promoCode = document.getElementById('promo-code').value;
    if (promoCode) {
        showNotification('Kode promo "' + promoCode + '" diterapkan!', 'success');
    } else {
        showNotification('Masukkan kode promo terlebih dahulu', 'warning');
    }
}

function proceedToCheckout() {
    if (cartItems.length === 0) {
        showNotification('Keranjang kosong! Tambahkan produk terlebih dahulu.', 'warning');
        return;
    }
    
    window.location.href = '/pesanan/checkout';
}
</script>
@endpush

@push('styles')
<style>
/* Cart layout styles */
.cart-item {
    display: block;
    border-bottom: 1px solid #dee2e6;
}

.cart-item-content {
    margin-bottom: 1rem;
}

.cart-item-controls .form-control {
    flex: 0 0 auto !important;
}
#cart-items .border-bottom {
    border-bottom: 1px solid #dee2e6 !important;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.form-control-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .d-flex.align-items-center {
        flex-direction: column;
        align-items: flex-start !important;
    }
    
    .ms-2 {
        margin-left: 0 !important;
        margin-top: 0.5rem;
    }
}
</style>
@endpush