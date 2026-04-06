@extends('admin.layout')

@section('title', 'Courier Integration Details')
@section('header', 'Courier Integration Details')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Courier Integration Details</h1>
                <p class="text-gray-600 mt-1">Order #{{ $integration->order->order_number }} - {{ $integration->courier_name }}</p>
            </div>
            <a href="{{ route('admin.courier-integrations.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2"></i>Back to Integrations
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Integration Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Integration Status</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="flex items-center">
                                {!! $integration->status_badge !!}
                                <span class="ml-3 text-sm text-gray-500">
                                    Created {{ $integration->created_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            @if($integration->status === 'failed')
                                <a href="{{ route('admin.courier-integrations.retry', $integration) }}" 
                                   class="inline-flex items-center px-3 py-2 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 transition-colors">
                                    <i class="fas fa-redo mr-2"></i>Retry
                                </a>
                            @endif
                            @if($integration->status === 'synced')
                                <form method="POST" action="{{ route('admin.courier-integrations.cancel', $integration) }}" 
                                      onsubmit="return confirm('Are you sure you want to cancel this integration?')">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-500 text-white text-sm font-medium rounded-lg hover:bg-red-600 transition-colors">
                                        <i class="fas fa-times mr-2"></i>Cancel
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @if($integration->error_message)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-red-500 mt-0.5 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-semibold text-red-800">Error Message</h4>
                                    <p class="text-sm text-red-700 mt-1">{{ $integration->error_message }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($integration->synced_at)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4">
                            <div class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                                <div>
                                    <h4 class="text-sm font-semibold text-green-800">Successfully Synced</h4>
                                    <p class="text-sm text-green-700 mt-1">Integration completed on {{ $integration->synced_at->format('M j, Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
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
                                    <dd class="text-sm font-medium text-gray-900">{{ $integration->order->order_number }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Total Amount:</dt>
                                    <dd class="text-sm font-medium text-gray-900">৳{{ number_format($integration->order->total_amount, 2) }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Payment Method:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $integration->order->payment->method_display_name ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-500">Order Status:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ ucfirst($integration->order->status) }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-3">Customer Information</h3>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm text-gray-500">Name:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $integration->customer_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Phone:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $integration->customer_phone }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Delivery Address:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $integration->delivery_address }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="mt-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Order Items</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($integration->order->items as $item)
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $item->product->name }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">{{ $item->quantity }}</td>
                                            <td class="px-4 py-2 text-sm text-gray-900">৳{{ number_format($item->price, 2) }}</td>
                                            <td class="px-4 py-2 text-sm font-medium text-gray-900">৳{{ number_format($item->price * $item->quantity, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Response (if available) -->
            @if($integration->api_response)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">API Response</h2>
                    </div>
                    <div class="p-6">
                        <pre class="bg-gray-50 rounded-lg p-4 text-xs overflow-x-auto">{{ json_encode($integration->api_response, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Tracking Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Tracking Information</h2>
                </div>
                <div class="p-6">
                    @if($integration->tracking_number)
                        <div class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tracking Number</dt>
                                <dd class="text-lg font-mono font-semibold text-gray-900">{{ $integration->tracking_number }}</dd>
                            </div>
                            @if($integration->consignment_id)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Consignment ID</dt>
                                    <dd class="text-lg font-mono font-semibold text-gray-900">{{ $integration->consignment_id }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Courier Service</dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ $integration->courier_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Package Weight</dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ $integration->package_weight }} kg</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">COD Amount</dt>
                                <dd class="text-sm font-semibold text-gray-900">৳{{ number_format($integration->cod_amount, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Delivery Charge</dt>
                                <dd class="text-sm font-semibold text-gray-900">৳{{ number_format($integration->delivery_charge, 2) }}</dd>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-gray-500">
                            <i class="fas fa-search text-3xl mb-3"></i>
                            <p>No tracking information available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Live Tracking (if available) -->
            @if($trackingInfo)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-800">Live Tracking</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @if(isset($trackingInfo['status']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Current Status</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $trackingInfo['status'] }}</dd>
                                </div>
                            @endif
                            @if(isset($trackingInfo['current_location']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Current Location</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $trackingInfo['current_location'] }}</dd>
                                </div>
                            @endif
                            @if(isset($trackingInfo['estimated_delivery']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Estimated Delivery</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $trackingInfo['estimated_delivery'] }}</dd>
                                </div>
                            @endif
                            <div class="pt-4 border-t border-gray-200">
                                <button onclick="refreshTracking()" class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-sync-alt mr-2"></i>Refresh Tracking
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Package Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">Package Information</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Description</dt>
                            <dd class="text-sm text-gray-900">{{ $integration->package_description }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Pickup Address</dt>
                            <dd class="text-sm text-gray-900">{{ $integration->pickup_address }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Delivery Address</dt>
                            <dd class="text-sm text-gray-900">{{ $integration->delivery_address }}</dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshTracking() {
    location.reload();
}
</script>
@endsection
