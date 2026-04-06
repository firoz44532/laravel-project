@extends('admin.layout')

@section('title', 'Inventory Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Inventory Dashboard</h1>
                    <p class="text-sm text-gray-500 mt-1">Monitor and manage your inventory</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.inventory.index') }}" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                        <i class="fas fa-box mr-2"></i>Manage Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_products']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">In Stock</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['in_stock']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Out of Stock</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['out_of_stock']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Low Stock</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['low_stock']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Stock Value</p>
                    <p class="text-2xl font-bold text-gray-900">৳{{ number_format($stats['total_stock_value'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Low Stock Products -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Low Stock Alerts</h2>
                    <p class="text-sm text-gray-500 mt-1">Products that need restocking</p>
                </div>
                <div class="p-6">
                    @if($lowStockProducts->count() > 0)
                        <div class="space-y-4">
                            @foreach($lowStockProducts as $product)
                                <div class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900">{{ $product->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $product->category->name ?? 'No Category' }}</p>
                                        <div class="flex items-center mt-2 space-x-4">
                                            <span class="text-sm font-medium text-gray-900">
                                                Stock: {{ $product->stock_quantity }}
                                            </span>
                                            <span class="text-sm text-yellow-600">
                                                Threshold: {{ $product->stockAlert->threshold_quantity }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="px-3 py-1 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition">
                                            Update Stock
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>
                            <p class="text-gray-500">No low stock products</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Stock Movements -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Movements</h2>
                    <p class="text-sm text-gray-500 mt-1">Latest stock changes</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($recentMovements as $movement)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    @switch($movement->action)
                                        @case('stock_in')
                                            <div class="p-2 bg-green-100 rounded-full">
                                                <i class="fas fa-arrow-down text-green-600 text-xs"></i>
                                            </div>
                                            @break
                                        @case('stock_out')
                                            <div class="p-2 bg-red-100 rounded-full">
                                                <i class="fas fa-arrow-up text-red-600 text-xs"></i>
                                            </div>
                                            @break
                                        @default
                                            <div class="p-2 bg-gray-100 rounded-full">
                                                <i class="fas fa-exchange-alt text-gray-600 text-xs"></i>
                                            </div>
                                    @endswitch
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $movement->product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }}</p>
                                    <p class="text-xs text-gray-400">{{ $movement->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products by Stock Value -->
    <div class="mt-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Top Products by Stock Value</h2>
                <p class="text-sm text-gray-500 mt-1">Highest inventory value products</p>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Value</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($topStockValue as $product)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $product->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($product->stock_quantity) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        ৳{{ number_format($product->price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ৳{{ number_format($product->total_value, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
