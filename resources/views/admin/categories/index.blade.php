@extends('layouts.app')

@section('title', 'Kelola Kategori - ECHO-SHOP')

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
                    <a href="{{ route('admin.categories.index') }}" class="list-group-item list-group-item-action active">
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
                <h2>Kelola Kategori</h2>
                <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Tambah Kategori
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
                    @if($categories->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Gambar</th>
                                        <th>Nama Kategori</th>
                                        <th>Jumlah Produk</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $index => $category)
                                        <tr>
                                            <td>{{ $index + 1 + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                                            <td>
                                                @if($category->image)
                                                    <img src="{{ asset($category->image_path) }}" alt="{{ $category->name }}" class="img-thumbnail" style="max-width: 60px; max-height: 60px;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $category->name }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $category->products_count }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i> Edit
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.categories.destroy', $category->id) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
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
                            {{ $categories->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-tags display-1 text-muted"></i>
                            <h4 class="mt-3 text-muted">Belum ada kategori</h4>
                            <p class="text-muted">Mulai dengan menambahkan kategori pertama</p>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Tambah Kategori
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection