@extends('layouts.admin')

@section('title', 'Orders - Uyayi Admin')

@section('breadcrumb')
<a href="{{ route('admin.dashboard') }}">Dashboard</a>
<span class="separator">&gt;</span>
<span class="current">Orders</span>
@endsection

@section('content')
<h1 class="page-title">Orders</h1>

<!-- Orders Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">All Orders ({{ $orders->total() }})</h5>
            </div>
            <div class="col-auto">
                <div class="btn-group">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-funnel"></i> Filter by Status
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?status=all">All Orders</a></li>
                        <li><a class="dropdown-item" href="?status=pending">Pending</a></li>
                        <li><a class="dropdown-item" href="?status=processing">Processing</a></li>
                        <li><a class="dropdown-item" href="?status=completed">Completed</a></li>
                        <li><a class="dropdown-item" href="?status=cancelled">Cancelled</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <strong>#{{ $order->id }}</strong>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $order->user->name ?? 'Guest Customer' }}</strong>
                                    @if($order->user)
                                        <br><small class="text-muted">{{ $order->user->email }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $order->items->count() }} item{{ $order->items->count() !== 1 ? 's' : '' }}
                                </span>
                                @if($order->items->count() > 0)
                                    <div class="small text-muted mt-1">
                                        @foreach($order->items->take(2) as $item)
                                            {{ $item->product->name ?? 'Deleted Product' }}{{ !$loop->last ? ', ' : '' }}
                                        @endforeach
                                        @if($order->items->count() > 2)
                                            <br>and {{ $order->items->count() - 2 }} more...
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>${{ number_format($order->total_amount, 2) }}</strong>
                            </td>
                            <td>
                                <select class="form-select form-select-sm status-select" 
                                        data-order-id="{{ $order->id }}"
                                        style="width: auto;">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </td>
                            <td>
                                <div>
                                    {{ $order->created_at->format('M j, Y') }}
                                    <br><small class="text-muted">{{ $order->created_at->format('g:i A') }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="viewOrderDetails({{ $order->id }})">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-secondary" onclick="printOrder({{ $order->id }})">
                                        <i class="bi bi-printer"></i>
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
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">No Orders Found</h4>
                <p class="text-muted">No orders have been placed yet.</p>
                <a href="{{ route('homepage') }}" class="btn btn-primary" target="_blank">
                    <i class="bi bi-shop"></i> Visit Store
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Order Details Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="orderDetailsContent">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Handle status changes
document.addEventListener('DOMContentLoaded', function() {
    const statusSelects = document.querySelectorAll('.status-select');
    
    statusSelects.forEach(select => {
        select.addEventListener('change', function() {
            const orderId = this.dataset.orderId;
            const newStatus = this.value;
            
            if (confirm('Are you sure you want to change this order status to "' + newStatus + '"?')) {
                updateOrderStatus(orderId, newStatus);
            } else {
                // Revert to original value if cancelled
                this.selectedIndex = Array.from(this.options).findIndex(option => 
                    option.hasAttribute('selected')
                );
            }
        });
    });
});

function updateOrderStatus(orderId, status) {
    // You can implement AJAX status update here
    fetch(`/admin/orders/${orderId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        // Show success message
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show';
        alert.innerHTML = `
            <i class="bi bi-check-circle"></i> Order #${orderId} status updated to ${status}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.container').insertBefore(alert, document.querySelector('.container').firstChild);
    })
    .catch(error => {
        alert('Error updating order status. Please try again.');
        console.error('Error:', error);
    });
}

function viewOrderDetails(orderId) {
    // You can implement order details view here
    const modalContent = document.getElementById('orderDetailsContent');
    modalContent.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading order details...</p>
        </div>
    `;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    modal.show();
    
    // Simulate loading (replace with actual order details fetch)
    setTimeout(() => {
        modalContent.innerHTML = `
            <div class="text-center">
                <h5>Order #${orderId}</h5>
                <p class="text-muted">Order details functionality coming soon!</p>
            </div>
        `;
    }, 1000);
}

function printOrder(orderId) {
    alert('Print functionality coming soon! Order ID: ' + orderId);
}
</script>
@endsection