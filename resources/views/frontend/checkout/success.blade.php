@extends('frontend.layout')

@section('title', 'Order Successful')
@section('header', 'Order Confirmation')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto">
        <!-- Success Message -->
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <div class="mb-6">
                <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Successful!</h1>
            <p class="text-gray-600 mb-6">Thank you for your order. We've received your order and will begin processing it shortly.</p>
            
            <!-- Order Details -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6 text-left">
                <h2 class="text-lg font-semibold mb-4">Order Details</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Order Number</p>
                        <p class="font-semibold">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Order Status</p>
                        <p class="font-semibold text-green-600">{{ ucfirst($order->status) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Amount</p>
                        <p class="font-semibold">৳{{ number_format($order->total_amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Payment Method</p>
                        <p class="font-semibold">{{ ucfirst($order->payment->method) }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('account.orders.show', ['order_id' => $order->id]) }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                    View Order Details
                </a>
                <a href="{{ route('home') }}" 
                   class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition duration-200">
                    Continue Shopping
                </a>
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow-lg p-8 mt-6">
            <h2 class="text-xl font-semibold mb-4">Order Items</h2>
            <div class="space-y-4">
                @foreach($order->items as $item)
                <div class="flex items-center justify-between border-b pb-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-gray-400"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold">{{ $item->product_name }}</h3>
                            <p class="text-sm text-gray-500">SKU: {{ $item->product_sku }}</p>
                            <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold">৳{{ number_format($item->total, 2) }}</p>
                        <p class="text-sm text-gray-500">৳{{ number_format($item->price, 2) }} each</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
// Voice notification for order success
document.addEventListener('DOMContentLoaded', function() {
    if (window.VoiceNotifications) {
        // Announce order success
        setTimeout(() => {
            window.VoiceNotifications.orderConfirmation('{{ $order->order_number }}');
        }, 1000);
        
        // Announce payment success if payment is completed
        @if($order->payment->status === 'completed')
        setTimeout(() => {
            window.VoiceNotifications.paymentSuccess('{{ number_format($order->total_amount, 2) }}');
        }, 3000);
        @endif
    }
});
</script>
@endsection
