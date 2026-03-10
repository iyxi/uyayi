@extends('layouts.admin')

@section('title', 'Products - Uyayi Admin')

@section('breadcrumb')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<span class="separator">&gt;</span>
<span class="current">Products</span>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Products</h1>
    <div class="btn-group">
        @if(isset($trashedCount) && $trashedCount > 0)
        <a href="{{ route('admin.products.trashed') }}" class="btn btn-outline-warning">
            <i class="bi bi-trash"></i> Trashed ({{ $trashedCount }})
        </a>
        @endif
        <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-file-earmark-excel"></i> Import Excel
        </button>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
            <i class="bi bi-plus-lg"></i> Add Product
        </button>
    </div>
</div>

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
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="product-image me-3">
                                        @if($product->images && count($product->images) > 0)
                                            <img src="{{ asset('storage/' . $product->images[0]) }}" 
                                                 class="rounded" 
                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                 alt="{{ $product->name }}">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
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
                            <td>
                                @if($product->category)
                                    <span class="badge bg-info text-dark">{{ $product->category->name }}</span>
                                @else
                                    <span class="text-muted">No Category</span>
                                @endif
                            </td>
                            <td><strong>₱{{ number_format($product->price, 2) }}</strong></td>
                            <td>
                                <span class="badge bg-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                    {{ $product->stock }} units
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $product->visible ? 'success' : 'secondary' }}">
                                    {{ $product->visible ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td>
                                @if($product->images && count($product->images) > 0)
                                    <button class="btn btn-sm btn-outline-secondary" onclick="viewImages({{ $product->id }}, '{{ $product->name }}')">
                                        <i class="bi bi-images"></i> {{ count($product->images) }}
                                    </button>
                                @else
                                    <span class="text-muted">No images</span>
                                @endif
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
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
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
                                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" name="price" id="price" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-control" name="category_id" id="category_id">
                                    <option value="">Select Category (Optional)</option>
                                    @if(isset($categories))
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Initial Stock <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="stock" id="stock" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="images" class="form-label">Product Images</label>
                                <input type="file" class="form-control" name="images[]" id="images" multiple accept="image/*">
                                <small class="text-muted">You can select multiple images. Max 2MB each.</small>
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

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editProductForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="edit_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_sku" class="form-label">SKU</label>
                                <input type="text" class="form-control" id="edit_sku" readonly disabled>
                                <small class="text-muted">SKU cannot be changed</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_price" class="form-label">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" name="price" id="edit_price" step="0.01" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_stock" class="form-label">Current Stock</label>
                                <input type="number" class="form-control" name="stock" id="edit_stock" min="0">
                                <small class="text-muted">Use Restock button to add more inventory</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_category_id" class="form-label">Category</label>
                                <select class="form-control" name="category_id" id="edit_category_id">
                                    <option value="">Select Category (Optional)</option>
                                    @if(isset($categories))
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="form-label">Current Images</label>
                                <div id="edit_current_images" class="d-flex flex-wrap gap-2 mb-2">
                                    <!-- Images will be loaded here via JS -->
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="edit_images" class="form-label">Add More Images</label>
                                <input type="file" class="form-control" name="images[]" id="edit_images" multiple accept="image/*">
                                <small class="text-muted">You can select multiple images. Max 2MB each.</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="edit_description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="visible" id="edit_visible" value="1">
                                <label class="form-check-label" for="edit_visible">
                                    Make product visible in store
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Restock Modal -->
<div class="modal fade" id="restockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="restockForm" method="POST" action="{{ route('admin.inventory.restock') }}">
                @csrf
                <input type="hidden" name="product_id" id="restock_product_id">
                <div class="modal-header">
                    <h5 class="modal-title">Restock Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Product</label>
                        <p class="fw-bold" id="restock_product_name"></p>
                    </div>
                    <div class="mb-3">
                        <label for="restock_quantity" class="form-label">Quantity to Add <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="quantity" id="restock_quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="restock_note" class="form-label">Note (optional)</label>
                        <textarea class="form-control" name="note" id="restock_note" rows="2" placeholder="E.g., Supplier, reason for restock..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-arrow-up-circle"></i> Add Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editProduct(id) {
    // Fetch product data
    fetch(`/admin/products/${id}/json`)
        .then(response => response.json())
        .then(product => {
            // Populate form fields
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_sku').value = product.sku;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_description').value = product.description || '';
            document.getElementById('edit_visible').checked = product.visible == 1;
            document.getElementById('edit_stock').value = product.stock || 0;
            document.getElementById('edit_category_id').value = product.category_id || '';
            
            // Set form action
            document.getElementById('editProductForm').action = `/admin/products/${id}`;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading product details');
        });
}

// Handle edit form submission
document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const productId = this.action.split('/').pop();
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
        
        // Show success message and reload
        alert('Product updated successfully!');
        window.location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating product');
    });
});

function restockProduct(id, name) {
    document.getElementById('restock_product_id').value = id;
    document.getElementById('restock_product_name').textContent = name;
    document.getElementById('restock_quantity').value = '';
    document.getElementById('restock_note').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('restockModal'));
    modal.show();
}

function deleteProduct(id, name) {
    if (confirm('Are you sure you want to delete "' + name + '"? This action cannot be undone.')) {
        fetch(`/admin/products/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                alert('Product deleted successfully!');
                window.location.reload();
            } else {
                alert('Error deleting product');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting product');
        });
    }
}
</script>
@endsection