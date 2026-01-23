@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan - ECHO-SHOP')

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="error-template">
                <h1 class="display-1 text-danger">404</h1>
                <h2 class="mb-4">Halaman Tidak Ditemukan</h2>
                <div class="error-details mb-4">
                    <p class="text-muted">Maaf, halaman yang Anda cari tidak ditemukan atau telah dipindahkan.</p>
                </div>
                <div class="error-actions">
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg me-2">
                        <i class="bi bi-house"></i> Beranda
                    </a>
                    @if(auth()->check() && auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-dark btn-lg">
                            <i class="bi bi-speedometer2"></i> Dashboard Admin
                        </a>
                    @endif
                </div>
                
                <hr class="my-5">
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="feature-box text-center mb-3">
                            <i class="bi bi-search display-4 text-primary mb-2"></i>
                            <h5>Cari Produk</h5>
                            <p class="text-muted">Temukan produk yang Anda butuhkan</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-box text-center mb-3">
                            <i class="bi bi-tags display-4 text-success mb-2"></i>
                            <h5>Lihat Kategori</h5>
                            <p class="text-muted">Jelajahi produk berdasarkan kategori</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-box text-center mb-3">
                            <i class="bi bi-headset display-4 text-info mb-2"></i>
                            <h5>Hubungi Kami</h5>
                            <p class="text-muted">Butuh bantuan? Tim kami siap membantu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-template {
    padding: 40px 15px;
    text-align: center;
}

.error-actions {
    margin: 20px 0;
}

.feature-box {
    padding: 20px;
    border-radius: 8px;
    background-color: #f8f9fa;
    transition: transform 0.2s;
}

.feature-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>
@endsection