@extends('admin.layout')

@section('title', 'Inventory Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Inventory Management</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage product stock and inventory</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.inventory.dashboard') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-chart-line mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('admin.inventory.bulk-update') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fas fa-upload mr-2"></i>Bulk Update
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4">
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-0">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                </div>
                <div>
                    <select name="stock_status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Stock Status</option>
                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div>
                    <select name="category" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
                <a href="{{ route('admin.inventory.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($products as $product)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($product->primaryImage)
                                        <img src="{{ $product->primaryImage->image_url }}" alt="{{ $product->name }}" class="h-10 w-10 rounded-full object-cover">
                                    @else
                                        <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $product->sku }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $product->category->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <span class="text-sm font-medium {{ $product->stock_quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $product->stock_quantity }}
                                    </span>
                                    @if($product->stockAlert && $product->stock_quantity <= $product->stockAlert->threshold_quantity)
                                        <span class="ml-2 px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Low</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                ৳{{ number_format($product->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($product->stock_quantity > 0)
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">In Stock</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Out of Stock</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button onclick="openStockModal({{ $product->id }}, '{{ $product->name }}', {{ $product->stock_quantity }}, {{ $product->price }})" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="{{ route('admin.inventory.history', $product->id) }}" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-history"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $products->links() }}
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div id="stockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Adjust Stock</h3>
            <form id="stockForm" method="POST" action="{{ route('admin.inventory.adjust-stock', ':id') }}">
                @csrf
                <input type="hidden" name="product_id" id="modalProductId">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                    <input type="text" id="modalProductName" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Stock</label>
                    <input type="text" id="modalCurrentStock" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Action</label>
                    <select name="action" id="modalAction" onchange="calculatePreview()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="stock_in">Stock In</option>
                        <option value="stock_out">Stock Out</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                    <input type="number" name="quantity" id="modalQuantity" min="1" required oninput="calculatePreview()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>
                
                <!-- Preview Section -->
                <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">📊 Preview Calculation</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Current Stock:</span>
                            <span class="font-medium" id="previewCurrent">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Change:</span>
                            <span class="font-medium" id="previewChange">+0</span>
                        </div>
                        <div class="flex justify-between font-bold text-blue-900 pt-2 border-t border-blue-200">
                            <span>New Stock:</span>
                            <span id="previewNew">0</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Stock Value:</span>
                            <span class="font-medium" id="previewValue">৳0.00</span>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <select name="reason" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="Purchase">Purchase</option>
                        <option value="Sale">Sale</option>
                        <option value="Return">Return</option>
                        <option value="Damage">Damage</option>
                        <option value="Lost">Lost</option>
                        <option value="Adjustment">Adjustment</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeStockModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        Update Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentProductPrice = 0;

function openStockModal(productId, productName, currentStock, productPrice = 0) {
    document.getElementById('modalProductId').value = productId;
    document.getElementById('modalProductName').value = productName;
    document.getElementById('modalCurrentStock').value = currentStock;
    currentProductPrice = productPrice;
    document.getElementById('stockModal').classList.remove('hidden');
    
    // Update form action
    const form = document.getElementById('stockForm');
    form.action = form.action.replace(':id', productId);
    
    // Initialize preview
    calculatePreview();
}

function closeStockModal() {
    document.getElementById('stockModal').classList.add('hidden');
    document.getElementById('stockForm').reset();
    currentProductPrice = 0;
}

function calculatePreview() {
    const currentStock = parseInt(document.getElementById('modalCurrentStock').value) || 0;
    const action = document.getElementById('modalAction').value;
    const quantity = parseInt(document.getElementById('modalQuantity').value) || 0;
    
    let change = 0;
    let changeText = '';
    
    switch(action) {
        case 'stock_in':
            change = quantity;
            changeText = `+${quantity}`;
            break;
        case 'stock_out':
            change = -quantity;
            changeText = `-${quantity}`;
            break;
        case 'adjustment':
            change = quantity;
            changeText = quantity >= 0 ? `+${quantity}` : `${quantity}`;
            break;
    }
    
    const newStock = currentStock + change;
    const stockValue = newStock * currentProductPrice;
    
    // Update preview elements
    document.getElementById('previewCurrent').textContent = currentStock;
    document.getElementById('previewChange').textContent = changeText;
    document.getElementById('previewNew').textContent = newStock;
    document.getElementById('previewValue').textContent = `৳${stockValue.toFixed(2)}`;
    
    // Color coding for new stock
    const newStockElement = document.getElementById('previewNew');
    if (newStock <= 0) {
        newStockElement.className = 'text-red-600 font-bold';
    } else if (newStock <= 10) {
        newStockElement.className = 'text-yellow-600 font-bold';
    } else {
        newStockElement.className = 'text-green-600 font-bold';
    }
    
    // Color coding for change
    const changeElement = document.getElementById('previewChange');
    if (change > 0) {
        changeElement.className = 'font-medium text-green-600';
    } else if (change < 0) {
        changeElement.className = 'font-medium text-red-600';
    } else {
        changeElement.className = 'font-medium text-gray-600';
    }
}
</script>
@endsection
