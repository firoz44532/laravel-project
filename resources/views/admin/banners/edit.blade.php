@extends('admin.layout')

@section('title', 'Edit Banner')
@section('header', 'Edit Banner')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b">
            <h1 class="text-xl font-semibold">Edit Banner</h1>
        </div>
        
        <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Banner Information -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Banner Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                            Banner Title <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" required
                               value="{{ $banner->title }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="position" class="block text-sm font-medium text-gray-700 mb-2">
                            Position <span class="text-red-500">*</span>
                        </label>
                        <select id="position" name="position" required
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                            <option value="hero" {{ $banner->position === 'hero' ? 'selected' : '' }}>Hero Section</option>
                            <option value="sidebar" {{ $banner->position === 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                            <option value="footer" {{ $banner->position === 'footer' ? 'selected' : '' }}>Footer</option>
                            <option value="category" {{ $banner->position === 'category' ? 'selected' : '' }}>Category Page</option>
                        </select>
                        @error('position')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">{{ $banner->description }}</textarea>
                    </div>
                </div>
            </div>
            
            <!-- Banner Link -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Banner Link</h2>
                <div>
                    <label for="link" class="block text-sm font-medium text-gray-700 mb-2">
                        Link URL
                    </label>
                    <input type="url" id="link" name="link"
                           value="{{ $banner->link }}"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary"
                           placeholder="https://example.com">
                    <p class="text-xs text-gray-500 mt-1">URL where users will be redirected when clicking the banner</p>
                    @error('link')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Banner Image -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Banner Image</h2>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                    <div class="text-center">
                        <i class="fas fa-image text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                        <p class="text-sm text-gray-500">PNG, JPG, GIF up to 2MB</p>
                        <input type="file" name="image" id="image" accept="image/*"
                               class="hidden">
                        <button type="button" onclick="document.getElementById('image').click()"
                                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                            Choose File
                        </button>
                    </div>
                    <div id="image-preview" class="mt-4">
                        @if($banner->image)
                            <img src="{{ asset('storage/' . $banner->image) }}" 
                                 alt="{{ $banner->title }}" 
                                 class="w-full h-48 object-cover rounded">
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Display Settings -->
            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Display Settings</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                            Sort Order
                        </label>
                        <input type="number" id="sort_order" name="sort_order" min="0"
                               value="{{ $banner->sort_order }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                    </div>
                    
                    <div>
                        <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date
                        </label>
                        <input type="date" id="starts_at" name="starts_at"
                               value="{{ $banner->starts_at ? $banner->starts_at->format('Y-m-d') : '' }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        @error('starts_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="expires_at" class="block text-sm font-medium text-gray-700 mb-2">
                            Expiry Date
                        </label>
                        <input type="date" id="expires_at" name="expires_at"
                               value="{{ $banner->expires_at ? $banner->expires_at->format('Y-m-d') : '' }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                        @error('expires_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               {{ $banner->is_active ? 'checked' : '' }}
                               class="mr-2">
                        <label for="is_active" class="text-sm font-medium text-gray-700">
                            Active (Banner will be visible on storefront)
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.banners.index') }}" 
                   class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                    Update Banner
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('image').addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-48 object-cover rounded">
                    <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
                        ${index + 1}
                    </div>
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endsection
