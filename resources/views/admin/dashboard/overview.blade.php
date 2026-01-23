@extends('admin.dashboard')

@section('title', 'Dashboard - ECHO-SHOP Admin')

@section('content')
<!-- Overview Cards -->
<div class="row g-4 mb-4">
    <!-- Total Revenue -->
    <div class="col-md-3">
        <div class="card text-center">
            <i class="bi bi-currency-dollar display-4 text-success mb-3"></i>
            <h3>Total Pendapatan</h3>
        </div>
        <div class="card-body">
            <h2 class="text-center">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h2>
            <p class="text-muted">{{ $stats['completed_orders'] }} pesanan selesai</p>
        </div>
        </div>
    </div>
    
    <!-- Orders Status -->
    <div class="col-md-3">
        <div class="card text-center">
            <i class="bi bi-box display-4 text-info mb-3"></i>
            <h3>Status Pesanan</h3>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-center mb-2">
                <div>
                    <span class="badge bg-warning text-dark">{{ $stats['pending_orders'] }}</span>
                    <small> menunggu diproses</small>
                </div>
                <div>
                    <span class="badge bg-info text-dark">{{ $stats['processing_orders'] }}</span>
                    <small> sedang diproses</small>
                </div>
            </div>
                <div>
                    <span class="badge bg-primary text-white">{{ $stats['shipped_orders'] }}</span>
                    <small> sedang dikirim</small>
                </div>
            </div>
                <div>
                    <span class="badge bg-success text-white">{{ $stats['delivered_orders'] }}</span>
                    <small> selesai dikirim</small>
                </div>
            </div>
        </div>
        </div>
    </div>
    
    <!-- Products & Categories -->
    <div class="col-md-6">
        <div class="card text-center">
            <i class="bi bi-box display-4 text-primary mb-3"></i>
            <h3>Produk & Kategori</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 text-center">
                    <div class="h4 text-primary">{{ $stats['total_products'] }}</div>
                    <p class="text-muted">Total Produk</p>
                </div>
            </div>
                <div class="col-md-6 text-center">
                    <div class="h4 text-info">{{ $stats['categories_count'] }}</div>
                    <p class="text-muted">Total Kategori</p>
                </div>
            </div>
        </div>
        </div>
    </div>
    
    <!-- Low Stock Alert -->
    @if($stats['low_stock_products'] > 0)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Peringatan!</strong>
            <p class="mb-0">Ada {{ $stats['low_stock_products'] }} produk dengan stok rendah.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.stats-card .card {
    transition: transform 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.stats-card i {
    font-size: 2rem;
}

.badge {
    font-size: 0.75rem;
}

.text-muted {
    color: #6c757d !important;
}

.table-striped th {
    border-color: #f8f9fa !important;
}

@media (max-width: 768px) {
    .col-md-6.text-center {
        text-align: left !important;
    }
}
</style>
@endpush