@extends('frontend.layout')

@section('title', 'My Wishlist')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-6">My Account</h2>
                <nav class="space-y-2">
                    <a href="{{ route('account.dashboard') }}" 
                       class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <a href="{{ route('account.profile') }}" 
                       class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-user mr-2"></i>Profile
                    </a>
                    <a href="{{ route('account.addresses') }}" 
                       class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-map-marker-alt mr-2"></i>Addresses
                    </a>
                    <a href="{{ route('account.orders') }}" 
                       class="block px-4 py-2 rounded-lg hover:bg-gray-100">
                        <i class="fas fa-shopping-bag mr-2"></i>Orders
                    </a>
                    <a href="{{ route('wishlist.index') }}" 
                       class="block px-4 py-2 rounded-lg bg-primary text-white">
                        <i class="fas fa-heart mr-2"></i>Wishlist
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="block">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 rounded-lg hover:bg-gray-100 text-left">
                            <i class="fas fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h1 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-heart text-red-500 mr-3"></i>
                        My Wishlist
                        <span class="ml-2 text-gray-600">({{ $wishlistItems->count() }} items)</span>
                    </h1>
                </div>
                
                <div class="p-6">
                    @forelse($wishlistItems as $item)
                        <div class="border rounded-lg p-4 mb-4 hover:bg-gray-50 transition">
                            <div class="flex items-start space-x-4">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    @if($item->product->primaryImage)
                                        <img src="{{ $item->product->primaryImage->image_url }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="w-20 h-20 object-cover rounded">
                                    @else
                                        <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Product Details -->
                                <div class="flex-1">
                                    <h3 class="font-semibold text-lg line-clamp-2">
                                        <a href="{{ route('products.show', $item->product->slug) }}" 
                                           class="text-gray-900 hover:text-primary">
                                            {{ $item->product->name }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500">{{ $item->product->category->name ?? 'No Category' }}</p>
                                    
                                    <!-- Price -->
                                    <div class="flex items-center space-x-2 mt-2">
                                        <span class="text-primary font-bold text-lg">৳{{ number_format($item->product->price, 2) }}</span>
                                        @if($item->product->compare_price)
                                            <span class="text-gray-400 line-through text-sm">৳{{ number_format($item->product->compare_price, 2) }}</span>
                                        @endif
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="flex items-center space-x-2 mt-4">
                                        <button onclick="addToCart({{ $item->product->id }})" 
                                                class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition text-sm">
                                            <i class="fas fa-shopping-cart mr-2"></i>Add to Cart
                                        </button>
                                        <button onclick="removeFromWishlist({{ $item->product->id }})" 
                                                class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-red-50 hover:text-red-700 transition text-sm">
                                            <i class="fas fa-trash mr-2"></i>Remove
                                        </button>
                                        <button onclick="toggleWishlist({{ $item->product->id }})" 
                                                class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-yellow-50 hover:text-yellow-700 transition text-sm">
                                            <i class="fas fa-heart mr-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <i class="fas fa-heart text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">Your wishlist is empty</h3>
                            <p class="text-gray-500 mb-4">Start adding products you love!</p>
                            <a href="{{ route('products.index') }}" 
                               class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                                Browse Products
                            </a>
                        </div>
                    @endforelse
                </div>
                
                <!-- Clear Wishlist Button -->
                @if($wishlistItems->count() > 0)
                    <div class="p-6 border-t">
                        <button onclick="clearWishlist()" 
                                class="w-full bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-trash mr-2"></i>Clear Wishlist
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function addToCart(productId) {
    // Show loading state
    event.target.textContent = 'Adding...';
    event.target.disabled = true;
    
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
        event.target.innerHTML = '<i class="fas fa-shopping-cart mr-2"></i>Add to Cart';
        event.target.disabled = false;
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        showNotification('Error adding product to cart', 'error');
        
        // Restore button state
        event.target.innerHTML = '<i class="fas fa-shopping-cart mr-2"></i>Add to Cart';
        event.target.disabled = false;
    });
}

function removeFromWishlist(productId) {
    fetch('/wishlist/remove', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateWishlistCount(data.wishlist_count);
            showNotification(data.message, 'success');
            // Remove from wishlist view if on wishlist page
            const element = document.querySelector(`[onclick="removeFromWishlist(${productId})"]`);
            if (element) {
                element.closest('.border').remove();
            }
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating wishlist', 'error');
    });
}

function toggleWishlist(productId) {
    fetch('/wishlist/toggle', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateWishlistCount(data.wishlist_count);
            showNotification(data.message, 'success');
            
            // Update button state
            const button = document.querySelector(`[onclick="toggleWishlist(${productId})"]`);
            if (button) {
                const icon = button.querySelector('i');
                if (data.is_in_wishlist) {
                    icon.className = 'fas fa-heart';
                } else {
                    icon.className = 'far fa-heart';
                }
            }
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating wishlist', 'error');
    });
}

function clearWishlist() {
    if (confirm('Are you sure you want to clear your entire wishlist?')) {
        fetch('/wishlist/clear', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error clearing wishlist');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error clearing wishlist');
        });
    }
}

function updateWishlistCount(count) {
    const wishlistCountElement = document.querySelector('.text-gray-600');
    if (wishlistCountElement) {
        wishlistElement.textContent = `(${count}) items`;
    }
}
</script>
@endsection
