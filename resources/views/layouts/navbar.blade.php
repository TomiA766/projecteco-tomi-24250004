<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            <i class="bi bi-shop"></i> ECHO-SHOP
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-house"></i> Beranda
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">
                        <i class="bi bi-grid"></i> Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#tentang">
                        <i class="bi bi-info-circle"></i> Tentang
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profile') }}">
                        <i class="bi bi-person"></i> Profil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}" href="{{ route('orders.index') }}">
                        <i class="bi bi-box-arrow-right"></i> Pesanan Saya
                    </a>
                </li>
            </ul>
            
            <!-- Search Bar -->
            <form class="d-flex me-3" action="{{ route('products.index') }}" method="GET">
                <input class="form-control me-2" type="search" name="search" placeholder="Cari produk..." value="{{ request('search') }}">
                <button class="btn btn-outline-light" type="submit">
                    <i class="bi bi-search"></i>
                </button>
            </form>
            
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-cart3"></i> Keranjang 
                        <span class="badge bg-danger rounded-pill" id="cart-count">{{ $cartCount ?? 0 }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" style="min-width: 300px;">
                        <li>
                            <a href="{{ route('cart.index') }}" class="dropdown-item">
                                <i class="bi bi-cart3"></i> Lihat Keranjang
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li><span class="dropdown-item text-muted">Total: Rp <span id="nav-cart-total">{{ number_format($cartTotal ?? 0, 0, ',', '.') }}</span></span></li>
                    </ul>
                </li>
                
                @guest
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                            <i class="bi bi-box-arrow-in-right"></i> Masuk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                            <i class="bi bi-person-plus"></i> Daftar
                        </a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile') }}"><i class="bi bi-person"></i> Profil</a></li>
                            <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i class="bi bi-box-arrow-right"></i> Pesanan</a></li>
                            @if(Auth::user()->isAdmin())
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-gear"></i> Admin Panel</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-left"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>

<style>
.navbar-brand {
    font-size: 1.5rem;
    font-weight: 700;
    color: #fff !important;
}
.navbar-brand:hover {
    color: #ffc107 !important;
}
.navbar-nav .nav-link.active {
    color: #ffc107 !important;
    font-weight: 600;
}
#cart-count {
    font-size: 0.7rem;
    margin-left: 5px;
}
@media (max-width: 768px) {
    .navbar-nav .nav-link {
        padding: 0.5rem 0.5rem !important;
    }
}

/* Cart animations */
.badge-animate {
    animation: badgePulse 0.3s ease-in-out;
}

.total-animate {
    animation: totalHighlight 0.5s ease-in-out;
}

@keyframes badgePulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

@keyframes totalHighlight {
    0% { color: #ffc107; }
    50% { color: #28a745; transform: scale(1.05); }
    100% { color: inherit; transform: scale(1); }
}
</style>

<script>
// Update cart count and total dynamically
function updateCartCount() {
    fetch('{{ route("cart.count") }}')
        .then(response => response.json())
        .then(data => {
            const cartCount = document.getElementById('cart-count');
            const navCartTotal = document.getElementById('nav-cart-total');
            
            if (cartCount) {
                cartCount.textContent = data.cart_count;
                // Add animation when count changes
                cartCount.classList.add('badge-animate');
                setTimeout(() => cartCount.classList.remove('badge-animate'), 300);
            }
            
            if (navCartTotal) {
                const currentTotal = navCartTotal.textContent;
                const newTotal = numberFormat(data.cart_total);
                
                if (currentTotal !== newTotal) {
                    navCartTotal.textContent = newTotal;
                    // Add animation when total changes
                    navCartTotal.classList.add('total-animate');
                    setTimeout(() => navCartTotal.classList.remove('total-animate'), 500);
                }
            }
        })
        .catch(error => {
            console.error('Error updating cart info:', error);
            // Graceful fallback - don't show errors to user
        });
}

// Format number for Indonesian currency
function numberFormat(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

// Handle add to cart with authentication check
function handleAddToCart(productId, quantity = 1) {
    fetch('{{ route("cart.add") }}', {
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count and total immediately
            updateCartCount();
            showNotification(data.message, 'success');
        } else {
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                showNotification(data.message, 'error');
            }
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        showNotification('Terjadi kesalahan', 'error');
    });
}

// Show notification
function showNotification(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : (type === 'error' ? 'danger' : 'warning')} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';
    toast.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="bi ${type === 'success' ? 'bi-check-circle' : (type === 'error' ? 'bi-exclamation-circle' : 'bi-info-circle')} me-2"></i>
            ${message}
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Update cart count when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Don't update on initial load since it's shared from backend
    
    // Listen for storage events to sync across tabs
    window.addEventListener('storage', function(e) {
        if (e.key === 'cartUpdated') {
            updateCartCount();
        }
    });
});
</script>