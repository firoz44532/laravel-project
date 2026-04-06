@extends('frontend.layout')

@section('title', $product->name . ' - ' . \App\Services\SettingsService::get('site_name', 'ShopBD'))

@push('meta-tags')
    <!-- Product-specific SEO meta tags -->
    <meta name="description" content="{{ Str::limit($product->description ?? $product->name, 160) }}">
    <meta name="keywords" content="{{ $product->name }}, {{ $product->category->name ?? '' }}, {{ $product->brand->name ?? '' }}, buy online, Bangladesh, best price">
    
    <!-- Product Open Graph tags -->
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ $product->name }}">
    <meta property="og:description" content="{{ Str::limit($product->description ?? $product->name, 160) }}">
    @if($product->primaryImage)
        <meta property="og:image" content="{{ $product->primaryImage->image_url }}">
        <meta property="og:image:width" content="800">
        <meta property="og:image:height" content="600">
    @endif
    <meta property="product:price:amount" content="{{ $product->price }}">
    <meta property="product:price:currency" content="BDT">
    @if($product->isInStock())
        <meta property="product:availability" content="in stock">
    @else
        <meta property="product:availability" content="out of stock">
    @endif
    
    <!-- Product Twitter Card -->
    <meta name="twitter:card" content="product">
    <meta name="twitter:title" content="{{ $product->name }}">
    <meta name="twitter:description" content="{{ Str::limit($product->description ?? $product->name, 160) }}">
    @if($product->primaryImage)
        <meta name="twitter:image" content="{{ $product->primaryImage->image_url }}">
    @endif
    <meta name="twitter:data1" content="{{ $product->price }} BDT">
    <meta name="twitter:label1" content="Price">
    
    <!-- Product Structured Data -->
    <script type="application/ld+json">
    @php
        $structuredData = [
            "@context" => "https://schema.org",
            "@type" => "Product",
            "name" => $product->name,
            "description" => Str::limit($product->description ?? $product->name, 160),
            "image" => $product->primaryImage ? [$product->primaryImage->image_url] : [],
            "sku" => $product->sku ?? $product->id,
            "brand" => [
                "@type" => "Brand",
                "name" => $product->brand->name ?? 'Unknown'
            ],
            "category" => $product->category->name ?? 'General',
            "offers" => [
                "@type" => "Offer",
                "price" => $product->price,
                "priceCurrency" => "BDT",
                "availability" => $product->isInStock() ? "InStock" : "OutOfStock",
                "url" => url('/products/' . $product->slug),
                "seller" => [
                    "@type" => "Organization",
                    "name" => \App\Services\SettingsService::get('site_name', 'ShopBD')
                ]
            ]
        ];
        
        if ($product->review_count > 0) {
            $structuredData["aggregateRating"] = [
                "@type" => "AggregateRating",
                "ratingValue" => $product->average_rating,
                "reviewCount" => $product->review_count
            ];
        }
        
        echo json_encode($structuredData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    @endphp
    </script>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="flex items-center space-x-2">
            <li><a href="{{ route('home') }}" class="text-gray-500 hover:text-primary">Home</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ route('products.index') }}" class="text-gray-500 hover:text-primary">Products</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-gray-900">{{ $product->name }}</span></li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Product Images -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <!-- Main Image -->
                <div class="mb-4">
                    @if($product->primaryImage)
                        <a href="{{ $product->primaryImage->image_url }}" 
                           target="_blank" 
                           class="block">
                            <img src="{{ $product->primaryImage->image_url }}" 
                                 alt="{{ $product->name }}" 
                                 id="main-image"
                                 class="w-full h-96 object-cover rounded-lg hover:opacity-90 transition cursor-pointer product-image">
                        </a>
                    @else
                        <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxOCIgZmlsbD0iIzk5OTk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0+NjAweDQwMDwvdGV4dD48L3N2Zz4=" alt="{{ $product->name }}" 
                             id="main-image"
                             class="w-full h-96 object-cover rounded-lg">
                    @endif
                </div>

                <!-- Thumbnail Gallery -->
                @if($product->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($product->images as $image)
                            <a href="{{ $image->image_url }}" 
                               target="_blank"
                               class="block thumbnail-link">
                                <img src="{{ $image->image_url }}" 
                                     alt="{{ $product->name }} - Image {{ $loop->iteration + 1 }}" 
                                     class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75 transition product-image">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Details -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h1 class="text-2xl font-bold mb-4">{{ $product->name }}</h1>
                
                <!-- Rating -->
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($product->average_rating))
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <span class="ml-2 text-gray-600">{{ number_format($product->average_rating, 1) }}</span>
                    <span class="ml-2 text-gray-500">({{ $product->review_count }} reviews)</span>
                    @if($product->review_count > 0)
                        <a href="#reviews" class="ml-2 text-primary hover:text-orange-600 text-sm">See all reviews</a>
                    @endif
                </div>

                <!-- Price -->
                <div class="flex items-center mb-6">
                    <span class="text-3xl font-bold text-primary">৳{{ number_format($product->price, 2) }}</span>
                    @if($product->compare_price)
                        <span class="ml-4 text-lg text-gray-400 line-through">৳{{ number_format($product->compare_price, 2) }}</span>
                        <span class="ml-2 bg-red-500 text-white px-2 py-1 rounded text-sm font-bold">
                            Save {{ $product->discount_percentage }}%
                        </span>
                    @endif
                </div>

                <!-- Stock Status -->
                <div class="mb-6">
                    @if($product->isInStock())
                        <div class="flex items-center text-green-600">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>In Stock ({{ $product->stock_quantity }} available)</span>
                        </div>
                    @else
                        <div class="flex items-center text-red-600">
                            <i class="fas fa-times-circle mr-2"></i>
                            <span>Out of Stock</span>
                        </div>
                    @endif
                </div>

                <!-- Product Details -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">Product Details</h3>
                    <div class="text-gray-700">
                        <ul class="list-disc list-inside space-y-2">
                            <li>High-quality product with premium materials</li>
                            <li>Durable and long-lasting construction</li>
                            <li>Modern and stylish design</li>
                            <li>Perfect for everyday use</li>
                        </ul>
                    </div>
                </div>

                <!-- About Item -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-3">About Item</h3>
                    <div class="text-gray-700">
                        <p>This product is carefully crafted to meet the highest standards of quality and performance. Designed with attention to detail and built to last, it offers exceptional value for your investment.</p>
                    </div>
                </div>

                <!-- Product Attributes -->
                @if($product->attributes)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3">Specifications</h3>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($product->attributes as $key => $value)
                                <div class="flex justify-between py-2 border-b">
                                    <span class="text-gray-600">{{ ucfirst($key) }}:</span>
                                    <span class="font-medium">{{ $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Category and Brand -->
                <div class="flex items-center space-x-6 text-sm text-gray-600">
                    @if($product->category)
                        <div>
                            <span class="font-medium">Category:</span>
                            <a href="{{ route('products.category', $product->category->slug) }}" class="text-primary hover:text-orange-600">
                                {{ $product->category->name }}
                            </a>
                        </div>
                    @endif
                    @if($product->brand)
                        <div>
                            <span class="font-medium">Brand:</span>
                            <a href="{{ route('products.brand', $product->brand->slug) }}" class="text-primary hover:text-orange-600">
                                {{ $product->brand->name }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Add to Cart Section -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Purchase Options</h3>
                
                <!-- Quantity Selector -->
                <div class="flex items-center mb-4">
                    <label class="text-sm font-medium text-gray-700 mr-4">Quantity:</label>
                    <div class="flex items-center border rounded-lg">
                        <button onclick="decrementQuantity()" 
                                class="px-3 py-2 hover:bg-gray-100">
                            <i class="fas fa-minus text-sm"></i>
                        </button>
                        <input type="number" id="quantity" value="1" min="1" max="{{ $product->stock_quantity ?? 999 }}" 
                               class="w-20 text-center border-x py-2 focus:outline-none">
                        <button onclick="incrementQuantity()" 
                                class="px-3 py-2 hover:bg-gray-100">
                            <i class="fas fa-plus text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-2 items-end">
                    <button onclick="addToCart()" 
                            class="bg-primary text-white py-2 px-2 rounded-lg hover:bg-orange-600 transition text-center font-semibold flex items-center justify-center cursor-pointer">
                        <i class="fas fa-shopping-cart mr-2 text-sm"></i>
                        Add to Cart
                    </button>
                    
                    <button onclick="buyNow()" 
                            class="bg-primary text-white py-2 px-2 rounded-lg hover:bg-orange-600 transition text-center font-semibold flex items-center justify-center cursor-pointer">
                        <i class="fas fa-bolt mr-2 text-sm"></i>
                        Buy Now
                    </button>

                    <!-- Wishlist Button -->
                    <button id="wishlist-btn" 
                            data-product-id="{{ $product->id }}"
                            data-is-in-wishlist="{{ Auth::check() && \App\Models\Wishlist::isInWishlist($product->id) ? 'true' : 'false' }}"
                            class="wishlist-btn bg-yellow-400 text-white py-2 px-2 rounded-lg hover:bg-yellow-500 transition text-center font-semibold flex items-center justify-center cursor-pointer">
                        <i class="far fa-heart mr-2 text-sm wishlist-icon"></i>
                        <span class="wishlist-text">Add to Wishlist</span>
                    </button>
                </div>

                <!-- Security Info -->
                <div class="mt-6 pt-6 border-t">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="fas fa-shield-alt mr-2"></i>
                        <span>Secure checkout with SSL encryption</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600 mt-2">
                        <i class="fas fa-truck mr-2"></i>
                        <span>Fast delivery across Bangladesh</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600 mt-2">
                        <i class="fas fa-undo mr-2"></i>
                        <span>7 days return policy</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Seller Info -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Seller Information</h3>
                <div class="text-center">
                    <div class="w-16 h-16 bg-gray-200 rounded-full mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-store text-gray-400"></i>
                    </div>
                    <h4 class="font-medium">ShopBD Official</h4>
                    <div class="flex items-center justify-center text-sm text-yellow-500 mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star"></i>
                        @endfor
                        <span class="ml-1">4.8</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">Official Store</p>
                    <div class="flex items-center justify-center text-sm text-green-600 mt-2">
                        <i class="fas fa-check-circle mr-1"></i>
                        Verified Seller
                    </div>
                </div>
            </div>

            <!-- Shipping Info -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Shipping & Delivery</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-truck text-primary mr-3"></i>
                        <span>Standard Delivery: 2-3 days</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-shipping-fast text-primary mr-3"></i>
                        <span>Express Delivery: 1-2 days</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-money-bill-wave text-primary mr-3"></i>
                        <span>Cash on Delivery Available</span>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Payment Methods</h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex items-center justify-center p-3 border rounded-lg">
                        <div class="w-8 h-8 bg-pink-500 rounded flex items-center justify-center mr-2">
                            <span class="text-white font-bold text-xs">b</span>
                        </div>
                        <span class="text-xs">bKash</span>
                    </div>
                    <div class="flex items-center justify-center p-3 border rounded-lg">
                        <div class="w-8 h-8 bg-orange-500 rounded flex items-center justify-center mr-2">
                            <span class="text-white font-bold text-xs">N</span>
                        </div>
                        <span class="text-xs">Nagad</span>
                    </div>
                    <div class="flex items-center justify-center p-3 border rounded-lg">
                        <div class="w-8 h-8 bg-purple-500 rounded flex items-center justify-center mr-2">
                            <span class="text-white font-bold text-xs">R</span>
                        </div>
                        <span class="text-xs">Rocket</span>
                    </div>
                    <div class="flex items-center justify-center p-3 border rounded-lg">
                        <div class="w-8 h-8 bg-blue-500 rounded flex items-center justify-center mr-2">
                            <span class="text-white font-bold text-xs">U</span>
                        </div>
                        <span class="text-xs">Upay</span>
                    </div>
                    <div class="flex items-center justify-center p-3 border rounded-lg">
                        <i class="fas fa-credit-card text-gray-700 mr-2"></i>
                        <span class="text-xs">Card</span>
                    </div>
                    <div class="flex items-center justify-center p-3 border rounded-lg">
                        <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>
                        <span class="text-xs">COD</span>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            @if($relatedProducts->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Related Products</h3>
                    <div class="space-y-4">
                        @foreach($relatedProducts as $relatedProduct)
                            <div class="flex items-center space-x-3">
                                @if($relatedProduct->primaryImage)
                                    <img src="{{ $relatedProduct->primaryImage->image_url }}" 
                                         alt="{{ $relatedProduct->name }}" 
                                         class="w-16 h-16 object-cover rounded">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                        <i class="fas fa-box text-gray-400 text-sm"></i>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium line-clamp-1">
                                        <a href="{{ route('products.show', $relatedProduct->slug) }}" 
                                           class="text-gray-900 hover:text-primary">
                                            {{ $relatedProduct->name }}
                                        </a>
                                    </h4>
                                    <div class="text-sm text-primary font-semibold">৳{{ number_format($relatedProduct->price, 2) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Reviews Section -->
    <div id="reviews" class="mt-12">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold mb-6">Customer Reviews</h2>
            
            <!-- Review Summary -->
            <div class="mb-8">
                <div class="flex items-center space-x-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold">{{ number_format($product->average_rating, 1) }}</div>
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($product->average_rating))
                                    <i class="fas fa-star"></i>
                                @else
                                    <i class="far fa-star"></i>
                                @endif
                            @endfor
                        </div>
                        <div class="text-sm text-gray-600">{{ $product->review_count }} Reviews</div>
                    </div>
                    
                    <!-- Rating Breakdown -->
                    <div class="flex-1">
                        @foreach([5, 4, 3, 2, 1] as $rating)
                            <div class="flex items-center mb-2">
                                <span class="text-sm text-gray-600 w-12">{{ $rating }} star</span>
                                <div class="flex-1 bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $product->review_count > 0 ? ($product->approvedReviews()->where('rating', $rating)->count() / $product->review_count) * 100 : 0 }}%"></div>
                                </div>
                                <span class="text-sm text-gray-600 w-12 text-right">
                                    {{ $product->approvedReviews()->where('rating', $rating)->count() }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Write Review Button -->
            <div class="mb-6">
                <button onclick="showReviewForm()" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                    <i class="fas fa-pen mr-2"></i>Write a Review
                </button>
            </div>

            <!-- Reviews List -->
            <div class="space-y-6">
                @forelse($product->approvedReviews as $review)
                    <div class="border-b pb-6 last:border-b-0">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 font-bold">{{ strtoupper(substr($review->user->name, 0, 1)) }}</span>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold">{{ $review->title }}</h4>
                                    <div class="flex text-yellow-400 text-sm">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fas fa-star"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-gray-700">{{ $review->comment }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <div class="text-sm text-gray-500">
                                        {{ $review->user->name }} • {{ $review->created_at->format('M j, Y') }}
                                    </div>
                                    @if($review->is_verified_purchase)
                                        <div class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                                            Verified Purchase
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500">No reviews yet. Be the first to review this product!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Review Form Modal -->
<div id="review-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
            <h3 class="text-xl font-semibold mb-4">Write a Review</h3>
            <form id="review-form">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <div class="flex space-x-2">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" onclick="setRating({{ $i }})" 
                                        class="rating-star text-2xl text-gray-300 hover:text-yellow-400 transition"
                                        data-rating="{{ $i }}">
                                    <i class="far fa-star"></i>
                                </button>
                            @endfor
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" name="title" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Review</label>
                    <textarea name="comment" rows="4" required
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary"></textarea>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeReviewForm()" 
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-orange-600">
                        Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let productId = {{ $product->id }};
let selectedRating = 0;

function incrementQuantity() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.max);
    if (input.value < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decrementQuantity() {
    const input = document.getElementById('quantity');
    if (input.value > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

function addToCart() {
    const quantity = document.getElementById('quantity').value;
    
    fetch('{{ route('cart.add') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
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
        console.error('Error adding to cart:', error);
        showNotification('Error adding product to cart', 'error');
    });
}

function buyNow() {
    const quantity = document.getElementById('quantity').value;
    
    fetch('{{ route('cart.add') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            updateCartCount(data.cart_count);
            // Redirect to checkout after a short delay
            setTimeout(() => {
                window.location.href = '{{ route('cart.index') }}';
            }, 1000);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        showNotification('Error adding product to cart', 'error');
    });
}

function changeMainImage(imageSrc) {
    const mainImage = document.getElementById('main-image');
    if (mainImage) {
        mainImage.src = imageSrc;
    }
}

function showReviewForm() {
    document.getElementById('review-modal').classList.remove('hidden');
}

function closeReviewForm() {
    document.getElementById('review-modal').classList.add('hidden');
}

function setRating(rating) {
    selectedRating = rating;
    const stars = document.querySelectorAll('.rating-star');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.innerHTML = '<i class="fas fa-star text-yellow-400"></i>';
        } else {
            star.innerHTML = '<i class="far fa-star text-gray-300"></i>';
        }
    });
}

// Review form submission
document.getElementById('review-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (selectedRating === 0) {
        alert('Please select a rating');
        return;
    }
    
    const formData = new FormData(this);
    formData.append('rating', selectedRating);
    
    fetch(`/products/{{ $product->slug }}/review`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeReviewForm();
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error submitting review');
    });
});
</script>

<!-- Enhanced Lightbox with Navigation -->
<div id="image-lightbox" class="fixed inset-0 bg-black bg-opacity-90 z-50 hidden flex items-center justify-center" onclick="closeLightbox(event)">
    <div class="relative max-w-6xl max-h-screen p-4" onclick="event.stopPropagation()">
        <img id="lightbox-image" src="" alt="Product Image" class="max-w-full max-h-full object-contain">
        
        <!-- Navigation Arrows -->
        <button onclick="navigateLightbox(-1)" class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 text-gray-800 rounded-full p-3 hover:bg-opacity-100 transition">
            <i class="fas fa-chevron-left text-xl"></i>
        </button>
        <button onclick="navigateLightbox(1)" class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 text-gray-800 rounded-full p-3 hover:bg-opacity-100 transition">
            <i class="fas fa-chevron-right text-xl"></i>
        </button>
        
        <!-- Close Button -->
        <button onclick="closeLightbox()" class="absolute top-4 right-4 bg-white text-gray-800 rounded-full p-2 hover:bg-gray-200 transition">
            <i class="fas fa-times"></i>
        </button>
        
        <!-- Image Counter -->
        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 bg-white bg-opacity-80 text-gray-800 px-3 py-1 rounded-full text-sm">
            <span id="image-counter">1 / 1</span>
        </div>
    </div>
</div>

<style>
#image-lightbox {
    display: none;
}

#image-lightbox.show {
    display: flex;
}
</style>

<script>
let currentImageIndex = 0;
let lightboxImages = [];

function closeLightbox(event) {
    if (event) event.stopPropagation();
    document.getElementById('image-lightbox').classList.remove('show');
    currentImageIndex = 0;
    lightboxImages = [];
}

function openLightbox(imageSrc, allImages = [], currentIndex = 0) {
    lightboxImages = allImages.length > 0 ? allImages : [imageSrc];
    currentImageIndex = currentIndex;
    updateLightboxImage();
    document.getElementById('image-lightbox').classList.add('show');
}

function navigateLightbox(direction) {
    if (lightboxImages.length <= 1) return;
    
    currentImageIndex += direction;
    if (currentImageIndex < 0) {
        currentImageIndex = lightboxImages.length - 1;
    } else if (currentImageIndex >= lightboxImages.length) {
        currentImageIndex = 0;
    }
    updateLightboxImage();
}

function updateLightboxImage() {
    const lightboxImg = document.getElementById('lightbox-image');
    const counter = document.getElementById('image-counter');
    
    lightboxImg.src = lightboxImages[currentImageIndex];
    counter.textContent = `${currentImageIndex + 1} / ${lightboxImages.length}`;
}

// Keyboard navigation
document.addEventListener('keydown', function(event) {
    if (!document.getElementById('image-lightbox').classList.contains('show')) return;
    
    switch(event.key) {
        case 'Escape':
            closeLightbox();
            break;
        case 'ArrowLeft':
            navigateLightbox(-1);
            break;
        case 'ArrowRight':
            navigateLightbox(1);
            break;
    }
});

// Simple image clicking without complex event listeners
function setupImageClicking() {
    // Collect all product images
    const allProductImages = [];
    
    // Main image
    const mainImage = document.getElementById('main-image');
    if (mainImage && mainImage.src) {
        allProductImages.push(mainImage.src);
        mainImage.style.cursor = 'pointer';
        mainImage.onclick = function() {
            openLightbox(this.src, allProductImages, 0);
        };
    }
    
    // Thumbnail images
    const thumbnails = document.querySelectorAll('.thumbnail-link img');
    thumbnails.forEach(function(img, index) {
        if (img.src && !allProductImages.includes(img.src)) {
            allProductImages.push(img.src);
        }
        img.style.cursor = 'pointer';
        img.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            changeMainImage(this.src);
            const imageIndex = allProductImages.indexOf(this.src);
            openLightbox(this.src, allProductImages, imageIndex);
        };
    });
}

// Setup when page loads
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupImageClicking);
} else {
    setupImageClicking();
}

// Wishlist functionality
document.addEventListener('DOMContentLoaded', function() {
    const wishlistBtn = document.getElementById('wishlist-btn');
    
    if (wishlistBtn) {
        // Initialize button state
        updateWishlistButton(wishlistBtn);
        
        // Add click handler
        wishlistBtn.addEventListener('click', function() {
            const productId = this.dataset.productId;
            toggleWishlist(productId, this);
        });
    }
});

function updateWishlistButton(button) {
    const isInWishlist = button.dataset.isInWishlist === 'true';
    const icon = button.querySelector('.wishlist-icon');
    const text = button.querySelector('.wishlist-text');
    
    if (isInWishlist) {
        icon.className = 'fas fa-heart mr-2 text-sm wishlist-icon text-red-500';
        text.textContent = 'Remove from Wishlist';
        button.classList.remove('bg-white', 'text-gray-700');
        button.classList.add('bg-red-50', 'text-red-600', 'border-red-200');
    } else {
        icon.className = 'far fa-heart mr-2 text-sm wishlist-icon';
        text.textContent = 'Add to Wishlist';
        button.classList.remove('bg-red-50', 'text-red-600', 'border-red-200');
        button.classList.add('bg-white', 'text-gray-700');
    }
}

function toggleWishlist(productId, button) {
    // Show loading state
    const originalText = button.querySelector('.wishlist-text').textContent;
    button.querySelector('.wishlist-text').textContent = 'Loading...';
    button.disabled = true;
    
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
            updateWishlistButton(button);
            
            // Show notification
            showNotification(data.message, data.is_in_wishlist ? 'success' : 'info');
            
            // Update wishlist count if exists
            updateWishlistCount(data.wishlist_count);
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Wishlist error:', error);
        showNotification('Error updating wishlist', 'error');
    })
    .finally(() => {
        button.disabled = false;
    });
}

function updateWishlistCount(count) {
    const wishlistCountElements = document.querySelectorAll('.wishlist-count');
    wishlistCountElements.forEach(element => {
        element.textContent = count;
        element.style.display = count > 0 ? 'inline-block' : 'none';
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
</script>

@endsection
