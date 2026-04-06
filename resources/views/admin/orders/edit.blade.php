@extends('admin.layout')

@section('title', 'Edit Order')
@section('header', 'Edit Order #' . $order->order_number)

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h1 class="text-xl font-semibold">Edit Order #{{ $order->order_number }}</h1>
            <div class="flex items-center space-x-4 text-sm">
                <span class="px-3 py-1 rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                    {{ ucfirst($order->status) }}
                </span>
                <span class="text-gray-600">
                    Placed on {{ $order->created_at->format('M j, Y H:i') }}
                </span>
            </div>
        </div>
        
        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Order Status -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Order Status</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Current Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Status <span class="text-red-500">*</span>
                        </label>
                        <select id="payment_status" name="payment_status" required
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                            <option value="pending" {{ $order->payment->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $order->payment->status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ $order->payment->status === 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="cancelled" {{ $order->payment->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="completed" {{ $order->payment->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                        @error('payment_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Customer Information -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Customer Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Customer Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="customer_name" name="customer_name" required
                               value="{{ $order->user->name }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary"
                               readonly>
                    </div>
                    
                    <div>
                        <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="customer_email" name="customer_email" required
                               value="{{ $order->user->email }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary"
                               readonly>
                    </div>
                    
                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="customer_phone" name="customer_phone" required
                               value="{{ $order->shippingAddress->phone }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                    </div>
                    </div>
                </div>
            </div>
            
            <!-- Shipping Address -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Shipping Address</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="shipping_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Shipping Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="shipping_name" name="shipping_name" required
                               value="{{ $order->shippingAddress->first_name }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                    </div>
                    
                    <div>
                        <label for="shipping_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Shipping Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="shipping_email" name="shipping_email" required
                               value="{{ $order->shippingAddress->email }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                    </div>
                    
                    <div>
                        <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Shipping Phone <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="shipping_phone" name="shipping_phone" required
                               value="{{ $order->shippingAddress->phone }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                    </div>
                </div>
            </div>
            
            <!-- Billing Address -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Billing Address</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="billing_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Billing Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="billing_name" name="billing_name" required
                               value="{{ $order->billingAddress->first_name }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                    </div>
                    
                    <div>
                        <label for="billing_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Billing Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="billing_email" name="billing_email" required
                               value="{{ $order->billingAddress->email }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                    </div>
                    
                    <div>
                        <label for="billing_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Billing Phone <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="billing_phone" name="billing_phone" required
                               value="{{ $order->billingAddress->phone }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Order Items</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="border rounded-lg p-4">
                            <div class="flex items-start space-x-4">
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
                                    <h4 class="font-medium text-gray-900">{{ $item->product_name }}</h4>
                                    <p class="text-sm text-gray-600">SKU: {{ $item->product_sku }}</p>
                                    <div class="flex items-center space-x-4 mt-2">
                                        <span class="text-sm text-gray-600">Qty: {{ $item->quantity }}</span>
                                        <span class="text-lg font-bold text-primary">৳{{ number_format($item->price * $item->quantity, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Order Summary</h2>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">৳{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-medium">৳{{ number_format($order->shipping_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax</span>
                        <span class="font-medium">৳{{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span class="text-primary">৳{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.orders.index') }}" 
                   class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                </a>
                <button type="submit" 
                        class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                    <i class="fas fa-save mr-2"></i>Update Order
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add real-time status updates
    const statusSelect = document.getElementById('status');
    const paymentStatusSelect = document.getElementById('payment_status');
    
    statusSelect.addEventListener('change', function() {
        updatePaymentStatus();
    });
    
    paymentStatusSelect.addEventListener('change', function() {
        updateOrderStatus();
    });
});

function updatePaymentStatus() {
    const status = document.getElementById('status').value;
    const paymentStatus = document.getElementById('payment_status').value;
    
    // Update payment status based on order status
    if (status === 'paid') {
        paymentStatus.value = 'paid';
    } else if (status === 'cancelled') {
        paymentStatus.value = 'cancelled';
    } else if (status === 'refunded') {
        paymentStatus.value = 'completed';
    } else if (status === 'delivered') {
        paymentStatus.value = 'completed';
    } else if (status === 'shipped') {
        paymentStatus.value = 'shipped';
    } else {
        paymentStatus.value = 'pending';
    }
}

function updateOrderStatus() {
    const status = document.getElementById('status').value;
    const paymentStatus = document.getElementById('payment_status').value;
    
    // Update order status based on payment status
    if (paymentStatus === 'paid') {
        status.value = 'paid';
    } else if (paymentStatus === 'cancelled') {
        status.value = 'cancelled';
    } else if (paymentStatus === 'refunded') {
        status.value = 'refunded';
    } else if (paymentStatus === 'completed') {
        status.value = 'delivered';
    } else if (paymentStatus === 'shipped') {
        status.value = 'shipped';
    } else {
        status.value = 'pending';
    }
}
</script>
@endsection
