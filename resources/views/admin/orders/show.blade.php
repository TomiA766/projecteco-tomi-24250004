@extends('layouts.app')

@section('title', 'Detail Pesanan #{{ $order->id }} - ECHO-SHOP')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 col-lg-2">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-speedometer2"></i> Admin Panel
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-tags"></i> Kategori
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-box"></i> Produk
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-receipt"></i> Pesanan
                    </a>
                    <hr>
                    <a href="{{ route('home') }}" class="list-group-item list-group-item-action">
                        <i class="bi bi-shop"></i> Toko
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="list-group-item list-group-item-action text-danger text-start border-0">
                            <i class="bi bi-box-arrow-right"></i> Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Detail Pesanan #{{ $order->id }}</h2>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <div class="row">
                <!-- Order Information -->
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Informasi Pesanan</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="fw-bold">ID Pesanan:</label>
                                <div>#{{ $order->id }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Tanggal:</label>
                                <div>{{ $order->created_at->format('d M Y, H:i') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Status:</label>
                                <div>
                                    @switch($order->status)
                                        @case('pending')
                                            <span class="badge bg-warning">Menunggu</span>
                                            @break
                                        @case('processing')
                                            <span class="badge bg-info">Diproses</span>
                                            @break
                                        @case('shipped')
                                            <span class="badge bg-primary">Dikirim</span>
                                            @break
                                        @case('delivered')
                                            <span class="badge bg-success">Diterima</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-danger">Dibatalkan</span>
                                            @break
                                    @endswitch
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Total:</label>
                                <div class="h5 text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Update Status -->
                    <div class="card shadow">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Update Status</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.orders.update.status', $order->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status Pesanan:</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Diproses</option>
                                        <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Dikirim</option>
                                        <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Diterima</option>
                                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="bi bi-save"></i> Update Status
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Customer Information & Order Items -->
                <div class="col-md-8">
                    <!-- Customer Information -->
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Informasi Pelanggan</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="fw-bold">Nama:</label>
                                        <div>{{ $order->user->name }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold">Email:</label>
                                        <div>{{ $order->user->email }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="fw-bold">Total Pesanan:</label>
                                        <div class="h4">{{ $order->orderItems->count() }} Produk</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <div class="card shadow">
                        <div class="card-header">
                            <h6 class="m-0 font-weight-bold text-primary">Detail Produk</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Harga</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->orderItems as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($item->product->image)
                                                            <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                                        @else
                                                            <div class="bg-light d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; border-radius: 4px;">
                                                                <i class="bi bi-image text-muted"></i>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="fw-bold">{{ $item->product->name }}</div>
                                                            <small class="text-muted">{{ $item->product->category->name }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $item->qty }}</td>
                                                <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                                <td class="text-end fw-bold">Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3" class="text-end">Total:</th>
                                            <th class="text-end text-primary h5">Rp {{ number_format($order->total, 0, ',', '.') }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection