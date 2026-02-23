@extends('layouts.app')

@section('title', 'Orders - Uyayi Store')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-cart"></i> Orders</h1>
        </div>
    </div>
</div>

@if($orders->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <code>{{ $order->order_number }}</code>
                            </td>
                            <td>
                                @if($order->user)
                                    <div>
                                        <strong>{{ $order->user->name }}</strong>
                                        <br><small class="text-muted">{{ $order->user->email }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">Unknown Customer</span>
                                @endif
                            </td>
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
                            <td>
                                <span class="fw-bold text-success">₱{{ number_format($order->total, 2) }}</span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $order->created_at->format('M d, Y') }}</small>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($orders->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
    @endif
@else
    <div class="text-center py-5">
        <div class="mb-4">
            <i class="bi bi-cart text-muted" style="font-size: 4rem;"></i>
        </div>
        <h4>No orders found</h4>
        <p class="text-muted">Orders will appear here once customers start placing them.</p>
    </div>
@endif
@endsection