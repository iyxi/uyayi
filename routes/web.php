<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminController;

// Customer-facing ecommerce routes
Route::get('/', [CustomerController::class, 'homepage'])->name('homepage');
Route::get('/shop', [CustomerController::class, 'shop'])->name('shop');
Route::get('/product/{product}', [CustomerController::class, 'product'])->name('product.show');
Route::get('/cart-view', [CustomerController::class, 'cartView'])->name('cart.view');
Route::get('/checkout-page', [CustomerController::class, 'checkoutPage'])->name('checkout.page');

// API routes for cart functionality
Route::get('/api/products', [CustomerController::class, 'index'])->name('api.products');

// Simple Auth routes (for demo purposes - use Laravel Breeze/Jetstream in production)
Route::get('/login', function() {
    return redirect()->route('homepage')->with('info', 'Please install Laravel Breeze or Jetstream for authentication');
})->name('login');

Route::get('/register', function() {
    return redirect()->route('homepage')->with('info', 'Please install Laravel Breeze or Jetstream for authentication');  
})->name('register');

Route::post('/logout', function() {
    return redirect()->route('homepage');
})->name('logout');

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
    Route::resource('products', AdminController::class)->only(['index','store','update','destroy']);
    Route::post('inventory/restock', [AdminController::class, 'restock'])->name('inventory.restock');
    Route::get('orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::patch('orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');
});
