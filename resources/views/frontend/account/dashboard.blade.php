@extends('frontend.layout')

@section('title', 'My Account')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-6">My Account</h2>
                <nav class="space-y-2">
                    <a href="{{ route('account.dashboard') }}" 
                       class="block px-4 py-2 rounded-lg bg-primary text-white">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('account.profile') }}" 
                       class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-user mr-2"></i>Profile
                    </a>
                    <a href="{{ route('account.addresses') }}" 
                       class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-map-marker-alt mr-2"></i>Addresses
                    </a>
                    <a href="{{ route('account.orders') }}" 
                       class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-shopping-bag mr-2"></i>Orders
                    </a>
                    @if(!Auth::user()->merchant)
                        <a href="{{ route('merchant.register') }}" 
                           class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-store mr-2"></i>Become a Seller
                        </a>
                    @else
                        <a href="{{ route('merchant.dashboard') }}" 
                           class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-store mr-2"></i>Merchant Dashboard
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 rounded-lg hover:bg-gray-100 text-left">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow p-6">
                <h1 class="text-2xl font-bold mb-6">Dashboard</h1>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100">Total Orders</p>
                                <p class="text-3xl font-bold">{{ $orders->count() }}</p>
                            </div>
                            <i class="fas fa-shopping-bag text-4xl text-blue-200"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100">Total Spent</p>
                                <p class="text-3xl font-bold">৳{{ number_format($orders->sum('total_amount'), 2) }}</p>
                            </div>
                            <i class="fas fa-money-bill-wave text-4xl text-green-200"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100">Saved Items</p>
                                <p class="text-3xl font-bold">{{ $addresses->count() }}</p>
                            </div>
                            <i class="fas fa-heart text-4xl text-purple-200"></i>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Recent Orders</h2>
                    @if($orders->count() > 0)
                        <div class="space-y-4">
                            @foreach($orders as $order)
                                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="flex items-center space-x-3">
                                                <span class="text-sm font-medium">{{ $order->order_number }}</span>
                                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                {{ $order->created_at->format('M j, Y') }} • {{ $order->items_count }} items
                                            </div>
                                            <div class="text-lg font-semibold mt-2">
                                                ৳{{ number_format($order->total_amount, 2) }}
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <a href="{{ route('account.orders.show', $order->id) }}" 
                                               class="text-primary hover:text-orange-600 text-sm">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-4">
                            <a href="{{ route('account.orders') }}" 
                               class="text-primary hover:text-orange-600">
                                View All Orders →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-shopping-bag text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">No Orders Yet</h3>
                            <p class="text-gray-500 mb-4">Start shopping to see your orders here</p>
                            <a href="{{ route('products.index') }}" 
                               class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                                Start Shopping
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(!Auth::user()->merchant)
                        <div class="border rounded-lg p-6 hover:bg-gray-50 transition bg-gradient-to-r from-purple-50 to-blue-50">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-store text-purple-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Become a Seller</h3>
                                    <p class="text-sm text-gray-600">Start selling your products online</p>
                                </div>
                            </div>
                            <div class="text-right mt-4">
                                <a href="{{ route('merchant.register') }}" 
                                   class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition">
                                    Register Now →
                                </a>
                            </div>
                        </div>
                    @endif
                    <div class="border rounded-lg p-6 hover:bg-gray-50 transition">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Shopping Cart</h3>
                                <p class="text-sm text-gray-600">View and manage your cart items</p>
                            </div>
                        </div>
                        <div class="text-right mt-4">
                            <a href="{{ route('cart.index') }}" 
                               class="text-primary hover:text-orange-600">
                                Go to Cart →
                            </a>
                        </div>
                    </div>
                    <div class="border rounded-lg p-6 hover:bg-gray-50 transition">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold">Addresses</h3>
                                <p class="text-sm text-gray-600">Manage shipping addresses</p>
                            </div>
                        </div>
                        <div class="text-right mt-4">
                            <a href="{{ route('account.addresses') }}" 
                               class="text-primary hover:text-orange-600">
                                Manage →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
