@extends('layouts.admin')

@section('title', 'Dashboard - Uyayi Admin')

@section('breadcrumb')
<span class="current">Dashboard</span>
@endsection

@section('content')
<h1 class="page-title">Dashboard</h1>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <!-- Products Summary -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--soft-yellow); color: var(--primary-green);">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="stat-value" style="color: var(--primary-green);">{{ $stats['products'] }}</div>
            <div class="stat-label">Total Products</div>
        </div>
    </div>

    <!-- Orders Summary -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: #d1fae5; color: #10b981;">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="stat-value" style="color: #10b981;">{{ $stats['orders'] }}</div>
            <div class="stat-label">Total Orders</div>
        </div>
    </div>

    <!-- Users Summary -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--warm-beige); color: var(--soft-brown);">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-value" style="color: var(--soft-brown);">{{ $stats['users'] }}</div>
            <div class="stat-label">Registered Users</div>
        </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="stat-value" style="color: #f59e0b;">{{ $stats['low_stock']->count() }}</div>
            <div class="stat-label">Low Stock Items</div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="data-card">
            <div class="data-card-header">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>Recent Orders</h5>
                <a href="{{ route('admin.orders') }}" class="action-btn">View All</a>
            </div>
            @if($stats['recent_orders']->count() > 0)
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_orders'] as $order)
                            <tr>
                                <td>
                                    <div class="customer-info">
                                        <div class="customer-avatar">
                                            {{ strtoupper(substr($order->user->name ?? 'G', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="customer-name">{{ $order->user->name ?? 'Guest' }}</div>
                                            <div class="order-number">Order #{{ $order->order_number ?? $order->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><strong>${{ number_format($order->total_amount ?? $order->total, 2) }}</strong></td>
                                <td>
                                    <span class="status-badge status-{{ strtolower($order->status) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('M j, Y') }}</td>
                                <td>
                                    <button class="action-btn">View Details</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2">No orders yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="data-card">
            <div class="data-card-header">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
            </div>
            <div class="p-3">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.index') }}" class="action-btn text-center d-block py-3">
                        <i class="bi bi-plus-lg me-2"></i>Add New Product
                    </a>
                    <button class="action-btn py-3" data-bs-toggle="modal" data-bs-target="#lowStockModal">
                        <i class="bi bi-exclamation-triangle me-2"></i>View Low Stock ({{ $stats['low_stock']->count() }})
                    </button>
                    <a href="{{ route('admin.orders') }}" class="action-btn text-center d-block py-3">
                        <i class="bi bi-eye me-2"></i>Review Orders
                    </a>
                    <a href="{{ route('homepage') }}" class="action-btn text-center d-block py-3" target="_blank">
                        <i class="bi bi-shop me-2"></i>View Store
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