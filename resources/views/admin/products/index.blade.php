@extends('layouts.app')

@section('title', 'Products Management - Uyayi Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-box"></i> Products Management</h1>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="bi bi-plus-lg"></i> Add Product
                </button>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Products Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">All Products ({{ $products->total() }})</h5>
            </div>
            <div class="col-auto">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search products..." id="productSearch">
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
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
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
                                        @if($product->description)
                                            <br><small class="text-muted">{{ Str::limit($product->description, 60) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><code>{{ $product->sku }}</code></td>
                            <td><strong>${{ number_format($product->price, 2) }}</strong></td>
                            <td>
                                @php $stock = $product->inventory->stock ?? 0 @endphp
                                <span class="badge bg-{{ $stock > 10 ? 'success' : ($stock > 0 ? 'warning' : 'danger') }}">
                                    {{ $stock }} units
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $product->visible ? 'success' : 'secondary' }}">
                                    {{ $product->visible ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="editProduct({{ $product->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-info" onclick="restockProduct({{ $product->id }}, '{{ $product->name }}')">
                                        <i class="bi bi-arrow-up-circle"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteProduct({{ $product->id }}, '{{ $product->name }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
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
                <i class="bi bi-box text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">No Products Found</h4>
                <p class="text-muted">Start by adding your first product to the store.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="bi bi-plus-lg"></i> Add First Product
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.products.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="sku" id="sku" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" name="price" id="price" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Initial Stock <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="stock" id="stock" min="0" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="visible" id="visible" value="1" checked>
                                <label class="form-check-label" for="visible">
                                    Make product visible in store
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-generate SKU from product name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const sku = name.toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0, 10);
    document.getElementById('sku').value = sku;
});

function editProduct(id) {
    alert('Edit functionality coming soon! Product ID: ' + id);
}

function restockProduct(id, name) {
    const quantity = prompt('How many units to add to "' + name + '" inventory?');
    if (quantity && quantity > 0) {
        // You can implement AJAX restock here
        alert('Restock functionality coming soon! Adding ' + quantity + ' units to product ID: ' + id);
    }
}

function deleteProduct(id, name) {
    if (confirm('Are you sure you want to delete "' + name + '"? This action cannot be undone.')) {
        // You can implement delete functionality here
        alert('Delete functionality coming soon! Product ID: ' + id);
    }
}
</script>
@endsection