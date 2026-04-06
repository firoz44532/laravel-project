@extends('admin.layout')

@section('title', 'Brands')
@section('header', 'Brands Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Brands</h1>
                    <p class="text-sm text-gray-500 mt-1">Manage product brands and manufacturers</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.brands.create') }}" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Brand
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Search and Filters -->
        <div class="px-6 py-4 border-b border-gray-200">
            <form method="GET" action="{{ route('admin.brands.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" 
                               placeholder="Search brands..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    
                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select id="status" name="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    
                    <!-- Featured Filter -->
                    <div>
                        <label for="featured" class="block text-sm font-medium text-gray-700 mb-1">Featured</label>
                        <select id="featured" name="featured" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            <option value="">All Brands</option>
                            <option value="yes" {{ request('featured') == 'yes' ? 'selected' : '' }}>Featured</option>
                            <option value="no" {{ request('featured') == 'no' ? 'selected' : '' }}>Not Featured</option>
                        </select>
                    </div>
                    
                    <!-- Search Button -->
                    <div class="flex items-end">
                        <button type="submit" 
                                class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </div>
                </div>
                
                @if(request()->hasAny(['search', 'status', 'featured']))
                    <div class="flex items-center space-x-2 mt-4">
                        <span class="text-sm text-gray-500">Active filters:</span>
                        @if(request('search'))
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                Search: {{ request('search') }}
                            </span>
                        @endif
                        @if(request('status'))
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                Status: {{ request('status') }}
                            </span>
                        @endif
                        @if(request('featured'))
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">
                                Featured: {{ request('featured') }}
                            </span>
                        @endif
                        <a href="{{ route('admin.brands.index') }}" class="text-sm text-orange-600 hover:text-orange-800">
                            <i class="fas fa-times mr-1"></i>Clear all
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Brands Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Brand
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Products
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sort Order
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($brands as $brand)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($brand->logo)
                                        <img src="{{ asset('storage/' . $brand->logo) }}" 
                                             alt="{{ $brand->name }}" class="w-10 h-10 object-cover rounded">
                                    @else
                                        <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-industry text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900">{{ $brand->name }}</div>
                                            @if($brand->is_featured)
                                                <i class="fas fa-star text-yellow-400 ml-2" title="Featured"></i>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $brand->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $brand->products_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $brand->sort_order }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-2">
                                    @if($brand->is_active)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactive
                                        </span>
                                    @endif
                                    @if($brand->is_featured)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Featured
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.brands.show', $brand) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.brands.edit', $brand) }}" 
                                       class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="toggleStatus({{ $brand->id }})" 
                                            class="text-yellow-600 hover:text-yellow-900" title="Toggle Status">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                    <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this brand?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                <div class="py-8">
                                    <i class="fas fa-industry text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-lg font-medium text-gray-900 mb-2">No brands found</p>
                                    <p class="text-sm text-gray-500 mb-4">Get started by creating your first brand.</p>
                                    <a href="{{ route('admin.brands.create') }}" 
                                       class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                                        <i class="fas fa-plus mr-2"></i>Add Brand
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($brands->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $brands->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleStatus(brandId) {
    fetch(`/admin/brands/${brandId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>
@endpush
