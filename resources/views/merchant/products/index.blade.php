@extends('layouts.merchant')

@section('title', 'My Products')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Amazon-Daraz Mixed Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-1 flex items-center">
                        <i class="fas fa-box mr-3"></i>
                        My Products
                    </h1>
                    <p class="text-orange-100 text-sm">Manage your product inventory and listings</p>
                </div>
                <div class="flex space-x-3">
                    <button class="bg-white text-orange-500 px-4 py-2 rounded-lg font-semibold hover:bg-orange-50 transition duration-200 shadow-md">
                        <i class="fas fa-download mr-2"></i>Export Products
                    </button>
                    <a href="{{ route('merchant.products.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-green-600 transition duration-200 shadow-md">
                        <i class="fas fa-plus mr-2"></i>Add New Product
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total Products -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-orange-500">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Total Products</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $products->total() }}</p>
                        </div>
                        <div class="bg-orange-100 rounded-full p-3">
                            <i class="fas fa-box text-orange-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Products -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-green-500">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Active</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $products->where('is_active', true)->count() }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-yellow-500">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Low Stock</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $products->where('stock_quantity', '<=', 5)->count() }}</p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-3">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Out of Stock -->
            <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border-l-4 border-red-500">
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 mb-1">Out of Stock</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $products->where('stock_quantity', 0)->count() }}</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-3">
                            <i class="fas fa-times-circle text-red-500 text-xl"></i>
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
                        <input type="text" placeholder="Search products..." 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                    </div>
                    <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">All Categories</option>
                        <option value="electronics">Electronics</option>
                        <option value="clothing">Clothing</option>
                        <option value="home">Home & Garden</option>
                    </select>
                    <select class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
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

        <!-- Products Grid/Table -->
        @if($products->count() > 0)
            <!-- View Toggle -->
            <div class="flex justify-between items-center mb-4">
                <p class="text-sm text-gray-600">
                    Showing <span class="font-medium">{{ $products->firstItem() }}</span> to 
                    <span class="font-medium">{{ $products->lastItem() }}</span> of 
                    <span class="font-medium">{{ $products->total() }}</span> products
                </p>
                <div class="flex items-center space-x-2">
                    <button class="p-2 bg-orange-500 text-white rounded-lg" id="gridView">
                        <i class="fas fa-th"></i>
                    </button>
                    <button class="p-2 bg-gray-200 text-gray-600 rounded-lg" id="listView">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Grid View (Amazon-style) -->
            <div id="gridViewContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden group">
                        <!-- Product Image -->
                        <div class="relative h-48 bg-gray-100 overflow-hidden">
                            @if($product->primaryImage)
                                <img src="{{ $product->primaryImage->image_url }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                            
                            <!-- Status Badge -->
                            <div class="absolute top-2 right-2">
                                <span class="px-2 py-1 text-xs font-bold rounded-full
                                    {{ $product->is_active ? 'bg-green-500 text-white' : 'bg-gray-500 text-white' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            
                            <!-- Stock Badge -->
                            @if($product->stock_quantity <= 5)
                                <div class="absolute top-2 left-2">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full
                                        {{ $product->stock_quantity == 0 ? 'bg-red-500 text-white' : 'bg-yellow-500 text-white' }}">
                                        {{ $product->stock_quantity == 0 ? 'Out of Stock' : 'Low Stock' }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Product Info -->
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600 mb-2">{{ $product->category->name ?? 'No Category' }}</p>
                            <p class="text-xs text-gray-500 mb-3">SKU: {{ $product->sku }}</p>
                            
                            <!-- Price and Stock -->
                            <div class="flex justify-between items-center mb-3">
                                <div>
                                    <p class="text-lg font-bold text-green-600">৳{{ number_format($product->price, 2) }}</p>
                                    <p class="text-xs text-gray-500">Stock: {{ $product->stock_quantity }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">{{ $product->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <a href="#" class="flex-1 bg-orange-500 text-white px-3 py-2 rounded-lg text-center hover:bg-orange-600 transition duration-200">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <button class="flex-1 bg-red-500 text-white px-3 py-2 rounded-lg hover:bg-red-600 transition duration-200">
                                    <i class="fas fa-trash mr-1"></i>Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- List View (Daraz-style) -->
            <div id="listViewContainer" class="hidden">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($products as $product)
                                <tr class="hover:bg-orange-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                @if($product->primaryImage)
                                                    <img class="h-12 w-12 rounded-lg object-cover" src="{{ $product->primaryImage->image_url }}" alt="{{ $product->name }}">
                                                @else
                                                    <div class="h-12 w-12 rounded-lg bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                                        <i class="fas fa-box text-gray-400"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $product->category->name ?? 'No Category' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->sku }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">৳{{ number_format($product->price, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $product->stock_quantity > 5 ? 'bg-green-100 text-green-800' : 
                                               ($product->stock_quantity > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="#" class="text-orange-500 hover:text-orange-700 transition-colors duration-200" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="text-red-500 hover:text-red-700 transition-colors duration-200" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="flex justify-center mt-6">
                {{ $products->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="bg-gradient-to-br from-orange-100 to-red-100 rounded-full w-32 h-32 mx-auto mb-6 flex items-center justify-center">
                    <i class="fas fa-box text-orange-500 text-5xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">No products yet</h3>
                <p class="text-gray-600 mb-6">Start selling by adding your first product to your inventory.</p>
                <a href="{{ route('merchant.products.create') }}" class="bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition duration-200 shadow-md">
                    <i class="fas fa-plus mr-2"></i>Add Your First Product
                </a>
            </div>
        @endif
    </div>
</div>

<style>
/* Custom styles for Amazon-Daraz mixed design */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

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
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridViewContainer = document.getElementById('gridViewContainer');
    const listViewContainer = document.getElementById('listViewContainer');
    
    gridView.addEventListener('click', function() {
        gridView.classList.add('bg-orange-500', 'text-white');
        gridView.classList.remove('bg-gray-200', 'text-gray-600');
        listView.classList.add('bg-gray-200', 'text-gray-600');
        listView.classList.remove('bg-orange-500', 'text-white');
        gridViewContainer.classList.remove('hidden');
        listViewContainer.classList.add('hidden');
    });
    
    listView.addEventListener('click', function() {
        listView.classList.add('bg-orange-500', 'text-white');
        listView.classList.remove('bg-gray-200', 'text-gray-600');
        gridView.classList.add('bg-gray-200', 'text-gray-600');
        gridView.classList.remove('bg-orange-500', 'text-white');
        listViewContainer.classList.remove('hidden');
        gridViewContainer.classList.add('hidden');
    });

    // Search functionality
    const searchInput = document.querySelector('input[placeholder="Search products..."]');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const productCards = document.querySelectorAll('#gridViewContainer > div');
            const tableRows = document.querySelectorAll('#listViewContainer tbody tr');
            
            // Filter grid view
            productCards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(searchTerm) ? '' : 'none';
            });
            
            // Filter list view
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
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
