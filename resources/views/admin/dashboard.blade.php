@extends('admin.layout')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Stats Cards -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-full">
                <i class="fas fa-box text-blue-600"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Products</h3>
                <p class="text-2xl font-bold">{{ $stats['total_products'] }}</p>
                <p class="text-sm text-gray-500">{{ $stats['active_products'] }} active</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-full">
                <i class="fas fa-shopping-cart text-green-600"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Orders</h3>
                <p class="text-2xl font-bold">{{ $stats['total_orders'] }}</p>
                <p class="text-sm text-gray-500">{{ $stats['pending_orders'] }} pending</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-full">
                <i class="fas fa-users text-purple-600"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Users</h3>
                <p class="text-2xl font-bold">{{ $stats['total_users'] }}</p>
                <p class="text-sm text-gray-500">{{ $stats['active_users'] }} active</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="p-3 bg-orange-100 rounded-full">
                <i class="fas fa-tags text-orange-600"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-700">Categories</h3>
                <p class="text-2xl font-bold">{{ $stats['total_categories'] }}</p>
                <p class="text-sm text-gray-500">{{ $stats['total_brands'] }} brands</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Recent Orders</h3>
        </div>
        <div class="p-6">
            @if($recent_orders->count() > 0)
                <div class="space-y-4">
                    @foreach($recent_orders as $order)
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium">{{ $order->order_number }}</p>
                                <p class="text-sm text-gray-500">{{ $order->user->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium">{{ $order->formatted_total }}</p>
                                <span class="inline-block px-2 py-1 text-xs rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No orders yet</p>
            @endif
        </div>
    </div>

    <!-- Latest Products -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Latest Products</h3>
        </div>
        <div class="p-6">
            @if($latest_products->count() > 0)
                <div class="space-y-4">
                    @foreach($latest_products as $product)
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="font-medium">{{ $product->name }}</p>
                                <p class="text-sm text-gray-500">{{ $product->category->name ?? 'No Category' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium">৳{{ number_format($product->price, 2) }}</p>
                                <span class="inline-block px-2 py-1 text-xs rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">No products yet</p>
            @endif
        </div>
    </div>
</div>

<!-- Order Status Chart -->
<div class="mt-6 bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold">Order Status Overview</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach(['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'] as $status)
                <div class="text-center">
                    <p class="text-2xl font-bold">{{ $order_stats[$status] ?? 0 }}</p>
                    <p class="text-sm text-gray-500 capitalize">{{ $status }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.VoiceNotifications) {
        // Welcome admin
        setTimeout(() => {
            window.VoiceNotifications.speak('Welcome to admin dashboard');
        }, 1000);
        
        // Alert for pending orders
        @if($stats['pending_orders'] > 0)
        setTimeout(() => {
            window.VoiceNotifications.speak(`You have {{ $stats['pending_orders'] }} pending orders that need attention`);
        }, 3000);
        @endif
        
        // Alert for low stock products
        @if(isset($low_stock_products) && $low_stock_products->count() > 0)
        setTimeout(() => {
            window.VoiceNotifications.lowStock('Multiple products', $low_stock_products->count() . ' items');
        }, 5000);
        @endif
        
        // Add voice control button
        const header = document.querySelector('h1');
        if (header) {
            const voiceButton = document.createElement('button');
            voiceButton.className = 'ml-4 bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700 transition';
            voiceButton.innerHTML = '<i class="fas fa-microphone mr-1"></i> Voice Controls';
            voiceButton.onclick = () => window.VoiceNotifications.showControlPanel();
            header.parentElement.appendChild(voiceButton);
        }
        
        // Periodic dashboard updates (every 60 seconds)
        setInterval(() => {
            if (document.visibilityState === 'visible') {
                // This would typically fetch new stats via AJAX
                console.log('Checking dashboard updates...');
            }
        }, 60000);
    }
});
</script>
@endsection
