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
    
    // Profile routes
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
});

// Admin routes
Route::prefix('admin')->middleware(['auth','can:admin-area'])->group(function(){
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Products
    Route::get('/products', [AdminController::class, 'index'])->name('admin.products.index');
    Route::get('/products/trashed', [AdminController::class, 'trashed'])->name('admin.products.trashed');
    Route::post('/products/import', [AdminController::class, 'importProducts'])->name('admin.products.import');
    Route::post('/products', [AdminController::class, 'store'])->name('admin.products.store');
    Route::get('/products/create', function() {
        return redirect()->route('admin.products.index');
    })->name('admin.products.create');
    Route::get('/products/{product}/json', [AdminController::class, 'show'])->name('admin.products.show');
    Route::patch('/products/{product}', [AdminController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [AdminController::class, 'destroy'])->name('admin.products.destroy');
    Route::post('/products/{id}/restore', [AdminController::class, 'restore'])->name('admin.products.restore');
    Route::delete('/products/{id}/force', [AdminController::class, 'forceDelete'])->name('admin.products.forceDelete');
    Route::delete('/products/{product}/image/{index}', [AdminController::class, 'deleteImage'])->name('admin.products.deleteImage');
    
    // Categories
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories.index');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::patch('/categories/{category}', [AdminController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [AdminController::class, 'destroyCategory'])->name('admin.categories.destroy');
    
    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('admin.users.show');
    Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('admin.users.status');
    Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('admin.users.role');
    
    // Orders
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.status');
    
    // Customers
    Route::get('/customers', [AdminController::class, 'customers'])->name('admin.customers');
    
    // Inventory
    Route::get('/inventory', [AdminController::class, 'inventory'])->name('admin.inventory');
    Route::get('/inventory/restocks', [AdminController::class, 'restocks'])->name('admin.inventory.restocks');
    Route::post('/inventory/restock', [AdminController::class, 'restock'])->name('admin.inventory.restock');
    
    // Finance
    Route::get('/payments', [AdminController::class, 'payments'])->name('admin.payments');
    Route::get('/expenses', [AdminController::class, 'expenses'])->name('admin.expenses');
    Route::post('/expenses', [AdminController::class, 'storeExpense'])->name('admin.expenses.store');
    Route::delete('/expenses/{id}', [AdminController::class, 'destroyExpense'])->name('admin.expenses.destroy');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    
    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::get('/account', [AdminController::class, 'account'])->name('admin.account');
    Route::put('/account', [AdminController::class, 'updateAccount'])->name('admin.account.update');
});
