@extends('layouts.customer')

@section('title', 'Checkout - Uyayi')
@section('description', 'Complete your order securely at Uyayi.')

@section('content')
<!-- Page Header -->
<section class="py-4" style="background-color: white; border-bottom: 2px solid var(--soft-tan);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="fw-bold mb-0" style="color: var(--primary-blue);">
                    <i class="bi bi-credit-card"></i> Checkout
                </h1>
                <p class="text-muted mb-0">Secure checkout for your eco-friendly items</p>
            </div>
            <div class="col-md-6 text-md-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-md-end mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('homepage') }}" class="text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cart.view') }}" class="text-decoration-none">Cart</a></li>
                        <li class="breadcrumb-item active">Checkout</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Checkout Progress -->
<section class="py-3" style="background-color: var(--warm-beige);">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="checkout-step active">
                        <div class="step-circle">1</div>
                        <small>Shipping</small>
                    </div>
                    <div class="checkout-step">
                        <div class="step-circle">2</div>
                        <small>Payment</small>
                    </div>
                    <div class="checkout-step">
                        <div class="step-circle">3</div>
                        <small>Review</small>
                    </div>
                    <div class="checkout-step">
                        <div class="step-circle">4</div>
                        <small>Complete</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Checkout Form -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8">
                <form id="checkout-form">
                    @csrf
                    
                    <!-- Login/Guest Checkout -->
                    @guest
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="fw-bold mb-3">Account Information</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="checkout_type" id="guest_checkout" value="guest" checked>
                                        <label class="form-check-label fw-bold" for="guest_checkout">
                                            Continue as Guest
                                        </label>
                                        <p class="small text-muted">Quick checkout without creating an account</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="checkout_type" id="login_checkout" value="login">
                                        <label class="form-check-label fw-bold" for="login_checkout">
                                            Login to Your Account
                                        </label>
                                        <p class="small text-muted">Access saved addresses and order history</p>
                                    </div>
                                </div>
                            </div>
                            <div id="login-form" style="display: none;" class="mt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="login-email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="login-email" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="login-password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="login-password" required>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary-custom mt-2" onclick="loginUser()">Login</button>
                            </div>
                        </div>
                    </div>
                    @endguest
                    
                    <!-- Shipping Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-truck me-2"></i>Shipping Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Street Address *</label>
                                <input type="text" class="form-control" id="address" name="address" placeholder="1234 Main St" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City *</label>
                                    <input type="text" class="form-control" id="city" name="city" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="state" class="form-label">Region *</label>
                                    <select class="form-select" id="state" name="state" required>
                                        <option value="">Select Region</option>
                                        <option value="NCR">NCR - National Capital Region</option>
                                        <option value="CAR">CAR - Cordillera Administrative Region</option>
                                        <option value="Region I">Region I - Ilocos Region</option>
                                        <option value="Region II">Region II - Cagayan Valley</option>
                                        <option value="Region III">Region III - Central Luzon</option>
                                        <option value="Region IV-A">Region IV-A - CALABARZON</option>
                                        <option value="Region IV-B">Region IV-B - MIMAROPA</option>
                                        <option value="Region V">Region V - Bicol Region</option>
                                        <option value="Region VI">Region VI - Western Visayas</option>
                                        <option value="Region VII">Region VII - Central Visayas</option>
                                        <option value="Region VIII">Region VIII - Eastern Visayas</option>
                                        <option value="Region IX">Region IX - Zamboanga Peninsula</option>
                                        <option value="Region X">Region X - Northern Mindanao</option>
                                        <option value="Region XI">Region XI - Davao Region</option>
                                        <option value="Region XII">Region XII - SOCCSKSARGEN</option>
                                        <option value="Region XIII">Region XIII - Caraga</option>
                                        <option value="BARMM">BARMM - Bangsamoro Autonomous Region</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="zip" class="form-label">ZIP Code *</label>
                                    <input type="text" class="form-control" id="zip" name="zip" required>
                                </div>
                            </div>
                            
                            <!-- Billing Address Option -->
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="same_billing" checked>
                                <label class="form-check-label" for="same_billing">
                                    Billing address is the same as shipping address
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Method -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-box me-2"></i>Shipping Method
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="shipping-options">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="shipping_method" id="standard" value="standard" checked>
                                    <label class="form-check-label w-100" for="standard">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Standard Shipping</strong>
                                                <p class="small text-muted mb-0">5-7 business days</p>
                                            </div>
                                            <span class="fw-bold">Free</span>
                                        </div>
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="shipping_method" id="express" value="express">
                                    <label class="form-check-label w-100" for="express">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Express Shipping</strong>
                                                <p class="small text-muted mb-0">2-3 business days</p>
                                            </div>
                                            <span class="fw-bold">₱300.00</span>
                                        </div>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipping_method" id="overnight" value="overnight">
                                    <label class="form-check-label w-100" for="overnight">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>Overnight Shipping</strong>
                                                <p class="small text-muted mb-0">1 business day</p>
                                            </div>
                                            <span class="fw-bold">₱500.00</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-credit-card me-2"></i>Payment Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="payment_method" value="cod">
                            <div class="alert alert-light border mb-0">
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-cash-coin me-2 fs-5"></i>
                                    <div>
                                        <strong>Cash on Delivery (COD)</strong>
                                        <p class="text-muted mb-0 small">Payment is collected when your order arrives at your address.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Notes -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Order Notes (Optional)</h6>
                            <textarea class="form-control" id="order_notes" name="order_notes" rows="3" placeholder="Any special instructions for your order..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 2rem;">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <!-- Order Items -->
                            <div id="checkout-items" class="mb-3">
                                <!-- Items will be loaded here -->
                            </div>
                            
                            <hr>
                            
                            <!-- Pricing -->
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="checkout-subtotal">₱0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span id="checkout-shipping">Free</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span id="checkout-tax">₱0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold fs-5">Total:</span>
                                <span class="fw-bold fs-5" style="color: var(--primary-blue);" id="checkout-total">₱0.00</span>
                            </div>
                            
                            <!-- Place Order Button -->
                            <button type="button" class="btn btn-primary-custom w-100 btn-lg mb-3" onclick="placeOrder()">
                                <i class="bi bi-lock me-2"></i>Place Order
                            </button>
                            
                            <!-- Security Info -->
                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="bi bi-shield-lock me-1"></i>
                                    Your payment information is secure and encrypted
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCheckoutItems();
    setupEventListeners();
    
    // Redirect if cart is empty
    if (Object.keys(cart).length === 0) {
        window.location.href = '{{ route("cart.view") }}';
    }
});

function setupEventListeners() {
    // Checkout type radio buttons
    document.querySelectorAll('input[name="checkout_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const loginForm = document.getElementById('login-form');
            loginForm.style.display = this.value === 'login' ? 'block' : 'none';
        });
    });
    
    // Shipping method change
    document.querySelectorAll('input[name="shipping_method"]').forEach(radio => {
        radio.addEventListener('change', updateShippingCost);
    });
}

function loadCheckoutItems() {
    const cartItems = Object.values(cart);
    const checkoutItems = document.getElementById('checkout-items');
    
    const itemsHTML = cartItems.map(item => {
        const product = item.product;
        const quantity = item.quantity;
        const subtotal = (parseFloat(product.price) * quantity).toFixed(2);
        
        return `
            <div class="d-flex align-items-center mb-3">
                <img src="${getProductPrimaryImage(product)}" 
                     alt="${product.name}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                <div class="flex-grow-1">
                    <h6 class="mb-0">${product.name}</h6>
                    <small class="text-muted">Qty: ${quantity} × ${formatPeso(product.price)}</small>
                    ${product.size ? `<br><small class="text-muted">Size: ${product.size}</small>` : ''}
                </div>
                <span class="fw-bold">${formatPeso(subtotal)}</span>
            </div>
        `;
    }).join('');
    
    checkoutItems.innerHTML = itemsHTML;
    updateCheckoutTotal();
}

function updateShippingCost() {
    const selectedShipping = document.querySelector('input[name="shipping_method"]:checked');
    updateCheckoutTotal();
}

function updateCheckostTotal() {
    const cartItems = Object.values(cart);
    const subtotal = cartItems.reduce((total, item) => {
        return total + (parseFloat(item.product.price) * item.quantity);
    }, 0);
    
    // Get shipping cost
    const selectedShipping = document.querySelector('input[name="shipping_method"]:checked');
    let shippingCost = 0;
    if (selectedShipping) {
        switch (selectedShipping.value) {
            case 'express':
                shippingCost = 300;
                break;
            case 'overnight':
                shippingCost = 500;
                break;
            default:
                shippingCost = subtotal >= 1000 ? 0 : 300;
        }
    }
    
    const taxRate = 0.08;
    const tax = subtotal * taxRate;
    const total = subtotal + shippingCost + tax;
    
    document.getElementById('checkout-subtotal').textContent = formatPeso(subtotal);
    document.getElementById('checkout-shipping').textContent = shippingCost === 0 ? 'Free' : formatPeso(shippingCost);
    document.getElementById('checkout-tax').textContent = formatPeso(tax);
    document.getElementById('checkout-total').textContent = formatPeso(total);
}

// Fix the function name typo
function updateCheckoutTotal() {
    updateCheckostTotal();
}

function placeOrder() {
    if (!validateForm()) {
        return;
    }

    const placeOrderBtn = document.querySelector('button[onclick="placeOrder()"]');
    const originalText = placeOrderBtn ? placeOrderBtn.innerHTML : '';
    const shippingAddress = [
        document.getElementById('address').value,
        document.getElementById('city').value,
        document.getElementById('state').value,
        document.getElementById('zip').value
    ].filter(Boolean).join(', ');

    const cartItems = Object.values(cart).map(item => ({
        product_id: item.product?.id,
        quantity: Number(item.quantity || 1)
    })).filter(item => item.product_id);

    if (cartItems.length === 0) {
        showToast('Your cart is empty', 'warning');
        return;
    }

    // Show loading state
    if (placeOrderBtn) {
        placeOrderBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        placeOrderBtn.disabled = true;
    }

    fetch('/checkout', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: JSON.stringify({
            shipping_address: shippingAddress,
            method: 'COD',
            cart_items: cartItems
        })
    })
    .then(async response => {
        if (!response.ok) {
            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Unable to place order');
            }

            throw new Error('Checkout request failed. Please log in again and retry.');
        }
        return response.json();
    })
    .then(data => {
        showToast('Order placed successfully! 🎉', 'success');
        cart = {};
        localStorage.removeItem('cart');
        updateCartCount();

        const redirectUrl = data.redirect_url || '{{ route("orders.index") }}';
        const orderId = data.order?.id || '';
        const suffix = orderId ? (redirectUrl.includes('?') ? `&placed=${orderId}` : `?placed=${orderId}`) : '';
        window.location.href = `${redirectUrl}${suffix}`;
    })
    .catch(error => {
        console.error('Checkout error:', error);
        showToast(error.message || 'Unable to place order right now.', 'error');

        if (placeOrderBtn) {
            placeOrderBtn.innerHTML = originalText;
            placeOrderBtn.disabled = false;
        }
    });
}

function validateForm() {
    const requiredFields = [
        'first_name', 'last_name', 'email', 'phone', 
        'address', 'city', 'state', 'zip'
    ];
    
    let isValid = true;
    
    requiredFields.forEach(field => {
        const element = document.getElementById(field);
        if (!element.value.trim()) {
            element.classList.add('is-invalid');
            isValid = false;
        } else {
            element.classList.remove('is-invalid');
        }
    });
    
    if (!isValid) {
        showToast('Please fill in all required fields', 'error');
    }
    
    return isValid;
}

function loginUser() {
    // Mock login functionality
    showToast('Login functionality will be implemented with authentication system', 'info');
}

function formatPeso(value) {
    const amount = Number(value || 0);
    return `₱${amount.toFixed(2)}`;
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

function getProductPrimaryImage(product) {
    const firstImage = Array.isArray(product.images) && product.images.length > 0
        ? product.images.find(Boolean)
        : null;

    return resolveProductImageUrl(firstImage);
}
</script>

<style>
.checkout-step {
    text-align: center;
    flex: 1;
    position: relative;
}

.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #e0e0e0;
    color: #666;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 5px;
    font-weight: bold;
}

.checkout-step.active .step-circle {
    background-color: var(--primary-blue);
    color: white;
}

.checkout-step.completed .step-circle {
    background-color: var(--primary-blue);
    color: white;
}

.checkout-step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 20px;
    right: -50%;
    width: 100%;
    height: 2px;
    background-color: #e0e0e0;
    z-index: -1;
}

.checkout-step.completed:not(:last-child)::after {
    background-color: var(--primary-blue);
}

.form-check-label {
    cursor: pointer;
}

.shipping-options .form-check {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    transition: all 0.3s ease;
}

.shipping-options .form-check:hover {
    border-color: var(--primary-blue);
    background-color: rgba(139, 154, 71, 0.05);
}

.shipping-options input[type="radio"]:checked + label {
    font-weight: bold;
}

.is-invalid {
    border-color: #dc3545 !important;
}

@media (max-width: 767.98px) {
    .sticky-top {
        position: relative;
        top: auto;
    }
    
    .checkout-step small {
        display: none;
    }
}
</style>
@endpush
