@php
    $orderNumber = $order->order_number ?? ('ORD-' . $order->id);
    $orderTotal = $order->total ?? $order->total_amount ?? optional($order->payment)->amount ?? 0;
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $orderNumber }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1, h2, h3 { margin: 0; }
        .header { margin-bottom: 16px; }
        .meta, .customer { margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
        .text-right { text-align: right; }
        .total-row td { font-weight: bold; }
        .small { color: #666; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ config('app.name') }} Receipt</h2>
        <p class="small">Generated on {{ now()->format('M d, Y h:i A') }}</p>
    </div>

    <div class="meta">
        <p><strong>Order Number:</strong> {{ $orderNumber }}</p>
        <p><strong>Order Date:</strong> {{ optional($order->created_at)->format('M d, Y h:i A') }}</p>
        <p><strong>Status:</strong> {{ ucfirst((string) $order->status) }}</p>
    </div>

    <div class="customer">
        <h3>Customer Details</h3>
        <p><strong>Name:</strong> {{ optional($order->user)->name ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ optional($order->user)->email ?? 'N/A' }}</p>
        <p><strong>Phone:</strong> {{ optional($order->user)->phone ?? 'N/A' }}</p>
        <p><strong>Shipping Address:</strong> {{ $order->shipping_address ?? (optional($order->user)->address ?? 'N/A') }}</p>
    </div>

    <h3>Order Items</h3>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($order->items as $item)
                <tr>
                    <td>{{ optional($item->product)->name ?? 'Deleted Product' }}</td>
                    <td class="text-right">{{ (int) $item->quantity }}</td>
                    <td class="text-right">Php {{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="text-right">Php {{ number_format((float) $item->subtotal, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No items found.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="3" class="text-right">Total</td>
                <td class="text-right">Php {{ number_format((float) $orderTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <p class="small" style="margin-top: 18px;">
        Payment Method: {{ strtoupper((string) (optional($order->payment)->method ?? 'cod')) }} |
        Payment Status: {{ ucfirst((string) (optional($order->payment)->status ?? 'pending')) }}
    </p>
</body>
</html>
