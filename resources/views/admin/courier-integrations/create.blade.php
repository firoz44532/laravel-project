@extends('admin.layout')

@section('title', 'Create Courier Integration')
@section('header', 'Create Courier Integration')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Create Courier Integration</h1>
                <p class="text-gray-600 mt-1">Integrate order #{{ $order->order_number }} with courier service</p>
            </div>
            <a href="{{ route('admin.courier-integrations.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Integrations
            </a>
        </div>
    </div>

    <!-- Order Details Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Order Details</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Order Information</h3>
                    <dl class="space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Order Number:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $order->order_number }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Total Amount:</dt>
                            <dd class="text-sm font-medium text-gray-900">৳{{ number_format($order->total_amount, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Payment Method:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $order->payment->method_display_name ?? 'N/A' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Items:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $order->items->count() }} items</dd>
                        </div>
                    </dl>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Shipping Information</h3>
                    <dl class="space-y-2">
                        <div>
                            <dt class="text-sm text-gray-500">Customer Name:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Phone:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $order->shippingAddress->phone }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Address:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $order->shippingAddress->address }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">City:</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $order->shippingAddress->city ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Courier Integration Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Courier Integration</h2>
        </div>
        <form method="POST" action="{{ route('admin.courier-integrations.store') }}" class="p-6">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">
            
            <div class="space-y-6">
                <!-- Courier Selection -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-4">Select Courier Service</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <label class="relative border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-colors">
                            <input type="radio" name="courier_type" value="steadfast" class="sr-only peer" required>
                            <div class="flex items-center">
                                <div class="bg-orange-100 text-orange-600 rounded-lg p-3 mr-4 peer-checked:bg-orange-500 peer-checked:text-white transition-colors">
                                    <i class="fas fa-truck text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Steadfast Courier</h3>
                                    <p class="text-sm text-gray-500">Fast and reliable delivery service</p>
                                </div>
                            </div>
                        </label>
                        <label class="relative border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-colors">
                            <input type="radio" name="courier_type" value="pathao" class="sr-only peer" required>
                            <div class="flex items-center">
                                <div class="bg-green-100 text-green-600 rounded-lg p-3 mr-4 peer-checked:bg-green-500 peer-checked:text-white transition-colors">
                                    <i class="fas fa-bicycle text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Pathao Courier</h3>
                                    <p class="text-sm text-gray-500">Quick delivery with real-time tracking</p>
                                </div>
                            </div>
                        </label>
                        <label class="relative border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-colors">
                            <input type="radio" name="courier_type" value="ecourier" class="sr-only peer" required>
                            <div class="flex items-center">
                                <div class="bg-blue-100 text-blue-600 rounded-lg p-3 mr-4 peer-checked:bg-blue-500 peer-checked:text-white transition-colors">
                                    <i class="fas fa-shipping-fast text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">eCourier</h3>
                                    <p class="text-sm text-gray-500">Professional e-commerce delivery</p>
                                </div>
                            </div>
                        </label>
                        <label class="relative border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-colors">
                            <input type="radio" name="courier_type" value="redx" class="sr-only peer" required>
                            <div class="flex items-center">
                                <div class="bg-red-100 text-red-600 rounded-lg p-3 mr-4 peer-checked:bg-red-500 peer-checked:text-white transition-colors">
                                    <i class="fas fa-box text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">RedX</h3>
                                    <p class="text-sm text-gray-500">E-commerce friendly delivery</p>
                                </div>
                            </div>
                        </label>
                        <label class="relative border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-colors">
                            <input type="radio" name="courier_type" value="paperfly" class="sr-only peer" required>
                            <div class="flex items-center">
                                <div class="bg-purple-100 text-purple-600 rounded-lg p-3 mr-4 peer-checked:bg-purple-500 peer-checked:text-white transition-colors">
                                    <i class="fas fa-paper-plane text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Paperfly</h3>
                                    <p class="text-sm text-gray-500">Paperless delivery service</p>
                                </div>
                            </div>
                        </label>
                        <label class="relative border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-colors">
                            <input type="radio" name="courier_type" value="sundarban" class="sr-only peer" required>
                            <div class="flex items-center">
                                <div class="bg-yellow-100 text-yellow-600 rounded-lg p-3 mr-4 peer-checked:bg-yellow-500 peer-checked:text-white transition-colors">
                                    <i class="fas fa-sun text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Sundarban Courier</h3>
                                    <p class="text-sm text-gray-500">Traditional and trusted service</p>
                                </div>
                            </div>
                        </label>
                        <label class="relative border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-colors">
                            <input type="radio" name="courier_type" value="saparibahan" class="sr-only peer" required>
                            <div class="flex items-center">
                                <div class="bg-indigo-100 text-indigo-600 rounded-lg p-3 mr-4 peer-checked:bg-indigo-500 peer-checked:text-white transition-colors">
                                    <i class="fas fa-bus text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">SA Paribahan</h3>
                                    <p class="text-sm text-gray-500">Large network coverage</p>
                                </div>
                            </div>
                        </label>
                        <label class="relative border-2 border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-colors">
                            <input type="radio" name="courier_type" value="janani" class="sr-only peer" required>
                            <div class="flex items-center">
                                <div class="bg-pink-100 text-pink-600 rounded-lg p-3 mr-4 peer-checked:bg-pink-500 peer-checked:text-white transition-colors">
                                    <i class="fas fa-female text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Janani Express</h3>
                                    <p class="text-sm text-gray-500">Women entrepreneur led</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Package Information -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Package Information</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-gray-500">Estimated Weight:</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    @php
                                        $totalWeight = 0;
                                        foreach($order->items as $item) {
                                            $productWeight = $item->product->weight ?? 0.5;
                                            $totalWeight += $productWeight * $item->quantity;
                                        }
                                        $totalWeight = max($totalWeight, 0.5);
                                    @endphp
                                    {{ number_format($totalWeight, 2) }} kg
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">COD Amount:</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    @if($order->payment && $order->payment->method === 'cod')
                                        ৳{{ number_format($order->total_amount, 2) }}
                                    @else
                                        ৳0.00 (Prepaid)
                                    @endif
                                </dd>
                            </div>
                            <div class="md:col-span-2">
                                <dt class="text-sm text-gray-500">Package Description:</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    @php
                                        $itemNames = $order->items->take(3)->pluck('product.name')->implode(', ');
                                        $itemCount = $order->items->count();
                                        if($itemCount > 3) {
                                            $itemNames .= ' and ' . ($itemCount - 3) . ' more items';
                                        }
                                        $description = "Order #{$order->order_number}: {$itemNames}";
                                    @endphp
                                    {{ $description }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                        <div class="text-sm text-blue-700">
                            <p class="font-semibold mb-1">Important Information:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Once integrated, the order status will be automatically updated to "Processing"</li>
                                <li>Tracking information will be generated and stored in the system</li>
                                <li>Customer will receive tracking details via email notification</li>
                                <li>Make sure all order details are correct before integration</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.courier-integrations.index') }}" 
                       class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 font-medium shadow-lg">
                        <i class="fas fa-shipping-fast mr-2"></i>Integrate with Courier
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Add visual feedback for courier selection
document.querySelectorAll('input[name="courier_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('label').forEach(label => {
            label.classList.remove('border-blue-500', 'bg-blue-50');
        });
        
        if (this.checked) {
            this.closest('label').classList.add('border-blue-500', 'bg-blue-50');
        }
    });
});
</script>
@endsection
