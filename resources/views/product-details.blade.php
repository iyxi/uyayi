@extends('layouts.customer')

@section('title', 'Product Details - Uyayi')
@section('description', 'View detailed information about our eco-friendly children\'s clothing.')

@section('content')
<!-- Breadcrumb -->
<section class="py-3" style="background-color: var(--warm-beige);">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('homepage') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop') }}" class="text-decoration-none">Shop</a></li>
                <li class="breadcrumb-item active" id="product-breadcrumb">Product</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Product Details -->
<section class="py-5">
    <div class="container">
        <div class="row" id="product-content">
            <!-- Content will be loaded here -->
        </div>
    </div>
</section>

<!-- Related Products -->
<section class="py-5" style="background-color: var(--warm-beige);">
    <div class="container">
        <h3 class="fw-bold mb-4" style="color: var(--primary-green);">You Might Also Like</h3>
        <div class="row" id="related-products">
            <!-- Related products will be loaded here -->
        </div>
    </div>
</section>

<!-- Product Loading Template -->
<div id="loading-template" style="display: none;">
    <div class="col-12 text-center py-5">
        <div class="spinner-border text-success" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 text-muted">Loading product details...</p>
    </div>
</div>

<!-- Product Error Template -->
<div id="error-template" style="display: none;">
    <div class="col-12 text-center py-5">
        <i class="bi bi-exclamation-triangle display-1 text-muted"></i>
        <h3 class="mt-3">Product not found</h3>
        <p class="text-muted">The product you're looking for doesn't exist or has been removed.</p>
        <a href="{{ route('shop') }}" class="btn btn-primary-custom">
            <i class="bi bi-arrow-left"></i> Back to Shop
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const productId = window.location.pathname.split('/').pop();
    loadProductDetails(productId);
    loadRelatedProducts();
});

function loadProductDetails(productId) {
    // Show loading state
    document.getElementById('product-content').innerHTML = document.getElementById('loading-template').innerHTML;
    
    fetch(`/api/products`)
        .then(response => response.json())
        .then(data => {
            const products = data.data || [];
            const product = products.find(p => p.id == productId);
            
            if (product) {
                displayProduct(product);
            } else {
                showProductError();
            }
        })
        .catch(error => {
            console.error('Error loading product:', error);
            showProductError();
        });
}

function displayProduct(product) {
    document.getElementById('product-breadcrumb').textContent = product.name;
    document.title = `${product.name} - Uyayi`;
    
    const productHTML = `
        <div class="col-lg-6 mb-4">
            <!-- Product Images -->
            <div class="product-images">
                <div class="main-image mb-3">
                    <img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                         alt="${product.name}" 
                         class="img-fluid rounded-3 shadow-sm"
                         id="main-product-image">
                </div>
                <div class="image-thumbnails">
                    <div class="row g-2">
                        <div class="col-3">
                            <img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" 
                                 alt="View 1" class="img-fluid rounded thumbnail-img" onclick="changeMainImage(this.src)">
                        </div>
                        <div class="col-3">
                            <img src="https://images.unsplash.com/photo-1519238263530-99bdd11df2ea?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" 
                                 alt="View 2" class="img-fluid rounded thumbnail-img" onclick="changeMainImage(this.src)">
                        </div>
                        <div class="col-3">
                            <img src="https://images.unsplash.com/photo-1522771739844-6a9f6d5f14af?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" 
                                 alt="View 3" class="img-fluid rounded thumbnail-img" onclick="changeMainImage(this.src)">
                        </div>
                        <div class="col-3">
                            <img src="https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" 
                                 alt="View 4" class="img-fluid rounded thumbnail-img" onclick="changeMainImage(this.src)">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <!-- Product Info -->
            <div class="product-info">
                <div class="eco-badge mb-3">🌱 Eco-Friendly Material</div>
                
                <h1 class="fw-bold mb-3" style="color: var(--primary-green);">${product.name}</h1>
                
                <div class="product-rating mb-3">
                    <div class="d-flex align-items-center">
                        <div class="stars me-2">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-half text-warning"></i>
                        </div>
                        <span class="text-muted">(4.5) • 24 reviews</span>
                    </div>
                </div>
                
                <div class="price mb-4">
                    <span class="display-5 fw-bold" style="color: var(--primary-green);">$${product.price}</span>
                    <span class="text-muted text-decoration-line-through ms-2">$${(parseFloat(product.price) * 1.25).toFixed(2)}</span>
                    <span class="badge bg-success ms-2">20% OFF</span>
                </div>
                
                <div class="product-meta mb-4">
                    <p><strong>SKU:</strong> ${product.sku}</p>
                    <p><strong>Availability:</strong> 
                        ${product.inventory && product.inventory.stock > 0 ? 
                            `<span class="text-success">In Stock (${product.inventory.stock} available)</span>` : 
                            '<span class="text-danger">Out of Stock</span>'
                        }
                    </p>
                </div>
                
                <div class="product-description mb-4">
                    <h5 class="fw-bold mb-3">Description</h5>
                    <p class="text-muted">${product.description || 'Beautiful, eco-friendly children\'s clothing made with sustainable materials. Perfect for your little one\'s comfort and style while caring for our planet.'}</p>
                </div>
                
                <div class="product-features mb-4">
                    <h6 class="fw-bold mb-2">Features:</h6>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle text-success me-2"></i>100% Organic Cotton</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Eco-friendly Dyes</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Machine Washable</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Hypoallergenic</li>
                        <li><i class="bi bi-check-circle text-success me-2"></i>Fair Trade Certified</li>
                    </ul>
                </div>
                
                <!-- Size Selection -->
                <div class="size-selection mb-4">
                    <h6 class="fw-bold mb-2">Size:</h6>
                    <div class="btn-group" role="group" aria-label="Size selection">
                        <input type="radio" class="btn-check" name="size" id="size-xs" value="XS">
                        <label class="btn btn-outline-secondary" for="size-xs">XS</label>
                        
                        <input type="radio" class="btn-check" name="size" id="size-s" value="S" checked>
                        <label class="btn btn-outline-secondary" for="size-s">S</label>
                        
                        <input type="radio" class="btn-check" name="size" id="size-m" value="M">
                        <label class="btn btn-outline-secondary" for="size-m">M</label>
                        
                        <input type="radio" class="btn-check" name="size" id="size-l" value="L">
                        <label class="btn btn-outline-secondary" for="size-l">L</label>
                        
                        <input type="radio" class="btn-check" name="size" id="size-xl" value="XL">
                        <label class="btn btn-outline-secondary" for="size-xl">XL</label>
                    </div>
                    <small class="text-muted d-block mt-1">
                        <a href="#" class="text-decoration-none">Size Guide</a>
                    </small>
                </div>
                
                <!-- Quantity and Add to Cart -->
                <div class="quantity-cart mb-4">
                    <div class="row g-3">
                        <div class="col-4">
                            <label for="quantity" class="form-label fw-bold">Quantity:</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" onclick="decreaseQuantity()">-</button>
                                <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="${product.inventory ? product.inventory.stock : 10}">
                                <button class="btn btn-outline-secondary" type="button" onclick="increaseQuantity()">+</button>
                            </div>
                        </div>
                        <div class="col-8">
                            <label class="form-label fw-bold d-block">&nbsp;</label>
                            <button class="btn btn-primary-custom w-100 btn-lg" onclick="addToCartWithDetails(${product.id}, ${JSON.stringify(product)})" ${!product.inventory || product.inventory.stock === 0 ? 'disabled' : ''}>
                                <i class="bi bi-bag-plus me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Actions -->
                <div class="product-actions mb-4">
                    <div class="row g-2">
                        <div class="col-6">
                            <button class="btn btn-outline-secondary w-100" onclick="toggleWishlist(${product.id})">
                                <i class="bi bi-heart me-2"></i>Add to Wishlist
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-outline-secondary w-100" onclick="shareProduct()">
                                <i class="bi bi-share me-2"></i>Share
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Info -->
                <div class="shipping-info">
                    <div class="card border-0" style="background-color: var(--warm-beige);">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2">Shipping & Returns</h6>
                            <p class="small mb-1"><i class="bi bi-truck me-2 text-success"></i>Free shipping on orders over $50</p>
                            <p class="small mb-1"><i class="bi bi-arrow-counterclockwise me-2 text-success"></i>30-day easy returns</p>
                            <p class="small mb-0"><i class="bi bi-shield-check me-2 text-success"></i>Quality guarantee</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('product-content').innerHTML = productHTML;
}

function showProductError() {
    document.getElementById('product-content').innerHTML = document.getElementById('error-template').innerHTML;
}

function loadRelatedProducts() {
    fetch('/api/products')
        .then(response => response.json())
        .then(data => {
            const products = data.data ? data.data.slice(0, 4) : [];
            const relatedHTML = products.map(product => `
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="product-card h-100 shadow-sm">
                        <img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" 
                             class="card-img-top" alt="${product.name}" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title fw-bold">${product.name}</h6>
                            <p class="text-muted mb-2">$${product.price}</p>
                            <a href="/product/${product.id}" class="btn btn-outline-success btn-sm w-100">View Details</a>
                        </div>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('related-products').innerHTML = relatedHTML;
        })
        .catch(error => console.error('Error loading related products:', error));
}

function changeMainImage(src) {
    document.getElementById('main-product-image').src = src.replace('w=200', 'w=800');
    
    // Update thumbnail active state
    document.querySelectorAll('.thumbnail-img').forEach(img => {
        img.classList.remove('border', 'border-success');
    });
    event.target.classList.add('border', 'border-success');
}

function increaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const max = parseInt(quantityInput.getAttribute('max'));
    const current = parseInt(quantityInput.value);
    if (current < max) {
        quantityInput.value = current + 1;
    }
}

function decreaseQuantity() {
    const quantityInput = document.getElementById('quantity');
    const current = parseInt(quantityInput.value);
    if (current > 1) {
        quantityInput.value = current - 1;
    }
}

function addToCartWithDetails(productId, product) {
    const quantity = parseInt(document.getElementById('quantity').value);
    const selectedSize = document.querySelector('input[name="size"]:checked')?.value || 'S';
    
    // Add size to product info
    const productWithSize = {
        ...product,
        size: selectedSize
    };
    
    addToCart(productId, productWithSize, quantity);
    
    // Show success message with details
    showToast(`Added ${quantity} × ${product.name} (Size: ${selectedSize}) to cart!`, 'success');
}

function toggleWishlist(productId) {
    showToast('Wishlist feature coming soon!', 'info');
}

function shareProduct() {
    if (navigator.share) {
        navigator.share({
            title: document.title,
            url: window.location.href
        });
    } else {
        // Fallback - copy URL to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            showToast('Product URL copied to clipboard!', 'success');
        });
    }
}

// Add thumbnail hover effects
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('thumbnail-img')) {
        document.querySelectorAll('.thumbnail-img').forEach(img => {
            img.style.opacity = '0.7';
        });
        e.target.style.opacity = '1';
    }
});
</script>

<style>
.thumbnail-img {
    cursor: pointer;
    transition: opacity 0.3s ease, border 0.3s ease;
    opacity: 0.7;
}

.thumbnail-img:hover {
    opacity: 1;
}

.thumbnail-img:first-child {
    opacity: 1;
}

.product-card:hover {
    transform: translateY(-2px);
}

.btn-check:checked + .btn-outline-secondary {
    background-color: var(--primary-green);
    border-color: var(--primary-green);
    color: white;
}
</style>
@endpush