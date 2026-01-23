@extends('layouts.app')

@section('title', 'Detail Pesanan #' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . ' - ECHO-SHOP')

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Beranda</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('orders.index') }}">Pesanan Saya</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Detail Pesanan #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
            </li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Order Info Card -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt"></i> Informasi Pesanan
                    </h5>
                    <div>
                        @if($order->status === 'pending')
                            <button class="btn btn-sm btn-outline-light me-2" onclick="editOrder()">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmCancelOrder()">
                                <i class="bi bi-x-circle"></i> Cancel
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Order Details -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Nomor Pesanan:</strong><br>
                            #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="col-md-6 text-md-end">
                            <strong>Tanggal Pemesanan:</strong><br>
                            {{ $order->created_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            <span class="badge {{ $order->status_color }}">
                                <i class="bi bi-{{ $order->status === 'pending' ? 'clock' : ($order->status === 'processing' ? 'gear' : ($order->status === 'shipped' ? 'truck' : ($order->status === 'delivered' ? 'check-circle' : 'x-circle'))) }}"></i> 
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <strong>Terakhir Update:</strong><br>
                            {{ $order->updated_at->format('d M Y, H:i') }}
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <h6 class="mb-3">
                        <i class="bi bi-box"></i> Produk yang Dipesan
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Produk</th>
                                    <th width="120">Harga</th>
                                    <th width="80">Qty</th>
                                    <th width="120" class="text-end">Subtotal</th>
                                    @if($order->status === 'pending')
                                        <th width="100" class="text-center">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $item->product->image ?? asset('images/default/default-product.png') }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     class="me-2 rounded"
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <strong>{{ $item->product->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $item->product->category->name }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="text-center" id="qty-{{ $item->id }}">{{ $item->qty }}</td>
                                        <td class="text-end fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        @if($order->status === 'pending')
                                            <td class="text-center">
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity({{ $item->id }}, -1)">
                                                        <i class="bi bi-dash"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity({{ $item->id }}, 1)">
                                                        <i class="bi bi-plus"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-active fw-bold">
                                    <td colspan="3" class="text-end">Subtotal:</td>
                                    <td class="text-end" id="subtotal">Rp {{ number_format($order->subtotal ?? $order->total / 1.1, 0, ',', '.') }}</td>
                                    @if($order->status === 'pending')
                                        <td></td>
                                    @endif
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">Pajak (10%):</td>
                                    <td class="text-end" id="tax">Rp {{ number_format(round(($order->subtotal ?? $order->total / 1.1) * 0.1), 0, ',', '.') }}</td>
                                    @if($order->status === 'pending')
                                        <td></td>
                                    @endif
                                </tr>
                                <tr class="table-primary text-white">
                                    <td colspan="3" class="text-end fw-bold">Total:</td>
                                    <td class="text-end fw-bold h5" id="total">Rp {{ number_format(($order->subtotal ?? $order->total / 1.1) * 1.1, 0, ',', '.') }}</td>
                                    @if($order->status === 'pending')
                                        <td></td>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Shipping & Summary Card -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-truck"></i> Informasi Pengiriman
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Alamat Pengiriman:</strong><br>
                        <span class="text-muted">Informasi pengiriman akan muncul di sini</span>
                    </div>
                    <div class="mb-3">
                        <strong>Metode Pembayaran:</strong><br>
                        <span class="badge bg-info text-white">Transfer Bank</span>
                    </div>
                    <div class="mb-3">
                        <strong>Biaya Pengiriman:</strong><br>
                        <span class="text-primary">Rp 15.000</span>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-credit-card"></i> Ringkasan Pembayaran
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Status Pembayaran:</strong><br>
                            <span class="badge {{ $order->status_color }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold">Total Pembayaran:</span>
                        <span class="h5 text-primary fw-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($order->status === 'pending')
                        <div class="d-grid gap-2">
                            <button class="btn btn-success w-100" onclick="saveOrderChanges()">
                                <i class="bi bi-check-circle"></i> Simpan Perubahan
                            </button>
                            <button class="btn btn-outline-secondary w-100" onclick="window.location.href='{{ route('orders.index') }}'">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Store original values
let originalQuantities = {};

document.addEventListener('DOMContentLoaded', function() {
    // Store original quantities
    @foreach($order->orderItems as $item)
        originalQuantities[{{ $item->id }}] = {{ $item->qty }};
    @endforeach
});

function updateQuantity(itemId, change) {
    const qtyElement = document.getElementById('qty-' + itemId);
    const currentQty = parseInt(qtyElement.textContent);
    const newQty = currentQty + change;
    
    if (newQty < 1) {
        alert('Quantity minimal adalah 1');
        return;
    }
    
    if (newQty > 99) {
        alert('Quantity maksimal adalah 99');
        return;
    }
    
    // Update display
    qtyElement.textContent = newQty;
    
    // Update subtotal and tax calculation
    updateOrderTotals();
}

function updateOrderTotals() {
    let subtotal = 0;
    
    @foreach($order->orderItems as $item)
        const quantity = parseInt(document.getElementById('qty-{{ $item->id }}').textContent);
        subtotal += {{ $item->price }} * quantity;
    @endforeach
    
    const tax = Math.round(subtotal * 0.1);
    const total = subtotal + tax;
    
    // Update display
    document.getElementById('subtotal').textContent = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('tax').textContent = 'Rp ' + tax.toLocaleString('id-ID');
    document.getElementById('total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function editOrder() {
    if (confirm('Apakah Anda ingin mengubah pesanan ini?')) {
        // Enable quantity controls
        const qtyElements = document.querySelectorAll('[id^="qty-"]');
        qtyElements.forEach(el => {
            const buttonGroup = el.closest('tr').querySelector('.btn-group');
            if (buttonGroup) {
                buttonGroup.style.display = 'flex';
            }
        });
        
        // Show save button
        document.querySelector('[onclick="saveOrderChanges()"]').style.display = 'block';
        document.querySelector('[onclick="editOrder()"]').style.display = 'none';
    }
}

function confirmCancelOrder() {
    if (confirm('Apakah Anda yakin ingin membatalkan pesanan ini? Pesanan yang dibatalkan tidak dapat dipulihkan.')) {
        // Send cancel request
        fetch('/pesanan/{{ $order->id }}/cancel', {
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
                window.location.href = '{{ route('orders.index') }}';
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

function saveOrderChanges() {
    const quantities = {};
    let hasChanges = false;
    
    @foreach($order->orderItems as $item)
        const currentQty = parseInt(document.getElementById('qty-{{ $item->id }}').textContent);
        if (currentQty !== originalQuantities[{{ $item->id }}]) {
            hasChanges = true;
        }
        quantities[{{ $item->id }}] = currentQty;
    @endforeach
    
    if (!hasChanges) {
        alert('Tidak ada perubahan untuk disimpan.');
        return;
    }
    
    if (confirm('Apakah Anda yakin ingin menyimpan perubahan?')) {
        fetch('/pesanan/{{ $order->id }}/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                quantities: quantities
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Perubahan berhasil disimpan!');
                window.location.reload();
            } else {
                alert('Gagal menyimpan perubahan: ' + data.message);
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
.table th {
    background-color: #f8f9fa;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}

.badge.bg-warning {
    background-color: #ffc107 !important;
    color: #000 !important;
}

.badge.bg-info {
    background-color: #17a2b8 !important;
    color: #fff !important;
}

.badge.bg-success {
    background-color: #28a745 !important;
    color: #fff !important;
}

.badge.bg-danger {
    background-color: #dc3545 !important;
    color: #fff !important;
}

.badge.bg-secondary {
    background-color: #6c757d !important;
    color: #fff !important;
}

.d-grid {
    display: grid;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .btn-group {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .col-md-6.text-md-end {
        text-align: left !important;
        margin-top: 0.5rem;
    }
}
</style>
@endpush