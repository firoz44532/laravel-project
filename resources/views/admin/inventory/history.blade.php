@extends('admin.layout')

@section('title', 'Stock History')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Stock History</h1>
                    <p class="text-sm text-gray-500 mt-1">Stock movement history for {{ $product->name }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.products.edit', $product->id) }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit Product
                    </a>
                    <a href="{{ route('admin.inventory.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Info -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4">
            <div class="flex items-center">
                @if($product->primaryImage)
                    <img src="{{ $product->primaryImage->image_url }}" alt="{{ $product->name }}" class="h-16 w-16 rounded-full object-cover">
                @else
                    <div class="h-16 w-16 bg-gray-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-box text-gray-400 text-2xl"></i>
                    </div>
                @endif
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                    <div class="flex items-center mt-2 space-x-4">
                        <span class="text-sm font-medium {{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                            Current Stock: {{ $product->stock_quantity }}
                        </span>
                        <span class="text-sm text-gray-500">
                            Price: ৳{{ number_format($product->price, 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock History Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Movement History</h2>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Total: {{ $logs->total() }} records</span>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Change</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Before</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">After</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($log->action)
                                    @case('stock_in')
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Stock In</span>
                                        @break
                                    @case('stock_out')
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Stock Out</span>
                                        @break
                                    @case('adjustment')
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Adjustment</span>
                                        @break
                                    @case('sale')
                                        <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">Sale</span>
                                        @break
                                    @case('return')
                                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Return</span>
                                        @break
                                    @default
                                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">{{ ucfirst($log->action) }}</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium {{ $log->quantity_change > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $log->quantity_change > 0 ? '+' : '' }}{{ $log->quantity_change }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->quantity_before }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $log->quantity_after }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->reason }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->user ? $log->user->name : 'System' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <span class="truncate max-w-xs block">{{ $log->notes ?: '-' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No stock history found for this product
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- Statistics -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">
                    {{ $logs->stockIn()->count() }}
                </div>
                <div class="text-sm text-gray-500 mt-1">Stock In</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600">
                    {{ $logs->stockOut()->count() }}
                </div>
                <div class="text-sm text-gray-500 mt-1">Stock Out</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">
                    {{ $logs->adjustment()->count() }}
                </div>
                <div class="text-sm text-gray-500 mt-1">Adjustments</div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">
                    {{ $logs->sale()->count() }}
                </div>
                <div class="text-sm text-gray-500 mt-1">Sales</div>
            </div>
        </div>
    </div>
</div>
@endsection
