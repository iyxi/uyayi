@extends('layouts.customer')

@section('title', 'Uyayi - Gentle Infant Toiletries for Your Little One')
@section('description', 'Discover safe, gentle, and reliable infant toiletries for your baby\'s delicate skin.')

@section('content')
<!-- Hero Section -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="display-4 fw-bold mb-3" style="color: var(--primary-blue-dark);">
                        Welcome to Uyayi 
                        <span style="font-family: 'Yellowtail';"></span>
                    </h1>
                    <p class="lead mb-4" style="color: var(--text-dark);">
                        Your trusted destination for safe, gentle, and reliable infant toiletries designed to protect and care for your baby’s delicate skin.
                    </p>
                    <div class="d-flex gap-3 align-items-center mb-4">
                        <div class="eco-badge">
                           Dermatologist-Tested & Baby-Safe
                        </div>
                        <div class="text-success fw-bold">
                            Buy 2 and Get 20% Off on Your First Order!
                        </div>
                    </div>
                    <a href="{{ route('shop') }}" class="btn btn-primary-custom btn-lg">
                        <i class="bi bi-arrow-right"></i> Shop Now
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-image-placeholder">
                    <img src="{{ asset('img/hero.jpg') }}" alt="description"
                         class="img-fluid rounded-3 shadow-lg"
                         style="max-height: 400px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold" style="color: var(--primary-blue-dark);">Featured Products</h2>
            <p class="lead text-muted">Carefully selected essentials for your baby’s daily hygiene and comfort.</p>
        </div>

        <div class="row justify-content-center mb-4">
            <div class="col-lg-7">
                <form method="GET" action="{{ route('homepage') }}" class="input-group input-group-lg">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search ?? '' }}"
                        class="form-control"
                        placeholder="Search products on homepage..."
                        aria-label="Search products"
                    >
                    <button class="btn btn-primary-custom" type="submit">
                        <i class="bi bi-search"></i> Search
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('homepage') }}" class="btn btn-outline-secondary">Clear</a>
                    @endif
                </form>
            </div>
        </div>

        <div class="text-center text-muted small mb-3">
            @if(!empty($search))
                Showing {{ $products->total() }} result(s) for "{{ $search }}"
            @else
                Showing {{ $products->total() }} product(s)
            @endif
        </div>
        
        <div class="row" id="featured-products">
            @forelse($products as $product)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        @php
                            $productImages = is_array($product->images) ? array_values(array_filter($product->images)) : [];
                            $fallbackImageUrl = asset('img/logo.png');
                            $resolveImageUrl = function (string $path) {
                                if (filter_var($path, FILTER_VALIDATE_URL)) {
                                    return $path;
                                }

                                $cleanPath = ltrim($path, '/');

                                if (str_starts_with($cleanPath, 'public/')) {
                                    $cleanPath = substr($cleanPath, 7);
                                }

                                if (str_starts_with($cleanPath, 'storage/')) {
                                    return asset($cleanPath);
                                }

                                if (str_starts_with($cleanPath, 'img/')) {
                                    return asset($cleanPath);
                                }

                                if (file_exists(public_path($cleanPath))) {
                                    return asset($cleanPath);
                                }

                                if (file_exists(public_path('img/' . $cleanPath))) {
                                    return asset('img/' . $cleanPath);
                                }

                                return asset('storage/' . $cleanPath);
                            };
                            $resolvedImages = array_map($resolveImageUrl, $productImages);
                        @endphp

                        @if(count($resolvedImages) > 0)
                            <div id="featuredProductCarousel{{ $product->id }}" class="carousel slide" data-bs-ride="false">
                                <div class="carousel-inner">
                                    @foreach($resolvedImages as $idx => $imageUrl)
                                        <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}">
                                            <img src="{{ $imageUrl }}"
                                                 class="card-img-top"
                                                 alt="{{ $product->name }}"
                                                 style="height: 220px; object-fit: cover;"
                                                 onerror="this.onerror=null;this.src='{{ $fallbackImageUrl }}';">
                                        </div>
                                    @endforeach
                                </div>

                                @if(count($resolvedImages) > 1)
                                    <button class="carousel-control-prev" type="button" data-bs-target="#featuredProductCarousel{{ $product->id }}" data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#featuredProductCarousel{{ $product->id }}" data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                @endif
                            </div>
                        @else
                            <img src="{{ $fallbackImageUrl }}" class="card-img-top" alt="No image" style="height: 220px; object-fit: cover;">
                        @endif
                        <div class="card-body d-flex flex-column">
                            @php
                                $cartPayload = [
                                    'id' => $product->id,
                                    'name' => $product->name,
                                    'price' => $product->price,
                                    'sku' => $product->sku,
                                    'stock' => $product->stock,
                                    'images' => $product->images,
                                ];
                            @endphp
                            <h5 class="card-title mb-2">{{ $product->name }}</h5>
                            <p class="card-text text-muted small flex-grow-1">{{ Str::limit($product->description, 60) }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="fw-bold text-success">₱{{ number_format($product->price, 2) }}</span>
                                <div class="d-flex gap-2">
                                    <a href="{{ route('product.show', $product->id) }}" class="btn btn-outline-primary btn-sm">View</a>
                                    <button
                                        type="button"
                                        class="btn btn-primary-custom btn-sm add-to-cart-homepage-btn"
                                        data-product-id="{{ $product->id }}"
                                        data-product-name="{{ e($product->name) }}"
                                        data-product-price="{{ $product->price }}"
                                        data-product-sku="{{ e($product->sku) }}"
                                        data-product-stock="{{ (int) $product->stock }}"
                                        data-product-images='@json($product->images ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)'
                                        {{ (int) $product->stock === 0 ? 'disabled' : '' }}
                                    >
                                        <i class="bi bi-bag-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted">No featured products available.</div>
            @endforelse
        </div>
        
        <div class="text-center mt-4">
            <a href="{{ route('shop') }}" class="btn btn-outline-success btn-lg">
                View All Products <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        @if($products->hasPages())
            <div class="d-flex justify-content-center mt-4">
                <nav aria-label="Homepage product pagination">
                    <ul class="pagination mb-0 align-items-center gap-2">
                        @if($products->onFirstPage())
                            <li class="page-item d-none"></li>
                        @else
                            <li class="page-item">
                                <a class="page-link rounded-pill px-3" href="{{ $products->previousPageUrl() }}" rel="prev" aria-label="Previous page">
                                    <i class="bi bi-arrow-left"></i>
                                </a>
                            </li>
                        @endif

                        <li class="page-item disabled">
                            <span class="page-link rounded-pill px-3 text-dark border-0 bg-light">
                                Page {{ $products->currentPage() }} of {{ $products->lastPage() }}
                            </span>
                        </li>

                        @if($products->hasMorePages())
                            <li class="page-item">
                                <a class="page-link rounded-pill px-3" href="{{ $products->nextPageUrl() }}" rel="next" aria-label="Next page">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </li>
                        @else
                            <li class="page-item d-none"></li>
                        @endif
                    </ul>
                </nav>
            </div>
        @endif
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.add-to-cart-homepage-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            try {
                const productId = Number(button.dataset.productId || 0);
                const images = JSON.parse(button.dataset.productImages || '[]');
                const payload = {
                    id: productId,
                    name: button.dataset.productName || 'Product',
                    price: Number(button.dataset.productPrice || 0),
                    sku: button.dataset.productSku || '',
                    stock: Number(button.dataset.productStock || 0),
                    images: Array.isArray(images) ? images : []
                };

                if (!productId || !payload.name) {
                    showToast('Unable to add this product right now.', 'warning');
                    return;
                }

                addToCart(productId, payload, 1);
            } catch (error) {
                console.error('Homepage add-to-cart error:', error);
                showToast('Unable to add this product right now.', 'warning');
            }
        });
    });
});
</script>
@endpush

<!-- Categories Section -->
<section class="py-5" style="background-color: white;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold" style="color: var(--primary-blue-dark);">Shop by Category</h2>
            <p class="lead text-muted">Find the right toiletries for your little one’s needs.</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="category-card text-center p-4 h-100 bg-light rounded-3">
                    <div class="category-icon mb-3">
                        <i class="bi bi-flower1 display-4" style="color: var(--primary-blue);"></i>
                    </div>
                    <h5 class="fw-bold">Bath Essentials</h5>
                    <p class="text-muted mb-3">Gentle baby wash, shampoo, and soap for delicate skin.</p>
                    <a href="{{ route('shop') }}?category=bath" class="btn btn-outline-success btn-sm">Shop Now</a>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="category-card text-center p-4 h-100 bg-light rounded-3">
                    <div class="category-icon mb-3">
                        <i class="bi bi-house-heart display-4" style="color: var(--accent-brown);"></i>
                    </div>
                    <h5 class="fw-bold">Diapering Care</h5>
                    <p class="text-muted mb-3">Diaper rash cream, wipes, and changing essentials.</p>
                    <a href="{{ route('shop') }}?category=diapering" class="btn btn-outline-success btn-sm">Shop Now</a>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="category-card text-center p-4 h-100 bg-light rounded-3">
                    <div class="category-icon mb-3">
                        <i class="bi bi-stars display-4" style="color: var(--primary-blue);"></i>
                    </div>
                    <h5 class="fw-bold">Skin Care</h5>
                    <p class="text-muted mb-3">Baby lotion, oils, powders, and moisturizing products.</p>
                    <a href="{{ route('shop') }}?sort=newest" class="btn btn-outline-success btn-sm">Shop Now</a>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="category-card text-center p-4 h-100 bg-light rounded-3">
                    <div class="category-icon mb-3">
                        <i class="bi bi-tag display-4" style="color: var(--accent-brown);"></i>
                    </div>
                    <h5 class="fw-bold">Health & Hygiene</h5>
                    <p class="text-muted mb-3">Baby-safe sanitizers, cotton buds, nail care, and grooming kits.</p>
                    <a href="{{ route('shop') }}?sale=true" class="btn btn-outline-successbtn-sm">Shop Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold" style="color: var(--primary-blue-dark);">Why Choose Uyayi?</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="feature-item">
                    <h5 class="fw-bold">Gentle & Safe Formulations</h5>
                    <p class="text-muted">Specially selected infant toiletries made for sensitive and delicate skin.</p>
                </div>
            </div>
            
            <div class="col-md-4 text-center">
                <div class="feature-item">
                    <h5 class="fw-bold">Trusted Quality</h5>
                    <p class="text-muted">Products carefully chosen to meet safety and hygiene standards for babies.</p>
                </div>
            </div>
            
            <div class="col-md-4 text-center">
                <div class="feature-item">
                    <h5 class="fw-bold">Parent-Friendly Convenience</h5>
                    <p class="text-muted">Easy ordering, secure transactions, and reliable delivery for busy parents.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<!-- Newsletter removed -->
@endsection