@extends('layouts.admin')

@section('title', 'Dashboard - Uyayi Admin')

@section('breadcrumb')
<span class="current">Dashboard</span>
<!-- Sales Charts -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="data-card">
            <div class="data-card-header">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-bar-chart-line me-2"></i>Sales by Month</h5>
            </div>
            <div class="p-3">
                <canvas id="salesByMonthChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="data-card">
            <div class="data-card-header">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-pie-chart me-2"></i>Top Products</h5>
            </div>
            <div class="p-3">
                <canvas id="topProductsChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Example data, replace with real data from backend
const salesByMonthData = @json($stats['sales_by_month'] ?? ['labels'=>[], 'data'=>[]]);
const topProductsData = @json($stats['top_products'] ?? ['labels'=>[], 'data'=>[]]);

const salesByMonthChart = new Chart(document.getElementById('salesByMonthChart'), {
    type: 'line',
    data: {
        labels: salesByMonthData.labels,
        datasets: [{
            label: 'Sales',
            data: salesByMonthData.data,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16,185,129,0.1)',
            fill: true
        }]
    },
    options: { responsive: true }
});

const topProductsChart = new Chart(document.getElementById('topProductsChart'), {
    type: 'pie',
    data: {
        labels: topProductsData.labels,
        datasets: [{
            label: 'Top Products',
            data: topProductsData.data,
            backgroundColor: ['#10b981', '#f59e0b', '#3b82f6', '#ef4444', '#6366f1', '#f472b6']
        }]
    },
    options: { responsive: true }
});
</script>
@endpush

@endsection

@section('content')
<h1 class="page-title">Dashboard</h1>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <!-- Products Summary -->
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--warm-beige); color: var(--primary-blue);">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="stat-value" style="color: var(--primary-blue);">{{ $stats['products'] }}</div>
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
            <div class="stat-icon" style="background: var(--warm-beige); color: var(--accent-brown);">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-value" style="color: var(--accent-brown);">{{ $stats['users'] }}</div>
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
                                <td><strong>₱{{ number_format($order->total_amount ?? $order->total, 2) }}</strong></td>
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

<!-- Recent Transactions -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="data-card">
            <div class="data-card-header">
                <h5 class="mb-0 fw-semibold"><i class="bi bi-cash-stack me-2"></i>Recent Transactions</h5>
            </div>

            @if(($stats['recent_transactions'] ?? collect())->count() > 0)
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Payment Status</th>
                                <th>Order Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_transactions'] as $txn)
                                <tr>
                                    <td>{{ $txn->created_at ? \Illuminate\Support\Carbon::parse($txn->created_at)->format('M j, Y h:i A') : 'N/A' }}</td>
                                    <td>{{ $txn->order->order_number ?? ('ORD-' . ($txn->order_id ?? 'N/A')) }}</td>
                                    <td>{{ $txn->user->name ?? 'Guest' }}</td>
                                    <td>{{ $txn->method ?? 'COD' }}</td>
                                    <td><strong>₱{{ number_format($txn->amount ?? 0, 2) }}</strong></td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower($txn->status ?? 'pending') }}">
                                            {{ ucfirst($txn->status ?? 'Pending') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower($txn->order->status ?? 'pending') }}">
                                            {{ ucfirst($txn->order->status ?? 'Pending') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-2 mb-0">No transactions yet</p>
                </div>
            @endif
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
                            <span class="badge bg-danger">{{ $product->stock }} left</span>
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
