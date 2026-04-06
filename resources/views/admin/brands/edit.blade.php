@extends('admin.layout')

@section('title', 'Edit Brand')
@section('header', 'Edit Brand')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h1 class="text-xl font-semibold">Edit Brand</h1>
        </div>
        
        <form action="{{ route('admin.brands.update', $brand->id) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Brand Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required
                               value="{{ $brand->name }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                            Brand Slug <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="slug" name="slug" required
                               value="{{ $brand->slug }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        <p class="text-xs text-gray-500 mt-1">URL-friendly version of the brand name</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Brand Description
                        </label>
                        <textarea id="description" name="description" rows="4"
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">{{ $brand->description }}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Brand Settings -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Brand Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                            Sort Order
                        </label>
                        <input type="number" id="sort_order" name="sort_order" min="0"
                               value="{{ $brand->sort_order }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                    </div>
                    
                    <div>
                        <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                            Brand Logo
                        </label>
                        <input type="file" id="logo" name="logo" accept="image/*"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                               {{ $brand->is_active ? 'checked' : '' }}
                               class="mr-2">
                        <label for="is_active" class="text-sm font-medium text-gray-700">
                            Active (Brand will be visible on storefront)
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" id="is_featured" name="is_featured" value="1"
                               {{ $brand->is_featured ? 'checked' : '' }}
                               class="mr-2">
                        <label for="is_featured" class="text-sm font-medium text-gray-700">
                            Featured (Show on homepage)
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Logo Preview -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Logo Preview</h2>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                    <div class="text-center">
                        <i class="fas fa-image text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 mb-2">Current logo</p>
                    </div>
                    <div id="logo-preview" class="mt-4">
                        @if($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}" 
                                 alt="{{ $brand->name }}" 
                                 class="w-32 h-32 object-cover rounded mx-auto">
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.brands.index') }}" 
                   class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                    Update Brand
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('logo').addEventListener('change', function(e) {
    const preview = document.getElementById('logo-preview');
    preview.innerHTML = '';
    
    const file = e.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <img src="${e.target.result}" class="w-32 h-32 object-cover rounded mx-auto">
            `;
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('name').addEventListener('input', function() {
    const slug = this.value.toLowerCase().replace(/[^a-z0-9\s-]/g, '-');
    document.getElementById('slug').value = slug;
});
</script>
@endsection
