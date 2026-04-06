@extends('frontend.layout')

@section('title', 'Products')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">All Products</h1>
        <p class="text-gray-600 mt-2">Discover our wide range of products</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Filters Sidebar -->
        <div class="w-full lg:w-64 flex-shrink-0">
            <form id="filter-form" method="GET" class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-lg mb-4">Filters</h3>
                
                <!-- Search -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Search products..."
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                </div>

                <!-- Categories -->
                <div class="mb-6">
                    <h4 class="font-medium mb-3">Categories</h4>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($categories as $category)
                            <label class="flex items-center">
                                <input type="checkbox" name="category[]" value="{{ $category->slug }}"
                                       {{ in_array($category->slug, (array) request('category')) ? 'checked' : '' }}
                                       class="mr-2 text-primary focus:ring-primary">
                                <span class="text-sm">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Brands -->
                <div class="mb-6">
                    <h4 class="font-medium mb-3">Brands</h4>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($brands->take(10) as $brand)
                            <label class="flex items-center">
                                <input type="checkbox" name="brand[]" value="{{ $brand->slug }}"
                                       {{ in_array($brand->slug, (array) request('brand')) ? 'checked' : '' }}
                                       class="mr-2 text-primary focus:ring-primary">
                                <span class="text-sm">{{ $brand->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Price Range -->
                <div class="mb-6">
                    <h4 class="font-medium mb-3">Price Range</h4>
                    <div class="space-y-3">
                        <input type="number" name="min_price" placeholder="Min Price"
                               value="{{ request('min_price') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary text-sm">
                        <input type="number" name="max_price" placeholder="Max Price"
                               value="{{ request('max_price') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary text-sm">
                    </div>
                </div>

                <!-- Rating Filter -->
                <div class="mb-6">
                    <h4 class="font-medium mb-3">Rating</h4>
                    <div class="space-y-2">
                        @for($i = 4; $i >= 1; $i--)
                            <label class="flex items-center">
                                <input type="checkbox" name="rating" value="{{ $i }}"
                                       {{ request('rating') == $i ? 'checked' : '' }}
                                       class="mr-2 text-primary focus:ring-primary">
                                <span class="text-sm">
                                    @for($j = 1; $j <= 5; $j++)
                                        <i class="fas fa-star {{ $j <= $i ? 'text-yellow-400' : 'text-gray-300' }} text-xs"></i>
                                    @endfor
                                    & up
                                </span>
                            </label>
                        @endfor
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" 
                            class="flex-1 bg-primary text-white py-2 rounded-lg hover:bg-orange-600 transition">
                        Apply Filters
                    </button>
                    <a href="{{ route('products.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Products Grid -->
        <div class="flex-1">
            <!-- Active Filters -->
            @if(request()->hasAny(['search', 'category', 'brand', 'min_price', 'max_price', 'rating']))
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-wrap gap-2">
                            <span class="text-sm font-medium text-blue-900">Active Filters:</span>
                            @if(request('search'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Search: {{ request('search') }}
                                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-2 text-blue-600 hover:text-blue-800">×</a>
                                </span>
                            @endif
                            @if(request('category'))
                                @foreach((array) request('category') as $cat)
                                    @php $category = \App\Models\Category::where('slug', $cat)->first() @endphp
                                    @if($category)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $category->name }}
                                            <a href="{{ request()->fullUrlWithQuery(['category' => array_diff((array) request('category'), [$cat])]) }}" class="ml-2 text-blue-600 hover:text-blue-800">×</a>
                                        </span>
                                    @endif
                                @endforeach
                            @endif
                            @if(request('brand'))
                                @foreach((array) request('brand') as $br)
                                    @php $brand = \App\Models\Brand::where('slug', $br)->first() @endphp
                                    @if($brand)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $brand->name }}
                                            <a href="{{ request()->fullUrlWithQuery(['brand' => array_diff((array) request('brand'), [$br])]) }}" class="ml-2 text-blue-600 hover:text-blue-800">×</a>
                                        </span>
                                    @endif
                                @endforeach
                            @endif
                            @if(request('min_price') || request('max_price'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Price: @if(request('min_price')) ৳{{ request('min_price') }} @endif - @if(request('max_price')) ৳{{ request('max_price') }} @endif
                                    <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}" class="ml-2 text-blue-600 hover:text-blue-800">×</a>
                                </span>
                            @endif
                            @if(request('rating'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ request('rating') }}+ Stars
                                    <a href="{{ request()->fullUrlWithQuery(['rating' => null]) }}" class="ml-2 text-blue-600 hover:text-blue-800">×</a>
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('products.index') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Clear All
                        </a>
                    </div>
                </div>
            @endif

            <!-- Toolbar -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <form method="GET" class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-600">
                        Showing {{ $products->count() }} of {{ $products->total() }} products
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="text-sm font-medium text-gray-700">Sort by:</label>
                        <select name="sort" onchange="this.form.submit()"
                                class="px-3 py-2 border rounded-lg focus:outline-none focus:border-primary text-sm">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Highest Rated</option>
                            <option value="reviews" {{ request('sort') == 'reviews' ? 'selected' : '' }}>Most Reviews</option>
                            <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Featured</option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Popular</option>
                        </select>
                    </div>
                    
                    <!-- Preserve other filter parameters -->
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('category'))
                        @foreach((array) request('category') as $cat)
                            <input type="hidden" name="category[]" value="{{ $cat }}">
                        @endforeach
                    @endif
                    @if(request('brand'))
                        @foreach((array) request('brand') as $br)
                            <input type="hidden" name="brand[]" value="{{ $br }}">
                        @endforeach
                    @endif
                    @if(request('min_price'))
                        <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                    @endif
                    @if(request('max_price'))
                        <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                    @endif
                    @if(request('rating'))
                        <input type="hidden" name="rating" value="{{ request('rating') }}">
                    @endif
                </form>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($products as $product)
                    @include('components.daraz-product-card', ['product' => $product])
                @empty
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No products found</h3>
                        <p class="text-gray-500">Try adjusting your filters or search terms</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Auto-submit search on Enter key with debounce
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    const priceInputs = document.querySelectorAll('input[name="min_price"], input[name="max_price"]');
    
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 1000); // Wait 1 second after user stops typing
        });
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout);
                this.form.submit();
            }
        });
    }
    
    // Auto-submit price filters with debounce
    priceInputs.forEach(input => {
        let priceTimeout;
        input.addEventListener('input', function() {
            clearTimeout(priceTimeout);
            priceTimeout = setTimeout(() => {
                this.form.submit();
            }, 800); // Wait 0.8 seconds after user stops typing
        });
    });
    
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
    
    // Add to Cart functionality for product grid
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            
            // Show loading state
            const originalText = this.textContent;
            this.textContent = 'Adding...';
            this.disabled = true;
            
            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1 // Default quantity for quick add
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCartPopup(data.product);
                    updateCartCount(data.cart_count);
                } else {
                    showNotification(data.message, 'error');
                }
                
                // Restore button state
                this.textContent = originalText;
                this.disabled = false;
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                showNotification('Error adding product to cart', 'error');
                
                // Restore button state
                this.textContent = originalText;
                this.disabled = false;
            });
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
