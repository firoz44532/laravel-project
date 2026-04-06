@extends('frontend.layout')

@section('title', 'All Brands')
@section('header', 'Shop by Brand')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Shop by Brand</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Discover products from your favorite brands. We work with the best manufacturers to bring you quality products.
        </p>
    </div>

    <!-- Featured Brands -->
    @if($featuredBrands->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Brands</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @foreach($featuredBrands as $brand)
                    <div class="text-center group">
                        <a href="{{ route('products.brand', $brand->slug) }}" 
                           class="block bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                            @if($brand->logo)
                                <img src="{{ asset('storage/' . $brand->logo) }}" 
                                     alt="{{ $brand->name }}" 
                                     class="w-full h-20 object-contain mb-4">
                            @else
                                <div class="w-full h-20 bg-gray-200 rounded-lg flex items-center justify-center mb-4">
                                    <i class="fas fa-industry text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                            <h3 class="font-medium text-gray-900 group-hover:text-orange-600 transition-colors">
                                {{ $brand->name }}
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $brand->active_products_count }} Products
                            </p>
                            <div class="mt-2">
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">
                                    <i class="fas fa-star mr-1"></i>Featured
                                </span>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- All Brands -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900 mb-6">All Brands</h2>
        
        @if($allBrands->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($allBrands as $brand)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center space-x-4">
                            @if($brand->logo)
                                <img src="{{ asset('storage/' . $brand->logo) }}" 
                                     alt="{{ $brand->name }}" 
                                     class="w-16 h-16 object-contain">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-industry text-gray-400 text-xl"></i>
                                </div>
                            @endif
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900 mb-1">
                                    <a href="{{ route('products.brand', $brand->slug) }}" 
                                       class="hover:text-orange-600 transition-colors">
                                        {{ $brand->name }}
                                    </a>
                                </h3>
                                @if($brand->description)
                                    <p class="text-sm text-gray-600 line-clamp-2 mb-2">{{ $brand->description }}</p>
                                @endif
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-500">
                                        <i class="fas fa-box mr-1"></i>
                                        {{ $brand->active_products_count }} Products
                                    </span>
                                    @if($brand->is_featured)
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">
                                            <i class="fas fa-star mr-1"></i>Featured
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $allBrands->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-industry text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No brands found</h3>
                <p class="text-gray-500 mb-6">We don't have any brands available at the moment.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
