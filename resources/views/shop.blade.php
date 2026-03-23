@extends('layouts.customer')

@section('title', "Shop - Uyayi Children's Clothing")
@section('description', "Browse our full collection of eco-friendly children's clothing. Find the perfect outfit for your little one.")

@section('content')
<style>
    #products-container { justify-content: center !important; }
    .shop-catalog-grid { row-gap: 2rem; }
    .shop-catalog-card {
        border-radius: 26px;
        background: #fff;
        box-shadow: 0 14px 34px rgba(95, 74, 45, 0.08);
        overflow: hidden;
        height: 100%;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .shop-catalog-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 40px rgba(95, 74, 45, 0.14);
    }
    .shop-catalog-card__media {
        position: relative;
        background: linear-gradient(180deg, #faf7f2 0%, #f3eee8 100%);
        border-radius: 26px;
        margin: 0.85rem;
        min-height: 220px;
        overflow: hidden;
    }
    .shop-catalog-card__media .carousel,
    .shop-catalog-card__media .carousel-inner,
    .shop-catalog-card__media .carousel-item { height: 220px; }
    .shop-catalog-card__image {
        width: 100%;
        height: 220px;
        object-fit: contain;
        padding: 1rem;
    }
    .shop-catalog-badge {
        position: absolute;
        top: 14px;
        left: 14px;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 54px;
        height: 28px;
        padding: 0 12px;
        border-radius: 999px;
        background: #7dc8f7;
        color: #fff;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }
    .shop-catalog-badge--sale { background: #a7d64b; }
    .shop-catalog-badge--out { background: #c7c7c7; }
    .shop-catalog-card__body {
        padding: 0.25rem 1.25rem 1.35rem;
        text-align: center;
    }
    .shop-catalog-card__title {
        font-size: 0.86rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #453624;
        min-height: 2.6em;
        margin-bottom: 0.45rem;
    }
    .shop-catalog-card__price {
        font-size: 1.4rem;
        line-height: 1;
        font-weight: 900;
        color: var(--primary-blue-dark);
        margin-bottom: 0.45rem;
    }
    .shop-catalog-card__stars {
        color: var(--accent-brown);
        font-size: 0.82rem;
        letter-spacing: 0.14em;
        margin-bottom: 0.9rem;
    }
    .shop-catalog-card__meta {
        font-size: 0.78rem;
        color: #8f816f;
        margin-bottom: 0.9rem;
    }
    .shop-catalog-card__actions {
        display: flex;
        gap: 0.7rem;
        align-items: center;
        justify-content: center;
    }
    .shop-catalog-icon-btn {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 2px solid #f0d7bc;
        background: #fff;
        color: #7b6241;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        transition: all 0.2s ease;
    }
    .shop-catalog-icon-btn:hover {
        border-color: var(--primary-blue);
        color: var(--primary-blue-dark);
    }
    .shop-catalog-icon-btn--primary {
        border-color: var(--primary-blue);
        background: var(--primary-blue);
        color: #fff;
    }
    .shop-catalog-icon-btn--primary:hover {
        background: var(--primary-blue-dark);
        border-color: var(--primary-blue-dark);
        color: #fff;
    }
    #category-filter {
        border-width: 2px;
        border-color: var(--primary-blue-dark);
        font-weight: bold;
    }
    #products-container .carousel-control-prev,
    #products-container .carousel-control-next {
        width: 34px;
        height: 34px;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.92);
        border-radius: 50%;
        opacity: 1;
    }
    #products-container .carousel-control-prev { left: 12px; }
    #products-container .carousel-control-next { right: 12px; }
    #products-container .carousel-control-prev-icon,
    #products-container .carousel-control-next-icon {
        filter: invert(37%) sepia(18%) saturate(510%) hue-rotate(349deg) brightness(92%) contrast(88%);
        width: 1rem;
        height: 1rem;
    }
</style>

<section class="py-4" style="background-color: white; border-bottom: 2px solid var(--soft-tan);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
                <h1 class="fw-bold mb-0" style="color: var(--primary-blue-dark);">
                    <i class="bi bi-shop"></i> Infant Essentials
                </h1>
                <p class="text-muted mb-0">Specially selected infant toiletries made for sensitive and delicate skin.</p>
            </div>
        </div>
    </div>
</section>

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
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" id="price-filter">
                    <option value="">All Prices</option>
                    <option value="0-25">&#8369;0 - &#8369;250</option>
                    <option value="25-50">&#8369;250 - &#8369;500</option>
                    <option value="50-75">&#8369;500 - &#8369;750</option>
                    <option value="75+">&#8369;750+</option>
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

<section class="py-5">
    <div class="container">
        <div id="loading-state" class="text-center py-5">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading our beautiful collection...</p>
        </div>

        <div class="row shop-catalog-grid" id="products-container" style="display: none;"></div>

        <div id="no-products-state" class="text-center py-5" style="display: none;">
            <i class="bi bi-search display-1 text-muted"></i>
            <h3 class="mt-3 text-muted">No products found</h3>
            <p class="text-muted">Try adjusting your search or filters to find what you're looking for.</p>
            <button class="btn btn-primary-custom" onclick="clearAllFilters()">
                <i class="bi bi-arrow-counterclockwise"></i> Reset Filters
            </button>
        </div>

        <div class="row mt-5" id="pagination-container">
            <div class="col-12">
                <nav aria-label="Products pagination">
                    <ul class="pagination justify-content-center" id="pagination"></ul>
                </nav>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
let currentPage = 1;
let currentFilters = { search: '', category: '', price: '', sort: 'name' };

document.addEventListener('DOMContentLoaded', function() {
    loadProducts();
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
    currentFilters = { search: '', category: '', price: '', sort: 'name' };
    currentPage = 1;
    document.getElementById('search-input').value = '';
    document.getElementById('category-filter').value = '';
    document.getElementById('price-filter').value = '';
    document.getElementById('sort-filter').value = 'name';
    loadProducts();
}

function loadProducts() {
    document.getElementById('loading-state').style.display = 'block';
    document.getElementById('products-container').style.display = 'none';
    document.getElementById('no-products-state').style.display = 'none';

    const params = new URLSearchParams({ page: currentPage });
    Object.entries(currentFilters).forEach(([key, value]) => {
        if (value !== null && value !== undefined && String(value).trim() !== '') {
            params.set(key, value);
        }
    });

    fetch(`/api/products?${params.toString()}`)
        .then(response => response.json())
        .then(data => displayProducts(data))
        .catch(error => {
            console.error('Error loading products:', error);
            document.getElementById('loading-state').style.display = 'none';
            document.getElementById('no-products-state').style.display = 'block';
        });
}

function renderProductImages(product) {
    const imagePaths = Array.isArray(product.images) ? product.images.filter(Boolean) : [];

    if (imagePaths.length === 0) {
        return `<img src="/img/logo.png" class="shop-catalog-card__image" alt="${product.name}">`;
    }

    const carouselId = `shopProductCarousel${product.id}`;
    const items = imagePaths.map((path, index) => `
        <div class="carousel-item ${index === 0 ? 'active' : ''}">
            <img src="${resolveProductImageUrl(path)}" class="shop-catalog-card__image" alt="${product.name}" onerror="this.onerror=null;this.src='/img/logo.png';">
        </div>
    `).join('');

    const controls = imagePaths.length > 1 ? `
        <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    ` : '';

    return `
        <div id="${carouselId}" class="carousel slide h-100" data-bs-ride="false">
            <div class="carousel-inner h-100">${items}</div>
            ${controls}
        </div>
    `;
}

function resolveProductImageUrl(path) {
    if (!path) return '/img/logo.png';
    const raw = String(path).trim();
    if (/^https?:\/\//i.test(raw)) return raw;
    let clean = raw.replace(/^\/+/, '');
    if (clean.startsWith('public/')) clean = clean.slice(7);
    if (clean.startsWith('storage/') || clean.startsWith('img/')) return `/${clean}`;
    if (clean.includes('/')) return `/storage/${clean}`;
    return `/img/${clean}`;
}

function formatPeso(value) {
    const amount = Number(value || 0);
    return `\u20B1${amount.toFixed(2)}`;
}

function renderRatingSummary(product) {
    const average = Number(product.reviews_avg_rating || 0);
    const count = Number(product.reviews_count || 0);
    const rounded = Math.round(average);
    let stars = '';

    for (let i = 1; i <= 5; i++) {
        stars += i <= rounded ? '★' : '☆';
    }

    return `${stars} <span class="ms-1 small">${average.toFixed(2)} (${count})</span>`;
}

function displayProducts(data) {
    const productsContainer = document.getElementById('products-container');
    const products = data.data || [];
    document.getElementById('loading-state').style.display = 'none';

    if (products.length === 0) {
        document.getElementById('no-products-state').style.display = 'block';
        document.getElementById('products-container').style.display = 'none';
        return;
    }

    document.getElementById('products-container').style.display = 'flex';
    productsContainer.innerHTML = products.map(product => {
        const stock = Number(product.stock || 0);
        const badgeLabel = stock <= 0 ? 'Out of Stock' : (stock <= 5 ? 'Sale!' : 'New!');
        const badgeClass = stock <= 0 ? 'shop-catalog-badge shop-catalog-badge--out' : (stock <= 5 ? 'shop-catalog-badge shop-catalog-badge--sale' : 'shop-catalog-badge');

        return `
            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                <div class="shop-catalog-card">
                    <div class="shop-catalog-card__media">
                        <span class="${badgeClass}">${badgeLabel}</span>
                        ${renderProductImages(product)}
                    </div>
                    <div class="shop-catalog-card__body">
                        <h6 class="shop-catalog-card__title">${product.name}</h6>
                        <div class="shop-catalog-card__price">${formatPeso(product.price)}</div>
                        <div class="shop-catalog-card__stars">★★★★★</div>
                        <div class="shop-catalog-card__meta">${stock > 0 ? `In stock (${stock})` : 'Currently unavailable'}</div>
                        <div class="shop-catalog-card__actions">
                            <button class="shop-catalog-icon-btn" onclick="viewProduct(${product.id})" aria-label="View ${product.name}">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="shop-catalog-icon-btn shop-catalog-icon-btn--primary" onclick="addToCart(${product.id})" aria-label="Add ${product.name} to cart" ${stock === 0 ? 'disabled' : ''}>
                                <i class="bi bi-bag-plus"></i>
                            </button>
                        </div>
                        <p class="small text-muted mt-3 mb-0">${product.description || 'Carefully selected baby essentials from your catalog.'}</p>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    productsContainer.querySelectorAll('.shop-catalog-card__stars').forEach((element, index) => {
        element.innerHTML = renderRatingSummary(products[index] || {});
    });

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
    if (data.current_page > 1) {
        paginationHTML += `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${data.current_page - 1})">Previous</a></li>`;
    }
    for (let i = 1; i <= data.last_page; i++) {
        paginationHTML += i === data.current_page
            ? `<li class="page-item active"><span class="page-link">${i}</span></li>`
            : `<li class="page-item"><a class="page-link" href="#" onclick="changePage(${i})">${i}</a></li>`;
    }
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
    showToast('Wishlist feature coming soon!', 'info');
}
</script>
@endpush
