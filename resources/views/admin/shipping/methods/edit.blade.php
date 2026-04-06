@extends('admin.layout')

@section('title', 'Edit Shipping Method')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center">
            <a href="{{ route('admin.shipping.methods') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                <i class="fas fa-arrow-left mr-2"></i> Back to Methods
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Shipping Method</h1>
                <p class="text-gray-600 mt-2">Update shipping method configuration</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Method Details</h2>
        </div>
        <form method="POST" action="{{ route('admin.shipping.methods.update', $method) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Method Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="code" 
                               name="code" 
                               required
                               value="{{ $method->code }}"
                               placeholder="e.g., standard, express, pickup"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Unique identifier for this method (lowercase, no spaces)</p>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Method Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               required
                               value="{{ $method->name }}"
                               placeholder="e.g., Standard Delivery"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  placeholder="Brief description of this shipping method"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $method->description }}</textarea>
                    </div>

                    <div>
                        <label for="estimated_days" class="block text-sm font-medium text-gray-700 mb-2">
                            Estimated Delivery Time
                        </label>
                        <input type="text" 
                               id="estimated_days" 
                               name="estimated_days" 
                               value="{{ $method->estimated_days }}"
                               placeholder="e.g., 2-3 business days"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Time estimate shown to customers</p>
                    </div>
                </div>

                <!-- Configuration -->
                <div class="space-y-6">
                    <div>
                        <label for="base_cost" class="block text-sm font-medium text-gray-700 mb-2">
                            Base Cost (৳) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="base_cost" 
                               name="base_cost" 
                               required
                               step="0.01"
                               min="0"
                               value="{{ $method->base_cost }}"
                               placeholder="0.00"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Default cost before zone-specific pricing</p>
                    </div>

                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                            Sort Order
                        </label>
                        <input type="number" 
                               id="sort_order" 
                               name="sort_order" 
                               min="0"
                               value="{{ $method->sort_order }}"
                               placeholder="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Order in which methods appear (lower numbers first)</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ $method->is_active ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">
                                Active
                            </label>
                        </div>
                        <p class="text-sm text-gray-500 ml-7">Customers can select this method at checkout</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="tracking_available" 
                                   name="tracking_available" 
                                   value="1"
                                   {{ $method->tracking_available ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="tracking_available" class="ml-3 text-sm font-medium text-gray-700">
                                Tracking Available
                            </label>
                        </div>
                        <p class="text-sm text-gray-500 ml-7">Customers can track shipments for this method</p>
                    </div>

                    <!-- Zone Coverage Info -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Current Zone Coverage</h3>
                        <div class="text-sm text-gray-600">
                            <p>Active in {{ $method->activeShippingZones->count() }} zones:</p>
                            @if($method->activeShippingZones->count() > 0)
                                <div class="mt-2 space-y-1">
                                    @foreach($method->activeShippingZones as $zone)
                                        <div class="flex justify-between items-center">
                                            <span>{{ $zone->name }}</span>
                                            <span class="text-gray-500">৳{{ number_format($zone->pivot->cost, 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-400 italic">Not assigned to any zones</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('admin.shipping.methods') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <i class="fas fa-save mr-2"></i>Update Method
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-format code input
    const codeInput = document.getElementById('code');
    codeInput.addEventListener('input', function() {
        this.value = this.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
    });
});
</script>
@endsection
