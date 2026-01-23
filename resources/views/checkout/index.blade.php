@extends('layouts.app')

@section('title', 'Checkout - ECHO-SHOP')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Beranda</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('cart.index') }}">Keranjang</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
    </nav>
    
    @if(session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i>
            {{ session('error') }}
        </div>
    @endif
    
    <!-- Checkout Form -->
    <div class="row">
        <!-- Cart Items Summary -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-cart3"></i> Ringkasan Pesanan
                    </h5>
                </div>
                <div class="card-body">
                    @if($cart->cartItems->count() > 0)
                        @foreach($cart->cartItems as $item)
                            <div class="d-flex align-items-center mb-3 p-3 border-bottom">
                                <img src="{{ $item->product->image ?? 'https://via.placeholder.com/80x80/dc3545/ffffff?text=Produk' }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="me-3 rounded">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $item->product->name }}</h6>
                                            <p class="text-muted small mb-0">{{ $item->product->category->name }}</p>
                                        </div>
                                        <div class="text-end">
                                            <span class="text-primary fw-bold h5">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted">Rp {{ number_format($item->product->price, 0, ',', '.') }} x {{ $item->qty }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x display-1 text-muted mb-3"></i>
                            <h4>Keranjang Kosong</h4>
                            <p class="text-muted">
                                Keranjang Anda kosong. Silakan tambahkan produk terlebih dahulu.
                            </p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary">
                                <i class="bi bi-arrow-right"></i> Lanjut Belanja
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Shipping & Payment Form -->
        <div class="col-lg-4">
            <form method="POST" action="{{ route('orders.place') }}">
                @csrf
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-truck"></i> Informasi Pengiriman
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="shipping_address" class="form-label">
                                Alamat Pengiriman <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="shipping_address" 
                                      name="shipping_address" 
                                      rows="3" 
                                      placeholder="Masukkan alamat lengkap pengiriman"
                                      required>{{ old('shipping_address') }}</textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="shipping_city" class="form-label">
                                    Kota <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="shipping_city" 
                                       name="shipping_city" 
                                       placeholder="Masukkan kota"
                                       value="{{ old('shipping_city') }}" 
                                       required>
                            </div>
                            <div class="col-md-6">
                                <label for="shipping_phone" class="form-label">
                                    No. Telepon <span class="text-danger">*</span>
                                </label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="shipping_phone" 
                                       name="shipping_phone" 
                                       placeholder="08xx-xxxx-xxxx"
                                       value="{{ old('shipping_phone') }}" 
                                       required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-credit-card"></i> Metode Pembayaran
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="transfer" value="transfer" required>
                                <label class="form-check-label" for="transfer">
                                    <i class="bi bi-bank"></i> Transfer Bank
                                </label>
                            </div>
                            <small class="text-muted d-block">Transfer ke rekening bank</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" required>
                                <label class="form-check-label" for="cod">
                                    <i class="bi bi-cash"></i> Bayar di Tempat (COD)
                                </label>
                            </div>
                            <small class="text-muted d-block">Bayar saat barang diterima</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="ewallet" value="ewallet" required>
                                <label class="form-check-label" for="ewallet">
                                    <i class="bi bi-wallet2"></i> E-Wallet
                                </label>
                            </div>
                            <small class="text-muted d-block">DANA, GoPay, OVO, dll</small>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-calculator"></i> Total Pembayaran
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $subtotal = $cart->cartItems->sum(function($item) {
                                return $item->subtotal;
                            });
                            $shippingCost = $cart->cartItems->count() > 0 ? 15000 : 0;
                            $tax = round(($subtotal + $shippingCost) * 0.11);
                            $total = $subtotal + $shippingCost + $tax;
                        @endphp
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Biaya Pengiriman:</span>
                            <strong>Rp {{ number_format($shippingCost, 0, ',', '.') }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Pajak (11%):</span>
                            <strong>Rp {{ number_format($tax, 0, ',', '.') }}</strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="h5">Total Pembayaran:</span>
                            <strong class="text-primary h3">Rp {{ number_format($total, 0, ',', '.') }}</strong>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Catatan:</strong> Pesanan akan diproses setelah pembayaran berhasil dikonfirmasi.
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg w-100" {{ $cart->cartItems->count() === 0 ? 'disabled' : '' }}>
                            <i class="bi bi-credit-card"></i> Proses Pembayaran
                        </button>
                        
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-left"></i> Kembali ke Keranjang
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-select first payment method
    const firstPaymentMethod = document.querySelector('input[name="payment_method"]');
    if (firstPaymentMethod) {
        firstPaymentMethod.checked = true;
    }
});
</script>
@endpush