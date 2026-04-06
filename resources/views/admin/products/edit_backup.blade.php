@extends('admin.layout')

@section('title', 'Edit Product')
@section('header', 'Edit Product')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Edit Product</h1>
                    <p class="text-sm text-gray-500 mt-1">Update product information and settings</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-orange-500"></i>
                    Basic Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required
                               value="{{ old('name', $product->name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="space-y-2">
                        <label for="sku" class="block text-sm font-medium text-gray-700">
                            SKU <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="sku" id="sku" required
                               value="{{ old('sku', $product->sku) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('sku') border-red-500 @enderror">
                        @error('sku')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2 space-y-2">
                        <label for="short_description" class="block text-sm font-medium text-gray-700">
                            Short Description
                        </label>
                        <textarea name="short_description" id="short_description" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">{{ old('short_description', $product->short_description) }}</textarea>
                        <p class="text-xs text-gray-500">Brief description shown in product listings</p>
                    </div>
                    
                    <div class="md:col-span-2 space-y-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Full Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Pricing -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-tag mr-2 text-orange-500"></i>
                    Pricing
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label for="price" class="block text-sm font-medium text-gray-700">
                            Selling Price (BDT) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price" id="price" step="0.01" min="0" required
                               value="{{ old('price', $product->price) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('price') border-red-500 @enderror">
                        @error('price')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="space-y-2">
                        <label for="compare_price" class="block text-sm font-medium text-gray-700">
                            Compare Price (BDT)
                        </label>
                        <input type="number" name="compare_price" id="compare_price" step="0.01" min="0"
                               value="{{ old('compare_price', $product->compare_price) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('compare_price') border-red-500 @enderror">
                        @error('compare_price')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500">Original price for discount display</p>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="cost_price" class="block text-sm font-medium text-gray-700">
                            Cost Price (BDT)
                        </label>
                        <input type="number" name="cost_price" id="cost_price" step="0.01" min="0"
                               value="{{ old('cost_price', $product->cost_price) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('cost_price') border-red-500 @enderror">
                        @error('cost_price')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500">Internal cost calculation (not shown to customers)</p>
                    </div>
                </div>
            </div>
            
            <!-- Inventory -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-boxes mr-2 text-orange-500"></i>
                    Inventory
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700">
                            Stock Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock_quantity" id="stock_quantity" min="0" required
                               value="{{ old('stock_quantity', $product->stock_quantity) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('stock_quantity') border-red-500 @enderror">
                        @error('stock_quantity')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="space-y-2">
                        <label for="weight" class="block text-sm font-medium text-gray-700">
                            Weight (kg)
                        </label>
                        <input type="number" name="weight" id="weight" step="0.01" min="0"
                               value="{{ old('weight', $product->weight) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('weight') border-red-500 @enderror">
                        @error('weight')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center space-x-3 pt-6">
                        <input type="hidden" name="track_stock" value="0">
                        <input type="checkbox" name="track_stock" id="track_stock" value="1"
                               {{ old('track_stock', $product->track_stock) ? 'checked' : '' }}
                               class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <label for="track_stock" class="text-sm font-medium text-gray-700">
                            Track Stock
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Categories -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-folder mr-2 text-orange-500"></i>
                    Categories
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="category_id" class="block text-sm font-medium text-gray-700">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" id="category_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('category_id') border-red-500 @enderror">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="space-y-2">
                        <label for="brand_id" class="block text-sm font-medium text-gray-700">
                            Brand
                        </label>
                        <select name="brand_id" id="brand_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm @error('brand_id') border-red-500 @enderror">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Product Images -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-images mr-2 text-orange-500"></i>
                    Product Images
                </h2>
                
                <!-- Existing Images -->
                @if($product->images->count() > 0)
                    <div class="mb-4">
                        <p class="text-sm text-gray-600 mb-2">Current Images:</p>
                        <div class="grid grid-cols-4 gap-4">
                            @foreach($product->images as $image)
                                <div class="relative group">
                                    <img src="{{ $image->image_url }}" alt="Product Image" 
                                         class="w-full h-24 object-cover rounded-lg border border-gray-200">
                                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center space-x-2">
                                        <form action="{{ route('admin.products.setPrimaryImage', $image) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return handleSetPrimary(this.form)"
                                                    class="bg-blue-500 text-white p-2 rounded-full hover:bg-blue-600 transition"
                                                    title="Set as Primary">
                                                <i class="fas fa-star text-xs"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.products.deleteImage', $image) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    onclick="return handleImageDelete(this.form)"
                                                    class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition"
                                                    title="Delete Image">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="absolute bottom-1 right-1 bg-black bg-opacity-70 text-white px-2 py-1 rounded text-xs">
                                        {{ $image->sort_order }}
                                    </div>
                                    @if($image->is_primary)
                                        <div class="absolute top-1 left-1 bg-orange-500 text-white px-2 py-1 rounded text-xs">
                                            Primary
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Upload New Images -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                    <div class="text-center">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                        <p class="text-sm text-gray-500">PNG, JPG, GIF up to 2MB each</p>
                        <input type="file" name="images[]" id="images" multiple accept="image/*"
                               class="hidden">
                        <button type="button" onclick="document.getElementById('images').click()"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm">
                            Choose Files
                        </button>
                    </div>
                    <div id="image-preview" class="grid grid-cols-4 gap-4 mt-4"></div>
                </div>
            </div>
            
            <!-- Options -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-cog mr-2 text-orange-500"></i>
                    Options
                </h2>
                <div class="space-y-4 bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <label for="is_active" class="text-sm font-medium text-gray-700">
                            Active (Product will be visible on storefront)
                        </label>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1"
                               {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                               class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <label for="is_featured" class="text-sm font-medium text-gray-700">
                            Featured (Show on homepage)
                        </label>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="sort_order" class="block text-sm font-medium text-gray-700">
                            Sort Order
                        </label>
                        <input type="number" name="sort_order" id="sort_order" min="0"
                               value="{{ old('sort_order', $product->sort_order) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                        <p class="text-xs text-gray-500">Lower numbers appear first</p>
                    </div>
                </div>
            </div>
            
            <!-- Options -->
            <div class="mb-8">
                <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-cog mr-2 text-orange-500"></i>
                    Options
                </h2>
                <div class="space-y-4 bg-gray-50 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <label for="is_active" class="text-sm font-medium text-gray-700">
                            Active (Product will be visible on storefront)
                        </label>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1"
                               {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}
                               class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <label for="is_featured" class="text-sm font-medium text-gray-700">
                            Featured (Show on homepage)
                        </label>
                    </div>
                    
                    <div class="space-y-2">
                        <label for="sort_order" class="block text-sm font-medium text-gray-700">
                            Sort Order
                        </label>
                        <input type="number" name="sort_order" id="sort_order" min="0"
                               value="{{ old('sort_order', $product->sort_order) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                        <p class="text-xs text-gray-500">Lower numbers appear first</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-6 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-2"></i>
                        Product last updated: {{ $product->updated_at->format('M d, Y H:i') }}
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.products.index') }}" 
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" id="update-btn"
                                class="px-6 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition-all duration-300 flex items-center">
                            <i class="fas fa-save mr-2" id="btn-icon"></i>
                            <span id="btn-text">Update Product</span>
                        </button>
                        
                        <!-- Test button -->
                        <button type="submit" name="test_submit" value="1"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600">
                            Test Submit
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            <div>
                <p class="font-semibold">{{ session('success') }}</p>
                <p class="text-green-100 text-sm">Operation completed successfully</p>
            </div>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg z-50">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle mr-3"></i>
            <div>
                <p class="font-semibold">{{ session('error') }}</p>
                <p class="text-red-100 text-sm">Please check the errors below</p>
            </div>
        </div>
    </div>
@endif

    
    @endsection
