@extends('admin.layout')

@section('title', 'Product Details')
@section('header', 'Product Details')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Product Details</h1>
                    <p class="text-sm text-gray-500 mt-1">View complete product information</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.products.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Products
                    </a>
                    <a href="{{ route('admin.products.edit', $product) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i> Edit Product
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-orange-500"></i>
                        Basic Information
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Product Name</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">SKU</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->sku }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Short Description</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->short_description ?: 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Full Description</label>
                            <div class="mt-1 text-sm text-gray-900 prose max-w-none">
                                {!! $product->description !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing & Inventory -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-tag mr-2 text-orange-500"></i>
                        Pricing & Inventory
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Selling Price</label>
                            <p class="mt-1 text-lg font-semibold text-green-600">৳{{ number_format($product->price, 2) }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Compare Price</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->compare_price ? '৳' . number_format($product->compare_price, 2) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Cost Price</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->cost_price ? '৳' . number_format($product->cost_price, 2) : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Stock Quantity</label>
                            <p class="mt-1 text-sm font-medium {{ $product->isInStock() ? 'text-green-600' : 'text-red-600' }}">
                                {{ $product->stock_quantity }} {{ $product->isInStock() ? '(In Stock)' : '(Out of Stock)' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Weight</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->weight ? $product->weight . ' kg' : 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Track Stock</label>
                            <p class="mt-1 text-sm">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $product->track_stock ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $product->track_stock ? 'Yes' : 'No' }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <p class="mt-1 text-sm">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Featured</label>
                            <p class="mt-1 text-sm">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $product->is_featured ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $product->is_featured ? 'Yes' : 'No' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Images -->
            @if($product->images->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-images mr-2 text-orange-500"></i>
                        Product Images
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach($product->images as $image)
                            <div class="relative group">
                                <img src="{{ $image->image_url }}" alt="{{ $image->alt_text }}" 
                                     class="w-full h-32 object-cover rounded-lg border border-gray-200">
                                <div class="absolute top-2 left-2">
                                    @if($image->is_primary)
                                        <span class="bg-orange-500 text-white px-2 py-1 rounded text-xs font-medium">
                                            Primary
                                        </span>
                                    @endif
                                </div>
                                <div class="absolute bottom-2 right-2 bg-black bg-opacity-70 text-white px-2 py-1 rounded text-xs">
                                    #{{ $image->sort_order }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Reviews -->
            @if($product->reviews->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-star mr-2 text-orange-500"></i>
                        Customer Reviews ({{ $product->reviews->count() }})
                    </h2>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <div class="flex items-center">
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= round($product->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                            <span class="ml-2 text-sm text-gray-600">
                                {{ number_format($product->average_rating, 1) }} out of 5
                            </span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        @foreach($product->reviews->take(5) as $review)
                            <div class="border-b border-gray-200 pb-4 last:border-b-0">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <span class="font-medium text-gray-900">{{ $review->user->name }}</span>
                                        <div class="flex items-center ml-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-xs {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</span>
                                </div>
                                <p class="text-sm text-gray-700">{{ $review->comment }}</p>
                            </div>
                        @endforeach
                    </div>
                    @if($product->reviews->count() > 5)
                        <div class="mt-4 text-center">
                            <a href="#" class="text-orange-600 hover:text-orange-700 text-sm font-medium">
                                View all {{ $product->reviews->count() }} reviews
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Categories & Brands -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-folder mr-2 text-orange-500"></i>
                        Categories & Brand
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Category</label>
                        @if($product->category)
                            <p class="mt-1 text-sm text-gray-900">{{ $product->category->name }}</p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">No category assigned</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Brand</label>
                        @if($product->brand)
                            <p class="mt-1 text-sm text-gray-900">{{ $product->brand->name }}</p>
                        @else
                            <p class="mt-1 text-sm text-gray-500">No brand assigned</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Product Metadata -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-cog mr-2 text-orange-500"></i>
                        Product Metadata
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Product ID</label>
                        <p class="mt-1 text-sm text-gray-900">#{{ $product->id }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Slug</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->slug }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Sort Order</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->sort_order ?: 0 }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Created</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-bolt mr-2 text-orange-500"></i>
                        Quick Actions
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('admin.products.edit', $product) }}" class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i> Edit Product
                    </a>
                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i> Delete Product
                        </button>
                    </form>
                    <a href="{{ route('products.show', $product->slug) }}" target="_blank" class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                        <i class="fas fa-external-link-alt mr-2"></i> View on Store
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
