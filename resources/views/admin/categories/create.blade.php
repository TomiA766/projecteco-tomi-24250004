@extends('layouts.app')

@section('title', 'Tambah Kategori - ECHO-SHOP')

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
                <h2>Tambah Kategori</h2>
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
            
            <div class="card shadow">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Kategori <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="image" class="form-label">Gambar Kategori</label>
                                    <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB</div>
                                    
                                    @if(old('image'))
                                        <div class="mt-2">
                                            <img src="{{ old('image') }}" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary me-2">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Kategori
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection