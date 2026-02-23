@extends('layouts.app')

@section('title', $product->name . ' - Uyayi Store')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-box"></i> Product Details</h1>
            <div>
                <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Products
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="text-muted">Product Information</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Name:</td>
                                <td>{{ $product->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">SKU:</td>
                                <td><code>{{ $product->sku }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Price:</td>
                                <td><span class="fw-bold text-success">₱{{ number_format($product->price, 2) }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Status:</td>
                                <td>
                                    @if($product->visible)
                                        <span class="badge bg-success">Visible</span>
                                    @else
                                        <span class="badge bg-secondary">Hidden</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-muted">Timestamps</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Created:</td>
                                <td>{{ $product->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Updated:</td>
                                <td>{{ $product->updated_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($product->description)
                    <div class="mt-4">
                        <h5 class="text-muted">Description</h5>
                        <div class="border rounded p-3 bg-light">
                            {{ $product->description }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-gear"></i> Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Product
                    </a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this product?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i> Delete Product
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Product Stats</h6>
            </div>
            <div class="card-body text-center">
                <div class="row">
                    <div class="col-12">
                        <div class="border rounded p-2 mb-2">
                            <div class="fw-bold">Total Orders</div>
                            <div class="text-muted">Coming Soon</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection