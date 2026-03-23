@extends('layouts.customer')

@section('title', 'Collections - Uyayi')
@section('description', 'Browse Uyayi collections grouped by category.')

@section('content')
<style>
    .collections-grid { row-gap: 2rem; }
    .shop-catalog-card {
        border-radius: 26px;
        background: #fff;
        box-shadow: 0 14px 34px rgba(95, 74, 45, 0.08);
        overflow: hidden;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .shop-catalog-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 40px rgba(95, 74, 45, 0.14);
    }
    .shop-catalog-card__media {
        position: relative;
        background: linear-gradient(180deg, #faf7f2 0%, #f3eee8 100%);
        border-radius: 26px;
        margin: 0.85rem;
        min-height: 220px;
        overflow: hidden;
    }
    .shop-catalog-card__image {
        width: 100%;
        height: 220px;
        object-fit: contain;
        padding: 1rem;
    }
    .shop-catalog-badge {
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
        background: var(--primary-blue);
        color: #fff;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
    .shop-catalog-badge--sale { background: #a7d64b; }
    .shop-catalog-badge--out { background: #c7c7c7; }
    .shop-catalog-card__body {
        padding: 0.25rem 1.25rem 1.35rem;
        text-align: center;
    }
    .shop-catalog-card__title {
        font-size: 0.86rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #453624;
        min-height: 2.6em;
        margin-bottom: 0.45rem;
    }
    .shop-catalog-card__price {
        font-size: 1.4rem;
        line-height: 1;
        font-weight: 900;
        color: var(--primary-blue-dark);
        margin-bottom: 0.45rem;
    }
    .shop-catalog-card__stars {
        color: var(--accent-brown);
        font-size: 0.82rem;
        letter-spacing: 0.14em;
        margin-bottom: 0.9rem;
    }
    .shop-catalog-card__meta {
        font-size: 0.78rem;
        color: #8f816f;
        margin-bottom: 0.9rem;
    }
    .shop-catalog-card__actions {
        display: flex;
        gap: 0.7rem;
        align-items: center;
        justify-content: center;
    }
    .shop-catalog-icon-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 2px solid #f0d7bc;
        background: #fff;
        color: #7b6241;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        transition: all 0.2s ease;
    }
    .shop-catalog-icon-btn:hover {
        border-color: var(--primary-blue);
        color: var(--primary-blue-dark);
    }
    .shop-catalog-icon-btn--primary {
        border-color: var(--primary-blue);
        background: var(--primary-blue);
        color: #fff;
    }
    .shop-catalog-icon-btn--primary:hover {
        background: var(--primary-blue-dark);
        border-color: var(--primary-blue-dark);
        color: #fff;
    }
    .collection-section + .collection-section {
        margin-top: 4rem;
    }
    .collection-header {
        margin-bottom: 1.5rem;
    }
    .collection-header h2 {
        color: var(--primary-blue-dark);
    }
</style>

<section class="py-4" style="background-color: white; border-bottom: 2px solid var(--soft-tan);">
    <div class="container">
        <h1 class="fw-bold mb-1" style="color: var(--primary-blue-dark);">
            <i class="bi bi-grid-3x3-gap"></i> Collections
        </h1>
        <p class="text-muted mb-0">Explore your products grouped by category.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        @forelse($categories as $category)
            @if($category->products->isNotEmpty())
                <div class="collection-section">
                    <div class="collection-header">
                        <h2 class="display-6 fw-bold mb-2">{{ $category->name }}</h2>
                        <p class="text-muted mb-0">{{ $category->products->count() }} product(s) in this collection</p>
                    </div>

                    <div class="row collections-grid">
                        @foreach($category->products as $product)
                            @php
                                $productImages = is_array($product->images) ? array_values(array_filter($product->images)) : [];
                                $fallbackImageUrl = asset('img/logo.png');
                                $primaryImage = count($productImages) > 0 ? $productImages[0] : null;
                                $resolveImageUrl = function (?string $path) {
                                    if (!$path) {
                                        return null;
                                    }
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
                                $imageUrl = $resolveImageUrl($primaryImage) ?: $fallbackImageUrl;
                                $stockCount = (int) ($product->stock ?? 0);
                                $badgeLabel = $stockCount <= 0 ? 'Out of Stock' : ($stockCount <= 5 ? 'Sale!' : 'New!');
                                $badgeClass = $stockCount <= 0 ? 'shop-catalog-badge shop-catalog-badge--out' : ($stockCount <= 5 ? 'shop-catalog-badge shop-catalog-badge--sale' : 'shop-catalog-badge');
                            @endphp

                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <div class="shop-catalog-card">
                                    <div class="shop-catalog-card__media">
                                        <span class="{{ $badgeClass }}">{{ $badgeLabel }}</span>
                                        <img src="{{ $imageUrl }}" class="shop-catalog-card__image" alt="{{ $product->name }}" onerror="this.onerror=null;this.src='{{ $fallbackImageUrl }}';">
                                    </div>
                                    <div class="shop-catalog-card__body">
                                        @php
                                            $averageRating = round((float) ($product->reviews_avg_rating ?? 0));
                                            $reviewCount = (int) ($product->reviews_count ?? 0);
                                        @endphp
                                        <h6 class="shop-catalog-card__title">{{ $product->name }}</h6>
                                        <div class="shop-catalog-card__price">&#8369;{{ number_format($product->price, 2) }}</div>
                                        <div class="shop-catalog-card__stars">
                                            @for($i = 1; $i <= 5; $i++)
                                                {{ $i <= $averageRating ? '★' : '☆' }}
                                            @endfor
                                            <span class="ms-1 small">{{ number_format((float) ($product->reviews_avg_rating ?? 0), 2) }} ({{ $reviewCount }})</span>
                                        </div>
                                        <div class="shop-catalog-card__meta">
                                            @if($stockCount > 0)
                                                In stock ({{ $stockCount }})
                                            @else
                                                Currently unavailable
                                            @endif
                                        </div>
                                        <div class="shop-catalog-card__actions">
                                            <a class="shop-catalog-icon-btn" href="{{ route('product.show', $product->id) }}" aria-label="View {{ $product->name }}">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button class="shop-catalog-icon-btn shop-catalog-icon-btn--primary" onclick="addToCart({{ $product->id }})" aria-label="Add {{ $product->name }} to cart" {{ $stockCount === 0 ? 'disabled' : '' }}>
                                                <i class="bi bi-bag-plus"></i>
                                            </button>
                                        </div>
                                        <p class="small text-muted mt-3 mb-0">{{ \Illuminate\Support\Str::limit($product->description ?: 'Carefully selected baby essentials from this collection.', 80) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @empty
            <div class="text-center py-5">
                <i class="bi bi-grid display-1 text-muted"></i>
                <h3 class="mt-3 text-muted">No collections available yet</h3>
            </div>
        @endforelse
    </div>
</section>
@endsection
