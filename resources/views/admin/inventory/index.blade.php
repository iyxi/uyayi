@extends('layouts.admin')

@section('title', 'Stock Management - Uyayi Admin')

@section('breadcrumb')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<span class="separator">&gt;</span>
<span class="current">Stock Management</span>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Stock Management</h1>
    <a href="{{ route('admin.inventory.restocks') }}" class="btn btn-outline-primary">
        <i class="bi bi-clock-history"></i> View Restock History
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Stock Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">All Products ({{ $products->total() }})</h5>
            </div>
            <div class="col-auto">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search products..." id="stockSearch">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Current Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        @php $stock = $product->inventory->stock ?? 0 @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="product-image me-3">
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <strong>{{ $product->name }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td><code>{{ $product->sku }}</code></td>
                            <td>
                                <strong class="{{ $stock < 10 ? 'text-danger' : 'text-success' }}">{{ $stock }}</strong> units
                            </td>
                            <td>
                                @if($stock == 0)
                                    <span class="badge bg-danger">Out of Stock</span>
                                @elseif($stock < 10)
                                    <span class="badge bg-warning">Low Stock</span>
                                @else
                                    <span class="badge bg-success">In Stock</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#restockModal" 
                                    onclick="setRestockProduct({{ $product->id }}, '{{ $product->name }}', {{ $stock }})">
                                    <i class="bi bi-plus-lg"></i> Restock
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="card-footer bg-white">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-boxes display-4 text-muted"></i>
                <p class="mt-3 text-muted">No products found</p>
            </div>
        @endif
    </div>
</div>

<!-- Restock Modal -->
<div class="modal fade" id="restockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.inventory.restock') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" id="restockProductId">
                <div class="modal-header">
                    <h5 class="modal-title">Restock Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Product: <strong id="restockProductName"></strong></p>
                    <p>Current Stock: <strong id="restockCurrentStock"></strong> units</p>
                    
                    <div class="mb-3">
                        <label class="form-label">Quantity to Add</label>
                        <input type="number" class="form-control" name="quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note (Optional)</label>
                        <textarea class="form-control" name="note" rows="2" placeholder="e.g., Supplier delivery"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setRestockProduct(id, name, stock) {
    document.getElementById('restockProductId').value = id;
    document.getElementById('restockProductName').textContent = name;
    document.getElementById('restockCurrentStock').textContent = stock;
}
</script>
@endsection
