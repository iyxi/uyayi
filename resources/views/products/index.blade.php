@extends('layouts.app')

@section('title', 'Products - Uyayi Store')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-box"></i> Products</h1>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus"></i> Add New Product
            </a>
        </div>
    </div>
</div>

@if($products->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>SKU</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>
                                <code>{{ $product->sku }}</code>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $product->name }}</strong>
                                    @if($product->description)
                                        <br><small class="text-muted">{{ Str::limit($product->description, 50) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-success">₱{{ number_format($product->price, 2) }}</span>
                            </td>
                            <td>
                                @if($product->visible)
                                    <span class="badge bg-success">Visible</span>
                                @else
                                    <span class="badge bg-secondary">Hidden</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $product->created_at->format('M d, Y') }}</small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('products.show', $product) }}" class="btn btn-outline-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
    @endif
@else
    <div class="text-center py-5">
        <div class="mb-4">
            <i class="bi bi-box text-muted" style="font-size: 4rem;"></i>
        </div>
        <h4>No products found</h4>
        <p class="text-muted">Start by adding your first product to the inventory.</p>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Add Your First Product
        </a>
    </div>
@endif
@endsection