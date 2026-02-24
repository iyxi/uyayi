@extends('layouts.customer')

@section('title', 'Checkout - Uyayi')
@section('description', 'Complete your order securely at Uyayi.')

@section('content')
<!-- Page Header -->
<section class="py-4" style="background-color: white; border-bottom: 2px solid var(--soft-yellow);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="fw-bold mb-0" style="color: var(--primary-green);">
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
                                    <label for="state" class="form-label">State *</label>
                                    <select class="form-select" id="state" name="state" required>
                                        <option value="">Select State</option>
                                        <option value="AL">Alabama</option>
                                        <option value="CA">California</option>
                                        <option value="FL">Florida</option>
                                        <option value="NY">New York</option>
                                        <option value="TX">Texas</option>
                                        <!-- Add more states as needed -->
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
                                            <span class="fw-bold">$12.99</span>
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
                                            <span class="fw-bold">$24.99</span>
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
                            <div class="payment-methods mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="credit_card" checked>
                                    <label class="form-check-label" for="credit_card">
                                        <i class="bi bi-credit-card me-2"></i>Credit/Debit Card
                                    </label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="paypal">
                                    <label class="form-check-label" for="paypal">
                                        <i class="bi bi-paypal me-2"></i>PayPal
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod">
                                    <label class="form-check-label" for="cod">
                                        <i class="bi bi-cash me-2"></i>Cash on Delivery
                                    </label>
                                </div>
                            </div>
                            
                            <div id="card-details">
                                <div class="mb-3">
                                    <label for="card_number" class="form-label">Card Number *</label>
                                    <input type="text" class="form-control" id="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="expiry_date" class="form-label">Expiry Date *</label>
                                        <input type="text" class="form-control" id="expiry_date" placeholder="MM/YY" maxlength="5">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="cvv" class="form-label">CVV *</label>
                                        <input type="text" class="form-control" id="cvv" placeholder="123" maxlength="4">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="card_name" class="form-label">Name on Card *</label>
                                    <input type="text" class="form-control" id="card_name">
                                </div>
                            </div>
                            
                            <div id="paypal-info" style="display: none;">
                                <p class="text-muted">You will be redirected to PayPal to complete your payment securely.</p>
                            </div>
                            
                            <div id="cod-info" style="display: none;">
                                <p class="text-muted">Pay with cash when your order is delivered. A small cash handling fee may apply.</p>
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
                                <span id="checkout-subtotal">$0.00</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span id="checkout-shipping">Free</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax:</span>
                                <span id="checkout-tax">$0.00</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="fw-bold fs-5">Total:</span>
                                <span class="fw-bold fs-5" style="color: var(--primary-green);" id="checkout-total">$0.00</span>
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
    
    // Payment method change
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('card-details').style.display = this.value === 'credit_card' ? 'block' : 'none';
            document.getElementById('paypal-info').style.display = this.value === 'paypal' ? 'block' : 'none';
            document.getElementById('cod-info').style.display = this.value === 'cod' ? 'block' : 'none';
        });
    });
    
    // Card number formatting
    document.getElementById('card_number').addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        value = value.replace(/(.{4})/g, '$1 ').trim();
        this.value = value;
    });
    
    // Expiry date formatting
    document.getElementById('expiry_date').addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        this.value = value;
    });
    
    // CVV numeric only
    document.getElementById('cvv').addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
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
                <img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" 
                     alt="${product.name}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                <div class="flex-grow-1">
                    <h6 class="mb-0">${product.name}</h6>
                    <small class="text-muted">Qty: ${quantity} × $${product.price}</small>
                    ${product.size ? `<br><small class="text-muted">Size: ${product.size}</small>` : ''}
                </div>
                <span class="fw-bold">$${subtotal}</span>
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
                shippingCost = 12.99;
                break;
            case 'overnight':
                shippingCost = 24.99;
                break;
            default:
                shippingCost = subtotal >= 50 ? 0 : 9.99;
        }
    }
    
    const taxRate = 0.08;
    const tax = subtotal * taxRate;
    const total = subtotal + shippingCost + tax;
    
    document.getElementById('checkout-subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('checkout-shipping').textContent = shippingCost === 0 ? 'Free' : `$${shippingCost.toFixed(2)}`;
    document.getElementById('checkout-tax').textContent = `$${tax.toFixed(2)}`;
    document.getElementById('checkout-total').textContent = `$${total.toFixed(2)}`;
}

// Fix the function name typo
function updateCheckoutTotal() {
    updateCheckostTotal();
}

function placeOrder() {
    if (!validateForm()) {
        return;
    }
    
    // Show loading state
    const placeOrderBtn = event.target;
    const originalText = placeOrderBtn.innerHTML;
    placeOrderBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    placeOrderBtn.disabled = true;
    
    // Simulate order processing
    setTimeout(() => {
        // For now, just show success and redirect
        showToast('Order placed successfully! 🎉', 'success');
        
        // Clear cart
        cart = {};
        localStorage.removeItem('cart');
        updateCartCount();
        
        // Redirect to success page (for now, redirect to homepage)
        setTimeout(() => {
            window.location.href = '{{ route("homepage") }}';
        }, 2000);
    }, 2000);
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
    
    // Validate payment method specific fields
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    if (paymentMethod === 'credit_card') {
        const cardFields = ['card_number', 'expiry_date', 'cvv', 'card_name'];
        cardFields.forEach(field => {
            const element = document.getElementById(field);
            if (!element.value.trim()) {
                element.classList.add('is-invalid');
                isValid = false;
            } else {
                element.classList.remove('is-invalid');
            }
        });
    }
    
    if (!isValid) {
        showToast('Please fill in all required fields', 'error');
    }
    
    return isValid;
}

function loginUser() {
    // Mock login functionality
    showToast('Login functionality will be implemented with authentication system', 'info');
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
    background-color: var(--primary-green);
    color: white;
}

.checkout-step.completed .step-circle {
    background-color: var(--primary-green);
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
    background-color: var(--primary-green);
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
    border-color: var(--primary-green);
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