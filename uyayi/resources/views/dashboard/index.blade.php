@extends('layouts.app')

@section('title', 'Dashboard - Uyayi Store')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-speedometer2"></i> Dashboard</h1>
            <span class="text-muted">Welcome to Uyayi Store Management</span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Products Summary Card -->
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-box text-primary" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Products</h5>
                <p class="card-text text-muted">Manage your product inventory</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-right"></i> View Products
                </a>
            </div>
        </div>
    </div>

    <!-- Orders Summary Card -->
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-cart text-success" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Orders</h5>
                <p class="card-text text-muted">Track and manage orders</p>
                <a href="{{ route('orders.index') }}" class="btn btn-success">
                    <i class="bi bi-arrow-right"></i> View Orders
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="col-md-4">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-plus-circle text-info" style="font-size: 3rem;"></i>
                </div>
                <h5 class="card-title">Quick Actions</h5>
                <p class="card-text text-muted">Add new items quickly</p>
                <a href="{{ route('products.create') }}" class="btn btn-info">
                    <i class="bi bi-plus"></i> Add Product
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> System Information</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <strong>Laravel Version</strong><br>
                        <span class="text-muted">{{ app()->version() }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>PHP Version</strong><br>
                        <span class="text-muted">{{ PHP_VERSION }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Environment</strong><br>
                        <span class="text-muted">{{ app()->environment() }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Debug Mode</strong><br>
                        <span class="text-muted">{{ config('app.debug') ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection