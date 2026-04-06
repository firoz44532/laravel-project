@extends('frontend.layout')

@section('title', $merchant->store_name . ' - Products')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Vendor Header -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center">
                        <span class="text-purple-600 font-bold text-2xl">
                            {{ strtoupper(substr($merchant->store_name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold mb-1">{{ $merchant->store_name }}</h1>
                        <p class="text-purple-100">{{ $merchant->store_description ?: 'Quality products from trusted vendor' }}</p>
                        <p class="text-sm text-purple-200 mt-1">
                            <i class="fas fa-user mr-1"></i>{{ $merchant->user->name }} • 
                            <i class="fas fa-box mr-1 ml-2"></i>{{ $products->total() }} Products
                        </p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button class="bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold hover:bg-purple-50 transition duration-200 shadow-md">
                        <i class="fas fa-heart mr-2"></i>Follow Store
                    </button>
                    <a href="{{ route('products.vendors') }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-purple-400 transition duration-200 shadow-md">
                        <i class="fas fa-arrow-left mr-2"></i>All Vendors
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-md mb-6 p-4">
            <form method="GET" action="{{ route('products.vendor', $merchant->store_slug) }}">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Search products..." 
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                        </div>
                        <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->slug }}" {{ request('category') == $category->slug ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="brand" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="">All Brands</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->slug }}" {{ request('brand') == $brand->slug ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="sort" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Top Rated</option>
                        </select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition duration-200">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        @if(request()->hasAny(['search', 'category', 'brand', 'sort']))
                            <a href="{{ route('products.vendor', $merchant->store_slug) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-200">
                                <i class="fas fa-times mr-2"></i>Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
            <!-- Results Count -->
            <div class="flex justify-between items-center mb-4">
                <p class="text-sm text-gray-600">
                    Showing <span class="font-medium">{{ $products->firstItem() }}</span> to 
                    <span class="font-medium">{{ $products->lastItem() }}</span> of 
                    <span class="font-medium">{{ $products->total() }}</span> products from {{ $merchant->store_name }}
                </p>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden group">
                        <!-- Product Image -->
                        <div class="relative h-48 bg-gray-100 overflow-hidden">
                            @if($product->primaryImage)
                                <img src="{{ $product->primaryImage->image_url }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                    <i class="fas fa-box text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                            
                            <!-- Discount Badge -->
                            @if($product->compare_price && $product->compare_price > $product->price)
                                <div class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                                    {{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}% OFF
                                </div>
                            @endif
                            
                            <!-- Stock Badge -->
                            @if($product->stock_quantity <= 5)
                                <div class="absolute top-2 right-2">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full
                                        {{ $product->stock_quantity == 0 ? 'bg-red-500 text-white' : 'bg-yellow-500 text-white' }}">
                                        {{ $product->stock_quantity == 0 ? 'Out of Stock' : 'Low Stock' }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Product Info -->
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-600 mb-2">{{ $product->category->name ?? 'No Category' }}</p>
                            
                            <!-- Price -->
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <p class="text-lg font-bold text-green-600">৳{{ number_format($product->price, 2) }}</p>
                                    @if($product->compare_price && $product->compare_price > $product->price)
                                        <p class="text-sm text-gray-500 line-through">৳{{ number_format($product->compare_price, 2) }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center">
                                    @if($product->average_rating > 0)
                                        <div class="flex items-center">
                                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                                            <span class="text-xs text-gray-600 ml-1">{{ number_format($product->average_rating, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex space-x-2">
                                <a href="{{ route('products.show', $product->slug) }}" 
                                   class="flex-1 bg-purple-600 text-white px-3 py-2 rounded-lg text-center hover:bg-purple-700 transition duration-200">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <button class="flex-1 bg-orange-500 text-white px-3 py-2 rounded-lg hover:bg-orange-600 transition duration-200">
                                    <i class="fas fa-cart-plus mr-1"></i>Add
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $products->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full w-32 h-32 mx-auto mb-6 flex items-center justify-center">
                    <i class="fas fa-box text-purple-500 text-5xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">No products found</h3>
                <p class="text-gray-600 mb-6">{{ $merchant->store_name }} hasn't added any products yet.</p>
                <a href="{{ route('products.vendors') }}" class="bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700 transition duration-200 shadow-md">
                    <i class="fas fa-arrow-left mr-2"></i>Back to All Vendors
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection
