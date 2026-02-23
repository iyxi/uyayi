<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AdminController;

Route::get('/', [CustomerController::class, 'index'])->name('home');

// Auth routes (use Laravel Breeze/Jetstream in production)

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
