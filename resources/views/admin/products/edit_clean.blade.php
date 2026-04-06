@extends('admin.layout')

@section('title', 'Edit Product')

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
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Products
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
                        <input type="text" name="name" id="name" required value="{{ old('name', $product->name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                    </div>
                    
                    <div class="space-y-2">
                        <label for="sku" class="block text-sm font-medium text-gray-700">
                            SKU <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="sku" id="sku" required value="{{ old('sku', $product->sku) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                    </div>
                    
                    <div class="md:col-span-2 space-y-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">
                            Full Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">{{ old('description', $product->description) }}</textarea>
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
                        <input type="number" name="price" id="price" step="0.01" min="0" required value="{{ old('price', $product->price) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                    </div>
                    
                    <div class="space-y-2">
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700">
                            Stock Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock_quantity" id="stock_quantity" min="0" required value="{{ old('stock_quantity', $product->stock_quantity) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                    </div>
                    
                    <div class="space-y-2">
                        <label for="category_id" class="block text-sm font-medium text-gray-700">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" id="category_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
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
                        <button type="submit" name="test_submit" value="1"
                                class="px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600">
                            Test Submit
                        </button>
                        <button type="submit" id="update-btn"
                                class="px-6 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition-all duration-300 flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Update Product
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
