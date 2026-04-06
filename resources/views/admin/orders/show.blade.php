@extends('admin.layout')

@section('title', 'Order ' . $order->order_number)
@section('header', 'Order ' . $order->order_number)

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('admin.orders.index') }}" class="text-gray-600 hover:text-gray-900 inline-flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Orders
        </a>
        <div class="flex gap-2">
            <button onclick="window.open('{{ route('admin.orders.print', $order) }}', '_blank')" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                <i class="fas fa-print mr-2"></i> Print Invoice
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Customer</h3>
            <p class="font-medium text-gray-900">{{ $order->user->name ?? 'N/A' }}</p>
            <p class="text-sm text-gray-600">{{ $order->user->email ?? 'N/A' }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Order Info</h3>
            <p class="text-sm text-gray-600">Status: <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">{{ ucfirst($order->status) }}</span></p>
            <p class="text-sm text-gray-600 mt-1">Date: {{ $order->created_at->format('M j, Y H:i') }}</p>
        </div>
    </div>

    @if($order->shippingAddress || $order->billingAddress)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        @if($order->shippingAddress)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Shipping Address</h3>
            <p class="text-gray-900">{{ $order->shippingAddress->full_address ?? $order->shippingAddress->address_line_1 }}</p>
            @if($order->shippingAddress->address_line_2)<p class="text-gray-600">{{ $order->shippingAddress->address_line_2 }}</p>@endif
            <p class="text-gray-600">{{ $order->shippingAddress->city ?? '' }}, {{ $order->shippingAddress->division ?? '' }} {{ $order->shippingAddress->postal_code ?? '' }}</p>
            @if($order->shippingAddress->phone)<p class="text-gray-600">{{ $order->shippingAddress->phone }}</p>@endif
        </div>
        @endif
        @if($order->billingAddress)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Billing Address</h3>
            <p class="text-gray-900">{{ $order->billingAddress->full_address ?? $order->billingAddress->address_line_1 }}</p>
            @if($order->billingAddress->address_line_2)<p class="text-gray-600">{{ $order->billingAddress->address_line_2 }}</p>@endif
            <p class="text-gray-600">{{ $order->billingAddress->city ?? '' }}, {{ $order->billingAddress->division ?? '' }} {{ $order->billingAddress->postal_code ?? '' }}</p>
        </div>
        @endif
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-semibold text-gray-900">Order Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item->product->name ?? 'Product #' . $item->product_id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($item->price, 2) }} {{ $order->currency }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ number_format($item->total, 2) }} {{ $order->currency }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-right space-y-1">
            @if($order->discount_amount > 0)
            <p class="text-sm text-gray-600">Discount: -{{ number_format($order->discount_amount, 2) }} {{ $order->currency }}</p>
            @endif
            @if($order->shipping_amount > 0)
            <p class="text-sm text-gray-600">Shipping: {{ number_format($order->shipping_amount, 2) }} {{ $order->currency }}</p>
            @endif
            @if($order->tax_amount > 0)
            <p class="text-sm text-gray-600">Tax: {{ number_format($order->tax_amount, 2) }} {{ $order->currency }}</p>
            @endif
            <p class="text-lg font-semibold text-gray-900">Total: {{ number_format($order->total_amount, 2) }} {{ $order->currency }}</p>
        </div>
    </div>

    @if($order->payment)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Payment</h3>
        <p class="text-sm text-gray-600">Method: {{ $order->payment->method ?? 'N/A' }}</p>
        <p class="text-sm text-gray-600">Status: {{ $order->payment->status ?? 'N/A' }}</p>
    </div>
    @endif
</div>
@endsection
