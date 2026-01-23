// Global Cart JavaScript Functions
let isCartLoading = false;

// Handle add to cart - Global function
window.handleAddToCart = function(productId, quantity = 1) {
    if (isCartLoading) return;
    
    isCartLoading = true;
    
    const btn = event ? event.target : null;
    if (btn) {
        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Menambah...';
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
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateGlobalCartCount(data.cart_count);
            updateGlobalCartTotal(data.cart_total);
            showNotification(data.message, 'success');
            
            // Update button feedback
            if (btn) {
                btn.innerHTML = '<i class="bi bi-check-circle"></i> Ditambahkan!';
                btn.classList.remove('btn-primary', 'btn-outline-primary');
                btn.classList.add('btn-success');
                
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.classList.remove('btn-success');
                    btn.classList.add(btn.classList.contains('btn-lg') ? 'btn-primary' : 'btn-outline-primary');
                    btn.disabled = false;
                    isCartLoading = false;
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

// Update global cart count with animation
window.updateGlobalCartCount = function(count) {
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        cartCount.textContent = count;
        cartCount.classList.add('badge-animate');
        setTimeout(() => {
            cartCount.classList.remove('badge-animate');
        }, 300);
    }
}

// Update global cart total with animation
window.updateGlobalCartTotal = function(total) {
    const navCartTotal = document.getElementById('nav-cart-total');
    if (navCartTotal) {
        navCartTotal.textContent = numberFormat(total);
        navCartTotal.classList.add('total-animate');
        setTimeout(() => {
            navCartTotal.classList.remove('total-animate');
        }, 500);
    }
}

// Reset button to original state
function resetButton(btn) {
    if (btn) {
        btn.disabled = false;
        isCartLoading = false;
    }
}

// Format number for Indonesian locale
window.numberFormat = function(num) {
    return new Intl.NumberFormat('id-ID').format(num);
}

// Show notification
window.showNotification = function(message, type = 'info') {
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
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Initialize cart functionality on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    // Get initial cart count from server
    fetch('/keranjang/count', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.cart_count !== undefined) {
            updateGlobalCartCount(data.cart_count);
        }
        if (data.cart_total !== undefined) {
            updateGlobalCartTotal(data.cart_total);
        }
    })
    .catch(error => {
        console.error('Error fetching cart count:', error);
    });
    
    // Set up add to cart buttons on product list pages
    document.querySelectorAll('.add-to-cart').forEach(button => {
        if (!button.hasAttribute('data-initialized')) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                if (this.disabled || isCartLoading) return;
                
                const productId = this.dataset.productId;
                handleAddToCart(productId, 1);
            });
            
            button.setAttribute('data-initialized', 'true');
        }
    });
});