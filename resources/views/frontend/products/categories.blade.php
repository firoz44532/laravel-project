@extends('frontend.layout')

@section('title', 'All Categories')
@section('header', 'Your Favorite Items Are Here')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Your Favorite Items Are Here</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Discover your favorite items from our curated collection. Find exactly what you're looking for with ease.
        </p>
    </div>

    <!-- Featured Categories -->
    @if($featuredCategories->count() > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Featured Categories</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($featuredCategories as $category)
                    @include('components.category-card', ['category' => $category])
                @endforeach
            </div>
        </div>
    @endif

    <!-- All Categories -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900 mb-6">All Categories</h2>
        
        @if($allCategories->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($allCategories as $category)
                    @include('components.category-card', ['category' => $category])
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $allCategories->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-folder text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No categories found</h3>
                <p class="text-gray-500 mb-6">We don't have any categories available at the moment.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Continue Shopping
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
