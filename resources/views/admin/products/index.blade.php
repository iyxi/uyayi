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

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle"></i>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                                    <button class="btn btn-sm btn-outline-secondary" onclick='viewImages({{ $product->id }}, @json($product->name))'>
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
                                    <button class="btn btn-outline-info" onclick='restockProduct({{ $product->id }}, @json($product->name))'>
                                        <i class="bi bi-arrow-up-circle"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick='deleteProduct({{ $product->id }}, @json($product->name))'>
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

<!-- Import Products Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Import Products from Worksheet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="import_file" class="form-label">Excel/CSV File</label>
                        <input type="file" class="form-control" id="import_file" name="file" accept=".xlsx,.xls,.csv" required>
                        <small class="text-muted">Accepted: .xlsx, .xls, .csv (max 10MB)</small>
                    </div>

                    <div class="alert alert-light border mb-0">
                        <strong>Expected columns:</strong>
                        <div class="small mt-1">name (required), sku, description, price, stock, visible, category or category_name</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-upload"></i> Import Products
                    </button>
                </div>
            </form>
        </div>
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
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" name="price" id="price" value="{{ old('price') }}" step="0.01" min="0" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock" class="form-label">Initial Stock <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('stock') is-invalid @enderror" name="stock" id="stock" value="{{ old('stock') }}" min="0" required>
                                @error('stock')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="images" class="form-label">Product Images</label>
                                <input type="file" class="form-control @error('images') is-invalid @enderror" name="images[]" id="images" multiple accept="image/*">
                                <small class="text-muted">You can select multiple images. Max 2MB each.</small>
                                @error('images')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                @foreach($errors->get('images.*') as $messages)
                                    @foreach($messages as $message)
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" name="description" id="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="visible" id="visible" value="1" @checked(old('visible', '1') == '1')>
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
                    <div id="edit_form_alert" class="alert alert-danger d-none" role="alert"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="edit_name" required>
                                <div class="invalid-feedback" data-error-for="name"></div>
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
                                    <div class="invalid-feedback" data-error-for="price"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_stock" class="form-label">Current Stock</label>
                                <input type="number" class="form-control" name="stock" id="edit_stock" min="0" required>
                                <small class="text-muted">Use Restock button to add more inventory</small>
                                <div class="invalid-feedback" data-error-for="stock"></div>
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
                                <div class="invalid-feedback" data-error-for="category_id"></div>
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
                                <div class="invalid-feedback" data-error-for="images"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="edit_description" class="form-label">Description</label>
                                <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                                <div class="invalid-feedback" data-error-for="description"></div>
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

<!-- Product Images Modal -->
<div class="modal fade" id="viewImagesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewImagesModalTitle">Product Images</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="view_images_container" class="row g-3"></div>
            </div>
        </div>
    </div>
</div>

<script>
const STORAGE_BASE_URL = @json(asset('storage'));

function buildImageUrl(path) {
    if (!path) return '';
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    return `${STORAGE_BASE_URL}/${path}`;
}

function viewImages(id, name) {
    fetch(`/admin/products/${id}/json`)
        .then(response => response.json())
        .then(product => {
            const container = document.getElementById('view_images_container');
            const title = document.getElementById('viewImagesModalTitle');
            const images = Array.isArray(product.images) ? product.images : [];

            title.textContent = `${name} - ${images.length} image${images.length === 1 ? '' : 's'}`;

            if (images.length === 0) {
                container.innerHTML = '<div class="col-12 text-center text-muted py-4">No images uploaded for this product.</div>';
            } else {
                container.innerHTML = images.map((imagePath, index) => `
                    <div class="col-md-4 col-sm-6">
                        <div class="border rounded p-2 h-100">
                            <a href="${buildImageUrl(imagePath)}" target="_blank" rel="noopener noreferrer">
                                <img src="${buildImageUrl(imagePath)}" alt="${name} image ${index + 1}" class="img-fluid rounded" style="width: 100%; height: 180px; object-fit: cover;">
                            </a>
                        </div>
                    </div>
                `).join('');
            }

            const modal = new bootstrap.Modal(document.getElementById('viewImagesModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading product images');
        });
}

function editProduct(id) {
    clearEditProductValidation();

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

            const currentImagesContainer = document.getElementById('edit_current_images');
            const images = Array.isArray(product.images) ? product.images : [];

            if (images.length === 0) {
                currentImagesContainer.innerHTML = '<span class="text-muted">No images uploaded</span>';
            } else {
                currentImagesContainer.innerHTML = images.map((imagePath, index) => `
                    <div class="position-relative border rounded p-1" style="width: 95px;">
                        <img src="${buildImageUrl(imagePath)}" alt="${product.name} image ${index + 1}" class="rounded" style="width: 85px; height: 85px; object-fit: cover;">
                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 delete-image-btn" title="Delete image" data-image="${encodeURIComponent(imagePath)}" style="padding:0.15rem 0.4rem; border-radius:50%; line-height:1;">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                `).join('');
                // Attach event listeners for delete buttons
                currentImagesContainer.querySelectorAll('.delete-image-btn').forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const imagePath = decodeURIComponent(this.getAttribute('data-image'));
                        if (confirm('Are you sure you want to delete this image?')) {
                            deleteProductImage(id, imagePath, this.closest('.position-relative'));
                        }
                    });
                });
            }
            // Delete product image handler
            function deleteProductImage(productId, imagePath, imageDiv) {
                fetch(`/admin/products/${productId}/delete-image`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ image: imagePath })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the image div from the UI
                        if (imageDiv) imageDiv.remove();
                    } else {
                        alert('Failed to delete image.');
                    }
                })
                .catch(() => alert('Error deleting image.'));
            }
            
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

function clearEditProductValidation() {
    const form = document.getElementById('editProductForm');
    if (!form) return;

    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('[data-error-for]').forEach(el => {
        el.textContent = '';
    });

    const alertBox = document.getElementById('edit_form_alert');
    if (alertBox) {
        alertBox.classList.add('d-none');
        alertBox.textContent = '';
    }
}

function applyEditProductValidationErrors(errors) {
    const fieldToInputId = {
        name: 'edit_name',
        price: 'edit_price',
        stock: 'edit_stock',
        category_id: 'edit_category_id',
        description: 'edit_description',
        images: 'edit_images',
    };

    Object.entries(errors).forEach(([field, messages]) => {
        const normalizedField = field.startsWith('images.') ? 'images' : field;
        const inputId = fieldToInputId[normalizedField];

        if (inputId) {
            const input = document.getElementById(inputId);
            if (input) input.classList.add('is-invalid');

            const errorEl = document.querySelector(`[data-error-for="${normalizedField}"]`);
            if (errorEl) {
                errorEl.textContent = Array.isArray(messages) ? messages[0] : String(messages);
            }
        }
    });
}

// Handle edit form submission
document.getElementById('editProductForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!this.checkValidity()) {
        this.reportValidity();
        return;
    }

    clearEditProductValidation();
    
    const formData = new FormData(this);
    const productId = this.action.split('/').pop();
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(async response => {
        const payload = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw { status: response.status, payload };
        }
        return payload;
    })
    .then(data => {
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('editProductModal')).hide();
        
        // Show success message and reload
        alert('Product updated successfully!');
        window.location.reload();
    })
    .catch(error => {
        if (error.status === 422 && error.payload && error.payload.errors) {
            applyEditProductValidationErrors(error.payload.errors);
            return;
        }

        const alertBox = document.getElementById('edit_form_alert');
        if (alertBox) {
            alertBox.textContent = 'Error updating product. Please try again.';
            alertBox.classList.remove('d-none');
        }

        console.error('Error:', error);
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

@if($errors->any() && old('name'))
document.addEventListener('DOMContentLoaded', function () {
    const addModalElement = document.getElementById('addProductModal');
    if (addModalElement) {
        const addModal = new bootstrap.Modal(addModalElement);
        addModal.show();
    }
});
@endif
</script>
@endsection