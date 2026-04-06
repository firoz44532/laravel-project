@extends('layouts.merchant')

@section('title', 'Merchant Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                    <p class="text-blue-100">{{ $merchant->store_name }} Dashboard</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('merchant.products.create') }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition duration-200">
                        <i class="fas fa-plus mr-2"></i>Add Product
                    </a>
                    <a href="{{ route('merchant.profile') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-400 transition duration-200">
                        <i class="fas fa-store mr-2"></i>Store Settings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Products Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Products</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_products'] }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-green-600 font-medium">{{ $stats['active_products'] }} active</span>
                            @if($stats['pending_approval_products'] > 0)
                                <span class="text-xs text-yellow-600 font-medium ml-2">{{ $stats['pending_approval_products'] }} pending</span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-box text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Orders Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_orders'] }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-green-600 font-medium">{{ $stats['completed_orders'] }} completed</span>
                            @if($stats['pending_orders'] > 0)
                                <span class="text-xs text-orange-600 font-medium ml-2">{{ $stats['pending_orders'] }} pending</span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Revenue Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">৳{{ number_format($stats['total_revenue'], 0) }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-500">Lifetime sales</span>
                        </div>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Earnings Card -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Your Earnings</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">৳{{ number_format($stats['total_earnings'], 0) }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-500">After commission</span>
                        </div>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <i class="fas fa-wallet text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Cards -->
        @if($stats['low_stock_products'] > 0)
        <div class="mt-6 bg-orange-50 border border-orange-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-orange-500 mr-3"></i>
                <div>
                    <p class="text-sm font-medium text-orange-800">Low Stock Alert</p>
                    <p class="text-sm text-orange-600">{{ $stats['low_stock_products'] }} products are running low on stock (≤5 items)</p>
                </div>
                <a href="{{ route('merchant.products.index') }}" class="ml-auto text-sm font-medium text-orange-600 hover:text-orange-800">Manage Stock</a>
            </div>
        </div>
        @endif
    </div>

    <!-- Main Content Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Recent Orders Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Recent Orders</h2>
                            <a href="{{ route('merchant.orders.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View All</a>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($recentOrders->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentOrders as $order)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-4">
                                            <div class="bg-white rounded-full p-2">
                                                <i class="fas fa-shopping-bag text-gray-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">#{{ $order->order_number }}</p>
                                                <p class="text-sm text-gray-500">{{ $order->user->name }}</p>
                                                <p class="text-xs text-gray-400">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">৳{{ number_format($order->total_amount, 0) }}</p>
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                   ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                                   ($order->status === 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-shopping-cart text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">No orders yet</p>
                                <p class="text-sm text-gray-400 mt-2">Your orders will appear here</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Top Products Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-6">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">Top Selling Products</h2>
                            <a href="{{ route('merchant.products.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View All</a>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($topProducts->count() > 0)
                            <div class="space-y-4">
                                @foreach($topProducts as $index => $product)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-gray-100 rounded-lg w-12 h-12 flex items-center justify-center font-semibold text-gray-600">
                                                {{ $index + 1 }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900">{{ Str::limit($product->name, 40) }}</p>
                                                <p class="text-sm text-gray-500">৳{{ number_format($product->price, 0) }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">{{ $product->order_items_count }}</p>
                                            <p class="text-xs text-gray-500">sold</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-trophy text-gray-300 text-4xl mb-4"></i>
                                <p class="text-gray-500">No sales yet</p>
                                <p class="text-sm text-gray-400 mt-2">Your best sellers will appear here</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('merchant.products.create') }}" class="flex items-center justify-between p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-plus-circle text-blue-600 mr-3"></i>
                                <span class="font-medium text-gray-900">Add New Product</span>
                            </div>
                            <i class="fas fa-arrow-right text-blue-600"></i>
                        </a>
                        <a href="{{ route('merchant.orders.index') }}" class="flex items-center justify-between p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-list text-green-600 mr-3"></i>
                                <span class="font-medium text-gray-900">Manage Orders</span>
                            </div>
                            <i class="fas fa-arrow-right text-green-600"></i>
                        </a>
                        <a href="{{ route('merchant.earnings') }}" class="flex items-center justify-between p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-chart-bar text-purple-600 mr-3"></i>
                                <span class="font-medium text-gray-900">View Analytics</span>
                            </div>
                            <i class="fas fa-arrow-right text-purple-600"></i>
                        </a>
                    </div>
                </div>

                <!-- Recent Products -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Products</h3>
                    </div>
                    <div class="p-6">
                        @if($recentProducts->count() > 0)
                            <div class="space-y-3">
                                @foreach($recentProducts as $product)
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="bg-gray-100 rounded-lg w-10 h-10 flex items-center justify-center">
                                                <i class="fas fa-box text-gray-600 text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 text-sm">{{ Str::limit($product->name, 25) }}</p>
                                                <p class="text-xs text-gray-500">৳{{ number_format($product->price, 0) }}</p>
                                            </div>
                                        </div>
                                        <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                            {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-6">
                                <i class="fas fa-box text-gray-300 text-3xl mb-3"></i>
                                <p class="text-gray-500 text-sm">No products yet</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Performance Summary -->
                @if($monthlyRevenue->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">This Month</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Revenue</span>
                            <span class="font-semibold text-gray-900">৳{{ number_format($monthlyRevenue->last()->revenue, 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Orders</span>
                            <span class="font-semibold text-gray-900">{{ $monthlyRevenue->last()->orders }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Avg. Order Value</span>
                            <span class="font-semibold text-gray-900">৳{{ number_format($monthlyRevenue->last()->revenue / $monthlyRevenue->last()->orders, 0) }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Add some interactive JavaScript for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects to cards
    const cards = document.querySelectorAll('.hover\\:shadow-lg');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endsection
