@extends('admin.layout')

@section('title', 'Products')
@section('header', 'Products Management')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold">Products</h1>
    <div class="flex space-x-3">
        <div class="relative">
            <button onclick="toggleExportMenu()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 shadow-md flex items-center">
                <i class="fas fa-download mr-2"></i>Export Products
                <i class="fas fa-chevron-down ml-2"></i>
            </button>
            <div id="exportMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-10">
                <a href="{{ route('admin.products.export') }}?format=csv" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                    <i class="fas fa-file-csv mr-2 text-green-600"></i>
                    Export as CSV
                </a>
                <a href="{{ route('admin.products.export') }}?format=excel" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                    <i class="fas fa-file-excel mr-2 text-green-700"></i>
                    Export as Excel
                </a>
                <a href="{{ route('admin.products.export') }}?format=pdf" class="w-full text-left px-4 py-2 hover:bg-gray-50 flex items-center">
                    <i class="fas fa-file-pdf mr-2 text-red-600"></i>
                    Export as PDF
                </a>
            </div>
        </div>
        <a href="{{ route('admin.products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-plus mr-2"></i>Add Product
        </a>
    </div>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-lg shadow mb-6 p-4">
    <form method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" placeholder="Search products..." 
                   value="{{ request('search') }}"
                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
        </div>
        <select name="category" class="px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
            <option value="">All Categories</option>
            @foreach($categories ?? [] as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <select name="status" class="px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
            <option value="">All Status</option>
            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
        </select>
        <select name="merchant" class="px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
            <option value="">All Merchants</option>
            <option value="0" {{ request('merchant') == '0' ? 'selected' : '' }}>Admin Products</option>
            @foreach($merchants ?? [] as $merchant)
                <option value="{{ $merchant->id }}" {{ request('merchant') == $merchant->id ? 'selected' : '' }}>
                    {{ $merchant->store_name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            <i class="fas fa-search mr-2"></i>Search
        </button>
        <a href="{{ route('admin.products.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
            <i class="fas fa-times mr-2"></i>Clear
        </a>
    </form>
</div>

<!-- Products Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Product
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        SKU
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Price
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Stock
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Merchant
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($product->primaryImage)
                                    <img src="{{ $product->primaryImage->image_url }}" 
                                         alt="{{ $product->name }}" class="w-10 h-10 object-cover rounded">
                                @else
                                    <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $product->category->name ?? 'No Category' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $product->sku }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">৳{{ number_format($product->price, 2) }}</div>
                            @if($product->compare_price)
                                <div class="text-sm text-gray-500 line-through">৳{{ number_format($product->compare_price, 2) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $product->stock_quantity }}</div>
                            @if($product->track_stock)
                                <div class="text-xs {{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $product->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->merchant)
                                <div class="text-sm font-medium text-gray-900">{{ $product->merchant->store_name }}</div>
                                <div class="text-xs text-gray-500">{{ $product->merchant->user->name }}</div>
                            @else
                                <span class="text-sm text-gray-500">Admin</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->is_active)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                            @if($product->is_featured)
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Featured
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.products.show', $product) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.products.edit', $product) }}" 
                                   class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product) }}" method="POST" 
                                      onsubmit="return confirm('Are you sure you want to delete this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No products found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $products->links() }}
    </div>
</div>

<script>
function toggleExportMenu() {
    const menu = document.getElementById('exportMenu');
    menu.classList.toggle('hidden');
}

// Close export menu when clicking outside
document.addEventListener('click', function(event) {
    const exportMenu = document.getElementById('exportMenu');
    const exportButton = event.target.closest('button[onclick="toggleExportMenu()"]');
    
    if (!exportButton && !exportMenu.contains(event.target)) {
        exportMenu.classList.add('hidden');
    }
});
</script>
@endsection
