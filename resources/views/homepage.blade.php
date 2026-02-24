@extends('layouts.customer')

@section('title', 'Uyayi - Eco-Friendly Children\'s Clothing')
@section('description', 'Discover beautiful, sustainable children\'s clothing at Uyayi. Browse our collection of eco-friendly dresses, tops, and more for your little ones.')

@section('content')
<!-- Hero Section -->
<section class="hero-section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="display-4 fw-bold mb-3" style="color: var(--primary-green);">
                        Welcome to Uyayi 
                        <span style="font-family: 'Yellowtail', cursive;"></span>
                    </h1>
                    <p class="lead mb-4" style="color: var(--text-dark);">
                        Discover beautiful, eco-friendly children's clothing made with love for your little ones and our planet.
                    </p>
                    <div class="d-flex gap-3 align-items-center mb-4">
                        <div class="eco-badge">
                            🌱 100% Eco-friendly and sustainable!
                        </div>
                        <div class="text-success fw-bold">
                            Buy 2 and Get 20% Off
                        </div>
                    </div>
                    <a href="{{ route('shop') }}" class="btn btn-primary-custom btn-lg">
                        <i class="bi bi-arrow-right"></i> Shop Now
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-image-placeholder">
                    <img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?ixlib=rb-4.0.3&auto=format&fit=crop&w=786&q=80" 
                         alt="Happy child in eco-friendly clothing" 
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
            <h2 class="display-5 fw-bold" style="color: var(--primary-green);">Featured Products</h2>
            <p class="lead text-muted">Handpicked favorites for your little ones</p>
        </div>
        
        <div class="row" id="featured-products">
            <!-- Products will be loaded here dynamically -->
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
            <h2 class="display-5 fw-bold" style="color: var(--primary-green);">Shop by Category</h2>
            <p class="lead text-muted">Find exactly what you're looking for</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3 col-sm-6">
                <div class="category-card text-center p-4 h-100 bg-light rounded-3">
                    <div class="category-icon mb-3">
                        <i class="bi bi-flower1 display-4" style="color: var(--primary-green);"></i>
                    </div>
                    <h5 class="fw-bold">Dresses</h5>
                    <p class="text-muted mb-3">Beautiful dresses for special moments</p>
                    <a href="{{ route('shop') }}?category=dresses" class="btn btn-outline-success btn-sm">Shop Dresses</a>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="category-card text-center p-4 h-100 bg-light rounded-3">
                    <div class="category-icon mb-3">
                        <i class="bi bi-house-heart display-4" style="color: var(--soft-brown);"></i>
                    </div>
                    <h5 class="fw-bold">Casual Wear</h5>
                    <p class="text-muted mb-3">Comfortable everyday clothing</p>
                    <a href="{{ route('shop') }}?category=casual" class="btn btn-outline-success btn-sm">Shop Casual</a>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="category-card text-center p-4 h-100 bg-light rounded-3">
                    <div class="category-icon mb-3">
                        <i class="bi bi-stars display-4" style="color: var(--primary-green);"></i>
                    </div>
                    <h5 class="fw-bold">New Arrivals</h5>
                    <p class="text-muted mb-3">Latest additions to our collection</p>
                    <a href="{{ route('shop') }}?sort=newest" class="btn btn-outline-success btn-sm">Shop New</a>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6">
                <div class="category-card text-center p-4 h-100 bg-light rounded-3">
                    <div class="category-icon mb-3">
                        <i class="bi bi-tag display-4" style="color: var(--soft-brown);"></i>
                    </div>
                    <h5 class="fw-bold">Sale Items</h5>
                    <p class="text-muted mb-3">Great deals on quality pieces</p>
                    <a href="{{ route('shop') }}?sale=true" class="btn btn-outline-danger btn-sm">Shop Sale</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold" style="color: var(--primary-green);">Why Choose Uyayi?</h2>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="feature-item">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-leaf display-4" style="color: var(--primary-green);"></i>
                    </div>
                    <h5 class="fw-bold">Eco-Friendly Materials</h5>
                    <p class="text-muted">Made from sustainable, organic materials that are gentle on your child's skin and the environment.</p>
                </div>
            </div>
            
            <div class="col-md-4 text-center">
                <div class="feature-item">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-heart display-4" style="color: var(--soft-brown);"></i>
                    </div>
                    <h5 class="fw-bold">Made with Love</h5>
                    <p class="text-muted">Every piece is carefully crafted with attention to detail and love for your little ones.</p>
                </div>
            </div>
            
            <div class="col-md-4 text-center">
                <div class="feature-item">
                    <div class="feature-icon mb-3">
                        <i class="bi bi-shield-check display-4" style="color: var(--primary-green);"></i>
                    </div>
                    <h5 class="fw-bold">Quality Guarantee</h5>
                    <p class="text-muted">We stand behind our quality with a satisfaction guarantee and easy returns.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-5" style="background: linear-gradient(135deg, var(--soft-yellow) 0%, var(--primary-green) 100%);">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h3 class="fw-bold text-white mb-3">Stay Updated with Uyayi</h3>
                <p class="text-white mb-4">Get the latest updates on new collections, eco-parenting tips, and exclusive offers!</p>
                <div class="input-group input-group-lg">
                    <input type="email" class="form-control" placeholder="Enter your email address">
                    <button class="btn btn-light fw-bold" type="button">
                        Subscribe <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
                <small class="text-white d-block mt-2">We respect your privacy. Unsubscribe at any time.</small>
            </div>
        </div>
    </div>
</section>
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
                                        <span class="fw-bold" style="color: var(--primary-green);">$${product.price}</span>
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