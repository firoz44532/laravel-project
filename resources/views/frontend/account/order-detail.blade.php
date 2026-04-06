@extends('frontend.layout')

@section('title', 'Order Details #' . $order->id)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('home') }}" class="text-gray-500 hover:text-primary">Home</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ route('account.orders') }}" class="text-gray-500 hover:text-primary">My Orders</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-gray-900">Order #{{ $order->id }}</span></li>
        </ol>
    </nav>

    <!-- Order Header -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Order #{{ $order->id }}</h1>
                    <p class="text-gray-600 mt-1">
                        Placed on {{ $order->created_at->format('M j, Y \a\t g:i A') }}
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    @if($order->status === 'pending')
                        <span class="px-4 py-2 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Pending
                        </span>
                    @elseif($order->status === 'processing')
                        <span class="px-4 py-2 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                            Processing
                        </span>
                    @elseif($order->status === 'shipped')
                        <span class="px-4 py-2 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                            Shipped
                        </span>
                    @elseif($order->status === 'delivered')
                        <span class="px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                            Delivered
                        </span>
                    @elseif($order->status === 'cancelled')
                        <span class="px-4 py-2 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                            Cancelled
                        </span>
                    @else
                        <span class="px-4 py-2 text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                            {{ ucfirst($order->status) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Items -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h2>
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            <div class="flex items-center space-x-4 pb-4 border-b last:border-b-0 last:pb-0">
                                @if($item->product->primaryImage)
                                    <img src="{{ $item->product->primaryImage->image_url }}" 
                                         alt="{{ $item->product_name }}" 
                                         class="w-20 h-20 object-cover rounded">
                                @else
                                    <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h4 class="text-base font-medium text-gray-900">
                                        {{ $item->product_name }}
                                    </h4>
                                    <p class="text-sm text-gray-600">
                                        {{ $item->quantity }} × ৳{{ number_format($item->price, 2) }}
                                    </p>
                                    @if($item->product)
                                        <a href="{{ route('products.show', $item->product->slug) }}" 
                                           class="text-sm text-primary hover:text-orange-600">
                                            View Product
                                        </a>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <div class="text-base font-medium text-gray-900">
                                        ৳{{ number_format($item->quantity * $item->price, 2) }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="text-gray-900">৳{{ number_format($order->subtotal ?? $order->total_amount, 2) }}</span>
                        </div>
                        
                        @if($order->shipping_cost)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Shipping</span>
                            <span class="text-gray-900">৳{{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        @endif
                        
                        @if($order->tax_amount)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax</span>
                            <span class="text-gray-900">৳{{ number_format($order->tax_amount, 2) }}</span>
                        </div>
                        @endif
                        
                        @if($order->discount_amount)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Discount</span>
                            <span class="text-green-600">-৳{{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                        @endif
                        
                        <div class="pt-3 border-t">
                            <div class="flex justify-between">
                                <span class="text-base font-medium text-gray-900">Total</span>
                                <span class="text-base font-semibold text-gray-900">৳{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    @if($order->payment)
                    <div class="mt-6 pt-6 border-t">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Payment Method</h3>
                        <div class="text-sm text-gray-600">
                            {{ ucfirst($order->payment->method ?? 'Cash on Delivery') }}
                        </div>
                        @if($order->payment->status)
                        <div class="text-sm mt-1">
                            @if($order->payment->status === 'paid')
                                <span class="text-green-600">Paid</span>
                            @else
                                <span class="text-yellow-600">Pending</span>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Shipping Address -->
                    @if($order->shippingAddress)
                    <div class="mt-6 pt-6 border-t">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Shipping Address</h3>
                        <div class="text-sm text-gray-600">
                            <p>{{ $order->shippingAddress->name }}</p>
                            <p>{{ $order->shippingAddress->address }}</p>
                            <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->postal_code }}</p>
                            <p>{{ $order->shippingAddress->phone }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 space-y-3">
                @if($order->status === 'shipped')
                    <a href="{{ route('tracking.index') }}" 
                       class="w-full flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium text-white bg-primary hover:bg-orange-600 rounded-md">
                        Track Order
                    </a>
                @endif
                
                @if(in_array($order->status, ['pending', 'processing']))
                    <button class="w-full flex items-center justify-center px-4 py-2 border border-red-300 text-sm font-medium text-red-700 bg-white hover:bg-red-50 rounded-md">
                        Cancel Order
                    </button>
                @endif
                
                <a href="{{ route('account.orders') }}" 
                   class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md">
                    Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
