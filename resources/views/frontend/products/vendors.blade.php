@extends('frontend.layout')

@section('title', 'Marketplace Sellers - Trusted Vendors')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Walmart-style Header with eBay Elements -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white relative overflow-hidden">
        <!-- eBay-style pattern overlay -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full -translate-x-32 -translate-y-32"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white rounded-full translate-x-48 translate-y-48"></div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative">
            <div class="flex flex-col lg:flex-row items-center justify-between">
                <div class="text-center lg:text-left mb-8 lg:mb-0">
                    <div class="flex items-center justify-center lg:justify-start mb-4">
                        <div class="bg-yellow-400 text-blue-800 rounded-lg p-3 mr-4">
                            <i class="fas fa-store text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl lg:text-5xl font-bold">Marketplace Sellers</h1>
                            <p class="text-blue-100 text-lg mt-2">
                                <span class="text-orange-300 font-semibold">Quality products</span> from 
                                <span class="text-orange-300 font-semibold">trusted local</span> and 
                                <span class="text-orange-300 font-semibold">international sellers</span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Walmart-style Stats Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-yellow-400">{{ $vendors->total() }}</p>
                        <p class="text-sm text-blue-100">Active Sellers</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-yellow-400">24/7</p>
                        <p class="text-sm text-blue-100">Support</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-yellow-400">Free</p>
                        <p class="text-sm text-blue-100">Returns</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-yellow-400">98%</p>
                        <p class="text-sm text-blue-100">Satisfaction</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Walmart-style Category Navigation -->
    <div class="bg-white border-b shadow-sm sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center space-x-2 overflow-x-auto">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold whitespace-nowrap">
                        All Sellers
                    </button>
                    <button class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg whitespace-nowrap flex items-center">
                        <i class="fas fa-star text-yellow-500 mr-2"></i>Top Rated
                    </button>
                    <button class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg whitespace-nowrap flex items-center">
                        <i class="fas fa-bolt text-blue-500 mr-2"></i>Fast Shipping
                    </button>
                    <button class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg whitespace-nowrap flex items-center">
                        <i class="fas fa-tag text-green-500 mr-2"></i>Best Deals
                    </button>
                    <button class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg whitespace-nowrap flex items-center">
                        <i class="fas fa-shield-alt text-purple-500 mr-2"></i>Verified
                    </button>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <button class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-th-large mr-2"></i>Grid
                    </button>
                    <button class="text-gray-400 hover:text-gray-900">
                        <i class="fas fa-list mr-2"></i>List
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Walmart-style Search Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('products.vendors') }}">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
                    <!-- Search Input -->
                    <div class="lg:col-span-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Sellers</label>
                        <div class="relative">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Search by store name, owner, or products..." 
                                   class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-4 top-3.5 text-gray-400"></i>
                        </div>
                    </div>
                    
                    <!-- Sort Dropdown -->
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                        <select name="sort" class="w-full appearance-none bg-white border border-gray-300 rounded-lg px-4 py-3 pr-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                            <option value="products_desc" {{ request('sort') == 'products_desc' ? 'selected' : '' }}>Most Products</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest First</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        </select>
                    </div>
                    
                    <!-- Search Button -->
                    <div class="lg:col-span-3">
                        <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 shadow-sm">
                            <i class="fas fa-search mr-2"></i>Search Sellers
                        </button>
                    </div>
                </div>
                
                <!-- Results Info -->
                <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        @if(request('search'))
                            <span class="font-semibold">{{ $vendors->total() }}</span> results for "{{ request('search') }}"
                        @else
                            <span class="font-semibold">{{ $vendors->total() }}</span> sellers available
                        @endif
                    </div>
                    @if(request('search'))
                        <a href="{{ route('products.vendors') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            <i class="fas fa-times mr-1"></i>Clear Search
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Vendors Grid - Walmart/eBay Mix -->
        @if($vendors->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                @foreach($vendors as $vendor)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden group border border-gray-200 hover:border-blue-300">
                        <!-- Walmart-style Card Header -->
                        <div class="relative">
                            <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-4 relative">
                                <!-- eBay-style Badge -->
                                @if($vendor->active_products_count > 50)
                                    <div class="absolute top-2 right-2 bg-yellow-400 text-blue-900 text-xs font-bold px-2 py-1 rounded">
                                        <i class="fas fa-star mr-1"></i>TOP SELLER
                                    </div>
                                @endif
                                
                                <!-- Store Info -->
                                <div class="flex items-center space-x-3">
                                    <div class="w-14 h-14 bg-white rounded-lg flex items-center justify-center shadow-md border-2 border-yellow-400">
                                        <span class="text-blue-600 font-bold text-lg">
                                            {{ strtoupper(substr($vendor->store_name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="text-white">
                                        <h3 class="font-bold text-base mb-1 line-clamp-1">{{ $vendor->store_name }}</h3>
                                        <p class="text-xs text-blue-100">
                                            <i class="fas fa-user mr-1"></i>{{ $vendor->user->name }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Walmart-style Card Body -->
                        <div class="p-4">
                            <!-- Store Description -->
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                {{ $vendor->store_description ?: 'Quality products, excellent service, and fast delivery guaranteed. Trusted seller with positive reviews.' }}
                            </p>
                            
                            <!-- Stats Grid -->
                            <div class="grid grid-cols-3 gap-2 mb-4">
                                <div class="text-center p-3 bg-blue-50 rounded-lg border border-blue-100">
                                    <p class="text-xl font-bold text-blue-600">{{ $vendor->active_products_count ?? 0 }}</p>
                                    <p class="text-xs text-gray-600">Items</p>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg border border-green-100">
                                    <p class="text-xl font-bold text-green-600">{{ $vendor->total_earnings ?? 0 }}</p>
                                    <p class="text-xs text-gray-600">Sold</p>
                                </div>
                                <div class="text-center p-3 bg-yellow-50 rounded-lg border border-yellow-100">
                                    <div class="flex items-center justify-center">
                                        <i class="fas fa-star text-yellow-400 text-xs mr-1"></i>
                                        <span class="text-xl font-bold text-yellow-600">4.8</span>
                                    </div>
                                    <p class="text-xs text-gray-600">Rating</p>
                                </div>
                            </div>
                            
                            <!-- Walmart-style Trust Indicators -->
                            <div class="bg-gray-50 rounded-lg p-3 mb-4">
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                        <span>Verified Seller</span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-shield-alt text-blue-500 mr-2"></i>
                                        <span>Buyer Protection</span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-truck text-purple-500 mr-2"></i>
                                        <span>Fast Shipping</span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <i class="fas fa-undo text-orange-500 mr-2"></i>
                                        <span>Easy Returns</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- eBay-style Badges -->
                            <div class="flex flex-wrap gap-1 mb-4">
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Verified
                                </span>
                                @if($vendor->active_products_count > 20)
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-star mr-1"></i>Popular
                                    </span>
                                @endif
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-bolt mr-1"></i>Fast Ship
                                </span>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <a href="{{ route('products.vendor', $vendor->store_slug) }}" 
                                   class="flex-1 bg-blue-600 text-white px-4 py-2.5 rounded-lg text-center text-sm font-semibold hover:bg-blue-700 transition duration-200">
                                    Visit Store
                                </a>
                                <button class="bg-gray-100 text-gray-700 px-3 py-2.5 rounded-lg hover:bg-gray-200 transition duration-200">
                                    <i class="fas fa-heart text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Walmart-style Pagination -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-600">
                        Showing {{ $vendors->firstItem() }}-{{ $vendors->lastItem() }} of {{ $vendors->total() }} sellers
                    </div>
                    <div class="flex items-center space-x-2">
                        {{ $vendors->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        @else
            <!-- Walmart-style Empty State -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <div class="bg-gray-100 rounded-full w-24 h-24 mx-auto mb-6 flex items-center justify-center">
                    <i class="fas fa-search text-gray-400 text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">No Sellers Found</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    {{ request('search') ? 'We couldn\'t find any sellers matching your search. Try different keywords.' : 'No sellers have joined yet. Check back soon!' }}
                </p>
                @if(request('search'))
                    <a href="{{ route('products.vendors') }}" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition duration-200">
                        Browse All Sellers
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Walmart-style Newsletter Section -->
    <div class="bg-gradient-to-r from-blue-800 to-blue-900 text-white py-12 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="bg-yellow-400 text-blue-800 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-bell text-2xl"></i>
                </div>
                <h3 class="text-2xl font-bold mb-3">Get Notified About New Sellers</h3>
                <p class="text-blue-100 mb-6">Be the first to know when new sellers join our marketplace with exclusive deals</p>
                <div class="max-w-md mx-auto flex gap-2">
                    <input type="email" placeholder="Enter your email address" class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button class="bg-yellow-400 hover:bg-yellow-500 text-blue-800 px-6 py-3 rounded-lg font-semibold transition duration-200">
                        Subscribe
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Walmart-style hover effects */
.group:hover {
    transform: translateY(-2px);
}

/* Custom scrollbar */
select::-webkit-scrollbar {
    width: 6px;
}

select::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

select::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

select::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Smooth transitions */
* {
    transition-property: color, background-color, border-color, transform, box-shadow;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}
</style>
@endsection
