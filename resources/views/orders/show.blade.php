@extends('layouts.app')

@section('title', 'Order #' . $order->order_number . ' - Uyayi Store')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-cart"></i> Order #{{ $order->order_number }}</h1>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Order Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Order Number:</td>
                                <td><code>{{ $order->order_number }}</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Customer:</td>
                                <td>
                                    @if($order->user)
                                        {{ $order->user->name }}<br>
                                        <small class="text-muted">{{ $order->user->email }}</small>
                                    @else
                                        <span class="text-muted">Unknown Customer</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Total:</td>
                                <td><span class="fw-bold text-success">₱{{ number_format($order->total, 2) }}</span></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Status:</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'Pending' => 'warning',
                                            'Processing' => 'info',
                                            'Shipped' => 'primary',
                                            'Completed' => 'success',
                                            'Cancelled' => 'danger'
                                        ];
                                        $color = $statusColors[$order->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ $order->status }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Created:</td>
                                <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Updated:</td>
                                <td>{{ $order->updated_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($order->shipping_address)
                    <div class="mt-4">
                        <h6 class="text-muted">Shipping Address</h6>
                        <div class="border rounded p-3 bg-light">
                            {{ $order->shipping_address }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> Order Items</h5>
            </div>
            <div class="card-body p-0">
                @if($order->orderItems && $order->orderItems->count() > 0)
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                <tr>
                                    <td>
                                        @if($item->product)
                                            {{ $item->product->name }}
                                            <br><small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                        @else
                                            <span class="text-muted">Product no longer available</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td>₱{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-4 text-center text-muted">
                        No items found for this order.
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Status Update -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="bi bi-gear"></i> Update Status</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('orders.updateStatus', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-3">
                        <select name="status" class="form-select" required>
                            <option value="Pending" {{ $order->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Processing" {{ $order->status === 'Processing' ? 'selected' : '' }}>Processing</option>
                            <option value="Shipped" {{ $order->status === 'Shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="Completed" {{ $order->status === 'Completed' ? 'selected' : '' }}>Completed</option>
                            <option value="Cancelled" {{ $order->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-save"></i> Update Status
                    </button>
                </form>
            </div>
        </div>

        <!-- Payment Information -->
        @if($order->payment)
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-credit-card"></i> Payment Info</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="fw-bold">Method:</td>
                            <td>{{ $order->payment->method }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Amount:</td>
                            <td>₱{{ number_format($order->payment->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">Status:</td>
                            <td>
                                @php
                                    $paymentColors = [
                                        'Pending' => 'warning',
                                        'Paid' => 'success',
                                        'Failed' => 'danger',
                                        'Refunded' => 'info'
                                    ];
                                    $paymentColor = $paymentColors[$order->payment->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $paymentColor }}">{{ $order->payment->status }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection