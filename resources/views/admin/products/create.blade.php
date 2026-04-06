@extends('admin.layout')

@section('title', 'Create Product')
@section('header', 'Create New Product')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h1 class="text-xl font-semibold">Create New Product</h1>
        </div>
        
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-6" id="product-form">
            @csrf
            
            <!-- Debug Info -->
            @if(session()->has('debug'))
                <div class="mb-4 p-4 bg-blue-100 border border-blue-300 rounded">
                    <h4>Debug Info:</h4>
                    <pre>{{ session('debug') }}</pre>
                </div>
            @endif
            
            <!-- Validation Errors -->
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-300 rounded">
                    <h4>Please fix the following errors:</h4>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <!-- Basic Information -->
            <div class="mb-8">
                <h2 class="text-lg font-medium mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" required
                               value="{{ old('name') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">
                            SKU <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="sku" id="sku" required
                               value="{{ old('sku') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        @error('sku')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="short_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Short Description
                        </label>
                        <textarea name="short_description" id="short_description" rows="2"
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">{{ old('short_description') }}</textarea>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="description" id="description" rows="4" required
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Pricing -->
            <div class="mb-8">
                <h2 class="text-lg font-medium mb-4">Pricing</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Selling Price (BDT) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price" id="price" step="0.01" min="0" required
                               value="{{ old('price') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="compare_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Compare Price (BDT)
                        </label>
                        <input type="number" name="compare_price" id="compare_price" step="0.01" min="0"
                               value="{{ old('compare_price') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        @error('compare_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="cost_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Cost Price (BDT)
                        </label>
                        <input type="number" name="cost_price" id="cost_price" step="0.01" min="0"
                               value="{{ old('cost_price') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        @error('cost_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Inventory -->
            <div class="mb-8">
                <h2 class="text-lg font-medium mb-4">Inventory</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                            Stock Quantity <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="stock_quantity" id="stock_quantity" min="0" required
                               value="{{ old('stock_quantity', 0) }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        @error('stock_quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                            Weight (kg)
                        </label>
                        <input type="number" name="weight" id="weight" step="0.01" min="0"
                               value="{{ old('weight') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        @error('weight')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="track_stock" id="track_stock" value="1"
                               {{ old('track_stock') ? 'checked' : '' }}
                               class="mr-2">
                        <label for="track_stock" class="text-sm font-medium text-gray-700">
                            Track Stock
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Categories -->
            <div class="mb-8">
                <h2 class="text-lg font-medium mb-4">Categories</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Category <span class="text-red-500">*</span>
                        </label>
                        <select name="category_id" id="category_id" required
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Brand
                        </label>
                        <select name="brand_id" id="brand_id"
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Product Images -->
            <div class="mb-8">
                <h2 class="text-lg font-medium mb-4">Product Images</h2>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                    <div class="text-center">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                        <p class="text-sm text-gray-500">PNG, JPG, GIF up to 2MB each</p>
                        <input type="file" name="images[]" id="images" multiple accept="image/*"
                               class="hidden">
                        <button type="button" onclick="document.getElementById('images').click()"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                            Choose Files
                        </button>
                    </div>
                    <div id="image-preview" class="grid grid-cols-3 gap-4 mt-4"></div>
                </div>
            </div>
            
            <!-- Options -->
            <div class="mb-8">
                <h2 class="text-lg font-medium mb-4">Options</h2>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked
                               class="mr-2">
                        <label for="is_active" class="text-sm font-medium text-gray-700">
                            Active (Product will be visible on storefront)
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1"
                               class="mr-2">
                        <label for="is_featured" class="text-sm font-medium text-gray-700">
                            Featured (Show on homepage)
                        </label>
                    </div>
                    
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                            Sort Order
                        </label>
                        <input type="number" name="sort_order" id="sort_order" min="0"
                               value="{{ old('sort_order', 0) }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                    </div>
                </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.products.index') }}" 
                   class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                    Create Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            // Check file size (2MB limit)
            if (file.size > 2 * 1024 * 1024) {
                alert(`File "${file.name}" is too large. Maximum size is 2MB.`);
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-32 object-cover rounded">
                    <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
                        ${index + 1}
                    </div>
                    <div class="absolute bottom-2 left-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
                        ${file.name}
                    </div>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        } else {
            alert(`File "${file.name}" is not a valid image.`);
        }
    });
});

// Add form submission debugging
document.querySelector('form').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('images');
    const files = fileInput.files;
    
    console.log('Form submitting with files:', files.length);
    for (let i = 0; i < files.length; i++) {
        console.log(`File ${i}:`, files[i].name, files[i].size, files[i].type);
    }
});
</script>
@endsection
