@extends('admin.layout')

@section('title', 'Bulk Stock Update')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Bulk Stock Update</h1>
                    <p class="text-sm text-gray-500 mt-1">Update multiple products at once</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.inventory.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Update Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4">
            <form id="bulkUpdateForm" method="POST" action="{{ route('admin.inventory.bulk-update.store') }}">
                @csrf
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Products</label>
                    <div class="flex items-center space-x-4 mb-4">
                        <button type="button" onclick="loadProducts()" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                            <i class="fas fa-download mr-2"></i>Load All Products
                        </button>
                        <button type="button" onclick="addCustomProduct()" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                            <i class="fas fa-plus mr-2"></i>Add Custom Product
                        </button>
                    </div>
                    
                    <div id="productsList" class="space-y-4 max-h-96 overflow-y-auto">
                        <!-- Products will be loaded here -->
                    </div>
                    
                    <!-- Bulk Preview Section -->
                    <div id="bulkPreview" class="hidden mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <h4 class="text-sm font-medium text-green-900 mb-2">📊 Bulk Update Preview</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div class="text-center">
                                <div class="text-lg font-bold text-green-700" id="totalProducts">0</div>
                                <div class="text-gray-600">Products</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-blue-700" id="totalQuantity">0</div>
                                <div class="text-gray-600">Total Units</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-purple-700" id="totalValue">৳0</div>
                                <div class="text-gray-600">Total Value</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-orange-700" id="lowStockCount">0</div>
                                <div class="text-gray-600">Low Stock</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Default Action</label>
                    <select id="defaultAction" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="stock_in">Stock In</option>
                        <option value="stock_out">Stock Out</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Default Reason</label>
                    <select id="defaultReason" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="Purchase">Purchase</option>
                        <option value="Sale">Sale</option>
                        <option value="Return">Return</option>
                        <option value="Damage">Damage</option>
                        <option value="Lost">Lost</option>
                        <option value="Adjustment">Adjustment</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="clearAll()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        <i class="fas fa-times mr-2"></i>Clear All
                    </button>
                    <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
                        <i class="fas fa-save mr-2"></i>Update All Stock
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-sm font-medium text-blue-900 mb-2">Instructions:</h3>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• Click "Load All Products" to load all products for bulk update</li>
            <li>• Or use "Add Custom Product" to add specific products</li>
            <li>• Set quantity and action for each product</li>
            <li>• Use default action/reason to apply to all products</li>
            <li>• Click "Update All Stock" to process all changes</li>
        </ul>
    </div>
</div>

<script>
let productCount = 0;

function loadProducts() {
    fetch('/admin/products')
        .then(response => response.text())
        .then(html => {
            // Parse the HTML response to extract product data
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // Look for product data in the response
            const productRows = doc.querySelectorAll('table tbody tr');
            const products = [];
            
            productRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 6) {
                    const productName = cells[0].querySelector('.text-sm.font-medium')?.textContent?.trim();
                    const sku = cells[1].textContent?.trim();
                    const stockText = cells[3].textContent?.trim();
                    const stockMatch = stockText.match(/(\d+)/);
                    const stock = stockMatch ? parseInt(stockMatch[1]) : 0;
                    
                    if (productName) {
                        products.push({
                            id: `product-${products.length + 1}`,
                            name: productName,
                            stock: stock,
                            price: 100 // Default price for calculation
                        });
                    }
                }
            });
            
            // If no products found from HTML, try API approach
            if (products.length === 0) {
                loadProductsFromAPI();
            } else {
                products.forEach(product => addProductToList(product));
            }
        })
        .catch(error => {
            console.error('Error loading products from HTML:', error);
            loadProductsFromAPI();
        });
}

function loadProductsFromAPI() {
    // Try to get products via API endpoint
    fetch('/api/products', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        }
        throw new Error('API not available');
    })
    .then(products => {
        if (Array.isArray(products) && products.length > 0) {
            products.forEach(product => {
                addProductToList({
                    id: product.id,
                    name: product.name,
                    stock: product.stock_quantity || 0,
                    price: product.price || 100
                });
            });
        } else {
            // Fallback to sample data if API returns empty
            loadSampleProducts();
        }
    })
    .catch(error => {
        console.error('Error loading products from API:', error);
        loadSampleProducts();
    });
}

function loadSampleProducts() {
    // Fallback sample products with more realistic data
    const sampleProducts = [
        {id: 'sample-1', name: 'T-Shirt', stock: 50, price: 299},
        {id: 'sample-2', name: 'Jeans', stock: 25, price: 899},
        {id: 'sample-3', name: 'Shoes', stock: 15, price: 1299},
        {id: 'sample-4', name: 'Watch', stock: 8, price: 2499},
        {id: 'sample-5', name: 'Bag', stock: 30, price: 599}
    ];
    
    sampleProducts.forEach(product => addProductToList(product));
    
    // Show notification that sample data is being used
    showNotification('Sample products loaded for demonstration. Use "Add Custom Product" for real products.', 'info');
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'info' ? 'bg-blue-500' : 'bg-green-500'
    } text-white max-w-md`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-info-circle mr-2"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

function addCustomProduct() {
    // Show Daraz-style modal
    const dialog = document.createElement('div');
    dialog.className = 'fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center';
    dialog.innerHTML = `
        <div class="relative bg-white rounded-lg shadow-2xl max-w-md w-full mx-4 my-8">
            <!-- Daraz-style Header -->
            <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white px-6 py-4 rounded-t-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-plus-circle text-2xl mr-3"></i>
                        <h3 class="text-xl font-bold">Add Custom Product</h3>
                    </div>
                    <button onclick="closeCustomProductDialog()" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <p class="text-sm mt-2 text-orange-100">Add a product for bulk inventory update</p>
            </div>
            
            <!-- Daraz-style Body -->
            <div class="px-6 py-6">
                <form id="customProductForm" class="space-y-5">
                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag text-orange-500 mr-1"></i>
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="customProductName" required 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm"
                               placeholder="e.g., T-Shirt, Jeans, Shoes">
                        <div class="absolute right-3 top-10 text-gray-400">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-cubes text-blue-500 mr-1"></i>
                                Current Stock <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="customProductStock" required min="0"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm"
                                   placeholder="0">
                            <div class="absolute right-3 top-10 text-gray-400">
                                <i class="fas fa-sort-numeric-up"></i>
                            </div>
                        </div>
                        
                        <div class="relative">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-dollar-sign text-green-500 mr-1"></i>
                                Price (৳) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="customProductPrice" required min="0" step="0.01"
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm"
                                   placeholder="0.00">
                            <div class="absolute right-3 top-10 text-gray-400">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-barcode text-purple-500 mr-1"></i>
                            Product ID (Optional)
                        </label>
                        <input type="text" id="customProductId"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all text-sm"
                               placeholder="Leave blank to auto-generate">
                        <div class="absolute right-3 top-10 text-gray-400">
                            <i class="fas fa-hashtag"></i>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 flex items-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            If empty, system will auto-generate from product name
                        </p>
                    </div>
                    
                    <!-- Auto-generated preview -->
                    <div id="autoPreview" class="hidden bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm font-semibold text-blue-800 mb-2">
                            <i class="fas fa-magic mr-1"></i>
                            Auto-Generated Preview:
                        </p>
                        <div class="text-sm text-blue-700">
                            <span class="font-mono bg-white px-2 py-1 rounded" id="previewId"></span>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Daraz-style Footer -->
            <div class="bg-gray-50 px-6 py-4 rounded-b-lg border-t">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Secure data entry
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" onclick="closeCustomProductDialog()" 
                                class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-all font-medium text-sm">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </button>
                        <button type="button" onclick="addCustomProductToList()" 
                                class="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-lg hover:from-orange-600 hover:to-red-600 transition-all font-medium text-sm shadow-lg">
                            <i class="fas fa-plus mr-2"></i>Add Product
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(dialog);
    
    // Add auto-preview functionality
    document.getElementById('customProductName').addEventListener('input', function() {
        const productName = this.value.trim();
        const previewDiv = document.getElementById('autoPreview');
        const previewId = document.getElementById('previewId');
        
        if (productName && !document.getElementById('customProductId').value) {
            const generatedId = generateProductId(productName);
            previewId.textContent = generatedId;
            previewDiv.classList.remove('hidden');
        } else {
            previewDiv.classList.add('hidden');
        }
    });
}

function closeCustomProductDialog() {
    const dialog = document.querySelector('.fixed.inset-0');
    if (dialog) {
        dialog.remove();
    }
}

function addCustomProductToList() {
    const productName = document.getElementById('customProductName').value.trim();
    const currentStock = parseInt(document.getElementById('customProductStock').value) || 0;
    const productPrice = parseFloat(document.getElementById('customProductPrice').value) || 0;
    let productId = document.getElementById('customProductId').value.trim();
    
    if (!productName) {
        showNotification('Product name is required', 'error');
        return;
    }
    
    if (currentStock < 0) {
        showNotification('Stock cannot be negative', 'error');
        return;
    }
    
    if (productPrice < 0) {
        showNotification('Price cannot be negative', 'error');
        return;
    }
    
    // Generate product ID if not provided
    if (!productId) {
        productId = generateProductId(productName);
    }
    
    addProductToList({
        id: productId,
        name: productName,
        stock: currentStock,
        price: productPrice
    });
    
    closeCustomProductDialog();
    showNotification(`✅ Product "${productName}" added successfully!`, 'success');
}

function showNotification(message, type = 'info') {
    // Remove existing notification
    const existingNotification = document.querySelector('.notification-toast');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-4 right-4 p-4 rounded-lg shadow-2xl z-50 transform transition-all duration-300 translate-x-full max-w-md`;
    
    // Daraz-style notification colors
    let bgColor = 'bg-blue-500';
    let icon = 'fa-info-circle';
    let borderColor = 'border-blue-600';
    
    if (type === 'success') {
        bgColor = 'bg-green-500';
        icon = 'fa-check-circle';
        borderColor = 'border-green-600';
    } else if (type === 'error') {
        bgColor = 'bg-red-500';
        icon = 'fa-exclamation-triangle';
        borderColor = 'border-red-600';
    }
    
    notification.className += ` ${bgColor} text-white border-2 ${borderColor}`;
    notification.innerHTML = `
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas ${icon} text-xl"></i>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-4 flex-shrink-0">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" 
                        class="text-white hover:text-gray-200 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
        notification.classList.add('translate-x-0');
    }, 100);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

function generateProductId(productName) {
    // Generate SKU-like ID from product name
    const words = productName.split(' ');
    let id = '';
    
    words.forEach(word => {
        id += word.substring(0, 3).toUpperCase();
    });
    
    // Add random number to make unique
    const randomNum = Math.floor(Math.random() * 1000);
    return `${id}-${randomNum}`;
}

// Apply default values when changed
document.getElementById('defaultAction').addEventListener('change', function() {
    const action = this.value;
    document.querySelectorAll('select[name*="[action]"]').forEach(select => {
        select.value = action;
    });
    updateBulkPreview();
});

document.getElementById('defaultReason').addEventListener('change', function() {
    const reason = this.value;
    document.querySelectorAll('select[name*="[reason]"]').forEach(select => {
        select.value = reason;
    });
});

// Call updateBulkPreview when products are added
function addProductToList(product) {
    productCount++;
    const productHtml = `
        <div class="product-item border border-gray-200 rounded-lg p-4" id="product-${product.id}" data-price="${product.price}">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <h4 class="font-medium text-gray-900">${product.name}</h4>
                    <p class="text-sm text-gray-500">Current Stock: ${product.stock}</p>
                    <p class="text-xs text-gray-400">Price: ৳${product.price}</p>
                </div>
                <button type="button" onclick="removeProduct('${product.id}')" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="grid grid-cols-3 gap-4 mt-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="updates[${productCount}][quantity]" min="0" required oninput="updateBulkPreview()" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                    <select name="updates[${productCount}][action]" onchange="updateBulkPreview()" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="stock_in">Stock In</option>
                        <option value="stock_out">Stock Out</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                    <select name="updates[${productCount}][reason]" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="Purchase">Purchase</option>
                        <option value="Sale">Sale</option>
                        <option value="Return">Return</option>
                        <option value="Damage">Damage</option>
                        <option value="Lost">Lost</option>
                        <option value="Adjustment">Adjustment</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
                <span class="preview-text">New stock: <span class="font-medium preview-new">${product.stock}</span></span>
            </div>
            <input type="hidden" name="updates[${productCount}][product_id]" value="${product.id}">
        </div>
    `;
    
    document.getElementById('productsList').insertAdjacentHTML('beforeend', productHtml);
    updateBulkPreview(); // Update preview after adding product
}

function removeProduct(productId) {
    const element = document.getElementById(`product-${productId}`);
    if (element) {
        element.remove();
        updateBulkPreview(); // Update preview after removing product
    }
}

function clearAll() {
    document.getElementById('productsList').innerHTML = '';
    document.getElementById('bulkPreview').classList.add('hidden');
    productCount = 0;
}

function updateBulkPreview() {
    const products = document.querySelectorAll('.product-item');
    let totalProducts = 0;
    let totalQuantity = 0;
    let totalValue = 0;
    let lowStockCount = 0;
    
    products.forEach(product => {
        const quantityInput = product.querySelector('input[name*="[quantity]"]');
        const actionSelect = product.querySelector('select[name*="[action]"]');
        const previewNew = product.querySelector('.preview-new');
        
        if (quantityInput && actionSelect && previewNew) {
            const quantity = parseInt(quantityInput.value) || 0;
            const action = actionSelect.value;
            const currentStock = parseInt(product.querySelector('p.text-sm.text-gray-500').textContent.match(/Current Stock: (\d+)/)?.[1] || 0);
            
            let newStock = currentStock;
            switch(action) {
                case 'stock_in':
                    newStock = currentStock + quantity;
                    break;
                case 'stock_out':
                    newStock = currentStock - quantity;
                    break;
                case 'adjustment':
                    newStock = quantity;
                    break;
            }
            
            previewNew.textContent = newStock;
            previewNew.className = newStock <= 0 ? 'font-medium text-red-600' : 
                                   newStock <= 10 ? 'font-medium text-yellow-600' : 
                                   'font-medium text-green-600';
            
            totalProducts++;
            totalQuantity += newStock;
            
            // Get product price from the data attribute or use default
            const productElement = document.getElementById(`product-${product.id.replace('product-', '').replace('sample-', '')}`);
            const productPrice = productElement?.dataset?.price || 100;
            totalValue += newStock * productPrice;
            
            if (newStock <= 10) lowStockCount++;
        }
    });
    
    // Update preview display
    document.getElementById('totalProducts').textContent = totalProducts;
    document.getElementById('totalQuantity').textContent = totalQuantity;
    document.getElementById('totalValue').textContent = `৳${totalValue.toFixed(0)}`;
    document.getElementById('lowStockCount').textContent = lowStockCount;
    
    // Show/hide preview section
    const previewSection = document.getElementById('bulkPreview');
    if (totalProducts > 0) {
        previewSection.classList.remove('hidden');
    } else {
        previewSection.classList.add('hidden');
    }
}

// Apply default values when changed
document.getElementById('defaultAction').addEventListener('change', function() {
    const action = this.value;
    document.querySelectorAll('select[name*="[action]"]').forEach(select => {
        select.value = action;
    });
});

document.getElementById('defaultReason').addEventListener('change', function() {
    const reason = this.value;
    document.querySelectorAll('select[name*="[reason]"]').forEach(select => {
        select.value = reason;
    });
});
</script>
@endsection
