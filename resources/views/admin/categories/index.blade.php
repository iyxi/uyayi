@extends('layouts.admin')

@section('title', 'Categories - Uyayi Admin')

@section('breadcrumb')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<span class="separator">&gt;</span>
<span class="current">Categories</span>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Categories</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        <i class="bi bi-plus-lg"></i> Add Category
    </button>
</div>

<!-- Categories Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">All Categories ({{ $categories->total() }})</h5>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($categories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Products Count</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="category-icon me-3">
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="bi bi-tag text-primary"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <strong>{{ $category->name }}</strong>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($category->description)
                                    <span class="text-muted">{{ Str::limit($category->description, 50) }}</span>
                                @else
                                    <span class="text-muted">No description</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $category->products_count }} products</span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $category->created_at->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="editCategory({{ $category->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deleteCategory({{ $category->id }}, '{{ $category->name }}', {{ $category->products_count }})">
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
                {{ $categories->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-tags text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">No Categories Found</h4>
                <p class="text-muted">Start by adding your first product category.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bi bi-plus-lg"></i> Add First Category
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">
                                Make category active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" name="description" id="edit_description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="edit_is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">
                                Make category active
                            </label>
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

<script>
function editCategory(id) {
    // Fetch category data
    const category = @json($categories->items()).find(cat => cat.id === id);
    
    if (category) {
        // Populate form fields
        document.getElementById('edit_name').value = category.name;
        document.getElementById('edit_description').value = category.description || '';
        document.getElementById('edit_is_active').checked = category.is_active == 1;
        
        // Set form action
        document.getElementById('editCategoryForm').action = `/admin/categories/${id}`;
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
        modal.show();
    }
}

// Handle edit form submission
document.getElementById('editCategoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert('Error: ' + data.error);
        } else {
            // Close modal and reload page
            bootstrap.Modal.getInstance(document.getElementById('editCategoryModal')).hide();
            window.location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the category');
    });
});

function deleteCategory(id, name, productsCount) {
    if (productsCount > 0) {
        alert(`Cannot delete category "${name}" because it has ${productsCount} product(s) assigned to it. Please reassign the products first.`);
        return;
    }
    
    if (confirm(`Are you sure you want to delete the category "${name}"?`)) {
        fetch(`/admin/categories/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                window.location.reload();
            } else {
                return response.json().then(data => Promise.reject(data));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (error.error) {
                alert('Error: ' + error.error);
            } else {
                alert('An error occurred while deleting the category');
            }
        });
    }
}
</script>
@endsection