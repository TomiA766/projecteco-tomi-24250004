@extends('layouts.app')

@section('title', 'Kelola Produk - ECHO-SHOP')

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
                    <a href="{{ route('admin.products.index') }}" class="list-group-item list-group-item-action active">
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
                <h2>Kelola Produk</h2>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Tambah Produk
                </a>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <div class="card shadow">
                <div class="card-body">
                    @if($products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Gambar</th>
                                        <th>Nama Produk</th>
                                        <th>Kategori</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                @if($product->image)
                                                    <img src="{{ asset($product->image_path) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-width: 60px; max-height: 60px;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $product->name }}</strong>
                                                    @if($product->stock < 5)
                                                        <span class="badge bg-danger ms-2">Stok Menipis</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ $product->category ? $product->category->name : '-' }}</td>
                                            <td>
                                                @if($product->hasDiscount())
                                                    <div>
                                                        <span class="text-decoration-line-through text-muted small">
                                                            <s>Rp {{ number_format($product->price, 0, ',', '.') }}</s>
                                                        </span><br>
                                                        <span class="text-danger fw-bold">Rp {{ number_format($product->discount_price, 0, ',', '.') }}</span>
                                                        <div class="badge bg-danger" style="font-size: 0.65rem;">-{{ $product->discount_percentage }}%</div>
                                                    </div>
                                                @else
                                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge @if($product->stock < 5) bg-danger @else bg-success @endif">{{ $product->stock }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-sm btn-outline-info" target="_blank">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.products.destroy', $product->id) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i> Hapus
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
                            {{ $products->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-box display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">Belum ada produk</h4>
                            <p class="text-muted">Mulai dengan menambahkan produk pertama</p>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Tambah Produk
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection