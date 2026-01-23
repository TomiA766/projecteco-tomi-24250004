@extends('layouts.app')

@section('title', 'Terjadi Kesalahan - ECHO-SHOP')

@section('content')
<div class="container-fluid py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="error-template">
                <div class="error-icon mb-4">
                    <i class="bi bi-exclamation-triangle-fill display-1 text-warning"></i>
                </div>
                <h1 class="mb-4">Terjadi Kesalahan</h1>
                <div class="error-details mb-4">
                    <p class="text-muted">Maaf, terjadi kesalahan sistem. Kami sedang bekerja untuk memperbaikinya.</p>
                    <p class="text-muted">Silakan coba beberapa saat lagi atau hubungi tim support kami.</p>
                </div>
                <div class="error-actions">
                    <button onclick="location.reload()" class="btn btn-primary btn-lg me-2">
                        <i class="bi bi-arrow-clockwise"></i> Coba Lagi
                    </button>
                    <a href="{{ route('home') }}" class="btn btn-secondary btn-lg">
                        <i class="bi bi-house"></i> Beranda
                    </a>
                </div>
                
                <hr class="my-5">
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="help-box text-center mb-3">
                            <i class="bi bi-check-circle display-4 text-success mb-2"></i>
                            <h5>Periksa Koneksi</h5>
                            <p class="text-muted">Pastikan koneksi internet Anda stabil</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="help-box text-center mb-3">
                            <i class="bi bi-arrow-clockwise display-4 text-info mb-2"></i>
                            <h5>Refresh Halaman</h5>
                            <p class="text-muted">Coba refresh halaman ini</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="help-box text-center mb-3">
                            <i class="bi bi-headset display-4 text-primary mb-2"></i>
                            <h5>Hubungi Support</h5>
                            <p class="text-muted">Jika masalah berlanjut, hubungi kami</p>
                        </div>
                    </div>
                </div>
                
                @if(config('app.debug'))
                    <div class="mt-5">
                        <button class="btn btn-outline-danger" onclick="toggleErrorDetails()">
                            <i class="bi bi-bug"></i> Show Debug Info
                        </button>
                        <div id="errorDetails" class="mt-3 text-start" style="display: none;">
                            <div class="alert alert-danger">
                                <small>
                                    <strong>Error Information:</strong><br>
                                    Time: {{ now()->format('Y-m-d H:i:s') }}<br>
                                    URL: {{ request()->fullUrl() }}<br>
                                    Method: {{ request()->method() }}<br>
                                    User ID: {{ auth()->check() ? auth()->id() : 'Guest' }}<br>
                                    User Agent: {{ request()->userAgent() }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endif
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

.error-icon {
    animation: pulse 2s infinite;
}

.help-box {
    padding: 20px;
    border-radius: 8px;
    background-color: #f8f9fa;
    transition: transform 0.2s;
}

.help-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
</style>

@if(config('app.debug'))
<script>
function toggleErrorDetails() {
    const details = document.getElementById('errorDetails');
    details.style.display = details.style.display === 'none' ? 'block' : 'none';
}
</script>
@endif
@endsection