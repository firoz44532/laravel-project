@extends('frontend.layout')

@section('title', 'Home - Mega Sale Up To 70% OFF | ' . \App\Services\SettingsService::get('site_name', 'ShopBD'))

@push('meta-tags')
    <!-- Homepage-specific SEO meta tags -->
    <meta name="description" content="{{ \App\Services\SettingsService::get('site_description', 'Shop online for electronics, fashion, groceries and more. Best prices in Bangladesh with fast delivery. Mega sale up to 70% OFF!') }}">
    <meta name="keywords" content="online shopping, Bangladesh, e-commerce, mega sale, electronics, fashion, groceries, best prices, fast delivery, up to 70% off">
    
    <!-- Homepage Open Graph tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Mega Sale Up To 70% OFF - {{ \App\Services\SettingsService::get('site_name', 'ShopBD') }}">
    <meta property="og:description" content="{{ \App\Services\SettingsService::get('site_description', 'Shop online for electronics, fashion, groceries and more. Best prices in Bangladesh with fast delivery. Mega sale up to 70% OFF!') }}">
    @if(\App\Services\SettingsService::get('site_logo'))
        <meta property="og:image" content="{{ asset(\App\Services\SettingsService::get('site_logo')) }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif
    
@endpush

@section('content')
<!-- Hero Banner Section -->
@if($heroBanners && $heroBanners->count() > 0)
    @foreach($heroBanners as $banner)
        @if($banner->link)
            <a href="{{ $banner->link }}" class="block">
        @endif
        <section class="relative bg-gradient-to-r from-orange-500 to-orange-600 text-white @if($banner->link) hover:from-orange-600 hover:to-orange-700 transition-colors cursor-pointer @endif">
            <div class="container mx-auto px-4 py-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div class="order-2 md:order-1 relative">
                        @if($banner->image)
                            <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" class="rounded-lg shadow-2xl w-full h-96 object-cover">
                        @else
                            <img src="{{ asset('images/placeholder-product.jpg') }}" alt="{{ $banner->title }}" class="rounded-lg shadow-2xl w-full h-96 object-cover">
                        @endif
                        <!-- Left Corner Badge -->
                        <div class="absolute -top-4 -left-4 bg-red-500 text-white px-4 py-2 rounded-full font-bold animate-pulse">
                            Limited Time!
                        </div>
                        <!-- Right Corner Badge -->
                        <div class="absolute -top-4 -right-4 bg-red-500 text-white px-4 py-2 rounded-full font-bold animate-pulse">
                            Limited Time!
                        </div>
                    </div>
                    <div class="order-1 md:order-2">
                        <h1 class="text-4xl md:text-6xl font-bold mb-4">{{ $banner->title }}</h1>
                        @if($banner->description)
                            <p class="text-lg mb-6">{{ $banner->description }}</p>
                        @endif
                        <div class="flex space-x-4">
                            @if($banner->link)
                                <a href="{{ $banner->link }}" class="bg-white text-orange-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition" onclick="event.stopPropagation()">
                                    Shop Now
                                </a>
                            @endif
                            <a href="{{ route('products.index') }}" class="border-2 border-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-orange-600 transition">
                                View Deals
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @if($banner->link)
            </a>
        @endif
    @endforeach
@else
    <!-- Fallback hardcoded banner -->
    <section class="relative bg-gradient-to-r from-orange-500 to-orange-600 text-white">
        <div class="container mx-auto px-4 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">Mega Sale</h1>
                    <h2 class="text-2xl md:text-3xl mb-4">Up To 70% OFF</h2>
                    <p class="text-lg mb-6">Biggest sale of the year! Grab amazing deals on electronics, fashion, groceries and more.</p>
                    <div class="flex space-x-4">
                        <a href="{{ route('products.index') }}" class="bg-white text-orange-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition">
                            Shop Now
                        </a>
                        <a href="#" class="border-2 border-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-orange-600 transition">
                            View Deals
                        </a>
                    </div>
                </div>
                <div class="relative">
                    <img src="{{ asset('images/placeholder-product.jpg') }}" alt="Sale Banner" class="rounded-lg shadow-2xl w-full h-96 object-cover">
                    <div class="absolute -top-4 -right-4 bg-red-500 text-white px-4 py-2 rounded-full font-bold animate-pulse">
                        Limited Time!
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

<!-- Categories Section -->
<section class="py-3">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-6">Your Favorite Items Are Here</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @if($categories)
                @foreach($categories as $category)
                    <a href="{{ route('products.category', $category->slug) }}" class="bg-white rounded-lg p-6 text-center hover:shadow-lg transition">
                        @if($category->image)
                            <img src="{{ asset('storage/' . $category->image) }}" 
                                 alt="{{ $category->name }} category - Shop for {{ $category->name }} products" 
                                 class="w-16 h-16 object-cover rounded-full mx-auto mb-3">
                        @else
                            <div class="w-16 h-16 bg-gray-200 rounded-full mx-auto mb-3 flex items-center justify-center">
                                <i class="fas fa-box text-2xl text-gray-500"></i>
                            </div>
                        @endif
                        <h3 class="font-semibold">{{ $category->name }}</h3>
                        @if($category->is_featured)
                            <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full mt-2">
                                <i class="fas fa-star mr-1"></i>Featured
                            </span>
                        @endif
                    </a>
                @endforeach
            @endif
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="pt-6 pb-12 bg-gray-100">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold">Featured Products</h2>
            <a href="{{ route('products.index') }}" class="text-primary hover:underline">View All →</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @isset($featuredProducts)
                @if($featuredProducts->count() > 0)
                @foreach($featuredProducts as $product)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                        <div class="relative">
                            <a href="{{ route('products.show', $product->slug) }}" class="block">
                                @if($product->primaryImage)
                                    <img src="{{ $product->primaryImage->image_url }}" 
                                         alt="{{ $product->name }} - Buy {{ $product->name }} online at best price in Bangladesh" 
                                         class="w-full h-48 object-cover rounded-t-lg hover:opacity-90 transition">
                                @else
                                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OTk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPjIwMHgyMDA8L3RleHQ+PC9zdmc+" 
                                         alt="{{ $product->name }} - Product image placeholder" 
                                         class="w-full h-48 object-cover rounded-t-lg hover:opacity-90 transition">
                                @endif
                            </a>
                            @if($product->discount_percentage > 0)
                                <span class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-xs font-bold">
                                    -{{ $product->discount_percentage }}%
                                </span>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-sm mb-2 line-clamp-2">
                                <a href="{{ route('products.show', $product->slug) }}" class="text-gray-900 hover:text-primary">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <div class="flex items-center mb-2">
                                <span class="text-primary font-bold">৳{{ number_format($product->price, 2) }}</span>
                                @if($product->compare_price)
                                    <span class="text-gray-400 line-through text-sm ml-2">৳{{ number_format($product->compare_price, 2) }}</span>
                                @endif
                            </div>
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <i class="fas fa-star text-yellow-400"></i>
                                <span class="ml-1">{{ number_format($product->average_rating, 1) }} ({{ $product->review_count }})</span>
                            </div>
                            <div class="flex gap-2">
                                <button class="flex-1 bg-primary text-white py-2 rounded hover:bg-orange-600 transition add-to-cart-btn" 
                                        data-product-id="{{ $product->id }}" 
                                        data-product-name="{{ $product->name }}">
                                    Add to Cart
                                </button>
                                <button class="buy-now-btn flex-1 bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition" 
                                        data-product-slug="{{ $product->slug }}">
                                    Buy Now
                                </button>
                                <button class="wishlist-btn p-2 border border-gray-300 rounded hover:bg-gray-50 transition" 
                                        data-product-id="{{ $product->id }}"
                                        data-is-in-wishlist="{{ Auth::check() && \App\Models\Wishlist::isInWishlist($product->id) ? 'true' : 'false' }}">
                                    <i class="wishlist-icon {{ Auth::check() && \App\Models\Wishlist::isInWishlist($product->id) ? 'fas text-red-500' : 'far text-gray-400' }} fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
                @else
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-500">No featured products available</p>
                    </div>
                @endif
            @else
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500">Featured products not loaded</p>
                </div>
            @endisset
        </div>
    </div>
</section>

<!-- Best Sellers Section -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold">Best Sellers</h2>
            <a href="{{ route('products.index') }}" class="text-primary hover:underline">View All →</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @if($bestSellingProducts->count() > 0)
                @foreach($bestSellingProducts as $product)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                        <div class="relative">
                            <a href="{{ route('products.show', $product->slug) }}" class="block">
                                @if($product->primaryImage)
                                    <img src="{{ $product->primaryImage->image_url }}" 
                                         alt="{{ $product->name }} - Buy {{ $product->name }} online at best price in Bangladesh" 
                                         class="w-full h-48 object-cover rounded-t-lg hover:opacity-90 transition">
                                @else
                                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OTk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPjIwMHgyMDA8L3RleHQ+PC9zdmc+" 
                                         alt="{{ $product->name }} - Product image placeholder" 
                                         class="w-full h-48 object-cover rounded-t-lg hover:opacity-90 transition">
                                @endif
                            </a>
                            <span class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded text-xs font-bold">
                                Best Seller
                            </span>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-sm mb-2 line-clamp-2">
                                <a href="{{ route('products.show', $product->slug) }}" class="text-gray-900 hover:text-primary">
                                    {{ $product->name }}
                                </a>
                            </h3>
                            <div class="flex items-center mb-2">
                                <span class="text-primary font-bold">৳{{ number_format($product->price, 2) }}</span>
                                @if($product->compare_price)
                                    <span class="text-gray-400 line-through text-sm ml-2">৳{{ number_format($product->compare_price, 2) }}</span>
                                @endif
                            </div>
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <i class="fas fa-star text-yellow-400"></i>
                                <span class="ml-1">{{ number_format($product->average_rating, 1) }} ({{ $product->review_count }})</span>
                            </div>
                            <div class="flex gap-2">
                                <button class="flex-1 bg-primary text-white py-2 rounded hover:bg-orange-600 transition add-to-cart-btn" 
                                        data-product-id="{{ $product->id }}" 
                                        data-product-name="{{ $product->name }}">
                                    Add to Cart
                                </button>
                                <button class="buy-now-btn flex-1 bg-blue-500 text-white py-2 rounded hover:bg-blue-600 transition" 
                                        data-product-slug="{{ $product->slug }}">
                                    Buy Now
                                </button>
                                <button class="wishlist-btn p-2 border border-gray-300 rounded hover:bg-gray-50 transition" 
                                        data-product-id="{{ $product->id }}"
                                        data-is-in-wishlist="{{ Auth::check() && \App\Models\Wishlist::isInWishlist($product->id) ? 'true' : 'false' }}">
                                    <i class="wishlist-icon {{ Auth::check() && \App\Models\Wishlist::isInWishlist($product->id) ? 'fas text-red-500' : 'far text-gray-400' }} fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500">No best selling products available</p>
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Brands Section -->
<section class="py-12 bg-gray-100">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-8">Top Brands</h2>
        <div class="grid grid-cols-3 md:grid-cols-6 gap-8">
            @if($brands)
                @foreach($brands as $brand)
                    <div class="bg-white rounded-lg p-4 flex items-center justify-center hover:shadow-lg transition">
                        @if($brand->logo)
                            <img src="{{ $brand->logo }}" alt="{{ $brand->name }}" class="h-12">
                        @else
                            <div class="h-12 w-12 bg-gray-200 rounded flex items-center justify-center">
                                <span class="text-xs font-semibold">{{ $brand->name }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-12 bg-primary text-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
            <div>
                <i class="fas fa-truck text-4xl mb-4"></i>
                <h3 class="font-semibold mb-2">Fast Delivery</h3>
                <p class="text-sm">Quick delivery across Bangladesh</p>
            </div>
            <div>
                <i class="fas fa-shield-alt text-4xl mb-4"></i>
                <h3 class="font-semibold mb-2">Secure Payment</h3>
                <p class="text-sm">100% secure payment methods</p>
            </div>
            <div>
                <i class="fas fa-undo text-4xl mb-4"></i>
                <h3 class="font-semibold mb-2">Easy Returns</h3>
                <p class="text-sm">7 days return policy</p>
            </div>
            <div>
                <i class="fas fa-headset text-4xl mb-4"></i>
                <h3 class="font-semibold mb-2">24/7 Support</h3>
                <p class="text-sm">Dedicated customer support</p>
            </div>
        </div>
    </div>
</section>

<!-- Become a Merchant CTA Section -->
<section class="py-16 bg-gradient-to-r from-purple-600 to-blue-600 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold mb-4">Want to Sell Your Products?</h2>
        <p class="text-xl mb-8">Join thousands of merchants and grow your business with us</p>
        @auth
            @if(!Auth::user()->merchant)
                <a href="{{ route('merchant.register') }}" class="bg-white text-purple-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition inline-block">
                    <i class="fas fa-store mr-2"></i> Become a Merchant
                </a>
            @else
                <a href="{{ route('merchant.dashboard') }}" class="bg-white text-purple-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition inline-block">
                    <i class="fas fa-tachometer-alt mr-2"></i> Go to Merchant Dashboard
                </a>
            @endif
        @else
            <div class="space-x-4">
                <a href="{{ route('register') }}" class="bg-white text-purple-600 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition inline-block">
                    <i class="fas fa-user-plus mr-2"></i> Sign Up First
                </a>
                <a href="{{ route('login') }}" class="border-2 border-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-purple-600 transition inline-block">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </a>
            </div>
        @endauth
    </div>
</section>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wishlist functionality for featured products
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            toggleWishlist(productId, this);
        });
    });
    
    // Buy Now functionality for featured products
    document.querySelectorAll('.buy-now-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productSlug = this.dataset.productSlug;
            window.location.href = `/products/${productSlug}`;
        });
    });
    
    // Add to Cart functionality for featured products
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
                    showNotification(data.message, 'success');
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
