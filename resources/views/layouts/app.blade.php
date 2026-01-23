<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'E-Commerce Indonesia')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    @php
        $cartCount = 0;
        $cartTotal = 0;
        
        if (Auth::check()) {
            $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                $cart->load(['cartItems.product']);
                $cartCount = $cart->cartItems->sum('qty');
                $cartTotal = $cart->cartItems->sum(function($item) {
                    return $item->product ? $item->product->effective_price * $item->qty : 0;
                });
            }
        }
    @endphp
    @include('layouts.navbar', ['cartCount' => $cartCount, 'cartTotal' => $cartTotal])
    
    <!-- Main Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>
    
    <!-- Footer -->
    @include('layouts.footer')
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>