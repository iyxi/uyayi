@extends('layouts.admin')

@section('title', 'Restock History - Uyayi Admin')

@section('breadcrumb')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<span class="separator">&gt;</span>
<a href="{{ route('admin.inventory') }}">Inventory</a>
<span class="separator">&gt;</span>
<span class="current">Restock History</span>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="page-title mb-0">Restock History</h1>
    <a href="{{ route('admin.inventory') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Inventory
    </a>
</div>

<!-- Restock History Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0">Recent Restocks ({{ $restocks->total() }})</h5>
    </div>
    <div class="card-body p-0">
        @if($restocks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Quantity Added</th>
                            <th>Note</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($restocks as $restock)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($restock->restock_date)->format('M d, Y h:i A') }}</td>
                            <td><strong>{{ $restock->product_name }}</strong></td>
                            <td><code>{{ $restock->sku }}</code></td>
                            <td>
                                <span class="badge bg-success">+{{ $restock->added_quantity }}</span>
                            </td>
                            <td>{{ $restock->note ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="card-footer bg-white">
                {{ $restocks->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-clock-history display-4 text-muted"></i>
                <p class="mt-3 text-muted">No restock history found</p>
            </div>
        @endif
    </div>
</div>
@endsection
