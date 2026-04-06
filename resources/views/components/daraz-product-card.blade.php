@props(['product'])
<div class="bg-white rounded-lg overflow-hidden shadow hover:shadow-lg transition transform hover:-translate-y-1">
    <a href="{{ route('products.show', $product->slug) }}" class="block relative">
        @if($product->primaryImage)
            <img src="{{ $product->primaryImage->image_url }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
        @else
            <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjNmNGY2Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OTk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPjIwMHgyMDA8L3RleHQ+PC9zdmc+" alt="{{ $product->name }}" class="w-full h-48 object-cover">
        @endif

        @if(isset($product->discount_percentage) && $product->discount_percentage > 0)
            <span class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-xs font-bold">-{{ $product->discount_percentage }}%</span>
        @endif
    </a>

    <div class="p-3">
        <h3 class="text-sm font-semibold text-gray-900 line-clamp-2 mb-2">
            <a href="{{ route('products.show', $product->slug) }}" class="hover:text-primary">{{ \Illuminate\Support\Str::limit($product->name, 60) }}</a>
        </h3>

        <div class="flex items-center justify-between mb-2">
            <div>
                <div class="text-primary font-bold">৳{{ number_format($product->price, 2) }}</div>
                @if($product->compare_price)
                    <div class="text-gray-400 text-xs line-through">৳{{ number_format($product->compare_price, 2) }}</div>
                @endif
            </div>
            <div class="text-xs text-gray-500 flex items-center">
                <i class="fas fa-star text-yellow-400 mr-1"></i>
                <span>{{ number_format($product->average_rating ?? 0, 1) }}</span>
            </div>
        </div>

        <div class="flex gap-2">
            <button class="flex-1 bg-orange-500 text-white py-2 rounded text-sm hover:bg-orange-600 transition add-to-cart-btn" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}">Add</button>
            <a href="{{ route('products.show', $product->slug) }}" class="flex-1 border border-gray-200 text-gray-700 py-2 rounded text-sm text-center hover:bg-gray-50 transition">Buy</a>
        </div>
    </div>
</div>
