@extends('frontend.layout')

@section('title', 'Product Comparison')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-balance-scale text-primary mr-3"></i>
                    Product Comparison
                </h1>
                <button onclick="clearComparison()" 
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-trash mr-2"></i>Clear All
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Attribute
                        </th>
                        @foreach($products as $product)
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex flex-col items-center">
                                    @if($product->primaryImage)
                                        <img src="{{ $product->primaryImage->image_url }}" 
                                             alt="{{ $product->name }}" 
                                             class="w-16 h-16 object-cover rounded mb-2">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center mb-2">
                                            <i class="fas fa-box text-gray-400"></i>
                                        </div>
                                    @endif
                                    <a href="{{ route('products.show', $product->slug) }}" 
                                       class="text-sm font-medium text-gray-900 hover:text-primary">
                                        {{ Str::limit($product->name, 30) }}
                                    </a>
                                    <button onclick="removeFromComparison({{ $product->id }})" 
                                            class="text-red-600 hover:text-red-800 text-xs">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <!-- Basic Attributes -->
                    <tr class="border-t">
                        <td class="px-4 py-3 font-medium text-gray-900">Price</td>
                        @foreach($products as $product)
                            <td class="px-4 py-3 text-center">
                                <div class="text-lg font-bold text-primary">
                                    ৳{{ number_format($product->price, 2) }}
                                </div>
                                @if($product->compare_price)
                                    <div class="text-sm text-gray-400 line-through">
                                        ৳{{ number_format($product->compare_price, 2) }}
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    
                    <tr class="border-t">
                        <td class="px-4 py-3 font-medium text-gray-900">Category</td>
                        @foreach($products as $product)
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    {{ $product->category ? $product->category->name : 'N/A' }}
                                </span>
                            </td>
                        @endforeach
                    </tr>
                    
                    <tr class="border-t">
                        <td class="px-4 py-3 font-medium text-gray-900">Brand</td>
                        @foreach($products as $product)
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                    {{ $product->brand ? $product->brand->name : 'N/A' }}
                                </span>
                            </td>
                        @endforeach
                    </tr>
                    
                    <tr class="border-t">
                        <td class="px-4 py-3 font-medium text-gray-900">Stock Status</td>
                        @foreach($products as $product)
                            <td class="px-4 py-3 text-center">
                                @if($product->stock_quantity > 0)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        In Stock ({{ $product->stock_quantity }})
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                        Out of Stock
                                    </span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    
                    <tr class="border-t">
                        <td class="px-4 py-3 font-medium text-gray-900">Rating</td>
                        @foreach($products as $product)
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center items-center">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $product->average_rating)
                                                <i class="fas fa-star text-sm"></i>
                                            @else
                                                <i class="far fa-star text-sm"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-sm text-gray-600">
                                        ({{ $product->approved_reviews_count }})
                                    </span>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                    
                    <tr class="border-t">
                        <td class="px-4 py-3 font-medium text-gray-900">Description</td>
                        @foreach($products as $product)
                            <td class="px-4 py-3 text-center">
                                <div class="text-sm text-gray-600 max-w-xs">
                                    {{ Str::limit($product->short_description ?? $product->description, 100) }}
                                </div>
                            </td>
                        @endforeach
                    </tr>
                    
                    <!-- Custom Attributes -->
                    @if(isset($attributes['custom']))
                        @foreach($attributes['custom'] as $attribute => $label)
                            <tr class="border-t">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $label }}</td>
                                @foreach($products as $product)
                                    <td class="px-4 py-3 text-center">
                                        <div class="text-sm text-gray-600">
                                            @php
                                                $attributes = json_decode($product->attributes, true) ?? [];
                                                $value = $attributes[$attribute] ?? 'N/A';
                                            @endphp
                                            {{ $value }}
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endif
                    
                    <!-- Actions -->
                    <tr class="border-t bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">Actions</td>
                        @foreach($products as $product)
                            <td class="px-4 py-3 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('products.show', $product->slug) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="addToCart({{ $product->id }})" 
                                            class="text-green-600 hover:text-green-800 text-sm">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                    <button onclick="toggleWishlist({{ $product->id }})" 
                                            class="text-red-600 hover:text-red-800 text-sm">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="p-6 border-t">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-2"></i>
                    Compare up to 4 products at a time. Add more products to see detailed comparison.
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('products.index') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-shopping-bag mr-2"></i>Add More Products
                    </a>
                    <button onclick="clearComparison()" 
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                        <i class="fas fa-trash mr-2"></i>Clear Comparison
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function removeFromComparison(productId) {
    fetch('/comparison/remove', {
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
            updateComparisonCount(data.comparison_count);
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function clearComparison() {
    fetch('/comparison/clear', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateComparisonCount(0);
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function addToCart(productId) {
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
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
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding to cart', 'error');
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
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating wishlist', 'error');
    });
}

function updateComparisonCount(count) {
    const comparisonCountElement = document.querySelector('.text-gray-600');
    if (comparisonCountElement) {
        comparisonCountElement.textContent = `Compare up to ${4 - count} more products`;
    }
}

function updateCartCount(count) {
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;
    }
}

function updateWishlistCount(count) {
    const wishlistCountElement = document.querySelector('.wishlist-count');
    if (wishlistCountElement) {
        wishlistCountElement.textContent = count;
    }
}

function showNotification(message, type) {
    // Simple notification implementation
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 ${
        type === 'success' ? 'bg-green-600' : 'bg-red-600'
    }`;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection
