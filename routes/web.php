<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

// Customer-facing ecommerce routes
Route::get('/', [CustomerController::class, 'homepage'])->name('homepage');
Route::get('/shop', [CustomerController::class, 'shop'])->name('shop');
Route::get('/product/{product}', [CustomerController::class, 'product'])->name('product.show');
Route::get('/cart-view', [CustomerController::class, 'cartView'])->name('cart.view');
Route::get('/checkout-page', [CustomerController::class, 'checkoutPage'])->name('checkout.page');

// API routes for cart functionality
Route::get('/api/products', [CustomerController::class, 'index'])->name('api.products');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Customer routes
Route::middleware(['auth'])->group(function(){
    Route::get('/cart', [CustomerController::class, 'cart'])->name('cart');
    Route::post('/cart/add/{product}', [CustomerController::class, 'addToCart'])->name('cart.add');
    Route::post('/checkout', [CustomerController::class, 'checkout'])->name('checkout');
    Route::get('/orders/{order}', [CustomerController::class, 'showOrder'])->name('orders.show');
});

// Admin routes
Route::prefix('admin')->middleware(['auth','can:admin-area'])->group(function(){
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/products', [AdminController::class, 'index'])->name('admin.products.index');
    Route::post('/products', [AdminController::class, 'store'])->name('admin.products.store');
    Route::get('/products/create', function() {
        return redirect()->route('admin.products.index');
    })->name('admin.products.create');
    Route::patch('/products/{product}', [AdminController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [AdminController::class, 'destroy'])->name('admin.products.destroy');
    Route::post('/inventory/restock', [AdminController::class, 'restock'])->name('admin.inventory.restock');
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');
});
