@extends('layouts.app')

@section('title', 'Pesanan Saya - ECHO-SHOP')

@section('content')
<div class="container py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Pesanan Saya</h1>
        <a href="{{ route('products.index') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Belanja Lagi
        </a>
    </div>
    
    <!-- Orders List -->
    @if($orders->count() > 0)
        <div class="row">
            @foreach($orders as $order)
                <div class="col-12 mb-4">
                    <div class="card order-card">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0 me-3">
                                    <i class="bi bi-receipt"></i> 
                                    Pesanan #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                                </h6>
                                <span class="badge {{ $order->status_color }} fs-6">
                                    <i class="bi bi-{{ $order->status === 'pending' ? 'clock' : ($order->status === 'processing' ? 'gear' : ($order->status === 'shipped' ? 'truck' : ($order->status === 'delivered' ? 'check-circle' : 'x-circle'))) }}"></i> 
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            
                            @if($order->status === 'pending')
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-warning" onclick="editOrder({{ $order->id }})">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="confirmCancelOrder({{ $order->id }})">
                                        <i class="bi bi-x-circle"></i> Cancel
                                    </button>
                                </div>
                            @endif
                        </div>
                        
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6 col-lg-4">
                                    <small class="text-muted d-block">Tanggal Pesanan</small>
                                    <div class="fw-semibold">{{ $order->created_at->format('d M Y') }}</div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <small class="text-muted d-block">Status</small>
                                    <div class="fw-semibold">
                                        <span class="badge {{ $order->status_color }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-12 col-lg-4 text-lg-end">
                                    <small class="text-muted d-block">Total</small>
                                    <div class="fw-semibold text-primary h5">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            
                            <!-- Order Items Preview -->
                            <div class="mb-3">
                                <small class="text-muted d-block mb-2">Produk ({{ $order->orderItems->count() }} item)</small>
                                <div class="row g-2">
                                    @foreach($order->orderItems->take(3) as $item)
                                        <div class="col-6 col-lg-4">
                                            <div class="d-flex align-items-start p-2 border rounded">
                                                <img src="{{ $item->product->image ?? asset('images/default/default-product.png') }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     class="me-2 rounded"
                                                     style="width: 50px; height: 50px; object-fit: cover; flex-shrink: 0;">
                                                <div class="flex-grow-1">
                                                    <div class="fw-medium small">{{ Str::limit($item->product->name, 25) }}</div>
                                                    <div class="text-muted tiny">{{ $item->product->category->name }}</div>
                                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                                        <span class="text-primary fw-semibold">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                                        <span class="badge bg-light text-dark ms-2">{{ $item->qty }}x</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($order->orderItems->count() > 3)
                                            <div class="col-12">
                                                <div class="text-center p-2 bg-light rounded">
                                                    <small class="text-muted">
                                                        <i class="bi bi-three-dots"></i> 
                                                        dan {{ $order->orderItems->count() - 3 }} produk lainnya
                                                    </small>
                                                </div>
                                            </div>
                                        @endif
                                </div>
                            </div>
                            
                            <!-- Tax & Financial Details -->
                            <div class="row mt-3 pt-3 border-top">
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Subtotal</small>
                                    <div class="fw-semibold">Rp {{ number_format($order->subtotal ?? $order->total / 1.1, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted d-block">Pajak (10%)</small>
                                    <div class="fw-semibold">Rp {{ number_format(round(($order->subtotal ?? $order->total / 1.1) * 0.1), 0, ',', '.') }}</div>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <small class="text-muted d-block">Total</small>
                                    <div class="fw-bold text-primary h5 mb-0">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="row mt-3">
                                <div class="col-12 d-flex justify-content-between align-items-center">
                                    <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i> Lihat Detail
                                    </a>
                                    
                                    <div>
                                        @if($order->status === 'pending')
                                            <small class="text-muted me-2">
                                                <i class="bi bi-clock"></i> 
                                                Dapat diedit sampai: {{ $order->created_at->addDays(1)->format('d M Y, H:i') }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-inbox display-1 text-muted mb-3"></i>
            <h4 class="text-muted">Belum Ada Pesanan</h4>
            <p class="text-muted mb-4">
                Anda belum memiliki pesanan. Ayo mulai berbelanja dan nikmati pengalaman berbelanja yang menyenangkan!
            </p>
            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                <i class="bi bi-arrow-right"></i> Mulai Belanja
            </a>
        </div>
    @endif
</div>

@push('scripts')
<script>
function editOrder(orderId) {
    // Redirect to order edit page or open modal
    window.location.href = '/pesanan/' + orderId + '/edit';
}

function confirmCancelOrder(orderId) {
    if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Pesanan yang dibatalkan tidak dapat dipulihkan.')) {
        // Send cancel request
        fetch('/pesanan/' + orderId + '/cancel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pesanan berhasil dibatalkan!');
                location.reload();
            } else {
                alert('Gagal membatalkan pesanan: ' + data.message);
            }
        })
        .catch(error => {
            alert('Terjadi kesalahan. Silakan coba lagi.');
            console.error('Error:', error);
        });
    }
}
</script>
@endpush

@push('styles')
<style>
.order-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.order-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #007bff;
}

.badge {
    font-size: 0.75rem;
    padding: 0.35rem 0.65rem;
}

.badge.bg-warning { background-color: #ffc107 !important; color: #000 !important; }
.badge.bg-info { background-color: #17a2b8 !important; color: #fff !important; }
.badge.bg-success { background-color: #28a745 !important; color: #fff !important; }
.badge.bg-danger { background-color: #dc3545 !important; color: #fff !important; }
.badge.bg-secondary { background-color: #6c757d !important; color: #fff !important; }

.fw-semibold { font-weight: 600; }
.fw-medium { font-weight: 500; }
.tiny { font-size: 0.75rem; }

.d-flex.gap-2 { gap: 0.5rem; }
.d-flex.gap-1 { gap: 0.25rem; }

@media (max-width: 768px) {
    .order-card {
        margin-bottom: 1rem;
    }
    
    .card-header {
        padding: 0.75rem;
    }
    
    .card-body {
        padding: 1rem;
    }
}
</style>
@endpush