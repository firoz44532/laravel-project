@extends('layouts.merchant')

@section('title', 'Orders')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Amazon-Daraz Mixed Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-1 flex items-center">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        Orders Management
                    </h1>
                    <p class="text-orange-100 text-sm">Track and manage customer orders efficiently</p>
                </div>
                <div class="flex space-x-3">
                    <button class="bg-white text-orange-500 px-4 py-2 rounded-lg font-semibold hover:bg-orange-50 transition duration-200 shadow-md">
                        <i class="fas fa-download mr-2"></i>Export Orders
                    </button>
                    <button class="bg-white text-orange-500 px-4 py-2 rounded-lg font-semibold hover:bg-orange-50 transition duration-200 shadow-md">
                        <i class="fas fa-print mr-2"></i>Print Labels
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Orders -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-orange-500">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $orders->total() }}</p>
                        </div>
                        <div class="bg-orange-100 rounded-full p-3">
                            <i class="fas fa-shopping-cart text-orange-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-yellow-500">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Pending</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $orders->where('status', 'pending')->count() }}</p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-3">
                            <i class="fas fa-clock text-yellow-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Processing Orders -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-blue-500">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Processing</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $orders->where('status', 'processing')->count() }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-cog text-blue-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Completed Orders -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-green-500">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Completed</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $orders->where('status', 'completed')->count() }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-md mb-6 p-4">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <input type="text" placeholder="Search orders..." 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                    <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <input type="date" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="flex items-center space-x-2">
                    <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200">
                        <i class="fas fa-sort mr-2"></i>Sort
                    </button>
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        @if($orders->count() > 0)
            <!-- View Toggle -->
            <div class="flex justify-between items-center mb-4">
                <p class="text-sm text-gray-600">
                    Showing <span class="font-medium">{{ $orders->firstItem() }}</span> to 
                    <span class="font-medium">{{ $orders->lastItem() }}</span> of 
                    <span class="font-medium">{{ $orders->total() }}</span> orders
                </p>
                <div class="flex items-center space-x-2">
                    <button class="p-2 bg-orange-500 text-white rounded-lg" id="tableView">
                        <i class="fas fa-list"></i>
                    </button>
                    <button class="p-2 bg-gray-200 text-gray-600 rounded-lg" id="cardView">
                        <i class="fas fa-th-large"></i>
                    </button>
                </div>
            </div>

            <!-- Table View (Amazon-style) -->
            <div id="tableViewContainer" class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr class="hover:bg-orange-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-orange-100 rounded-lg p-2 mr-3">
                                            <i class="fas fa-shopping-bag text-orange-500"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $order->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-gray-200 rounded-full h-8 w-8 flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-gray-500 text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $order->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $order->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        @foreach($order->items->take(2) as $item)
                                            <div class="flex items-center mb-1">
                                                <div class="bg-gray-100 rounded p-1 mr-2">
                                                    @if($item->product && $item->product->primaryImage)
                                                        <img src="{{ $item->product->primaryImage->image_url }}" alt="{{ $item->product_name }}" class="w-6 h-6 object-cover rounded">
                                                    @else
                                                        <i class="fas fa-box text-gray-400 text-xs"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <span class="font-medium">{{ $item->product_name }}</span>
                                                    <span class="text-gray-500">x{{ $item->quantity }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($order->items->count() > 2)
                                            <div class="text-xs text-gray-500 mt-1">+{{ $order->items->count() - 2 }} more items</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-green-600">৳{{ number_format($order->total_amount, 2) }}</div>
                                    <div class="text-xs text-gray-500">{{ $order->items->count() }} items</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full
                                        {{ $order->status === 'completed' ? 'bg-green-100 text-green-800 border border-green-200' : 
                                           ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 
                                           ($order->status === 'processing' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-gray-100 text-gray-800 border border-gray-200')) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        <div class="font-medium text-gray-900">{{ $order->created_at->format('M d, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->created_at->format('h:i A') }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button class="text-orange-500 hover:text-orange-700 transition-colors duration-200" title="View Details">
                                            <i class="fas fa-eye text-lg"></i>
                                        </button>
                                        @if($order->status === 'pending')
                                            <button class="text-green-500 hover:text-green-700 transition-colors duration-200" title="Accept Order">
                                                <i class="fas fa-check-circle text-lg"></i>
                                            </button>
                                        @endif
                                        <button class="text-blue-500 hover:text-blue-700 transition-colors duration-200" title="Print">
                                            <i class="fas fa-print text-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Card View (Daraz-style) -->
            <div id="cardViewContainer" class="hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($orders as $order)
                        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                            <!-- Order Header -->
                            <div class="bg-gradient-to-r from-orange-500 to-red-500 p-4 text-white">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="flex items-center mb-2">
                                            <i class="fas fa-shopping-bag mr-2"></i>
                                            <h3 class="font-bold text-lg">{{ $order->order_number }}</h3>
                                        </div>
                                        <p class="text-orange-100 text-sm">{{ $order->created_at->format('M d, Y - h:i A') }}</p>
                                    </div>
                                    <span class="px-3 py-1 text-xs font-bold rounded-full bg-white bg-opacity-20">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Order Body -->
                            <div class="p-4">
                                <!-- Customer Info -->
                                <div class="flex items-center mb-4 pb-4 border-b border-gray-200">
                                    <div class="bg-gray-200 rounded-full h-10 w-10 flex items-center justify-center mr-3">
                                        <i class="fas fa-user text-gray-500"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $order->user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $order->user->email }}</p>
                                    </div>
                                </div>
                                
                                <!-- Products -->
                                <div class="mb-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Products ({{ $order->items->count() }})</p>
                                    <div class="space-y-2">
                                        @foreach($order->items->take(3) as $item)
                                            <div class="flex items-center justify-between text-sm">
                                                <div class="flex items-center">
                                                    <div class="bg-gray-100 rounded p-1 mr-2">
                                                        @if($item->product && $item->product->primaryImage)
                                                            <img src="{{ $item->product->primaryImage->image_url }}" alt="{{ $item->product_name }}" class="w-4 h-4 object-cover rounded">
                                                        @else
                                                            <i class="fas fa-box text-gray-400 text-xs"></i>
                                                        @endif
                                                    </div>
                                                    <span class="text-gray-700">{{ $item->product_name }}</span>
                                                </div>
                                                <span class="text-gray-500">x{{ $item->quantity }}</span>
                                            </div>
                                        @endforeach
                                        @if($order->items->count() > 3)
                                            <p class="text-xs text-gray-500">+{{ $order->items->count() - 3 }} more items</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Total -->
                                <div class="flex justify-between items-center mb-4 pt-4 border-t border-gray-200">
                                    <span class="text-sm font-medium text-gray-700">Total Amount</span>
                                    <span class="text-lg font-bold text-green-600">৳{{ number_format($order->total_amount, 2) }}</span>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex space-x-2">
                                    <button class="flex-1 bg-orange-500 text-white px-3 py-2 rounded-lg hover:bg-orange-600 transition duration-200">
                                        <i class="fas fa-eye mr-1"></i>View
                                    </button>
                                    @if($order->status === 'pending')
                                        <button class="flex-1 bg-green-500 text-white px-3 py-2 rounded-lg hover:bg-green-600 transition duration-200">
                                            <i class="fas fa-check mr-1"></i>Accept
                                        </button>
                                    @endif
                                    <button class="flex-1 bg-blue-500 text-white px-3 py-2 rounded-lg hover:bg-blue-600 transition duration-200">
                                        <i class="fas fa-print mr-1"></i>Print
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex justify-center mt-6">
                {{ $orders->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="bg-gradient-to-br from-orange-100 to-red-100 rounded-full w-32 h-32 mx-auto mb-6 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-orange-500 text-5xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">No orders yet</h3>
                <p class="text-gray-600 mb-6">Orders will appear here when customers purchase your products.</p>
                <a href="{{ route('merchant.products.index') }}" class="bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition duration-200 shadow-md">
                    <i class="fas fa-plus mr-2"></i>Add More Products
                </a>
            </div>
        @endif
    </div>
</div>

<style>
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #ff5722, #ff9800);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View toggle functionality
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');
    const tableViewContainer = document.getElementById('tableViewContainer');
    const cardViewContainer = document.getElementById('cardViewContainer');
    
    tableView.addEventListener('click', function() {
        tableView.classList.add('bg-orange-500', 'text-white');
        tableView.classList.remove('bg-gray-200', 'text-gray-600');
        cardView.classList.add('bg-gray-200', 'text-gray-600');
        cardView.classList.remove('bg-orange-500', 'text-white');
        tableViewContainer.classList.remove('hidden');
        cardViewContainer.classList.add('hidden');
    });
    
    cardView.addEventListener('click', function() {
        cardView.classList.add('bg-orange-500', 'text-white');
        cardView.classList.remove('bg-gray-200', 'text-gray-600');
        tableView.classList.add('bg-gray-200', 'text-gray-600');
        tableView.classList.remove('bg-orange-500', 'text-white');
        cardViewContainer.classList.remove('hidden');
        tableViewContainer.classList.add('hidden');
    });

    // Search functionality
    const searchInput = document.querySelector('input[placeholder="Search orders..."]');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('#tableViewContainer tbody tr');
            const orderCards = document.querySelectorAll('#cardViewContainer > div');
            
            // Filter table view
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
            
            // Filter card view
            orderCards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    // Add smooth transitions for all interactive elements
    document.querySelectorAll('button, a, .hover\\:bg-orange-50').forEach(element => {
        element.style.transition = 'all 0.3s ease';
    });
});
</script>
@endsection
