@extends('layouts.customer')

@section('title', 'My Orders - Uyayi')
@section('description', 'Track your placed orders and payment status.')

@section('content')
<section class="py-4" style="background-color: white; border-bottom: 2px solid var(--soft-tan);">
    <div class="container">
        <h1 class="fw-bold mb-1" style="color: var(--primary-blue-dark);">
            <i class="bi bi-box-seam me-2"></i>My Orders
        </h1>
        <p class="text-muted mb-0">Track your orders and payment progress.</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        @if(request()->filled('placed'))
            <div class="alert alert-success">
                Order placed successfully. Your new order is now in the list below.
            </div>
        @endif

        @if($orders->count() > 0)
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Order #</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Order Status</th>
                                <th>Payment</th>
                                <th>Transaction</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                @php
                                    $itemsSubtotal = (float) $order->items->sum(function ($item) {
                                        if (isset($item->subtotal)) {
                                            return (float) $item->subtotal;
                                        }

                                        return ((float) ($item->unit_price ?? 0)) * ((int) ($item->quantity ?? 0));
                                    });
                                    $paidAmount = (float) ($order->payment->amount ?? $order->total ?? 0);
                                    $shippingAmount = 0.0;
                                    $taxAmount = max(0, $paidAmount - $itemsSubtotal - $shippingAmount);
                                    $detailsId = 'order-details-' . $order->id;
                                @endphp
                                <tr>
                                    <td><strong>{{ $order->order_number ?? ('ORD-' . $order->id) }}</strong></td>
                                    <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                    <td>{{ $order->items->sum('quantity') }}</td>
                                    <td><strong>₱{{ number_format($order->total, 2) }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ strtolower($order->status) === 'completed' ? 'success' : (strtolower($order->status) === 'cancelled' ? 'danger' : 'warning') }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="small">
                                            <div>{{ $order->payment->method ?? 'COD' }}</div>
                                            <div class="text-muted">{{ $order->payment->status ?? 'Pending' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <button
                                            class="btn btn-sm btn-outline-primary"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#{{ $detailsId }}"
                                            aria-expanded="false"
                                            aria-controls="{{ $detailsId }}"
                                        >
                                            View Details
                                        </button>
                                    </td>
                                </tr>

                                <tr class="bg-light">
                                    <td colspan="7" class="p-0 border-0">
                                        <div class="collapse" id="{{ $detailsId }}">
                                            <div class="p-3 border-top">
                                                <div class="row g-3">
                                                    <div class="col-lg-8">
                                                        <h6 class="fw-bold mb-3">Transaction Items</h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm align-middle mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th>Product</th>
                                                                        <th class="text-center">Qty</th>
                                                                        <th class="text-end">Unit Price</th>
                                                                        <th class="text-end">Subtotal</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse($order->items as $item)
                                                                        <tr>
                                                                            <td>
                                                                                <div class="fw-semibold">{{ $item->product->name ?? 'Deleted Product' }}</div>
                                                                                @if($item->product && $item->product->sku)
                                                                                    <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                                                                @endif
                                                                            </td>
                                                                            <td class="text-center">{{ (int) $item->quantity }}</td>
                                                                            <td class="text-end">₱{{ number_format((float) $item->unit_price, 2) }}</td>
                                                                            <td class="text-end">₱{{ number_format((float) $item->subtotal, 2) }}</td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr>
                                                                            <td colspan="4" class="text-center text-muted py-3">No items found for this order.</td>
                                                                        </tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>

                                                        <div class="mt-3">
                                                            <h6 class="fw-bold mb-2">Delivery Details</h6>
                                                            <div class="small text-muted">{{ $order->shipping_address ?: (auth()->user()->address ?: 'No shipping address provided.') }}</div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-4">
                                                        <div class="card border-0 bg-white shadow-sm">
                                                            <div class="card-header bg-white">
                                                                <h6 class="mb-0 fw-bold">Transaction Summary</h6>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="d-flex justify-content-between mb-2">
                                                                    <span>Subtotal:</span>
                                                                    <span>₱{{ number_format($itemsSubtotal, 2) }}</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-2">
                                                                    <span>Shipping:</span>
                                                                    <span>{{ $shippingAmount > 0 ? '₱' . number_format($shippingAmount, 2) : 'Free' }}</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-2">
                                                                    <span>Tax:</span>
                                                                    <span>₱{{ number_format($taxAmount, 2) }}</span>
                                                                </div>
                                                                <hr>
                                                                <div class="d-flex justify-content-between fw-bold fs-6 mb-3">
                                                                    <span>Total:</span>
                                                                    <span class="text-primary">₱{{ number_format($paidAmount, 2) }}</span>
                                                                </div>

                                                                <div class="small text-muted">
                                                                    <div><strong>Payment Method:</strong> {{ strtoupper((string) ($order->payment->method ?? 'COD')) }}</div>
                                                                    <div><strong>Payment Status:</strong> {{ ucfirst((string) ($order->payment->status ?? 'pending')) }}</div>
                                                                    <div><strong>Order Status:</strong> {{ ucfirst((string) $order->status) }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inboxes display-4 text-muted"></i>
                <h4 class="mt-3">No orders yet</h4>
                <p class="text-muted">Once you place an order, it will appear here.</p>
                <a href="{{ route('shop') }}" class="btn btn-primary-custom">Start Shopping</a>
            </div>
        @endif
    </div>
</section>
@endsection
