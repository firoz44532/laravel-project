@extends('layouts.merchant')

@section('title', 'Add New Product')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Amazon-Daraz Mixed Header -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-1 flex items-center">
                        <i class="fas fa-plus-circle mr-3"></i>
                        Add New Product
                    </h1>
                    <p class="text-orange-100 text-sm">Create a new product listing for your inventory</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('merchant.products.index') }}" class="bg-white text-orange-500 px-4 py-2 rounded-lg font-semibold hover:bg-orange-50 transition duration-200 shadow-md">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <form method="POST" action="{{ route('merchant.products.store') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Form Section -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Product Information Card -->
                    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white px-6 py-4">
                            <h2 class="text-lg font-semibold flex items-center">
                                <i class="fas fa-box mr-2"></i>
                                Product Information
                            </h2>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Product Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Enter product name"
                                           required>
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="sku" class="block text-sm font-semibold text-gray-900 mb-2">
                                        SKU <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200" 
                                           id="sku" 
                                           name="sku" 
                                           value="{{ old('sku') }}" 
                                           placeholder="e.g., PRD-001"
                                           required>
                                    @error('sku')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="short_description" class="block text-sm font-semibold text-gray-900 mb-2">
                                    Short Description
                                </label>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200" 
                                          id="short_description" 
                                          name="short_description" 
                                          rows="2" 
                                          placeholder="Brief description for product listings">{{ old('short_description') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Brief description shown in product listings (max 500 characters)</p>
                                @error('short_description')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
                                    Full Description <span class="text-red-500">*</span>
                                </label>
                                <textarea class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200" 
                                          id="description" 
                                          name="description" 
                                          rows="6" 
                                          placeholder="Detailed product description including features and specifications"
                                          required>{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Product Images Card -->
                    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-4">
                            <h2 class="text-lg font-semibold flex items-center">
                                <i class="fas fa-images mr-2"></i>
                                Product Images
                            </h2>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-900 mb-2">
                                    Product Images
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-purple-500 transition-colors duration-200">
                                    <div class="space-y-4">
                                        <div class="mx-auto w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-cloud-upload-alt text-purple-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600">Click to upload or drag and drop</p>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB each</p>
                                        </div>
                                        <input type="file" 
                                               id="product_images" 
                                               name="product_images[]" 
                                               multiple 
                                               accept="image/*"
                                               class="hidden">
                                        <button type="button" 
                                                onclick="document.getElementById('product_images').click()"
                                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-200">
                                            <i class="fas fa-upload mr-2"></i>Select Images
                                        </button>
                                    </div>
                                </div>
                                <p class="mt-2 text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Upload multiple product images. First image will be set as primary.
                                </p>
                            </div>

                            <!-- Image Preview Area -->
                            <div id="imagePreview" class="hidden">
                                <h4 class="text-sm font-semibold text-gray-900 mb-3">Image Preview</h4>
                                <div id="previewContainer" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <!-- Preview images will be added here dynamically -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Inventory Card -->
                    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-4">
                            <h2 class="text-lg font-semibold flex items-center">
                                <i class="fas fa-dollar-sign mr-2"></i>
                                Pricing & Inventory
                            </h2>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label for="price" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Sale Price <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-medium">৳</span>
                                        </div>
                                        <input type="number" 
                                               class="w-full pl-8 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200" 
                                               id="price" 
                                               name="price" 
                                               value="{{ old('price') }}" 
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00"
                                               required>
                                    </div>
                                    @error('price')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="compare_price" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Compare Price
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-medium">৳</span>
                                        </div>
                                        <input type="number" 
                                               class="w-full pl-8 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200" 
                                               id="compare_price" 
                                               name="compare_price" 
                                               value="{{ old('compare_price') }}" 
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Original price for discount display</p>
                                    @error('compare_price')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="cost_price" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Cost Price
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 font-medium">৳</span>
                                        </div>
                                        <input type="number" 
                                               class="w-full pl-8 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200" 
                                               id="cost_price" 
                                               name="cost_price" 
                                               value="{{ old('cost_price') }}" 
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00">
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Your cost (not shown to customers)</p>
                                    @error('cost_price')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="weight" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Weight (kg)
                                    </label>
                                    <input type="number" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200" 
                                           id="weight" 
                                           name="weight" 
                                           value="{{ old('weight') }}" 
                                           step="0.01" 
                                           min="0" 
                                           placeholder="0.00">
                                    @error('weight')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="category_id" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Category <span class="text-red-500">*</span>
                                    </label>
                                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200" 
                                            id="category_id" 
                                            name="category_id" 
                                            required>
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="brand_id" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Brand
                                    </label>
                                    <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200" 
                                            id="brand_id" 
                                            name="brand_id">
                                        <option value="">Select Brand</option>
                                        @foreach($brands as $brand)
                                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                                {{ $brand->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('brand_id')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="stock_quantity" class="block text-sm font-semibold text-gray-900 mb-2">
                                        Stock Quantity <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent transition duration-200" 
                                           id="stock_quantity" 
                                           name="stock_quantity" 
                                           value="{{ old('stock_quantity', 0) }}" 
                                           min="0" 
                                           placeholder="0"
                                           required>
                                    @error('stock_quantity')
                                        <p class="mt-2 text-sm text-red-600 flex items-center">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="flex items-center">
                                        <input class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded" 
                                               type="checkbox" 
                                               id="track_stock" 
                                               name="track_stock" 
                                               {{ old('track_stock') ? 'checked' : '' }}>
                                        <label class="ml-3 text-sm font-medium text-gray-900" for="track_stock">
                                            Track Stock Quantity
                                        </label>
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="flex items-center">
                                        <input class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="ml-3 text-sm font-medium text-gray-900" for="is_active">
                                            Active (visible to customers)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center bg-white rounded-lg shadow-md p-6">
                        <a href="{{ route('merchant.products.index') }}" class="bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-600 transition duration-200 shadow-md">
                            <i class="fas fa-times mr-2"></i>Cancel
                        </a>
                        <button type="submit" class="bg-orange-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-orange-600 transition duration-200 shadow-md">
                            <i class="fas fa-save mr-2"></i>Create Product
                        </button>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Product Guidelines Card -->
                    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4">
                            <h3 class="text-lg font-semibold flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Product Guidelines
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                        Product Requirements
                                    </h4>
                                    <ul class="space-y-2 text-sm text-gray-600">
                                        <li class="flex items-start">
                                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                            <span>High-quality product images</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                            <span>Accurate descriptions</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                            <span>Competitive pricing</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-check text-green-500 mr-2 mt-0.5"></i>
                                            <span>Proper categorization</span>
                                        </li>
                                    </ul>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-dollar-sign text-blue-500 mr-2"></i>
                                        Commission & Fees
                                    </h4>
                                    <ul class="space-y-2 text-sm text-gray-600">
                                        <li class="flex justify-between">
                                            <span>Platform commission:</span>
                                            <span class="font-semibold">10%</span>
                                        </li>
                                        <li class="flex justify-between">
                                            <span>Payment processing:</span>
                                            <span class="font-semibold">2.5%</span>
                                        </li>
                                        <li class="flex justify-between font-semibold text-orange-600">
                                            <span>Total:</span>
                                            <span>12.5%</span>
                                        </li>
                                    </ul>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2 flex items-center">
                                        <i class="fas fa-times-circle text-red-500 mr-2"></i>
                                        Prohibited Items
                                    </h4>
                                    <ul class="space-y-2 text-sm text-gray-600">
                                        <li class="flex items-start">
                                            <i class="fas fa-times text-red-500 mr-2 mt-0.5"></i>
                                            <span>Illegal products</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-times text-red-500 mr-2 mt-0.5"></i>
                                            <span>Counterfeit goods</span>
                                        </li>
                                        <li class="flex items-start">
                                            <i class="fas fa-times text-red-500 mr-2 mt-0.5"></i>
                                            <span>Harmful items</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats Card -->
                    <div class="bg-gradient-to-br from-orange-100 to-red-100 rounded-lg p-6 border border-orange-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-chart-line text-orange-500 mr-2"></i>
                            Your Store Stats
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total Products</span>
                                <span class="font-semibold text-gray-900">{{ \App\Models\Product::where('merchant_id', Auth::user()->merchant->id)->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Active Listings</span>
                                <span class="font-semibold text-green-600">{{ \App\Models\Product::where('merchant_id', Auth::user()->merchant->id)->where('is_active', true)->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Low Stock Items</span>
                                <span class="font-semibold text-yellow-600">{{ \App\Models\Product::where('merchant_id', Auth::user()->merchant->id)->where('stock_quantity', '<=', 5)->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* Custom styles for Amazon-Daraz mixed design */
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

/* Smooth transitions for all interactive elements */
.transition-all {
    transition: all 0.3s ease;
}

/* Focus styles */
.focus\\:ring-2:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(251, 146, 60, 0.1);
}

.focus\\:border-transparent:focus {
    border-color: #fb923c;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth transitions for all interactive elements
    document.querySelectorAll('button, a, input, select, textarea').forEach(element => {
        element.style.transition = 'all 0.3s ease';
    });

    // Add hover effects to cards
    document.querySelectorAll('.hover\\:shadow-xl').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Image Upload Preview Functionality
    const imageInput = document.getElementById('product_images');
    const imagePreview = document.getElementById('imagePreview');
    const previewContainer = document.getElementById('previewContainer');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            if (files.length > 0) {
                imagePreview.classList.remove('hidden');
                previewContainer.innerHTML = ''; // Clear previous previews
                
                files.forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const imageDiv = document.createElement('div');
                            imageDiv.className = 'relative group';
                            
                            imageDiv.innerHTML = `
                                <div class="aspect-square rounded-lg overflow-hidden border-2 border-gray-200">
                                    <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-full object-cover">
                                </div>
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <button type="button" onclick="removeImage(${index})" class="bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                                ${index === 0 ? '<div class="absolute top-2 left-2 bg-orange-500 text-white text-xs px-2 py-1 rounded">Primary</div>' : ''}
                            `;
                            
                            previewContainer.appendChild(imageDiv);
                        };
                        
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                imagePreview.classList.add('hidden');
                previewContainer.innerHTML = '';
            }
        });
    }

    // Drag and Drop Functionality
    const dropZone = document.querySelector('.border-dashed');
    
    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight(e) {
            dropZone.classList.add('border-purple-500', 'bg-purple-50');
        }
        
        function unhighlight(e) {
            dropZone.classList.remove('border-purple-500', 'bg-purple-50');
        }
        
        dropZone.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                imageInput.files = files;
                const event = new Event('change', { bubbles: true });
                imageInput.dispatchEvent(event);
            }
        }
    }
});

// Remove image function
function removeImage(index) {
    const imageInput = document.getElementById('product_images');
    const files = Array.from(imageInput.files);
    
    // Remove the file at the specified index
    files.splice(index, 1);
    
    // Create a new FileList with remaining files
    const dt = new DataTransfer();
    files.forEach(file => dt.items.add(file));
    imageInput.files = dt.files;
    
    // Trigger change event to update preview
    const event = new Event('change', { bubbles: true });
    imageInput.dispatchEvent(event);
}
</script>
@endsection
