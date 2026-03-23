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
        <h3 class="fw-bold mb-4" style="color: var(--primary-blue);">You Might Also Like</h3>
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
const IS_AUTHENTICATED = @json(auth()->check());
const CSRF_TOKEN = @json(csrf_token());
let currentProductDetails = null;

document.addEventListener('DOMContentLoaded', function() {
    const productId = window.location.pathname.split('/').pop();
    loadProductDetails(productId);
    loadRelatedProducts();
});

function loadProductDetails(productId) {
    // Show loading state
    document.getElementById('product-content').innerHTML = document.getElementById('loading-template').innerHTML;
    
    fetch(`/api/products/${productId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Product not found');
            }

            return response.json();
        })
        .then(product => {
            if (product) {
                displayProduct(product);
                return;
            }

            showProductError();
        })
        .catch(error => {
            console.error('Error loading product:', error);
            showProductError();
        });
}

function getProductImages(product) {
    if (Array.isArray(product.images) && product.images.length > 0) {
        return product.images
            .filter(Boolean)
            .map(path => resolveProductImageUrl(path));
    }

    return ['/img/logo.png'];
}

function resolveProductImageUrl(path) {
    if (!path) {
        return '/img/logo.png';
    }

    const raw = String(path).trim();
    if (/^https?:\/\//i.test(raw)) {
        return raw;
    }

    let clean = raw.replace(/^\/+/, '');
    if (clean.startsWith('public/')) {
        clean = clean.slice(7);
    }

    if (clean.startsWith('storage/') || clean.startsWith('img/')) {
        return `/${clean}`;
    }

    if (clean.includes('/')) {
        return `/storage/${clean}`;
    }

    return `/img/${clean}`;
}

function formatPeso(value) {
    const amount = Number(value || 0);
    return `₱${amount.toFixed(2)}`;
}

function renderCatalogRating(product) {
    const average = Number(product.reviews_avg_rating || 0);
    const count = Number(product.reviews_count || 0);
    const rounded = Math.round(average);
    let stars = '';

    for (let i = 1; i <= 5; i += 1) {
        stars += i <= rounded ? '★' : '☆';
    }

    return `${stars} <span class="ms-1 small">${average.toFixed(2)} (${count})</span>`;
}

function displayProduct(product) {
    currentProductDetails = product;
    document.getElementById('product-breadcrumb').textContent = product.name;
    document.title = `${product.name} - Uyayi`;
    const imageUrls = getProductImages(product);
    const mainImage = imageUrls[0];
    const thumbnails = imageUrls.slice(0, 4);
    
    const productHTML = `
        <div class="col-lg-6 mb-4">
            <!-- Product Images -->
            <div class="product-images">
                <div class="main-image mb-3">
                    <img src="${mainImage}" 
                         alt="${product.name}" 
                         class="img-fluid rounded-3 shadow-sm"
                         id="main-product-image">
                </div>
                <div class="image-thumbnails">
                    <div class="row g-2">
                        ${thumbnails.map((imageUrl, index) => `
                            <div class="col-3">
                                <img src="${imageUrl}" 
                                     alt="${product.name} view ${index + 1}" class="img-fluid rounded thumbnail-img ${index === 0 ? 'border border-success' : ''}" onclick="changeMainImage(this.src, this)">
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <!-- Product Info -->
            <div class="product-info">
                <div class="eco-badge mb-3">🌱 Eco-Friendly Material</div>
                
                <h1 class="fw-bold mb-3" style="color: var(--primary-blue);">${product.name}</h1>
                
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
                    <span class="display-5 fw-bold" style="color: var(--primary-blue);">${formatPeso(product.price)}</span>
                    <span class="text-muted text-decoration-line-through ms-2">${formatPeso(parseFloat(product.price || 0) * 1.25)}</span>
                    <span class="badge bg-success ms-2">20% OFF</span>
                </div>
                
                <div class="product-meta mb-4">
                    <p><strong>SKU:</strong> ${product.sku}</p>
                    <p><strong>Availability:</strong> 
                        ${Number(product.stock || 0) > 0 ? 
                            `<span class="text-success">In Stock (${product.stock} available)</span>` : 
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
                
                <!-- Quantity and Add to Cart -->
                <div class="quantity-cart mb-4">
                    <div class="row g-3">
                        <div class="col-4">
                            <label for="quantity" class="form-label fw-bold">Quantity:</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button" onclick="decreaseQuantity()">-</button>
                                <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="${Number(product.stock || 0) > 0 ? product.stock : 10}">
                                <button class="btn btn-outline-secondary" type="button" onclick="increaseQuantity()">+</button>
                            </div>
                        </div>
                        <div class="col-8">
                            <label class="form-label fw-bold d-block">&nbsp;</label>
                            <button class="btn btn-primary-custom w-100 btn-lg" onclick="addToCartWithDetails(${product.id})" ${Number(product.stock || 0) === 0 ? 'disabled' : ''}>
                                <i class="bi bi-bag-plus me-2"></i>Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Info -->
                <div class="shipping-info">
                    <div class="card border-0" style="background-color: var(--warm-beige);">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2">Shipping & Returns</h6>
                            <p class="small mb-1"><i class="bi bi-truck me-2 text-success"></i>Free shipping on orders over ₱50</p>
                            <p class="small mb-1"><i class="bi bi-arrow-counterclockwise me-2 text-success"></i>30-day easy returns</p>
                            <p class="small mb-0"><i class="bi bi-shield-check me-2 text-success"></i>Quality guarantee</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <h5 class="fw-bold mb-0" style="color: var(--primary-blue);">Customer Reviews</h5>
                                <div id="review-summary" class="text-muted small">Loading reviews...</div>
                            </div>
                            <div id="review-form-wrapper" class="mb-3"></div>
                            <div id="reviews-list"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('product-content').innerHTML = productHTML;
    loadProductReviews(product.id);
}

function renderStars(rating) {
    const full = Math.max(0, Math.min(5, Number(rating || 0)));
    let html = '';
    for (let i = 1; i <= 5; i += 1) {
        html += `<i class="bi ${i <= full ? 'bi-star-fill text-warning' : 'bi-star text-muted'}"></i>`;
    }
    return html;
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };

    return String(text || '').replace(/[&<>"']/g, m => map[m]);
}

function loadProductReviews(productId) {
    fetch(`/api/products/${productId}/reviews`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load reviews');
            }
            return response.json();
        })
        .then(payload => {
            const summary = payload.summary || { average_rating: 0, count: 0 };
            const averageRating = Number(summary.average_rating || 0);
            const count = Number(summary.count || 0);

            const summaryEl = document.getElementById('review-summary');
            if (summaryEl) {
                summaryEl.innerHTML = `${renderStars(Math.round(averageRating))} <span class="ms-1">${averageRating.toFixed(2)} / 5 (${count} review${count === 1 ? '' : 's'})</span>`;
            }

            const headerStarsEl = document.querySelector('.product-rating .stars');
            const headerCountEl = document.querySelector('.product-rating .text-muted');
            if (headerStarsEl) {
                headerStarsEl.innerHTML = renderStars(Math.round(averageRating));
            }
            if (headerCountEl) {
                headerCountEl.textContent = `${averageRating.toFixed(2)} • ${count} review${count === 1 ? '' : 's'}`;
            }

            renderReviewForm(productId, payload);
            renderReviewList(payload.reviews || []);
        })
        .catch(error => {
            console.error(error);
            const summaryEl = document.getElementById('review-summary');
            if (summaryEl) {
                summaryEl.textContent = 'Unable to load reviews right now.';
            }
        });
}

function renderReviewForm(productId, payload) {
    const wrapper = document.getElementById('review-form-wrapper');
    if (!wrapper) return;

    if (!IS_AUTHENTICATED) {
        wrapper.innerHTML = `<div class="alert alert-info mb-0">Please <a href="/login">log in</a> to write a review.</div>`;
        return;
    }

    if (!payload.can_review) {
        wrapper.innerHTML = `<div class="alert alert-warning mb-0">Only customers who bought and completed this product order can post a review.</div>`;
        return;
    }

    const existing = payload.user_review || null;
    const selectedRating = existing ? Number(existing.rating) : 5;
    const comment = existing?.comment ? escapeHtml(existing.comment) : '';

    wrapper.innerHTML = `
        <form id="review-form" class="border rounded p-3" style="background-color: var(--soft-cream);">
            <h6 class="fw-bold mb-2">${existing ? 'Update Your Review' : 'Write a Review'}</h6>
            <div class="mb-2">
                <label class="form-label mb-1">Rating</label>
                <select class="form-select" id="review-rating" required>
                    <option value="5" ${selectedRating === 5 ? 'selected' : ''}>5 - Excellent</option>
                    <option value="4" ${selectedRating === 4 ? 'selected' : ''}>4 - Very Good</option>
                    <option value="3" ${selectedRating === 3 ? 'selected' : ''}>3 - Good</option>
                    <option value="2" ${selectedRating === 2 ? 'selected' : ''}>2 - Fair</option>
                    <option value="1" ${selectedRating === 1 ? 'selected' : ''}>1 - Poor</option>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label mb-1">Comment</label>
                <textarea class="form-control" id="review-comment" rows="3" maxlength="1000" placeholder="Share your experience...">${comment}</textarea>
            </div>
            <button type="submit" class="btn btn-primary-custom btn-sm">${existing ? 'Update Review' : 'Post Review'}</button>
        </form>
    `;

    const form = document.getElementById('review-form');
    if (!form) return;

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const rating = Number(document.getElementById('review-rating').value || 5);
        const reviewComment = document.getElementById('review-comment').value.trim();

        fetch(`/api/products/${productId}/reviews`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                rating,
                comment: reviewComment
            })
        })
            .then(async response => {
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(data.message || 'Failed to save review');
                }
                return data;
            })
            .then(data => {
                showToast(data.message || 'Review saved.', 'success');
                loadProductReviews(productId);
            })
            .catch(err => {
                showToast(err.message || 'Unable to save review.', 'error');
            });
    });
}

function renderReviewList(reviews) {
    const list = document.getElementById('reviews-list');
    if (!list) return;

    if (!Array.isArray(reviews) || reviews.length === 0) {
        list.innerHTML = '<p class="text-muted mb-0">No reviews yet. Be the first to review this product.</p>';
        return;
    }

    list.innerHTML = reviews.map(review => {
        const createdAt = review.created_at ? new Date(review.created_at).toLocaleString() : 'N/A';
        const reviewerName = escapeHtml(review.user?.name || 'Anonymous');
        const reviewComment = escapeHtml(review.comment || 'No comment provided.');

        return `
            <div class="border-top pt-3 mt-3">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <div class="fw-semibold">${reviewerName}</div>
                        <div class="small">${renderStars(review.rating)}</div>
                    </div>
                    <div class="small text-muted">${createdAt}</div>
                </div>
                <p class="mt-2 mb-0 text-muted">${reviewComment}</p>
            </div>
        `;
    }).join('');
}

function showProductError() {
    document.getElementById('product-content').innerHTML = document.getElementById('error-template').innerHTML;
}

function loadRelatedProducts() {
    fetch('/api/products')
        .then(response => response.json())
        .then(data => {
            const products = data.data ? data.data.slice(0, 4) : [];
            const relatedHTML = products.map(product => {
                const stock = Number(product.stock || 0);
                const badgeLabel = stock <= 0 ? 'Out of Stock' : (stock <= 5 ? 'Sale!' : 'New!');
                const badgeClass = stock <= 0 ? 'shop-catalog-badge shop-catalog-badge--out' : (stock <= 5 ? 'shop-catalog-badge shop-catalog-badge--sale' : 'shop-catalog-badge');

                return `
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="shop-catalog-card">
                            <div class="shop-catalog-card__media">
                                <span class="${badgeClass}">${badgeLabel}</span>
                                <img src="${getProductImages(product)[0]}"
                                     class="shop-catalog-card__image" alt="${product.name}" onerror="this.onerror=null;this.src='/img/logo.png';">
                            </div>
                            <div class="shop-catalog-card__body">
                                <h6 class="shop-catalog-card__title">${product.name}</h6>
                                <div class="shop-catalog-card__price">${formatPeso(product.price)}</div>
                                <div class="shop-catalog-card__stars">${renderCatalogRating(product)}</div>
                                <div class="shop-catalog-card__meta">${stock > 0 ? `In stock (${stock})` : 'Currently unavailable'}</div>
                                <div class="shop-catalog-card__actions">
                                    <button class="shop-catalog-icon-btn" onclick="viewProduct(${product.id})" aria-label="View ${product.name}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="shop-catalog-icon-btn shop-catalog-icon-btn--primary" onclick="addToCart(${product.id})" aria-label="Add ${product.name} to cart" ${stock <= 0 ? 'disabled' : ''}>
                                        <i class="bi bi-bag-plus"></i>
                                    </button>
                                </div>
                                <p class="small text-muted mt-3 mb-0">${product.description || 'Carefully selected baby essentials from your catalog.'}</p>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            
            document.getElementById('related-products').innerHTML = relatedHTML;
        })
        .catch(error => console.error('Error loading related products:', error));
}

function viewProduct(productId) {
    window.location.href = `/product/${productId}`;
}

function changeMainImage(src, selectedThumb) {
    document.getElementById('main-product-image').src = src;
    
    // Update thumbnail active state
    document.querySelectorAll('.thumbnail-img').forEach(img => {
        img.classList.remove('border', 'border-success');
    });

    if (selectedThumb) {
        selectedThumb.classList.add('border', 'border-success');
    }
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

async function addToCartWithDetails(productId) {
    const product = currentProductDetails;
    if (!product || Number(product.id) !== Number(productId)) {
        showToast('Unable to add this product right now.', 'warning');
        return;
    }

    const quantity = parseInt(document.getElementById('quantity').value);
    const added = await addToCart(productId, product, quantity, true);
    
    if (!added) {
        return;
    }
    return;
    showToast(`Added ${quantity} × ${product.name} to cart!`, 'success');
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

#related-products {
    justify-content: center;
}

#related-products .shop-catalog-card {
    border-radius: 26px;
    background: #fff;
    box-shadow: 0 14px 34px rgba(95, 74, 45, 0.08);
    overflow: hidden;
    height: 100%;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

#related-products .shop-catalog-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(95, 74, 45, 0.14);
}

#related-products .shop-catalog-card__media {
    position: relative;
    background: linear-gradient(180deg, #faf7f2 0%, #f3eee8 100%);
    border-radius: 26px;
    margin: 0.85rem;
    min-height: 220px;
    overflow: hidden;
}

#related-products .shop-catalog-card__image {
    width: 100%;
    height: 220px;
    object-fit: contain;
    padding: 1rem;
}

#related-products .shop-catalog-badge {
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

#related-products .shop-catalog-badge--sale {
    background: #a7d64b;
}

#related-products .shop-catalog-badge--out {
    background: #c7c7c7;
}

#related-products .shop-catalog-card__body {
    padding: 0.25rem 1.25rem 1.35rem;
    text-align: center;
}

#related-products .shop-catalog-card__title {
    font-size: 0.86rem;
    font-weight: 800;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    color: #453624;
    min-height: 2.6em;
    margin-bottom: 0.45rem;
}

#related-products .shop-catalog-card__price {
    font-size: 1.4rem;
    line-height: 1;
    font-weight: 900;
    color: var(--primary-blue-dark);
    margin-bottom: 0.45rem;
}

#related-products .shop-catalog-card__stars {
    color: var(--accent-brown);
    font-size: 0.82rem;
    letter-spacing: 0.14em;
    margin-bottom: 0.9rem;
}

#related-products .shop-catalog-card__meta {
    font-size: 0.78rem;
    color: #8f816f;
    margin-bottom: 0.9rem;
}

#related-products .shop-catalog-card__actions {
    display: flex;
    gap: 0.7rem;
    align-items: center;
    justify-content: center;
}

#related-products .shop-catalog-icon-btn {
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

#related-products .shop-catalog-icon-btn:hover {
    border-color: var(--primary-blue);
    color: var(--primary-blue-dark);
}

#related-products .shop-catalog-icon-btn--primary {
    border-color: var(--primary-blue);
    background: var(--primary-blue);
    color: #fff;
}

#related-products .shop-catalog-icon-btn--primary:hover {
    background: var(--primary-blue-dark);
    border-color: var(--primary-blue-dark);
    color: #fff;
}

.btn-check:checked + .btn-outline-secondary {
    background-color: var(--primary-blue);
    border-color: var(--primary-blue);
    color: white;
}
</style>
@endpush
