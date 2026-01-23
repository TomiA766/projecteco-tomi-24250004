@extends('layouts.app')

@section('title', 'Beranda - E-Commerce Indonesia')

@section('content')
<div class="container">
    <!-- Banner Carousel -->
    <section class="banner-carousel-section mt-4 mb-5">
        <div id="homepageCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
            <!-- Indicators -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#homepageCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#homepageCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            </div>
            
            <!-- Slides -->
            <div class="carousel-inner">
                <div class="carousel-item active" data-slide="0">
                    <a href="{{ route('products.index') }}" class="d-block">
                        <div class="banner-wrapper">
                            <img src="{{ asset('images/couponsaving.png') }}" class="d-block w-100" alt="Coupon Saving">
                            <div class="banner-overlay banner-gradient"></div>
                            <div class="banner-content">
                                <h2 class="banner-title">Hemat dengan Kupon</h2>
                                <p class="banner-text">Dapatkan penawaran terbaik untuk produk pilihan</p>
                                <span class="banner-cta">Belanja Sekarang →</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="carousel-item" data-slide="1">
                    <a href="{{ route('products.index', ['category' => 'smartphone-tablet']) }}" class="d-block">
                        <div class="banner-wrapper">
                            <img src="{{ asset('images/phoneflashsale.png') }}" class="d-block w-100" alt="Phone Flash Sale">
                            <div class="banner-overlay banner-gradient"></div>
                            <div class="banner-content">
                                <h2 class="banner-title">Flash Sale Smartphone</h2>
                                <p class="banner-text">Diskon spesial untuk ponsel pilihan</p>
                                <span class="banner-cta">Lihat Penawaran →</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            
            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#homepageCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#homepageCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>
    
    <!-- Categories -->
    <section class="categories-section mb-5">
        <h2 class="text-center mb-4">Kategori Produk</h2>
        <div class="row">
            <!-- Categories from Database -->
            @php
                $categories = \App\Models\Category::take(4)->get();
            @endphp
            @foreach($categories as $category)
                <div class="col-md-3 mb-3">
                    <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="text-decoration-none">
                        <div class="card text-center category-card">
                            <div class="card-body">
                                <div class="category-icon mb-2">
                                    @switch($category->slug)
                                        @case('smartphone-tablet')
                                            <i class="bi bi-phone display-4 text-primary"></i>
                                            @break
                                        @case('laptop-komputer')
                                            <i class="bi bi-laptop display-4 text-primary"></i>
                                            @break
                                        @case('audio-video')
                                            <i class="bi bi-headphones display-4 text-primary"></i>
                                            @break
                                        @case('gaming-console')
                                            <i class="bi bi-controller display-4 text-primary"></i>
                                            @break
                                        @default
                                            <i class="bi bi-box display-4 text-primary"></i>
                                            @break
                                    @endswitch
                                </div>
                                <h5 class="card-title">{{ $category->name }}</h5>
                                <p class="text-muted small mb-0">Lihat semua produk</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </section>
    
            <!-- Featured Products from Database -->
            <section class="featured-products mb-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Produk Unggulan</h2>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="row">
                    @php
                        $featuredProducts = \App\Models\Product::with('category')
                            ->orderBy('created_at', 'desc')
                            ->take(6)
                            ->get();
                    @endphp
                    @foreach($featuredProducts as $product)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card product-card">
                                <div class="position-relative">
                                    <img src="{{ $product->image_url }}" 
                                         class="card-img-top" 
                                         alt="{{ $product->name }}"
                                         style="height: 200px; object-fit: cover;">
                                    @if($product->stock < 5)
                                        <span class="position-absolute top-0 end-0 badge bg-danger m-2">
                                            Stok Terbatas
                                        </span>
                                    @endif
                                    @if($product->hasDiscount())
                                        <span class="position-absolute top-0 start-0 badge bg-danger m-2" style="font-size: 0.75rem;">
                                            Diskon {{ $product->discount_percentage }}%
                                        </span>
                                    @endif
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ Str::limit($product->name, 25) }}</h5>
                                    <p class="text-muted small mb-2">{{ $product->category->name }}</p>
                                    <p class="card-text">{{ Str::limit($product->description ?? 'Produk berkualitas tinggi.', 60) }}</p>
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            @if($product->hasDiscount())
                                                <div class="position-relative">
                                                    <span class="badge bg-danger position-absolute top-0 start-0" style="font-size: 0.65rem; z-index: 1;">
                                                        Diskon {{ $product->discount_percentage }}%
                                                    </span>
                                                    <div class="text-decoration-line-through text-muted small">
                                                        {{ $product->formatted_price }}
                                                    </div>
                                                    <div class="fw-bold text-danger h5 mb-0">
                                                        {{ $product->formatted_effective_price }}
                                                    </div>
                                                </div>
                                            @else
                                                <span class="fw-bold text-primary h5 mb-0">
                                                    {{ $product->formatted_price }}
                                                </span>
                                            @endif
                                        </div>
                                        <a href="{{ route('products.show', $product->id) }}" class="btn btn-primary btn-sm">
                                            Lihat Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
    
    <!-- Why Choose Us -->
    <section class="why-choose-us py-5 bg-light rounded">
        <div class="container text-center">
            <h2 class="mb-4">Mengapa Memilih Kami?</h2>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <i class="bi bi-truck display-4 text-primary mb-3"></i>
                    <h5>Pengiriman Cepat</h5>
                    <p>Pengiriman barang tepat waktu dan aman ke seluruh Indonesia</p>
                </div>
                <div class="col-md-3 mb-3">
                    <i class="bi bi-shield-check display-4 text-primary mb-3"></i>
                    <h5>100% Aman</h5>
                    <p>Transaksi aman dan terjamin keamanan data pribadi Anda</p>
                </div>
                <div class="col-md-3 mb-3">
                    <i class="bi bi-arrow-repeat display-4 text-primary mb-3"></i>
                    <h5>Mudah Dikembalikan</h5>
                    <p>Garansi pengembalian barang jika tidak sesuai atau cacat</p>
                </div>
                <div class="col-md-3 mb-3">
                    <i class="bi bi-headset display-4 text-primary mb-3"></i>
                    <h5>Support 24/7</h5>
                    <p>Tim customer service siap membantu Anda kapan saja</p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('styles')
<style>
.category-card {
    transition: transform 0.2s, box-shadow 0.2s;
    height: 100%;
    border: 1px solid #dee2e6;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    background-color: #f8f9fa;
    text-decoration: none;
}

.category-icon {
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 50%;
    margin: 0 auto;
}

.product-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #dee2e6;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Banner Carousel Styles */
.banner-carousel-section {
    margin-bottom: 3rem;
    overflow: hidden;
}

.carousel-fade .carousel-item {
    opacity: 0;
    transition-duration: 0.8s;
    transition-property: opacity;
}

.carousel-fade .carousel-item.active,
.carousel-fade .carousel-item-next.carousel-item-start,
.carousel-fade .carousel-item-prev.carousel-item-end {
    opacity: 1;
}

.carousel-fade .carousel-item-start,
.carousel-fade .carousel-item-end {
    opacity: 0;
}

.banner-wrapper {
    position: relative;
    width: 100%;
    height: 400px;
    overflow: hidden;
    border-radius: 0.75rem;
}

.banner-wrapper img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.banner-wrapper:hover img {
    transform: scale(1.05);
}

.banner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
}

.banner-gradient {
    background: linear-gradient(
        to bottom,
        rgba(0, 0, 0, 0) 0%,
        rgba(0, 0, 0, 0.1) 20%,
        rgba(0, 0, 0, 0.3) 50%,
        rgba(0, 0, 0, 0.5) 80%,
        rgba(0, 0, 0, 0.7) 100%
    );
}

.banner-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 2rem;
    color: white;
    text-align: center;
    z-index: 2;
}

.banner-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
    animation: slideInUp 0.8s ease-out;
}

.banner-text {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
    animation: slideInUp 0.8s ease-out 0.2s both;
}

.banner-cta {
    display: inline-block;
    padding: 0.5rem 1.5rem;
    background-color: rgba(255, 255, 255, 0.2);
    border: 2px solid white;
    border-radius: 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
    animation: slideInUp 0.8s ease-out 0.4s both;
}

.banner-cta:hover {
    background-color: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
}

/* Carousel controls styling */
.carousel-control-prev,
.carousel-control-next {
    width: 50px;
    height: 50px;
    background-color: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    margin: 0 1rem;
    transition: all 0.3s ease;
}

.carousel-control-prev:hover,
.carousel-control-next:hover {
    background-color: rgba(0, 0, 0, 0.8);
    transform: translateY(-50%) scale(1.1);
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
    filter: brightness(0) invert(1);
}

/* Carousel indicators styling */
.carousel-indicators {
    bottom: 1rem;
    z-index: 3;
}

.carousel-indicators button {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.5);
    border: 2px solid white;
    margin: 0 0.25rem;
    transition: all 0.3s ease;
}

.carousel-indicators button.active {
    background-color: white;
    transform: scale(1.2);
}

/* Hero Section Styles */
.hero-section {
    padding: 1.5rem 0;
    margin-bottom: 2rem !important;
}

.hero-section .container {
    max-width: 100%;
    padding: 0 1rem;
}

.hero-section h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.hero-section p {
    font-size: 1.1rem;
    margin-bottom: 1.5rem;
}

.hero-section .btn {
    padding: 0.75rem 2rem;
    font-size: 1rem;
}

/* Animations */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .banner-wrapper {
        height: 250px;
        border-radius: 0.5rem;
    }
    
    .banner-content {
        padding: 1rem;
    }
    
    .banner-title {
        font-size: 1.8rem;
    }
    
    .banner-text {
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
    
    .banner-cta {
        padding: 0.4rem 1rem;
        font-size: 0.9rem;
    }
    
    .carousel-control-prev,
    .carousel-control-next {
        width: 40px;
        height: 40px;
        margin: 0 0.5rem;
    }
}

@media (max-width: 576px) {
    .banner-wrapper {
        height: 200px;
    }
    
    .banner-title {
        font-size: 1.5rem;
    }
    
    .banner-text {
        font-size: 0.9rem;
    }
    
    .banner-cta {
        padding: 0.3rem 0.8rem;
        font-size: 0.8rem;
    }
    
    /* Hero section responsive adjustments */
    .hero-section {
        padding: 1rem 0;
        margin-bottom: 1.5rem !important;
    }
    
    .hero-section h1 {
        font-size: 1.8rem;
        margin-bottom: 0.75rem;
    }
    
    .hero-section p {
        font-size: 1rem;
        margin-bottom: 1rem;
    }
    
    .hero-section .btn {
        padding: 0.5rem 1.5rem;
        font-size: 0.9rem;
    }
}

@media (max-width: 768px) {
    .hero-section {
        padding: 1.25rem 0;
    }
    
    .hero-section h1 {
        font-size: 2.2rem;
        margin-bottom: 0.875rem;
    }
    
    .hero-section p {
        font-size: 1.05rem;
        margin-bottom: 1.25rem;
    }
}
</style>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Initialize carousel with additional safety checks
        const carouselElement = document.getElementById('homepageCarousel');
        if (carouselElement) {
            // Ensure Bootstrap carousel is properly initialized
            const carousel = new bootstrap.Carousel(carouselElement, {
                interval: 4000,
                wrap: true,
                keyboard: true,
                touch: true,
                pause: 'hover'
            });
            
            // Add additional safety for slide transitions
            carouselElement.addEventListener('slide.bs.carousel', function (event) {
                // Preload next image for smooth transitions
                const nextSlide = event.relatedTarget;
                const nextImage = nextSlide.querySelector('img');
                if (nextImage && nextImage.src) {
                    const img = new Image();
                    img.src = nextImage.src;
                }
            });
            
            // Ensure auto-slide continues after user interaction
            carouselElement.addEventListener('slid.bs.carousel', function () {
                // Reset interval to ensure continuous auto-sliding
                setTimeout(() => {
                    carousel.cycle();
                }, 100);
            });
        }
        
        // Ensure all banner links work properly
        const bannerLinks = document.querySelectorAll('.banner-wrapper > a');
        bannerLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // Ensure link doesn't interfere with carousel functionality
                const href = this.getAttribute('href');
                if (href && href !== '#') {
                    // Allow normal navigation
                    return true;
                } else {
                    e.preventDefault();
                    return false;
                }
            });
        });
        
        // Add error handling for image loading
        const bannerImages = document.querySelectorAll('.banner-wrapper img');
        bannerImages.forEach(img => {
            img.addEventListener('error', function() {
                // Set fallback if image fails to load
                this.src = '{{ asset("images/default/default-product.png") }}';
                this.onerror = null; // Prevent infinite loop
            });
        });
        
    } catch (error) {
        console.warn('Carousel initialization warning:', error);
        // Continue gracefully if carousel fails to initialize
    }
});
</script>
@endpush