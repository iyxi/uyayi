<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Uyayi - Children\'s Eco-Friendly Clothing')</title>
    <meta name="description" content="@yield('description', 'Discover beautiful, eco-friendly children\'s clothing at Uyayi. Sustainable fashion for little ones.')">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Yellowtail&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
       <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-green: #8b9c41;
            --soft-yellow: #F4E4A6;
            --warm-beige: #F5F0E8;
            --soft-brown: #8B7355;
            --text-dark: #3D3D3D;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--text-dark);
            background-color: var(--warm-beige);
        }

        .navbar-brand {
            font-family: 'Yellowtail', cursive;
            font-size: 2rem;
            color: var(--primary-green) !important;
        }

        .navbar-custom {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-green) !important;
        }

        .btn-primary-custom {
            background-color: var(--primary-green);
            border-color: var(--primary-green);
            color: white;
            font-weight: 600;
            border-radius: 25px;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            background-color: var(--soft-brown);
            border-color: var(--soft-brown);
            transform: translateY(-2px);
        }

        .cart-badge {
            background-color: var(--primary-green);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.75rem;
            position: absolute;
            top: -5px;
            right: -5px;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--soft-yellow) 0%, var(--warm-beige) 100%);
            border-bottom: 3px solid var(--primary-green);
        }

        .product-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: white;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .footer-custom {
            background-color: var(--primary-green);
            color: white;
        }

        .decorative-border {
            height: 4px;
            background: repeating-linear-gradient(
                90deg,
                var(--primary-green) 0px,
                var(--primary-green) 10px,
                var(--soft-yellow) 10px,
                var(--soft-yellow) 20px
            );
        }

        .eco-badge {
            background-color: var(--primary-green);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Decorative top border -->
    <div class="decorative-border"></div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('homepage') }}">
                Uyayi
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('homepage') ? 'active' : '' }}" href="{{ route('homepage') }}">
                            Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shop') ? 'active' : '' }}" href="{{ route('shop') }}">
                            Shop
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#collections">
                            Collections
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">
                            About
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item me-3">
                        <a class="nav-link position-relative" href="{{ route('cart.view') }}">
                            <i class="bi bi-bag"></i>
                            <span class="cart-count cart-badge">0</span>
                        </a>
                    </li>
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu">
                                @if(Auth::user()->email === 'admin@uyayi.com')
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i> Admin Dashboard</a></li>
                                @else
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> My Account</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-box"></i> My Orders</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item me-2">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary-custom btn-sm" href="{{ route('register') }}">Sign Up</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-0 border-0 rounded-0" role="alert">
            <div class="container">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0 border-0 rounded-0" role="alert">
            <div class="container">
                <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer-custom mt-5">
        <div class="container py-5">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="fw-bold mb-3">Uyayi Store</h5>
                    <p class="mb-3">Providing safe, gentle, and essential infant toiletries to support healthy beginnings.</p>
                    <div class="eco-badge">
                        Safe. Gentle. Trusted.
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Shop</h6>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('shop') }}" class="text-light text-decoration-none">All Products</a></li>
                        <li><a href="#" class="text-light text-decoration-none">New Arrivals</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Bestsellers</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Sale</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="fw-bold mb-3">Customer Care</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light text-decoration-none">Product Guide</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Shipping Information</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Returns & Exchanges</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="fw-bold mb-3">Stay Connected</h6>
                        <div class="d-flex gap-3">
                            <a href="#" class="text-light fs-4"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="text-light fs-4"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="text-light fs-4"><i class="bi bi-pinterest"></i></a>
                        </div>
                </div>
            </div>
            <hr class="border-light">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2026 Uyayi Official</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <small>Gentle & Sustainable Baby Care</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Cart Management Script -->
    <script>
        // Global cart management
        let cart = JSON.parse(localStorage.getItem('cart') || '{}');
        
        function updateCartCount() {
            const count = Object.keys(cart).length;
            document.querySelector('.cart-count').textContent = count;
        }
        
        function addToCart(productId, product, quantity = 1) {
            cart[productId] = {
                product: product,
                quantity: parseInt(quantity)
            };
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
            
            // Show success message
            showToast('Product added to cart!', 'success');
        }
        
        function removeFromCart(productId) {
            delete cart[productId];
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartCount();
        }
        
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <i class="bi bi-check-circle"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toast);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
        
        // Initialize cart count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
        });
    </script>
    
    @stack('scripts')
</body>
</html>