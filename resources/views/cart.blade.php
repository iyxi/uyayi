@extends('layouts.customer')

@section('title', 'Shopping Cart - Uyayi')
@section('description', 'Review your selected eco-friendly children\'s clothing before checkout.')

@section('content')
<section class="py-4" style="background-color: white; border-bottom: 2px solid var(--soft-tan); box-shadow: 0 2px 8px rgba(122,185,232,0.08);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="fw-bold mb-0" style="color: var(--primary-blue-dark);">
                    <i class="bi bi-bag"></i> Shopping Cart
                </h1>
                <p class="text-muted mb-0">Review your items before checkout</p>
            </div>
            <div class="col-md-6 text-md-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-md-end mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('homepage') }}" class="text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('shop') }}" class="text-decoration-none">Shop</a></li>
                        <li class="breadcrumb-item active">Cart</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-8">
                <div id="empty-cart" class="text-center py-5" style="display: none;">
                    <i class="bi bi-bag display-1 text-muted"></i>
                    <h3 class="mt-3 text-muted">Your cart is empty</h3>
                    <p class="text-muted">Start shopping to add beautiful eco-friendly clothing to your cart.</p>
                    <a href="{{ route('shop') }}" class="btn btn-primary-custom btn-lg mt-3">
                        <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>

                <div id="cart-items">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="mb-0 fw-bold">Cart Items (<span id="cart-item-count">0</span>)</h5>
                                </div>
                                <div class="col-auto">
                                    <button class="btn btn-outline-danger btn-sm" onclick="clearCart()">
                                        <i class="bi bi-trash me-1"></i>Clear Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div id="cart-items-list"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-top cart-summary-sticky">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold">Order Summary</h5>
                        </div>
                        <div class="card-body" id="order-summary-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span class="fw-bold" id="cart-subtotal">&#8369;0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2 align-items-center">
                                <span>Shipping Fee:</span>
                                <span class="fw-bold text-primary" id="shipping-cost">Free</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span class="fw-bold" id="tax-amount">&#8369;0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold fs-5">Total:</span>
                                <span class="fw-bold fs-5" style="color: var(--primary-blue-dark);" id="cart-total">&#8369;0.00</span>
                            </div>

                            <div class="mb-3">
                                <label for="promo-code" class="form-label mb-1">Promo Code</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Enter promo code" id="promo-code">
                                    <button class="btn btn-outline-secondary rounded-circle" type="button" style="width:40px; height:40px; display:flex; align-items:center; justify-content:center;" onclick="applyPromoCode()" title="Apply Promo Code">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </div>
                            </div>

                            <button class="btn btn-primary-custom w-100 btn-lg mb-3" onclick="proceedToCheckout()" id="checkout-btn" disabled>
                                <i class="bi bi-credit-card me-2"></i>Proceed to Checkout
                            </button>

                            <div class="text-center">
                                <a href="{{ route('shop') }}" class="text-decoration-none">
                                    <i class="bi bi-arrow-left me-1"></i>Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, var(--warm-beige) 0%, var(--soft-cream) 100%);">
                        <div class="card-body text-center">
                            <i class="bi bi-leaf display-4" style="color: var(--primary-blue);"></i>
                            <h6 class="fw-bold mt-2">Eco-Friendly Choice!</h6>
                            <p class="small mb-0">Every purchase helps support sustainable fashion and a healthier planet for our children.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background-color: var(--warm-beige);">
    <div class="container">
        <h3 class="fw-bold mb-4 text-center" style="color: var(--primary-blue-dark);">You Might Also Like</h3>
        <div class="row" id="recommended-products"></div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCartItems();
    loadRecommendedProducts();
});

function getCartState() {
    if (typeof syncCartFromStorage === 'function') {
        return syncCartFromStorage();
    }

    return (typeof window.cart === 'object' && window.cart !== null) ? window.cart : {};
}

function persistCartState(cartState) {
    window.cart = cartState;

    if (typeof cartStorageKey === 'string' && cartStorageKey.length > 0) {
        if (Object.keys(cartState).length === 0) {
            localStorage.removeItem(cartStorageKey);
        } else {
            localStorage.setItem(cartStorageKey, JSON.stringify(cartState));
        }
    }
}

function resolveProductImageUrl(path) {
    if (!path) return '/img/logo.png';
    const raw = String(path).trim();
    if (/^https?:\/\//i.test(raw)) return raw;

    let clean = raw.replace(/^\/+/, '');
    if (clean.startsWith('public/')) clean = clean.slice(7);
    if (clean.startsWith('storage/') || clean.startsWith('img/')) return `/${clean}`;
    if (clean.includes('/')) return `/storage/${clean}`;
    return `/img/${clean}`;
}

function getProductPrimaryImage(product) {
    const firstImage = Array.isArray(product.images) && product.images.length > 0
        ? product.images.find(Boolean)
        : null;

    return resolveProductImageUrl(firstImage);
}

function formatPeso(value) {
    const amount = Number(value || 0);
    return `\u20B1${amount.toFixed(2)}`;
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

function loadCartItems() {
    const cartState = getCartState();
    const cartItems = Object.values(cartState).filter(item => item && item.product && Number(item.product.id || 0));
    const cartItemsList = document.getElementById('cart-items-list');
    const cartItemCount = document.getElementById('cart-item-count');
    const emptyCart = document.getElementById('empty-cart');
    const cartItemsContainer = document.getElementById('cart-items');
    const checkoutBtn = document.getElementById('checkout-btn');

    if (cartItems.length === 0) {
        cartItemsList.innerHTML = '';
        cartItemCount.textContent = '0';
        emptyCart.style.display = 'block';
        cartItemsContainer.style.display = 'none';
        checkoutBtn.disabled = true;
        updateCartTotal();
        return;
    }

    emptyCart.style.display = 'none';
    cartItemsContainer.style.display = 'block';
    checkoutBtn.disabled = false;
    cartItemCount.textContent = cartItems.reduce((total, item) => total + Math.max(1, Number(item.quantity || 1)), 0);

    cartItemsList.innerHTML = cartItems.map(item => {
        const product = item.product || {};
        const quantity = Math.max(1, Number(item.quantity || 1));
        const subtotal = Number(product.price || 0) * quantity;

        return `
            <div class="border-bottom p-4" id="cart-item-${product.id}">
                <div class="row align-items-center">
                    <div class="col-md-2 col-3 mb-3 mb-md-0">
                        <img src="${getProductPrimaryImage(product)}" alt="${product.name || 'Product'}" class="img-fluid rounded">
                    </div>
                    <div class="col-md-4 col-9 mb-3 mb-md-0">
                        <h6 class="fw-bold mb-1">${product.name || 'Product'}</h6>
                        <p class="text-muted small mb-1">SKU: ${product.sku || '-'}</p>
                        ${product.size ? `<p class="text-muted small mb-1">Size: ${product.size}</p>` : ''}
                        <div class="eco-badge">Eco-Friendly</div>
                    </div>
                    <div class="col-md-2 col-4 mb-3 mb-md-0">
                        <div class="input-group input-group-sm">
                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${product.id}, ${quantity - 1})">-</button>
                            <input type="text" class="form-control text-center" value="${quantity}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${product.id}, ${quantity + 1})">+</button>
                        </div>
                    </div>
                    <div class="col-md-2 col-4 mb-3 mb-md-0 text-center">
                        <div class="fw-bold" style="color: var(--primary-blue-dark);">${formatPeso(subtotal)}</div>
                        <small class="text-muted">${formatPeso(product.price || 0)} each</small>
                    </div>
                    <div class="col-md-2 col-4 text-center">
                        <button class="btn btn-outline-danger btn-sm" onclick="removeFromCart(${product.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    updateCartTotal();
}

function updateQuantity(productId, newQuantity) {
    const cartState = getCartState();
    if (newQuantity < 1) {
        removeFromCart(productId);
        return;
    }

    if (!cartState[productId]) {
        return;
    }

    cartState[productId].quantity = newQuantity;
    persistCartState(cartState);
    loadCartItems();
    updateCartCount();
}

function removeFromCart(productId) {
    if (!confirm('Remove this item from your cart?')) {
        return;
    }

    const cartState = getCartState();
    delete cartState[productId];
    persistCartState(cartState);
    loadCartItems();
    updateCartCount();
    showToast('Item removed from cart', 'success');
}

function clearCart() {
    if (!confirm('Remove all items from your cart?')) {
        return;
    }

    persistCartState({});
    loadCartItems();
    updateCartCount();
    showToast('Cart cleared', 'success');
}

function updateCartTotal() {
    const cartItems = Object.values(getCartState()).filter(item => item && item.product);
    const subtotal = cartItems.reduce((total, item) => total + (Number(item.product.price || 0) * Math.max(1, Number(item.quantity || 1))), 0);
    const shippingThreshold = 1000;
    const shipping = subtotal === 0 ? 0 : (subtotal >= shippingThreshold ? 0 : 50);
    const tax = subtotal * 0.08;
    const total = subtotal + shipping + tax;

    document.getElementById('cart-subtotal').textContent = formatPeso(subtotal);
    document.getElementById('shipping-cost').textContent = shipping === 0 ? 'Free' : formatPeso(shipping);
    document.getElementById('tax-amount').textContent = formatPeso(tax);
    document.getElementById('cart-total').textContent = formatPeso(total);

    if (subtotal > 0 && subtotal < shippingThreshold) {
        showShippingMessage(`Add ${formatPeso(shippingThreshold - subtotal)} more for free shipping!`);
    } else {
        const existingMessage = document.getElementById('shipping-message');
        if (existingMessage) {
            existingMessage.remove();
        }
    }
}

function showShippingMessage(message) {
    const existingMessage = document.getElementById('shipping-message');
    if (existingMessage) {
        existingMessage.remove();
    }

    const messageDiv = document.createElement('div');
    messageDiv.id = 'shipping-message';
    messageDiv.className = 'alert alert-info alert-sm';
    messageDiv.innerHTML = `<i class="bi bi-truck me-2"></i>${message}`;

    const summaryBody = document.getElementById('order-summary-body');
    if (summaryBody) {
        summaryBody.insertBefore(messageDiv, summaryBody.firstChild);
    }
}

function applyPromoCode() {
    const promoCode = document.getElementById('promo-code').value.trim();
    if (!promoCode) {
        showToast('Please enter a promo code', 'warning');
        return;
    }

    const validCodes = {
        'UYAYI5': '5% off your order',
        'BABY10': '10% off your order',
        'WELCOME50': '₱50 off your order',
    };

    const normalizedCode = promoCode.toUpperCase();
    if (!validCodes[normalizedCode]) {
        showToast('Invalid promo code', 'error');
        return;
    }

    document.getElementById('promo-code').value = normalizedCode;
    showToast(`Promo code valid: ${normalizedCode} (${validCodes[normalizedCode]})`, 'success');
}

function proceedToCheckout() {
    if (Object.keys(getCartState()).length === 0) {
        showToast('Your cart is empty', 'warning');
        return;
    }

    window.location.href = '{{ route("checkout.page") }}';
}

function loadRecommendedProducts() {
    fetch('/api/products')
        .then(response => response.json())
        .then(data => {
            const products = data.data ? data.data.slice(0, 4) : [];
            document.getElementById('recommended-products').innerHTML = products.map(product => {
                const stock = Number(product.stock || 0);
                const badgeLabel = stock <= 0 ? 'Out of Stock' : (stock <= 5 ? 'Sale!' : 'New!');
                const badgeClass = stock <= 0 ? 'shop-catalog-badge shop-catalog-badge--out' : (stock <= 5 ? 'shop-catalog-badge shop-catalog-badge--sale' : 'shop-catalog-badge');

                return `
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="shop-catalog-card">
                            <div class="shop-catalog-card__media">
                                <span class="${badgeClass}">${badgeLabel}</span>
                                <img src="${getProductPrimaryImage(product)}" class="shop-catalog-card__image" alt="${product.name}" onerror="this.onerror=null;this.src='/img/logo.png';">
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
        })
        .catch(error => console.error('Error loading recommended products:', error));
}

function viewProduct(productId) {
    window.location.href = `/product/${productId}`;
}

window.removeFromCart = removeFromCart;
</script>

<style>
.sticky-top {
    position: sticky;
    top: 2rem;
    z-index: 1020;
}

.cart-summary-sticky {
    top: 1.5rem;
}

#cart-items-list .border-bottom:last-child {
    border-bottom: 0 !important;
}

#recommended-products {
    justify-content: center;
}

#recommended-products .shop-catalog-card {
    border-radius: 26px;
    background: #fff;
    box-shadow: 0 14px 34px rgba(95, 74, 45, 0.08);
    overflow: hidden;
    height: 100%;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

#recommended-products .shop-catalog-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 40px rgba(95, 74, 45, 0.14);
}

#recommended-products .shop-catalog-card__media {
    position: relative;
    background: linear-gradient(180deg, #faf7f2 0%, #f3eee8 100%);
    border-radius: 26px;
    margin: 0.85rem;
    min-height: 220px;
    overflow: hidden;
}

#recommended-products .shop-catalog-card__image {
    width: 100%;
    height: 220px;
    object-fit: contain;
    padding: 1rem;
}

#recommended-products .shop-catalog-badge {
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

#recommended-products .shop-catalog-badge--sale {
    background: #a7d64b;
}

#recommended-products .shop-catalog-badge--out {
    background: #c7c7c7;
}

#recommended-products .shop-catalog-card__body {
    padding: 0.25rem 1.25rem 1.35rem;
    text-align: center;
}

#recommended-products .shop-catalog-card__title {
    font-size: 0.86rem;
    font-weight: 800;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    color: #453624;
    min-height: 2.6em;
    margin-bottom: 0.45rem;
}

#recommended-products .shop-catalog-card__price {
    font-size: 1.4rem;
    line-height: 1;
    font-weight: 900;
    color: var(--primary-blue-dark);
    margin-bottom: 0.45rem;
}

#recommended-products .shop-catalog-card__stars {
    color: var(--accent-brown);
    font-size: 0.82rem;
    letter-spacing: 0.14em;
    margin-bottom: 0.9rem;
}

#recommended-products .shop-catalog-card__meta {
    font-size: 0.78rem;
    color: #8f816f;
    margin-bottom: 0.9rem;
}

#recommended-products .shop-catalog-card__actions {
    display: flex;
    gap: 0.7rem;
    align-items: center;
    justify-content: center;
}

#recommended-products .shop-catalog-icon-btn {
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

#recommended-products .shop-catalog-icon-btn:hover {
    border-color: var(--primary-blue);
    color: var(--primary-blue-dark);
}

#recommended-products .shop-catalog-icon-btn--primary {
    border-color: var(--primary-blue);
    background: var(--primary-blue);
    color: #fff;
}

#recommended-products .shop-catalog-icon-btn--primary:hover {
    background: var(--primary-blue-dark);
    border-color: var(--primary-blue-dark);
    color: #fff;
}

.alert-sm {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}

.input-group-sm .form-control,
.input-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.eco-badge {
    display: inline-block;
    background-color: var(--primary-blue);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

@media (max-width: 767.98px) {
    .sticky-top {
        position: relative;
        top: auto;
    }

    #cart-items-list .row > div {
        text-align: left !important;
    }
}
</style>
@endpush
