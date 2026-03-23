@php
    $orderNumber = $order->order_number ?? ('ORD-' . $order->id);
    $orderTotal = $order->total ?? $order->total_amount ?? optional($order->payment)->amount ?? 0;
@endphp

<h2>Order status updated</h2>
<p>Hello {{ optional($order->user)->name ?? 'Customer' }},</p>
<p>Your order status has been updated.</p>
<p><strong>Order Number:</strong> {{ $orderNumber }}</p>
<p><strong>New Status:</strong> {{ ucfirst((string) $order->status) }}</p>
<p><strong>Total:</strong> Php {{ number_format((float) $orderTotal, 2) }}</p>
<p>We attached your latest receipt in PDF format for reference.</p>
<p>Thank you for shopping with {{ config('app.name') }}.</p>
