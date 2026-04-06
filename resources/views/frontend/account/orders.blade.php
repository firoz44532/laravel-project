@extends('frontend.layout')

@section('title', 'My Orders')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
        <p class="text-gray-600 mt-2">View and track your order history</p>
    </div>

    <!-- Orders List -->
    @forelse($orders as $order)
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <!-- Order Header -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 pb-4 border-b">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            Order #{{ $order->id }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            Placed on {{ $order->created_at->format('M j, Y') }}
                        </p>
                    </div>
                    <div class="mt-2 sm:mt-0">
                        @if($order->status === 'pending')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending
                            </span>
                        @elseif($order->status === 'processing')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                Processing
                            </span>
                        @elseif($order->status === 'shipped')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                Shipped
                            </span>
                        @elseif($order->status === 'delivered')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Delivered
                            </span>
                        @elseif($order->status === 'cancelled')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Cancelled
                            </span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ ucfirst($order->status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Order Items -->
                <div class="space-y-4 mb-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center space-x-4">
                            @if($item->product->primaryImage)
                                <img src="{{ $item->product->primaryImage->image_url }}" 
                                     alt="{{ $item->product_name }}" 
                                     class="w-16 h-16 object-cover rounded">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">
                                    {{ $item->product_name }}
                                </h4>
                                <p class="text-sm text-gray-600">
                                    Quantity: {{ $item->quantity }} × ৳{{ number_format($item->price, 2) }}
                                </p>
                            </div>
                            <div class="text-sm font-medium text-gray-900">
                                ৳{{ number_format($item->quantity * $item->price, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Total -->
                <div class="flex justify-between items-center pt-4 border-t">
                    <div class="text-sm text-gray-600">
                        Total Amount:
                    </div>
                    <div class="text-lg font-semibold text-gray-900">
                        ৳{{ number_format($order->total_amount, 2) }}
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 mt-6">
                    <a href="{{ route('account.orders.show', $order) }}" 
                       class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 rounded-md">
                        View Details
                    </a>
                    @if($order->status === 'shipped')
                        <a href="{{ route('tracking.index') }}" 
                           class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium text-white bg-primary hover:bg-orange-600 rounded-md">
                            Track Order
                        </a>
                    @endif
                    @if(in_array($order->status, ['pending', 'processing']))
                        <button class="inline-flex items-center justify-center px-4 py-2 border border-red-300 text-sm font-medium text-red-700 bg-white hover:bg-red-50 rounded-md">
                            Cancel Order
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow text-center py-12">
            <i class="fas fa-shopping-bag text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No orders yet</h3>
            <p class="text-gray-600 mb-6">You haven't placed any orders yet. Start shopping to see your orders here.</p>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium text-white bg-primary hover:bg-orange-600 rounded-md">
                Start Shopping
            </a>
        </div>
    @endforelse

    <!-- Pagination -->
    @if($orders->hasPages())
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
