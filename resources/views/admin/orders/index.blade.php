@extends('layouts.app')

@section('title', 'Kelola Pesanan - ECHO-SHOP')

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
                    <a href="{{ route('admin.orders.index') }}" class="list-group-item list-group-item-action active">
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
                <h2>Kelola Pesanan</h2>
                <div>
                    <form method="GET" action="{{ route('admin.orders.index') }}" class="d-inline">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Cari pesanan..." value="{{ request('search') }}">
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <div class="card shadow">
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Pelanggan</th>
                                        <th>Tanggal</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>
                                                <div>
                                                    <strong>{{ $order->user->name }}</strong><br>
                                                    <small class="text-muted">{{ $order->user->email }}</small>
                                                </div>
                                            </td>
                                            <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                                            <td>Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                                            <td>
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
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> Detail
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.orders.destroy', $order->id) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini? Stok akan dikembalikan.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-receipt display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">Belum ada pesanan</h4>
                            <p class="text-muted">Belum ada pesanan dari pelanggan</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection