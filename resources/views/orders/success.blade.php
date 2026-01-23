@extends('layouts.app')

@section('title', 'Pesanan Berhasil - ECHO-SHOP')

@section('content')
<div class="container py-4">
    <!-- Success Message -->
    <div class="row justify-content-center mb-4">
        <div class="col-md-8">
            <div class="alert alert-success">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill display-4 me-3"></i>
                    <div>
                        <h4 class="mb-1">Pesanan Berhasil!</h4>
                        <p class="mb-0">
                            Terima kasih! Pesanan Anda dengan nomor #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }} telah berhasil kami terima.
                        </p>
                        <p class="mb-0">
                            Kami akan segera memproses pesanan Anda dan mengirimkannya sesuai alamat pengiriman.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Order Details -->
    <div class="row">
        <!-- Order Summary -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt"></i> Detail Pesanan #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Order Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Tanggal Pemesanan:</strong><br>
                            {{ $order->created_at->format('d M Y, H:i') }}
                        </div>
                        <div class="col-md-6">
                            <strong>Status Pesanan:</strong><br>
                            <span class="badge bg-warning">
                                <i class="bi bi-clock"></i> {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Shipping Info -->
                    <h6 class="mb-3">
                        <i class="bi bi-truck"></i> Informasi Pengiriman
                    </h6>
                    <div class="row">
                        <div class="col-md-12">
                            <strong>Alamat:</strong><br>
                            {{ $shippingInfo['shipping_address'] ?? 'Tidak tersedia' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Kota:</strong><br>
                            {{ $shippingInfo['shipping_city'] ?? 'Tidak tersedia' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Telepon:</strong><br>
                            {{ $shippingInfo['shipping_phone'] ?? 'Tidak tersedia' }}
                        </div>
                    </div>
                    </div>
                    
                    <!-- Payment Info -->
                    <h6 class="mb-3">
                        <i class="bi bi-credit-card"></i> Informasi Pembayaran
                    </h6>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Metode Pembayaran:</strong><br>
                            @switch($shippingInfo['payment_method'] ?? 'transfer')
                                @case('transfer')
                                    <span class="text-primary">Transfer Bank</span>
                                    @break
                                @case('cod')
                                    <span class="text-primary">Bayar di Tempat (COD)</span>
                                    @break
                                @case('ewallet')
                                    <span class="text-primary">E-Wallet</span>
                                    @break
                                @default
                                    <span class="text-primary">{{ ucfirst($shippingInfo['payment_method'] ?? 'transfer') }}</span>
                                    @break
                            @endswitch
                        </div>
                        <div class="col-md-6">
                            <strong>Biaya Pengiriman:</strong><br>
                            Rp {{ number_format($shippingInfo['shipping_cost'] ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <h6 class="mb-3">
                        <i class="bi bi-box"></i> Produk yang Dipesan
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Qty</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item->product->image ?? 'https://via.placeholder.com/50x50/dc3545/ffffff?text=Produk' }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     class="me-2 rounded"
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                                {{ $item->product->name }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                {{ $item->product->category->name }}
                                            </span>
                                        </td>
                                        <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="text-center">{{ $item->qty }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Total Summary -->
                    <div class="row mt-4">
                        <div class="col-md-8 offset-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <strong>Rp {{ number_format($order->total - ($shippingInfo['shipping_cost'] ?? 0) - round(($order->total - ($shippingInfo['shipping_cost'] ?? 0)) * 0.11), 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Biaya Pengiriman:</span>
                                        <strong>Rp {{ number_format($shippingInfo['shipping_cost'] ?? 0, 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Pajak (11%):</span>
                                        <strong>Rp {{ number_format(round(($order->total - ($shippingInfo['shipping_cost'] ?? 0)) * 0.11), 0, ',', '.') }}</strong>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span>Total Pembayaran:</span>
                                        <strong class="text-primary h4">Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-tools"></i> Tindakan
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary">
                            <i class="bi bi-eye"></i> Lihat Detail
                        </a>
                        
                        @if($order->status === 'pending')
                            <button class="btn btn-outline-warning" disabled>
                                <i class="bi bi-clock"></i> Menunggu Pembayaran
                            </button>
                        @elseif($order->status === 'processing')
                            <button class="btn btn-outline-info" disabled>
                                <i class="bi bi-gear"></i> Sedang Diproses
                            </button>
                        @elseif($order->status === 'shipped')
                            <button class="btn btn-outline-success" disabled>
                                <i class="bi bi-truck"></i> Sedang Dikirim
                            </button>
                        @else
                            <button class="btn btn-success" disabled>
                                <i class="bi bi-check-circle"></i> Selesai
                            </button>
                        @endif
                        
                        <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Lanjut Belanja
                        </a>
                        
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-list"></i> Lihat Semua Pesanan
                        </a>
                    </div>
                    
                    <!-- Help Section -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="mb-2">
                            <i class="bi bi-question-circle"></i> Butuh Bantuan?
                        </h6>
                        <p class="small mb-2">
                            Jika Anda memiliki pertanyaan mengenai pesanan Anda, silakan hubungi tim layanan pelanggan kami.
                        </p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-telephone"></i> Hubungi Kami
                            </button>
                            <button class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-envelope"></i> Kirim Email
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection