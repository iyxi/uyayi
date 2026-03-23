@php
    $orderNumber = $order->order_number ?? ('ORD-' . $order->id);
    $orderTotal = $order->total ?? $order->total_amount ?? optional($order->payment)->amount ?? 0;
@endphp

<h2>Transaction completed</h2>
<p>Hello {{ optional($order->user)->name ?? 'Customer' }},</p>
<p>Your transaction has been completed successfully.</p>
<p><strong>Order Number:</strong> {{ $orderNumber }}</p>
<p><strong>Status:</strong> {{ ucfirst((string) $order->status) }}</p>
<p><strong>Total:</strong> Php {{ number_format((float) $orderTotal, 2) }}</p>
<p>We attached your receipt in PDF format for your records.</p>
<p>Thank you for shopping with {{ config('app.name') }}.</p>
