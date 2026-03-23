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
                        Your trusted destination for safe, gentle, and reliable infant toiletries designed to protect and care for your baby's delicate skin.
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
<style>
    .catalog-grid { row-gap: 2rem; }
    .catalog-card {
        border: 0;
        border-radius: 26px;
        background: #fff;
        box-shadow: 0 14px 34px rgba(95, 74, 45, 0.08);
        overflow: hidden;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .catalog-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 40px rgba(95, 74, 45, 0.14);
    }
    .catalog-card__media {
        position: relative;
        background: linear-gradient(180deg, #faf7f2 0%, #f3eee8 100%);
        border-radius: 26px;
        margin: 0.85rem;
        min-height: 220px;
        overflow: hidden;
    }
    .catalog-card__media .carousel,
    .catalog-card__media .carousel-inner,
    .catalog-card__media .carousel-item { height: 220px; }
    .catalog-card__image {
        width: 100%;
        height: 220px;
        object-fit: contain;
        padding: 1rem;
    }
    .catalog-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 54px;
        height: 28px;
        padding: 0 12px;
        border-radius: 999px;
        background: #7dc8f7;
        color: #fff;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
    .catalog-badge--sale { background: #a7d64b; }
    .catalog-badge--out { background: #c7c7c7; }
    .catalog-card__body {
        padding: 0.25rem 1.25rem 1.35rem;
        text-align: center;
    }
    .catalog-card__title {
        font-size: 0.86rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #453624;
        min-height: 2.6em;
        margin-bottom: 0.45rem;
    }
    .catalog-card__price {
        font-size: 1.4rem;
        line-height: 1;
        font-weight: 900;
        color: var(--primary-blue-dark);
        margin-bottom: 0.45rem;
    }
    .catalog-card__stars {
        color: var(--accent-brown);
        font-size: 0.82rem;
        letter-spacing: 0.14em;
        margin-bottom: 0.9rem;
    }
    .catalog-card__meta {
        font-size: 0.78rem;
        color: #8f816f;
        margin-bottom: 0.9rem;
    }
    .catalog-card__actions {
        display: flex;
        justify-content: center;
        gap: 0.7rem;
    }
    .catalog-icon-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 2px solid #f0d7bc;
        background: #fff;
        color: #7b6241;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .catalog-icon-btn:hover {
        border-color: var(--primary-blue);
        color: var(--primary-blue-dark);
    }
    .catalog-icon-btn--primary {
        border-color: var(--primary-blue);
        background: var(--primary-blue);
        color: #fff;
    }
    .catalog-icon-btn--primary:hover {
        background: var(--primary-blue-dark);
        border-color: var(--primary-blue-dark);
        color: #fff;
    }
    #featured-products .carousel-control-prev,
    #featured-products .carousel-control-next {
        width: 34px;
        height: 34px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.92);
        border-radius: 50%;
        opacity: 1;
    }
    #featured-products .carousel-control-prev { left: 12px; }
    #featured-products .carousel-control-next { right: 12px; }
    #featured-products .carousel-control-prev-icon,
    #featured-products .carousel-control-next-icon {
        filter: invert(37%) sepia(18%) saturate(510%) hue-rotate(349deg) brightness(92%) contrast(88%);
        width: 1rem;
        height: 1rem;
    }
</style>
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold" style="color: var(--primary-blue-dark);">Featured Products</h2>
            <p class="lead text-muted">Styled like your reference, powered by your real product database.</p>
        </div>

        <div class="row justify-content-center mb-4">
            <div class="col-lg-7">
                <form method="GET" action="{{ route('homepage') }}" class="input-group input-group-lg">
                    <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Search products on homepage..." aria-label="Search products">
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

        <div class="row catalog-grid" id="featured-products">
            @forelse($products as $product)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="catalog-card">
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
                                if (str_starts_with($cleanPath, 'storage/') || str_starts_with($cleanPath, 'img/')) {
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
                            $stockCount = (int) ($product->stock ?? 0);
                            $badgeLabel = $stockCount <= 0 ? 'Out of Stock' : ($stockCount <= 5 ? 'Sale!' : 'New!');
                            $badgeClass = $stockCount <= 0 ? 'catalog-badge catalog-badge--out' : ($stockCount <= 5 ? 'catalog-badge catalog-badge--sale' : 'catalog-badge');
                        @endphp
                        <div class="catalog-card__media">
                            <span class="{{ $badgeClass }}">{{ $badgeLabel }}</span>
                            @if(count($resolvedImages) > 0)
                                <div id="featuredProductCarousel{{ $product->id }}" class="carousel slide h-100" data-bs-ride="false">
                                    <div class="carousel-inner h-100">
                                        @foreach($resolvedImages as $idx => $imageUrl)
                                            <div class="carousel-item h-100 {{ $idx === 0 ? 'active' : '' }}">
                                                <img src="{{ $imageUrl }}" class="catalog-card__image" alt="{{ $product->name }}" onerror="this.onerror=null;this.src='{{ $fallbackImageUrl }}';">
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
                                <img src="{{ $fallbackImageUrl }}" class="catalog-card__image" alt="No image">
                            @endif
                        </div>
                        <div class="catalog-card__body">
                            @php
                                $averageRating = round((float) ($product->reviews_avg_rating ?? 0));
                                $reviewCount = (int) ($product->reviews_count ?? 0);
                            @endphp
                            <h5 class="catalog-card__title">{{ $product->name }}</h5>
                            <div class="catalog-card__price">&#8369;{{ number_format($product->price, 2) }}</div>
                            <div class="catalog-card__stars">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $averageRating ? '★' : '☆' }}
                                @endfor
                                <span class="ms-1 small">{{ number_format((float) ($product->reviews_avg_rating ?? 0), 2) }} ({{ $reviewCount }})</span>
                            </div>
                            <div class="catalog-card__meta">
                                @if($stockCount > 0)
                                    In stock ({{ $stockCount }})
                                @else
                                    Currently unavailable
                                @endif
                            </div>
                            <div class="catalog-card__actions">
                                <a href="{{ route('product.show', $product->id) }}" class="catalog-icon-btn" aria-label="View {{ $product->name }}">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <button type="button" class="catalog-icon-btn catalog-icon-btn--primary add-to-cart-homepage-btn" data-product-id="{{ $product->id }}" data-product-name="{{ e($product->name) }}" data-product-price="{{ $product->price }}" data-product-sku="{{ e($product->sku) }}" data-product-stock="{{ (int) $product->stock }}" data-product-images='@json($product->images ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT)' aria-label="Add {{ $product->name }} to cart" {{ (int) $product->stock === 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-bag-plus"></i>
                                </button>
                            </div>
                            <p class="small text-muted mt-3 mb-0">{{ \Illuminate\Support\Str::limit($product->description ?: 'Carefully selected baby essentials from your catalog.', 48) }}</p>
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
                addToCart(productId, payload, 1, true);
            } catch (error) {
                console.error('Homepage add-to-cart error:', error);
                showToast('Unable to add this product right now.', 'warning');
            }
        });
    });
});
</script>
@endpush

<section class="py-5" style="background-color: white;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold" style="color: var(--primary-blue-dark);">Shop by Category</h2>
            <p class="lead text-muted">Find the right toiletries for your little one's needs.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="category-card text-center p-4 h-100 bg-light rounded-3">
                    <div class="category-icon mb-3"><i class="bi bi-flower1 display-4" style="color: var(--primary-blue);"></i></div>
                    <h5 class="fw-bold">Bath Essentials</h5>
                    <p class="text-muted mb-3">Gentle baby wash, shampoo, and soap for delicate skin.</p>
                    <a href="{{ route('shop') }}?category=bath" class="btn btn-outline-success btn-sm">Shop Now</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="category-card text-center p-4 h-100 bg-light rounded-3">
                    <div class="category-icon mb-3"><i class="bi bi-house-heart display-4" style="color: var(--accent-brown);"></i></div>
                    <h5 class="fw-bold">Diapering Care</h5>
                    <p class="text-muted mb-3">Diaper rash cream, wipes, and changing essentials.</p>
                    <a href="{{ route('shop') }}?category=diapering" class="btn btn-outline-success btn-sm">Shop Now</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="category-card text-center p-4 h-100 bg-light rounded-3">
                    <div class="category-icon mb-3"><i class="bi bi-stars display-4" style="color: var(--primary-blue);"></i></div>
                    <h5 class="fw-bold">Skin Care</h5>
                    <p class="text-muted mb-3">Baby lotion, oils, powders, and moisturizing products.</p>
                    <a href="{{ route('shop') }}?sort=newest" class="btn btn-outline-success btn-sm">Shop Now</a>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="category-card text-center p-4 h-100 bg-light rounded-3">
                    <div class="category-icon mb-3"><i class="bi bi-tag display-4" style="color: var(--accent-brown);"></i></div>
                    <h5 class="fw-bold">Health & Hygiene</h5>
                    <p class="text-muted mb-3">Baby-safe sanitizers, cotton buds, nail care, and grooming kits.</p>
                    <a href="{{ route('shop') }}?sale=true" class="btn btn-outline-success btn-sm">Shop Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

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
@endsection
