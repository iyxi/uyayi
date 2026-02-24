@extends('layouts.customer')

@section('title', 'Shopping Cart - Uyayi')
@section('description', 'Review your selected eco-friendly children\'s clothing before checkout.')

@section('content')
<!-- Page Header -->
<section class="py-4" style="background-color: white; border-bottom: 2px solid var(--soft-yellow);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="fw-bold mb-0" style="color: var(--primary-green);">
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

<!-- Cart Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <!-- Empty Cart State -->
                <div id="empty-cart" class="text-center py-5" style="display: none;">
                    <i class="bi bi-bag display-1 text-muted"></i>
                    <h3 class="mt-3 text-muted">Your cart is empty</h3>
                    <p class="text-muted">Start shopping to add beautiful eco-friendly clothing to your cart.</p>
                    <a href="{{ route('shop') }}" class="btn btn-primary-custom btn-lg mt-3">
                        <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
                
                <!-- Cart Items List -->
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
                            <div id="cart-items-list">
                                <!-- Cart items will be populated here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 2rem;">
                    <!-- Order Summary -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span class="fw-bold" id="cart-subtotal">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span class="fw-bold" id="shipping-cost">Free</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span class="fw-bold" id="tax-amount">$0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold fs-5">Total:</span>
                                <span class="fw-bold fs-5" style="color: var(--primary-green);" id="cart-total">$0.00</span>
                            </div>
                            
                            <!-- Promo Code -->
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Promo code" id="promo-code">
                                    <button class="btn btn-outline-secondary" type="button" onclick="applyPromoCode()">Apply</button>
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
                    
                    <!-- Eco Message -->
                    <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, var(--soft-yellow) 0%, var(--warm-beige) 100%);">
                        <div class="card-body text-center">
                            <i class="bi bi-leaf display-4" style="color: var(--primary-green);"></i>
                            <h6 class="fw-bold mt-2">Eco-Friendly Choice!</h6>
                            <p class="small mb-0">Every purchase helps support sustainable fashion and a healthier planet for our children.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Recommended Products -->
<section class="py-5" style="background-color: var(--warm-beige);">
    <div class="container">
        <h3 class="fw-bold mb-4 text-center" style="color: var(--primary-green);">You Might Also Like</h3>
        <div class="row" id="recommended-products">
            <!-- Recommended products will be loaded here -->
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCartItems();
    loadRecommendedProducts();
});

function loadCartItems() {
    const cartItems = Object.values(cart);
    const cartItemsList = document.getElementById('cart-items-list');
    const cartItemCount = document.getElementById('cart-item-count');
    const emptyCart = document.getElementById('empty-cart');
    const cartItemsContainer = document.getElementById('cart-items');
    const checkoutBtn = document.getElementById('checkout-btn');
    
    if (cartItems.length === 0) {
        emptyCart.style.display = 'block';
        cartItemsContainer.style.display = 'none';
        checkoutBtn.disabled = true;
        updateCartTotal();
        return;
    }
    
    emptyCart.style.display = 'none';
    cartItemsContainer.style.display = 'block';
    checkoutBtn.disabled = false;
    cartItemCount.textContent = cartItems.length;
    
    const cartHTML = cartItems.map(item => {
        const product = item.product;
        const quantity = item.quantity;
        const subtotal = (parseFloat(product.price) * quantity).toFixed(2);
        
        return `
            <div class="border-bottom p-4" id="cart-item-${product.id}">
                <div class="row align-items-center">
                    <div class="col-md-2 col-3 mb-3 mb-md-0">
                        <img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" 
                             alt="${product.name}" class="img-fluid rounded">
                    </div>
                    <div class="col-md-4 col-9 mb-3 mb-md-0">
                        <h6 class="fw-bold mb-1">${product.name}</h6>
                        <p class="text-muted small mb-1">SKU: ${product.sku}</p>
                        ${product.size ? `<p class="text-muted small mb-1">Size: ${product.size}</p>` : ''}
                        <div class="eco-badge">🌱 Eco-Friendly</div>
                    </div>
                    <div class="col-md-2 col-4 mb-3 mb-md-0">
                        <div class="input-group input-group-sm">
                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${product.id}, ${quantity - 1})">-</button>
                            <input type="text" class="form-control text-center" value="${quantity}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity(${product.id}, ${quantity + 1})">+</button>
                        </div>
                    </div>
                    <div class="col-md-2 col-4 mb-3 mb-md-0 text-center">
                        <div class="fw-bold" style="color: var(--primary-green);">$${subtotal}</div>
                        <small class="text-muted">$${product.price} each</small>
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
    
    cartItemsList.innerHTML = cartHTML;
    updateCartTotal();
}

function updateQuantity(productId, newQuantity) {
    if (newQuantity < 1) {
        removeFromCart(productId);
        return;
    }
    
    if (cart[productId]) {
        cart[productId].quantity = newQuantity;
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCartItems();
        updateCartCount();
    }
}

function removeFromCart(productId) {
    if (confirm('Remove this item from your cart?')) {
        delete cart[productId];
        localStorage.setItem('cart', JSON.stringify(cart));
        loadCartItems();
        updateCartCount();
        showToast('Item removed from cart', 'success');
    }
}

function clearCart() {
    if (confirm('Remove all items from your cart?')) {
        cart = {};
        localStorage.removeItem('cart');
        loadCartItems();
        updateCartCount();
        showToast('Cart cleared', 'success');
    }
}

function updateCartTotal() {
    const cartItems = Object.values(cart);
    const subtotal = cartItems.reduce((total, item) => {
        return total + (parseFloat(item.product.price) * item.quantity);
    }, 0);
    
    const shippingThreshold = 50;
    const shipping = subtotal >= shippingThreshold ? 0 : 9.99;
    const taxRate = 0.08; // 8% tax
    const tax = subtotal * taxRate;
    const total = subtotal + shipping + tax;
    
    document.getElementById('cart-subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('shipping-cost').textContent = shipping === 0 ? 'Free' : `$${shipping.toFixed(2)}`;
    document.getElementById('tax-amount').textContent = `$${tax.toFixed(2)}`;
    document.getElementById('cart-total').textContent = `$${total.toFixed(2)}`;
    
    // Show shipping message
    if (subtotal > 0 && subtotal < shippingThreshold) {
        const remaining = shippingThreshold - subtotal;
        showShippingMessage(`Add $${remaining.toFixed(2)} more for free shipping!`);
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
    
    const cartSummary = document.querySelector('.card-body');
    cartSummary.insertBefore(messageDiv, cartSummary.firstChild);
}

function applyPromoCode() {
    const promoCode = document.getElementById('promo-code').value.trim();
    if (!promoCode) {
        showToast('Please enter a promo code', 'warning');
        return;
    }
    
    // Mock promo code validation
    const validCodes = {
        'WELCOME10': 0.10,
        'SAVE20': 0.20,
        'ECOFRIENDLY': 0.15
    };
    
    if (validCodes[promoCode.toUpperCase()]) {
        showToast(`Promo code applied! ${(validCodes[promoCode.toUpperCase()] * 100)}% discount`, 'success');
        // Apply discount logic here
    } else {
        showToast('Invalid promo code', 'error');
    }
}

function proceedToCheckout() {
    if (Object.keys(cart).length === 0) {
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
            const recommendedHTML = products.map(product => `
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="product-card h-100 shadow-sm">
                        <img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" 
                             class="card-img-top" alt="${product.name}" style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title fw-bold">${product.name}</h6>
                            <p class="text-muted mb-2 flex-grow-1">$${product.price}</p>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-success btn-sm flex-grow-1" onclick="viewProduct(${product.id})">
                                    View
                                </button>
                                <button class="btn btn-primary-custom btn-sm" onclick="addToCart(${product.id}, ${JSON.stringify(product)})">
                                    <i class="bi bi-bag-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            
            document.getElementById('recommended-products').innerHTML = recommendedHTML;
        })
        .catch(error => console.error('Error loading recommended products:', error));
}

function viewProduct(productId) {
    window.location.href = `/product/${productId}`;
}

// Override the global removeFromCart to work with the cart page
window.removeFromCart = removeFromCart;
</script>

<style>
.sticky-top {
    position: sticky;
    top: 2rem;
    z-index: 1020;
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
    background-color: var(--primary-green);
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
}
</style>
@endpush