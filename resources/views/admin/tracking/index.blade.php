@extends('admin.layout')

@section('title', 'Order Tracking - Admin')
@section('header', 'Order Tracking')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Daraz-style Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-xl shadow-lg p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">
                    <i class="fas fa-shipping-fast mr-3"></i>Order Tracking
                </h1>
                <p class="text-orange-100">Track and manage customer orders efficiently</p>
            </div>
            <div class="text-right">
                <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2">
                    <div class="text-white text-sm">Total Orders</div>
                    <div class="text-2xl font-bold text-white">{{ \App\Models\Order::count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daraz-style Search Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="border-b border-gray-200">
            <div class="flex space-x-1 p-1">
                <button type="button" onclick="setSearchMethod('order_number')" 
                        id="order-number-btn" 
                        class="flex-1 px-4 py-3 text-sm font-medium rounded-lg bg-orange-500 text-white transition-all duration-200">
                    <i class="fas fa-hashtag mr-2"></i>Order Number
                </button>
                <button type="button" onclick="setSearchMethod('customer_details')" 
                        id="customer-details-btn"
                        class="flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 transition-all duration-200">
                    <i class="fas fa-user mr-2"></i>Customer Details
                </button>
            </div>
        </div>

        <form id="tracking-form" method="GET" action="{{ route('admin.tracking.index') }}" class="p-6">
            <!-- Order Number Search -->
            <div id="order-number-search" class="search-method">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Order Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-lg" 
                                   id="order_number" name="order_number" 
                                   placeholder="Enter order number (e.g., ORD-12345678)" 
                                   value="{{ request('order_number') }}">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Find orders by their unique order number</p>
                    </div>
                    <div class="flex items-end space-x-3">
                        <button type="submit" class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-6 py-3 rounded-lg hover:from-orange-600 hover:to-red-600 transition-all duration-200 font-medium shadow-lg">
                            <i class="fas fa-search mr-2"></i>Search Order
                        </button>
                        <button type="button" onclick="clearForm()" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-all duration-200 font-medium">
                            <i class="fas fa-times mr-2"></i>Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Customer Details Search -->
            <div id="customer-details-search" class="search-method" style="display: none;">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Customer Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                   id="customer_name" name="customer_name" 
                                   placeholder="First or last name" value="{{ request('customer_name') }}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-gray-400"></i>
                            </div>
                            <input type="text" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                   id="phone" name="phone" 
                                   placeholder="01xxxxxxxxx" value="{{ request('phone') }}">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" 
                                   id="email" name="email" 
                                   placeholder="email@example.com" value="{{ request('email') }}">
                        </div>
                    </div>
                </div>
                <div class="flex justify-between items-center mt-4">
                    <p class="text-sm text-gray-500">Search by any combination of customer details</p>
                    <div class="flex space-x-3">
                        <button type="submit" class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-6 py-3 rounded-lg hover:from-orange-600 hover:to-red-600 transition-all duration-200 font-medium shadow-lg">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                        <button type="button" onclick="clearForm()" class="bg-gray-100 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-200 transition-all duration-200 font-medium">
                            <i class="fas fa-times mr-2"></i>Clear
                        </button>
                    </div>
                </div>
            </div>

            <input type="hidden" name="search_method" id="search_method" value="{{ request('search_method', 'order_number') }}">
        </form>
    </div>

    <!-- Search Results -->
    @if($searchPerformed)
        @if($orders->isNotEmpty())
            <!-- Results Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-list mr-2 text-orange-500"></i>Search Results ({{ $orders->count() }} orders)
                </h2>
                @if($orders->count() > 1)
                    <button type="button" onclick="showBulkUpdateModal()" class="bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-2 rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 font-medium shadow-lg">
                        <i class="fas fa-edit mr-2"></i>Bulk Update Status
                    </button>
                @endif
            </div>

            <!-- Daraz-style Results Cards -->
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 overflow-hidden">
                        <div class="p-6">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                <!-- Order Info -->
                                <div class="flex items-start space-x-4 mb-4 lg:mb-0">
                                    <div class="bg-gradient-to-br from-orange-400 to-red-500 text-white rounded-xl p-3 flex items-center justify-center shadow-lg">
                                        <i class="fas fa-shopping-bag text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800 mb-1">{{ $order->order_number }}</h3>
                                        <p class="text-sm text-gray-500">{{ $order->items_count }} items • {{ $order->created_at->format('M j, Y H:i') }}</p>
                                        <div class="mt-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                @if($order->status == 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($order->status == 'paid') bg-blue-100 text-blue-800
                                                @elseif($order->status == 'processing') bg-purple-100 text-purple-800
                                                @elseif($order->status == 'shipped') bg-indigo-100 text-indigo-800
                                                @elseif($order->status == 'delivered') bg-green-100 text-green-800
                                                @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Customer Info -->
                                <div class="flex-1 lg:mx-8 mb-4 lg:mb-0">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <h4 class="text-sm font-semibold text-gray-700 mb-2">Customer Details</h4>
                                        <div class="space-y-1">
                                            <div class="flex items-center text-sm">
                                                <i class="fas fa-user text-gray-400 mr-2 w-4"></i>
                                                <span class="font-medium text-gray-800">{{ $order->shippingAddress->first_name }} {{ $order->shippingAddress->last_name }}</span>
                                                @if($order->user)
                                                    <span class="ml-2 text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full">Registered</span>
                                                @else
                                                    <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">Guest</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <i class="fas fa-phone text-gray-400 mr-2 w-4"></i>
                                                {{ $order->shippingAddress->phone }}
                                            </div>
                                            @if($order->user)
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <i class="fas fa-envelope text-gray-400 mr-2 w-4"></i>
                                                    {{ $order->user->email }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Price & Actions -->
                                <div class="flex flex-col items-end space-y-3">
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-gray-800">৳{{ number_format($order->total_amount, 2) }}</div>
                                        <p class="text-sm text-gray-500">{{ $order->payment->method_display_name ?? 'N/A' }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.tracking.show', $order->order_number) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-orange-500 text-white text-sm font-medium rounded-lg hover:bg-orange-600 transition-colors duration-200">
                                            <i class="fas fa-eye mr-2"></i>View
                                        </a>
                                        <a href="{{ route('admin.orders.show', $order->id) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                                            <i class="fas fa-edit mr-2"></i>Manage
                                        </a>
                                        @if(!$order->courierIntegrations()->exists())
                                            <a href="{{ route('admin.courier-integrations.create', $order->id) }}" 
                                               class="inline-flex items-center px-3 py-2 bg-green-500 text-white text-sm font-medium rounded-lg hover:bg-green-600 transition-colors duration-200">
                                                <i class="fas fa-shipping-fast mr-2"></i>Courier
                                            </a>
                                        @else
                                            <a href="{{ route('admin.courier-integrations.show', $order->courierIntegrations->first()->id) }}" 
                                               class="inline-flex items-center px-3 py-2 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition-colors duration-200">
                                                <i class="fas fa-truck mr-2"></i>Track
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- No Results -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 text-center py-12">
                <div class="mb-4">
                    <div class="bg-gray-100 rounded-full p-4 inline-flex items-center justify-center">
                        <i class="fas fa-search text-gray-400 text-3xl"></i>
                    </div>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Orders Found</h3>
                <p class="text-gray-500 mb-6">We couldn't find any orders matching your search criteria.</p>
                <button type="button" onclick="clearForm()" class="bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600 transition-all duration-200 font-medium">
                    <i class="fas fa-redo mr-2"></i>Try Different Search
                </button>
            </div>
        @endif
    @endif

    <!-- Quick Stats (when no search performed) -->
    @if(!$searchPerformed)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-200">
                <div class="flex items-center">
                    <div class="bg-gradient-to-br from-blue-400 to-blue-600 text-white rounded-xl p-3 flex items-center justify-center mr-4">
                        <i class="fas fa-shopping-bag text-xl"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 font-medium">Total Orders</div>
                        <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Order::count() }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-200">
                <div class="flex items-center">
                    <div class="bg-gradient-to-br from-yellow-400 to-orange-500 text-white rounded-xl p-3 flex items-center justify-center mr-4">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 font-medium">Pending</div>
                        <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Order::where('status', 'pending')->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-200">
                <div class="flex items-center">
                    <div class="bg-gradient-to-br from-indigo-400 to-purple-600 text-white rounded-xl p-3 flex items-center justify-center mr-4">
                        <i class="fas fa-truck text-xl"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 font-medium">Shipped</div>
                        <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Order::where('status', 'shipped')->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-200">
                <div class="flex items-center">
                    <div class="bg-gradient-to-br from-green-400 to-green-600 text-white rounded-xl p-3 flex items-center justify-center mr-4">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <div>
                        <div class="text-sm text-gray-500 font-medium">Delivered</div>
                        <div class="text-2xl font-bold text-gray-800">{{ \App\Models\Order::where('status', 'delivered')->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Bulk Update Modal -->
<div id="bulkUpdateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-edit mr-2 text-orange-500"></i>Bulk Update Order Status
                </h3>
                <button type="button" onclick="closeBulkUpdateModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="bulk-update-form" method="POST" action="{{ route('admin.tracking.bulk-update') }}">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">New Status</label>
                            <select class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" id="bulk_status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="pending">Pending</option>
                                <option value="paid">Paid</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tracking Number</label>
                            <input type="text" class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" id="bulk_tracking_number" 
                                   name="tracking_number" placeholder="Enter tracking number">
                            <p class="mt-1 text-sm text-gray-500">For shipped orders</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                        <textarea class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" id="bulk_notes" name="notes" rows="3" 
                                  placeholder="Add any notes about this status update"></textarea>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            <span class="text-sm text-blue-700">This will update {{ $orders->count() }} selected orders.</span>
                        </div>
                    </div>
                    <input type="hidden" name="order_ids" id="bulk_order_ids">
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeBulkUpdateModal()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all duration-200 font-medium">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 font-medium shadow-lg">
                        <i class="fas fa-save mr-2"></i>Update Orders
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function setSearchMethod(method) {
    const orderNumberBtn = document.getElementById('order-number-btn');
    const customerDetailsBtn = document.getElementById('customer-details-btn');
    const orderNumberSearch = document.getElementById('order-number-search');
    const customerDetailsSearch = document.getElementById('customer-details-search');
    const searchMethodInput = document.getElementById('search_method');

    if (method === 'order_number') {
        orderNumberBtn.className = 'flex-1 px-4 py-3 text-sm font-medium rounded-lg bg-orange-500 text-white transition-all duration-200';
        customerDetailsBtn.className = 'flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 transition-all duration-200';
        orderNumberSearch.style.display = 'block';
        customerDetailsSearch.style.display = 'none';
        searchMethodInput.value = 'order_number';
    } else {
        customerDetailsBtn.className = 'flex-1 px-4 py-3 text-sm font-medium rounded-lg bg-orange-500 text-white transition-all duration-200';
        orderNumberBtn.className = 'flex-1 px-4 py-3 text-sm font-medium rounded-lg text-gray-600 hover:bg-gray-100 transition-all duration-200';
        customerDetailsSearch.style.display = 'block';
        orderNumberSearch.style.display = 'none';
        searchMethodInput.value = 'customer_details';
    }
}

function clearForm() {
    document.getElementById('tracking-form').reset();
    document.getElementById('search_method').value = 'order_number';
    setSearchMethod('order_number');
}

function showBulkUpdateModal() {
    const orderIds = @json($orders->pluck('id'));
    document.getElementById('bulk_order_ids').value = JSON.stringify(orderIds);
    document.getElementById('bulkUpdateModal').classList.remove('hidden');
}

function closeBulkUpdateModal() {
    document.getElementById('bulkUpdateModal').classList.add('hidden');
}

// Set initial search method based on URL parameter
document.addEventListener('DOMContentLoaded', function() {
    const searchMethod = '{{ request("search_method", "order_number") }}';
    setSearchMethod(searchMethod);
    
    // Close modal when clicking outside
    document.getElementById('bulkUpdateModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeBulkUpdateModal();
        }
    });
});
</script>
@endsection
