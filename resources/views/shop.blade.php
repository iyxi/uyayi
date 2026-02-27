@extends('layouts.customer')

@section('title', 'Shop - Uyayi Children\'s Clothing')
@section('description', 'Browse our full collection of eco-friendly children\'s clothing. Find the perfect outfit for your little one.')

@section('content')
<!-- Page Header -->
<section class="py-4" style="background-color: white; border-bottom: 2px solid var(--soft-yellow);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
                <h1 class="fw-bold mb-0" style="color: var(--primary-green);">
                    <i class="bi bi-shop"></i> Infant Essentials
                </h1>
                <p class="text-muted mb-0">Specially selected infant toiletries made for sensitive and delicate skin.</p>
            </div>
        </div>
    </div>
</section>

<!-- Filters and Search -->
<section class="py-4" style="background-color: var(--warm-beige);">
    <div class="container">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search products..." id="search-input">
                    <button class="btn btn-outline-secondary" type="button" id="search-btn">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="category-filter">
                    <option value="">All Categories</option>
                    <option value="dresses">Bath Essentials</option>
                    <option value="casual">Diapering Care</option>
                    <option value="formal">Skin Care</option>
                    <option value="accessories">Health & Hygiene</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="price-filter">
                    <option value="">All Prices</option>
                    <option value="0-25">₱0 - ₱250</option>
                    <option value="25-50">₱250 - ₱500</option>
                    <option value="50-75">₱500 - ₱750</option>
                    <option value="75+">₱750+</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="sort-filter">
                    <option value="name">Sort by Name</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="newest">Newest First</option>
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-secondary w-100" id="clear-filters">
                    <i class="bi bi-x-circle"></i> Clear
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Products Grid -->
<section class="py-5">
    <div class="container">
        <!-- Loading State -->
        <div id="loading-state" class="text-center py-5">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading our beautiful collection...</p>
        </div>
        
        <!-- Products Container -->
        <div class="row" id="products-container" style="display: none;">
            <!-- Products will be loaded here -->
        </div>
        
        <!-- No Products State -->
        <div id="no-products-state" class="text-center py-5" style="display: none;">
            <i class="bi bi-search display-1 text-muted"></i>
            <h3 class="mt-3 text-muted">No products found</h3>
            <p class="text-muted">Try adjusting your search or filters to find what you're looking for.</p>
            <button class="btn btn-primary-custom" onclick="clearAllFilters()">
                <i class="bi bi-arrow-counterclockwise"></i> Reset Filters
            </button>
        </div>
        
        <!-- Pagination -->
        <div class="row mt-5" id="pagination-container">
            <div class="col-12">
                <nav aria-label="Products pagination">
                    <ul class="pagination justify-content-center" id="pagination">
                        <!-- Pagination will be generated here -->
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let currentFilters = {
    search: '',
    category: '',
    price: '',
    sort: 'name'
};

document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
    
    // Event listeners for filters
    document.getElementById('search-btn').addEventListener('click', handleSearch);
    document.getElementById('search-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            handleSearch();
        }
    });
    
    document.getElementById('category-filter').addEventListener('change', function() {
        currentFilters.category = this.value;
        loadProducts();
    });
    
    document.getElementById('price-filter').addEventListener('change', function() {
        currentFilters.price = this.value;
        loadProducts();
    });
    
    document.getElementById('sort-filter').addEventListener('change', function() {
        currentFilters.sort = this.value;
        loadProducts();
    });
    
    document.getElementById('clear-filters').addEventListener('click', clearAllFilters);
});

function handleSearch() {
    currentFilters.search = document.getElementById('search-input').value;
    currentPage = 1;
    loadProducts();
}

function clearAllFilters() {
    currentFilters = {
        search: '',
        category: '',
        price: '',
        sort: 'name'
    };
    currentPage = 1;
    
    // Reset form elements
    document.getElementById('search-input').value = '';
    document.getElementById('category-filter').value = '';
    document.getElementById('price-filter').value = '';
    document.getElementById('sort-filter').value = 'name';
    
    loadProducts();
}

function loadProducts() {
    // Show loading state
    document.getElementById('loading-state').style.display = 'block';
    document.getElementById('products-container').style.display = 'none';
    document.getElementById('no-products-state').style.display = 'none';
    
    // Build query string
    const params = new URLSearchParams({
        page: currentPage,
        ...currentFilters
    });
    
    fetch(`/api/products?${params.toString()}`)
        .then(response => response.json())
        .then(data => {
            displayProducts(data);
        })
        .catch(error => {
            console.error('Error loading products:', error);
            document.getElementById('loading-state').style.display = 'none';
            document.getElementById('no-products-state').style.display = 'block';
        });
}

function displayProducts(data) {
    const productsContainer = document.getElementById('products-container');
    const products = data.data || [];
    
    // Hide loading state
    document.getElementById('loading-state').style.display = 'none';
    
    if (products.length === 0) {
        document.getElementById('no-products-state').style.display = 'block';
        document.getElementById('products-container').style.display = 'none';
        return;
    }
    
    // Show products
    document.getElementById('products-container').style.display = 'flex';
    
    productsContainer.innerHTML = products.map(product => `
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="product-card h-100 shadow-sm">
                <div class="position-relative">
                    <img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80" 
                         class="card-img-top" alt="${product.name}" style="height: 250px; object-fit: cover;">
                    ${product.inventory && product.inventory.stock < 10 ? 
                        '<div class="position-absolute top-0 start-0 m-2"><span class="badge bg-warning">Low Stock</span></div>' : ''
                    }
                    <div class="position-absolute top-0 end-0 m-2">
                        <button class="btn btn-light btn-sm rounded-circle" onclick="toggleWishlist(${product.id})">
                            <i class="bi bi-heart"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <h6 class="card-title fw-bold">${product.name}</h6>
                    <p class="card-text text-muted small flex-grow-1">${product.description || 'Beautiful eco-friendly clothing for children'}</p>
                    
                    <div class="product-meta mb-3">
                        <small class="text-muted">SKU: ${product.sku}</small>
                        ${product.inventory ? 
                            `<small class="text-success d-block">In Stock (${product.inventory.stock})</small>` :
                            '<small class="text-danger d-block">Out of Stock</small>'
                        }
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="price">
                            <span class="fw-bold fs-5" style="color: var(--primary-green);">$${product.price}</span>
                        </div>
                        <div class="product-actions">
                            <button class="btn btn-outline-secondary btn-sm me-2" onclick="viewProduct(${product.id})">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-primary-custom btn-sm" onclick="addToCart(${product.id}, ${JSON.stringify(product)})" ${!product.inventory || product.inventory.stock === 0 ? 'disabled' : ''}>
                                <i class="bi bi-bag-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
    // Update pagination
    updatePagination(data);
}

function updatePagination(data) {
    const pagination = document.getElementById('pagination');
    if (!data.last_page || data.last_page <= 1) {
        document.getElementById('pagination-container').style.display = 'none';
        return;
    }
    
    document.getElementById('pagination-container').style.display = 'block';
    
    let paginationHTML = '';
    
    // Previous button
    if (data.current_page > 1) {
        paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${data.current_page - 1})">Previous</a></li>`;
    }
    
    // Page numbers
    for (let i = 1; i <= data.last_page; i++) {
        if (i === data.current_page) {
            paginationHTML += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${i})">${i}</a></li>`;
        }
    }
    
    // Next button
    if (data.current_page < data.last_page) {
        paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${data.current_page + 1})">Next</a></li>`;
    }
    
    pagination.innerHTML = paginationHTML;
}

function changePage(page) {
    currentPage = page;
    loadProducts();
    window.scrollTo(0, 0);
}

function viewProduct(productId) {
    window.location.href = `/product/${productId}`;
}

function toggleWishlist(productId) {
    // Implement wishlist functionality
    showToast('Wishlist feature coming soon!', 'info');
}
</script>
@endpush