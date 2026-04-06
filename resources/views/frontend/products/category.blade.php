@extends('frontend.layout')

@section('title', $category->name . ' - Products')
@section('header', $category->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Category Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $category->name }}</h1>
                    @if($category->description)
                        <p class="text-gray-600 mt-2">{{ $category->description }}</p>
                    @endif
                    <div class="flex items-center space-x-4 mt-4">
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-box mr-1"></i>
                            {{ $products->total() }} Products
                        </span>
                        @if($category->is_featured)
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">
                                <i class="fas fa-star mr-1"></i>Featured Category
                            </span>
                        @endif
                    </div>
                </div>
                @if($category->image)
                    <img src="{{ asset('storage/' . $category->image) }}" 
                         alt="{{ $category->name }}" 
                         class="w-24 h-24 object-cover rounded-lg">
                @endif
            </div>
        </div>
    </div>

    <!-- Subcategories (if any) -->
    @if($category->children && $category->children->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Subcategories</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($category->children->where('is_active', true) as $subcategory)
                        <a href="{{ route('products.category', $subcategory->slug) }}" 
                           class="text-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            @if($subcategory->image)
                                <img src="{{ asset('storage/' . $subcategory->image) }}" 
                                     alt="{{ $subcategory->name }}" 
                                     class="w-full h-16 object-cover rounded mb-2">
                            @else
                                <div class="w-full h-16 bg-gray-200 rounded mb-2 flex items-center justify-center">
                                    <i class="fas fa-folder text-gray-400"></i>
                                </div>
                            @endif
                            <h3 class="text-sm font-medium text-gray-900">{{ $subcategory->name }}</h3>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $subcategory->products()->where('is_active', true)->count() }} products
                            </p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Products Grid -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Products in {{ $category->name }}</h2>
            <div class="flex items-center space-x-4">
                <label class="text-sm font-medium text-gray-700">Sort by:</label>
                <select class="px-3 py-2 border rounded-lg focus:outline-none focus:border-primary text-sm" onchange="window.location.href='?sort='+this.value">
                    <option value="">Latest</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name: A-Z</option>
                    <option value="price-low" {{ request('sort') == 'price-low' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price-high" {{ request('sort') == 'price-high' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                    <option value="reviews" {{ request('sort') == 'reviews' ? 'selected' : '' }}>Most Reviews</option>
                    <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Featured</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                </select>
            </div>
        </div>

        @if($products->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    @include('components.daraz-product-card', ['product' => $product])
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
                <p class="text-gray-500 mb-6">This category doesn't have any products available at the moment.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                </a>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wishlist functionality for product grid
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            toggleWishlist(productId, this);
        });
    });
    
    // Buy Now functionality for product grid
    document.querySelectorAll('.buy-now-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productSlug = this.dataset.productSlug;
            window.location.href = `/products/${productSlug}`;
        });
    });
    
    function toggleWishlist(productId, button) {
        // Show loading state
        const icon = button.querySelector('.wishlist-icon');
        const originalClass = icon.className;
        icon.className = 'fas fa-spinner fa-spin wishlist-icon';
        
        fetch('/wishlist/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update button state
                button.dataset.isInWishlist = data.is_in_wishlist ? 'true' : 'false';
                
                // Update icon
                if (data.is_in_wishlist) {
                    icon.className = 'fas fa-heart wishlist-icon text-red-500';
                } else {
                    icon.className = 'far fa-heart wishlist-icon text-gray-400';
                }
                
                // Show notification
                showNotification(data.message, data.is_in_wishlist ? 'success' : 'info');
                
                // Update wishlist count if exists
                if (typeof updateWishlistCount === 'function') {
                    updateWishlistCount(data.wishlist_count);
                }
            } else {
                // Restore original icon
                icon.className = originalClass;
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Wishlist error:', error);
            // Restore original icon
            icon.className = originalClass;
            showNotification('Error updating wishlist', 'error');
        });
    }
    
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
        
        // Set color based on type
        const colors = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            info: 'bg-blue-500 text-white',
            warning: 'bg-yellow-500 text-white'
        };
        
        notification.className += ' ' + colors[type];
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
});
</script>
@endsection
