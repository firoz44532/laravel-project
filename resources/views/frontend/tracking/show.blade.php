@extends('frontend.layout')

@if(isset($orders))
    @section('title', 'Order Tracking Results')
@else
    @section('title', 'Order Tracking - ' . $order->order_number)
@endif

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        
        @if(isset($orders))
            <!-- Multiple Orders Results -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b">
                    <h1 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-list text-primary mr-3"></i>
                        Found {{ $orders->count() }} Order(s)
                    </h1>
                    <p class="text-gray-600 mt-2">Orders found for your name and mobile number</p>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($orders as $order)
                            <div class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3 mb-2">
                                            <h3 class="font-semibold text-lg">#{{ $order->order_number }}</h3>
                                            <span class="px-2 py-1 text-xs rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-1">
                                            Placed on {{ $order->created_at->format('M j, Y H:i') }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{ $order->items_count }} items • ৳{{ number_format($order->total_amount, 2) }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}
                                        </p>
                                    </div>
                                    <a href="{{ route('track.show', ['order_number' => $order->order_number]) }}" 
                                       class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition text-sm font-medium">
                                        <i class="fas fa-eye mr-2"></i>View Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6 text-center">
                        <a href="{{ route('track.index') }}" class="text-primary hover:text-orange-600 font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Tracking
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Single Order Details -->
            <!-- Order Header -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold flex items-center">
                                <i class="fas fa-truck text-primary mr-3"></i>
                                Order #{{ $order->order_number }}
                            </h1>
                            <div class="flex items-center space-x-4 mt-2">
                                <span class="px-3 py-1 text-sm rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                    {{ ucfirst($order->status) }}
                                </span>
                                <span class="text-sm text-gray-600">
                                    Placed on {{ $order->created_at->format('M j, Y H:i') }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-primary">
                                ৳{{ number_format($order->total_amount, 2) }}
                            </div>
                            <div class="text-sm text-gray-600">{{ $order->items_count }} items</div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Tracking Timeline -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-6">Order Status</h2>
                
                <div class="space-y-6">
                    @php $trackingHistory = $order->getTrackingHistory(); @endphp
                    @foreach($trackingHistory as $index => $event)
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-{{ $event['color'] }}-100 rounded-full flex items-center justify-center">
                                    <i class="fas {{ $event['icon'] }} text-{{ $event['color'] }}-600"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-semibold">{{ $event['status'] }}</h3>
                                    <span class="text-sm text-gray-500">{{ $event['date'] }}</span>
                                </div>
                                <p class="text-gray-600 mt-1">{{ $event['description'] }}</p>
                            </div>
                        </div>
                        
                        @if($index < count($trackingHistory) - 1)
                            <div class="ml-5 border-l-2 border-gray-300 h-6"></div>
                        @endif
                    @endforeach
                </div>
                
                <!-- Estimated Delivery -->
                <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-clock text-blue-600 mr-3"></i>
                        <div>
                            <h3 class="font-semibold text-blue-900">Estimated Delivery</h3>
                            <p class="text-blue-700">{{ $order->getEstimatedDelivery() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Details -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-6">Order Details</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Shipping Address -->
                    <div>
                        <h3 class="font-semibold mb-3">Shipping Address</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="font-medium">{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</p>
                            <p class="text-gray-600">{{ $order->shippingAddress->address_line_1 }}</p>
                            @if($order->shippingAddress->address_line_2)
                                <p class="text-gray-600">{{ $order->shippingAddress->address_line_2 }}</p>
                            @endif
                            <p class="text-gray-600">{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->division }}</p>
                            <p class="text-gray-600">{{ $order->shippingAddress->country }}</p>
                            <p class="text-gray-600">{{ $order->shippingAddress->phone }}</p>
                        </div>
                    </div>
                    
                    <!-- Billing Address -->
                    <div>
                        <h3 class="font-semibold mb-3">Billing Address</h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="font-medium">{{ $order->billingAddress->first_name }} {{ $order->billingAddress->last_name }}</p>
                            <p class="text-gray-600">{{ $order->billingAddress->address_line_1 }}</p>
                            @if($order->billingAddress->address_line_2)
                                <p class="text-gray-600">{{ $order->billingAddress->address_line_2 }}</p>
                            @endif
                            <p class="text-gray-600">{{ $order->billingAddress->city }}, {{ $order->billingAddress->division }}</p>
                            <p class="text-gray-600">{{ $order->billingAddress->country }}</p>
                            <p class="text-gray-600">{{ $order->billingAddress->phone }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Information -->
                <div class="mt-6">
                    <h3 class="font-semibold mb-3">Payment Information</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Payment Method</p>
                                <p class="font-medium">{{ ucfirst($order->payment->method) }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Transaction ID</p>
                                <p class="font-medium">{{ $order->payment->transaction_id }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Payment Status</p>
                                <p class="font-medium">{{ ucfirst($order->payment->status) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-6">Order Items</h2>
                
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center space-x-4 p-4 border rounded-lg">
                            <div class="flex-shrink-0">
                                @if($item->product->primaryImage)
                                    <img src="{{ $item->product->primaryImage->image_url }}" 
                                         alt="{{ $item->product_name }}" 
                                         class="w-20 h-20 object-cover rounded">
                                @else
                                    <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1">
                                <h4 class="font-medium">{{ $item->product_name }}</h4>
                                <p class="text-sm text-gray-600">SKU: {{ $item->product_sku }}</p>
                                <div class="flex items-center space-x-4 mt-2">
                                    <span class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</span>
                                    <span class="text-sm text-gray-600">Price: ৳{{ number_format($item->price, 2) }}</span>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <p class="font-semibold">৳{{ number_format($item->total, 2) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Order Summary -->
                <div class="mt-6 pt-6 border-t">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span>৳{{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            <span>৳{{ number_format($order->shipping_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tax</span>
                            <span>৳{{ number_format($order->tax_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between font-semibold text-lg">
                            <span>Total</span>
                            <span class="text-primary">৳{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-center space-x-4">
            <a href="{{ route('track.index') }}" 
               class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to Tracking
            </a>
            <button onclick="window.print()" 
                    class="bg-primary text-white px-6 py-3 rounded-lg hover:bg-orange-600 transition">
                <i class="fas fa-print mr-2"></i>Print Tracking
            </button>
        </div>
        @endif
    </div>
</div>
@endsection
