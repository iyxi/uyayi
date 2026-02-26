@extends('layouts.app')

@section('title', 'Admin Dashboard - Uyayi Store')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-speedometer2"></i> Admin Dashboard</h1>
            <div>
                <span class="text-muted">Welcome back, {{ Auth::user()->name }}</span>
                <span class="badge bg-primary ms-2">Administrator</span>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-5">
    <!-- Products Summary -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-box text-primary" style="font-size: 2.5rem;"></i>
                </div>
                <h3 class="fw-bold text-primary">{{ $stats['products'] }}</h3>
                <p class="card-text text-muted mb-3">Total Products</p>
                <a href="{{ route('admin.products.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-arrow-right"></i> Manage
                </a>
            </div>
        </div>
    </div>

    <!-- Orders Summary -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-cart3 text-success" style="font-size: 2.5rem;"></i>
                </div>
                <h3 class="fw-bold text-success">{{ $stats['orders'] }}</h3>
                <p class="card-text text-muted mb-3">Total Orders</p>
                <a href="{{ route('admin.orders') }}" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-arrow-right"></i> View Orders
                </a>
            </div>
        </div>
    </div>

    <!-- Users Summary -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-people text-info" style="font-size: 2.5rem;"></i>
                </div>
                <h3 class="fw-bold text-info">{{ $stats['users'] }}</h3>
                <p class="card-text text-muted mb-3">Registered Users</p>
                <button class="btn btn-outline-info btn-sm" disabled>
                    <i class="bi bi-arrow-right"></i> Manage
                </button>
            </div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 2.5rem;"></i>
                </div>
                <h3 class="fw-bold text-warning">{{ $stats['low_stock']->count() }}</h3>
                <p class="card-text text-muted mb-3">Low Stock Items</p>
                <button class="btn btn-outline-warning btn-sm" onclick="$('#lowStockModal').modal('show')">
                    <i class="bi bi-eye"></i> View
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <!-- Recent Orders -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Orders</h5>
            </div>
            <div class="card-body">
                @if($stats['recent_orders']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stats['recent_orders'] as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ $order->user->name ?? 'Guest' }}</td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('M j, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.orders') }}" class="btn btn-primary">View All Orders</a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-2">No orders yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add New Product
                    </a>
                    <button class="btn btn-outline-secondary" onclick="$('#restockModal').modal('show')">
                        <i class="bi bi-arrow-up-circle"></i> Restock Items
                    </button>
                    <a href="{{ route('admin.orders') }}" class="btn btn-outline-info">
                        <i class="bi bi-eye"></i> Review Orders
                    </a>
                    <a href="{{ route('homepage') }}" class="btn btn-outline-success" target="_blank">
                        <i class="bi bi-shop"></i> View Store
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Modal -->
<div class="modal fade" id="lowStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Low Stock Alert</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($stats['low_stock']->count() > 0)
                    <div class="list-group">
                        @foreach($stats['low_stock'] as $product)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $product->name }}</strong>
                                <br>
                                <small class="text-muted">{{ $product->sku }}</small>
                            </div>
                            <span class="badge bg-danger">{{ $product->inventory->stock ?? 0 }} left</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No low stock items!</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection