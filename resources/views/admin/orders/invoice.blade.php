<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_number }}</title>
    <script>
        window.onload = function() { window.print(); };
    </script>
    <style>
        body { font-family: system-ui, sans-serif; font-size: 14px; color: #111; max-width: 800px; margin: 0 auto; padding: 24px; }
        h1 { font-size: 24px; margin-bottom: 8px; }
        .meta { color: #666; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f9fafb; font-weight: 600; }
        .text-right { text-align: right; }
        .totals { margin-top: 16px; text-align: right; }
        .totals p { margin: 4px 0; }
        .total { font-size: 18px; font-weight: 700; margin-top: 8px; }
        .section { margin-bottom: 24px; }
        .section h3 { font-size: 12px; text-transform: uppercase; color: #6b7280; margin-bottom: 8px; }
        @media print { body { padding: 16px; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 16px;">
        <button onclick="window.print()" style="padding: 8px 16px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer;">Print</button>
        <button onclick="window.close()" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer; margin-left: 8px;">Close</button>
    </div>

    <h1>Invoice</h1>
    <div class="meta">Order #{{ $order->order_number }} &middot; {{ $order->created_at->format('F j, Y H:i') }}</div>

    <div class="section">
        <h3>Bill To</h3>
        <p><strong>{{ $order->user->name ?? 'Customer' }}</strong><br>
        {{ $order->user->email ?? '' }}</p>
        @if($order->billingAddress)
        <p>{{ $order->billingAddress->address_line_1 ?? '' }}<br>
        @if($order->billingAddress->address_line_2){{ $order->billingAddress->address_line_2 }}<br>@endif
        {{ $order->billingAddress->city ?? '' }}, {{ $order->billingAddress->division ?? '' }} {{ $order->billingAddress->postal_code ?? '' }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product->name ?? 'Product #' . $item->product_id }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->price, 2) }} {{ $order->currency }}</td>
                <td class="text-right">{{ number_format($item->total, 2) }} {{ $order->currency }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        @if($order->discount_amount > 0)
        <p>Discount: -{{ number_format($order->discount_amount, 2) }} {{ $order->currency }}</p>
        @endif
        @if($order->shipping_amount > 0)
        <p>Shipping: {{ number_format($order->shipping_amount, 2) }} {{ $order->currency }}</p>
        @endif
        @if($order->tax_amount > 0)
        <p>Tax: {{ number_format($order->tax_amount, 2) }} {{ $order->currency }}</p>
        @endif
        <p class="total">Total: {{ number_format($order->total_amount, 2) }} {{ $order->currency }}</p>
    </div>

    <p style="margin-top: 32px; color: #6b7280; font-size: 12px;">Thank you for your order.</p>
</body>
</html>
