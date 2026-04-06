@extends('frontend.layout')

@section('title', 'Shopping Cart')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Cart Items -->
        <div class="flex-1">
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4">
                    <h1 class="text-2xl font-bold">Shopping Cart</h1>
                    <p class="text-gray-600 mt-1">{{ $cartItems->count() }} items in your cart</p>
                </div>

                @forelse($cartItems as $item)
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <!-- Product Image -->
                            <div class="flex-shrink-0">
                                @if($item->primaryImage)
                                    <img src="{{ $item->primaryImage->image_url }}" 
                                         alt="{{ $item->name }}" class="w-24 h-24 object-cover rounded">
                                @else
                                    <div class="w-24 h-24 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Details -->
                            <div class="flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="font-semibold text-lg">
                                            <a href="{{ route('products.show', $item->slug) }}" class="text-gray-900 hover:text-primary">
                                                {{ $item->name }}
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">{{ $item->sku }}</p>
                                        <p class="text-sm text-gray-500">{{ $item->category->name ?? 'No Category' }}</p>
                                        
                                        <!-- Stock Status -->
                                        @if($item->track_stock)
                                            @if($item->stock_quantity > 0)
                                                <p class="text-sm text-green-600 mt-2">
                                                    <i class="fas fa-check-circle"></i> In Stock ({{ $item->stock_quantity }} available)
                                                </p>
                                            @else
                                                <p class="text-sm text-red-600 mt-2">
                                                    <i class="fas fa-times-circle"></i> Out of Stock
                                                </p>
                                            @endif
                                        @endif
                                    </div>

                                    <!-- Remove Button -->
                                    <button onclick="removeFromCart({{ $item->id }})" 
                                            class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <!-- Price and Quantity -->
                                <div class="flex justify-between items-end mt-4">
                                    <div class="flex items-center gap-4">
                                        <!-- Quantity Selector -->
                                        <div class="flex items-center rounded-lg">
                                            <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                                    class="px-3 py-2 hover:bg-gray-100 {{ $item->quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                    {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                                <i class="fas fa-minus text-sm"></i>
                                            </button>
                                            <input type="number" value="{{ $item->quantity }}" 
                                                   id="quantity-{{ $item->id }}"
                                                   class="w-16 text-center py-2 focus:outline-none"
                                                   min="1" max="{{ $item->stock_quantity ?? 999 }}"
                                                   onchange="updateQuantity({{ $item->id }}, this.value)">
                                            <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                                    class="px-3 py-2 hover:bg-gray-100">
                                                <i class="fas fa-plus text-sm"></i>
                                            </button>
                                        </div>

                                        <!-- Price -->
                                        <div>
                                            <p class="font-semibold text-lg">৳{{ number_format($item->price, 2) }}</p>
                                            <p class="text-sm text-gray-500">each</p>
                                        </div>
                                    </div>

                                    <!-- Item Total -->
                                    <div class="text-right">
                                        <p class="font-semibold text-lg">৳{{ number_format($item->price * $item->quantity, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <i class="fas fa-shopping-cart text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">Your cart is empty</h3>
                        <p class="text-gray-500 mb-6">Add some products to get started!</p>
                        <a href="{{ route('products.index') }}" 
                           class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                            Continue Shopping
                        </a>
                    </div>
                @endforelse
            </div>

            @if($cartItems->count() > 0)
                <!-- Continue Shopping Link -->
                <div class="mt-6 text-center">
                    <a href="{{ route('products.index') }}" 
                       class="text-primary hover:text-orange-600">
                        <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                    </a>
                </div>
            @endif
        </div>

        <!-- Order Summary -->
        @if($cartItems->count() > 0)
            <div class="w-full lg:w-96">
                <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                    <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                    
                    <!-- Subtotal -->
                    <div class="flex justify-between mb-4">
                        <span class="text-gray-600">Subtotal ({{ $cartItems->count() }} items)</span>
                        <span class="font-semibold">৳{{ number_format($subtotal, 2) }}</span>
                    </div>

                    <!-- Shipping -->
                    <div class="flex justify-between mb-4">
                        <span class="text-gray-600">Shipping</span>
                        <span class="font-semibold">৳50.00</span>
                    </div>

                    <!-- Tax -->
                    <div class="flex justify-between mb-4">
                        <span class="text-gray-600">Tax (VAT 15%)</span>
                        <span class="font-semibold">৳{{ number_format($subtotal * 0.15, 2) }}</span>
                    </div>

                    <!-- Divider -->
                    <div class="pt-4 mb-4">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold">Total</span>
                            <span class="text-lg font-bold text-primary">
                                ৳{{ number_format($subtotal + 50 + ($subtotal * 0.15), 2) }}
                            </span>
                        </div>
                    </div>

                    <!-- Promo Code -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Promo Code</label>
                        <div class="flex gap-2">
                            <input type="text" placeholder="Enter promo code" 
                                   class="flex-1 px-3 py-2 rounded-lg focus:outline-none focus:border-primary">
                            <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                                Apply
                            </button>
                        </div>
                    </div>

                    <!-- Checkout Button -->
                    <a href="{{ route('checkout.index') }}" 
                       class="w-full bg-primary text-white py-2 px-2 rounded-lg hover:bg-orange-600 transition text-center font-semibold">
                        Proceed to Checkout
                    </a>

                    <!-- Security Note -->
                    <div class="mt-4 text-center text-sm text-gray-500">
                        <i class="fas fa-lock mr-1"></i>
                        Secure checkout powered by SSL encryption
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function updateQuantity(productId, newQuantity) {
    if (newQuantity < 1) return;
    
    const maxStock = parseInt(document.getElementById(`quantity-${productId}`).max);
    if (newQuantity > maxStock) {
        alert('Only ' + maxStock + ' items available in stock');
        return;
    }

    fetch('/cart/update', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: newQuantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating cart');
    });
}

function removeFromCart(productId) {
    if (confirm('Are you sure you want to remove this item from cart?')) {
        fetch('/cart/remove', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing item');
        });
    }
}
</script>
@endsection
