@extends('frontend.layout')

@section('title', $brand->name . ' - Products')
@section('header', $brand->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Brand Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-8">
            <div class="flex items-center space-x-6">
                @if($brand->logo)
                    <img src="{{ asset('storage/' . $brand->logo) }}" 
                         alt="{{ $brand->name }}" 
                         class="w-20 h-20 object-contain">
                @else
                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-industry text-gray-400 text-2xl"></i>
                    </div>
                @endif
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $brand->name }}</h1>
                    @if($brand->description)
                        <p class="text-gray-600 mt-2">{{ $brand->description }}</p>
                    @endif
                    <div class="flex items-center space-x-4 mt-4">
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-box mr-1"></i>
                            {{ $products->total() }} Products
                        </span>
                        @if($brand->is_featured)
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">
                                <i class="fas fa-star mr-1"></i>Featured Brand
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Products from {{ $brand->name }}</h2>
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
            </div
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-box-open text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
                <p class="text-gray-500 mb-6">This brand doesn't have any products available at the moment.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
