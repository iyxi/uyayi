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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
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
