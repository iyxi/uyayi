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
        
        <div class="row" id="featured-products">
            @forelse($products as $product)
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        @if(!empty($product->images) && is_array($product->images) && count($product->images) > 0)
                            <img src="{{ asset('storage/' . $product->images[0]) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 220px; object-fit: cover;">
                        @else
                            <img src="{{ asset('img/no-image.png') }}" class="card-img-top" alt="No image" style="height: 220px; object-fit: cover;">
                        @endif
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-2">{{ $product->name }}</h5>
                            <p class="card-text text-muted small flex-grow-1">{{ Str::limit($product->description, 60) }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="fw-bold text-success">₱{{ number_format($product->price, 2) }}</span>
                                <a href="{{ route('product.show', $product->id) }}" class="btn btn-outline-primary btn-sm">View</a>
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
    </div>
</section>

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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load featured products
    fetch('/api/products')
        .then(response => response.json())
        .then(data => {
            const productsContainer = document.getElementById('featured-products');
            const products = data.data ? data.data.slice(0, 4) : []; // Show first 4 products
            
            if (products.length > 0) {
                productsContainer.innerHTML = products.map(product => `
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="product-card h-100 shadow-sm">
                            <div class="product-image">
                                <img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" 
                                     class="card-img-top" alt="${product.name}" style="height: 200px; object-fit: cover;">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title fw-bold">${product.name}</h6>
                                <p class="card-text text-muted small flex-grow-1">${product.description || 'Beautiful eco-friendly clothing for children'}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="price">
                                        <span class="fw-bold" style="color: var(--primary-blue-dark);">$${product.price}</span>
                                    </div>
                                    <button class="btn btn-primary-custom btn-sm" onclick="addToCart(${product.id}, ${JSON.stringify(product)})">
                                        <i class="bi bi-bag-plus"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');
            } else {
                productsContainer.innerHTML = `
                    <div class="col-12 text-center">
                        <p class="text-muted">No products available at the moment. Please check back soon!</p>
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
            document.getElementById('featured-products').innerHTML = `
                <div class="col-12 text-center">
                    <p class="text-muted">Unable to load products. Please refresh the page.</p>
                </div>
            `;
        });
});
</script>
@endpush