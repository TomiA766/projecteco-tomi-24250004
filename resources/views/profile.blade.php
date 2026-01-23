@extends('layouts.app')

@section('title', 'Profil Saya - ECHO-SHOP')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle"></i> Profil Pengguna
                    </h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" alt="{{ auth()->user()->name }}" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                                <i class="bi bi-person display-4"></i>
                            </div>
                        @endif
                    </div>
                    <h5>{{ auth()->user()->name }}</h5>
                    <p class="text-muted">{{ auth()->user()->email }}</p>
                    <span class="badge bg-{{ auth()->user()->isAdmin() ? 'danger' : 'success' }}">
                        {{ auth()->user()->isAdmin() ? 'Administrator' : 'Pelanggan' }}
                    </span>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Menu Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-receipt"></i> Pesanan Saya
                        </a>
                        <a href="{{ route('cart.index') }}" class="list-group-item list-group-item-action">
                            <i class="bi bi-cart3"></i> Keranjang Belanja
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="list-group-item list-group-item-action">
                                <i class="bi bi-speedometer2"></i> Dashboard Admin
                            </a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="list-group-item list-group-item-action text-danger text-start border-0">
                                <i class="bi bi-box-arrow-right"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle"></i> Informasi Akun
                    </h5>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="name" value="{{ auth()->user()->name }}" readonly>
                                    <div class="form-text">Hubungi admin untuk mengubah data ini</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" value="{{ auth()->user()->email }}" readonly>
                                    <div class="form-text">Hubungi admin untuk mengubah data ini</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Peran</label>
                                    <input type="text" class="form-control" id="role" value="{{ auth()->user()->isAdmin() ? 'Administrator' : 'Pelanggan' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="member_since" class="form-label">Anggota Sejak</label>
                                    <input type="text" class="form-control" id="member_since" value="{{ auth()->user()->created_at->format('d F Y') }}" readonly>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
                <div class="card mt-3">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-shield-check"></i> Administrator Access
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-3">Anda memiliki akses administrator ke sistem.</p>
                        <div class="row text-center">
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3">
                                    <i class="bi bi-tags display-4 text-primary mb-2"></i>
                                    <h6>Kelola Kategori</h6>
                                    <p class="small text-muted">Tambah, edit, hapus kategori produk</p>
                                    <a href="{{ route('admin.categories.index') }}" class="btn btn-primary btn-sm">Buka</a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3">
                                    <i class="bi bi-box display-4 text-success mb-2"></i>
                                    <h6>Kelola Produk</h6>
                                    <p class="small text-muted">Tambah, edit, hapus produk</p>
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-success btn-sm">Buka</a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3">
                                    <i class="bi bi-receipt display-4 text-warning mb-2"></i>
                                    <h6>Kelola Pesanan</h6>
                                    <p class="small text-muted">Lihat dan kelola pesanan pelanggan</p>
                                    <a href="{{ route('admin.orders.index') }}" class="btn btn-warning btn-sm">Buka</a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="border rounded p-3">
                                    <i class="bi bi-speedometer2 display-4 text-info mb-2"></i>
                                    <h6>Dashboard</h6>
                                    <p class="small text-muted">Lihat statistik dan laporan</p>
                                    <a href="{{ route('admin.dashboard') }}" class="btn btn-info btn-sm">Buka</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history"></i> Aktivitas Terbaru
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Login Terakhir</h6>
                                <p class="text-muted mb-0">{{ auth()->user()->updated_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        
                        @if(auth()->user()->orders && auth()->user()->orders->count() > 0)
                            @foreach(auth()->user()->orders->take(3) as $order)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Pesanan #{{ $order->id }}</h6>
                                        <p class="text-muted mb-0">{{ $order->created_at->format('d M Y, H:i') }} - Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="timeline-item">
                                <div class="timeline-marker bg-secondary"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Belum Ada Pesanan</h6>
                                    <p class="text-muted mb-0">Anda belum melakukan pesanan</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content {
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-content h6 {
    color: #495057;
    font-weight: 600;
}
</style>
@endsection