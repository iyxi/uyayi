@extends('layouts.admin')

@section('title', 'Reports - Uyayi Admin')

@section('breadcrumb')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<span class="separator">&gt;</span>
<span class="current">Reports</span>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Reports</h1>
</div>

<!-- Overview Stats -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Sales</p>
                        <h3 class="mb-0">${{ number_format($stats['total_sales'], 2) }}</h3>
                    </div>
                    <div class="stat-icon bg-success bg-opacity-10 text-success">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Orders</p>
                        <h3 class="mb-0">{{ $stats['total_orders'] }}</h3>
                    </div>
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Customers</p>
                        <h3 class="mb-0">{{ $stats['total_customers'] }}</h3>
                    </div>
                    <div class="stat-icon bg-info bg-opacity-10 text-info">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Products</p>
                        <h3 class="mb-0">{{ $stats['total_products'] }}</h3>
                    </div>
                    <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Sales by Month -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Sales by Month</h5>
            </div>
            <div class="card-body p-0">
                @if($salesByMonth->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Month</th>
                                    <th>Orders</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesByMonth as $sale)
                                <tr>
                                    <td>{{ date('F Y', mktime(0, 0, 0, $sale->month, 1, $sale->year)) }}</td>
                                    <td>{{ $sale->count }}</td>
                                    <td><strong>${{ number_format($sale->total, 2) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-graph-up display-4 text-muted"></i>
                        <p class="mt-3 text-muted">No sales data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0">Top Products</h5>
            </div>
            <div class="card-body p-0">
                @if($topProducts->count() > 0)
                    <ul class="list-group list-group-flush">
                        @foreach($topProducts as $index => $product)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-secondary me-2">{{ $index + 1 }}</span>
                                {{ $product->name }}
                            </div>
                            <span class="badge bg-primary">{{ $product->order_items_count }} sold</span>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-box-seam display-4 text-muted"></i>
                        <p class="mt-3 text-muted">No product data</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}
</style>
@endsection
