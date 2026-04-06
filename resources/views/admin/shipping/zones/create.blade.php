@extends('admin.layout')

@section('title', 'Create Shipping Zone')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center">
            <a href="{{ route('admin.shipping.zones') }}" class="text-gray-600 hover:text-gray-900 mr-4">
                <i class="fas fa-arrow-left mr-2"></i> Back to Zones
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create Shipping Zone</h1>
                <p class="text-gray-600 mt-2">Add a new geographic zone with specific shipping rates</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Zone Details</h2>
        </div>
        <form method="POST" action="{{ route('admin.shipping.zones.store') }}" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Zone Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="code" 
                               name="code" 
                               required
                               placeholder="e.g., dhaka_metro, outside_dhaka"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Unique identifier (lowercase, underscores only)</p>
                    </div>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Zone Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               required
                               placeholder="e.g., Dhaka Metro"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  placeholder="Brief description of this shipping zone"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="space-y-6">
                    <div>
                        <label for="default_cost" class="block text-sm font-medium text-gray-700 mb-2">
                            Default Shipping Cost (৳) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="default_cost" 
                               name="default_cost" 
                               required
                               step="0.01"
                               min="0"
                               placeholder="60.00"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Cost for standard delivery</p>
                    </div>

                    <div>
                        <label for="express_cost" class="block text-sm font-medium text-gray-700 mb-2">
                            Express Shipping Cost (৳) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="express_cost" 
                               name="express_cost" 
                               required
                               step="0.01"
                               min="0"
                               placeholder="100.00"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Cost for express delivery</p>
                    </div>

                    <div>
                        <label for="delivery_days" class="block text-sm font-medium text-gray-700 mb-2">
                            Standard Delivery Time
                        </label>
                        <input type="text" 
                               id="delivery_days" 
                               name="delivery_days" 
                               placeholder="e.g., 2-3 business days"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="express_days" class="block text-sm font-medium text-gray-700 mb-2">
                            Express Delivery Time
                        </label>
                        <input type="text" 
                               id="express_days" 
                               name="express_days" 
                               placeholder="e.g., 1-2 business days"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-2">
                            Sort Order
                        </label>
                        <input type="number" 
                               id="sort_order" 
                               name="sort_order" 
                               min="0"
                               placeholder="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Display order (lower numbers first)</p>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="is_active" 
                               name="is_active" 
                               value="1"
                               checked
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">
                            Active
                        </label>
                    </div>
                </div>
            </div>

            <!-- Geographic Coverage -->
            <div class="mt-8 border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Geographic Coverage</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="cities" class="block text-sm font-medium text-gray-700 mb-2">
                            Cities <span class="text-gray-500">(comma-separated)</span>
                        </label>
                        <textarea id="cities" 
                                  name="cities" 
                                  rows="4"
                                  placeholder="Dhaka, Chittagong, Sylhet, Rajshahi"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        <p class="mt-1 text-sm text-gray-500">List of cities covered by this zone</p>
                    </div>

                    <div>
                        <label for="areas" class="block text-sm font-medium text-gray-700 mb-2">
                            Specific Areas <span class="text-gray-500">(comma-separated)</span>
                        </label>
                        <textarea id="areas" 
                                  name="areas" 
                                  rows="4"
                                  placeholder="Dhanmondi, Gulshan, Banani, Baridhara"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        <p class="mt-1 text-sm text-gray-500">Specific areas within cities (optional)</p>
                    </div>
                </div>
            </div>

            <!-- Shipping Methods -->
            <div class="mt-8 border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Available Shipping Methods</h3>
                
                <div class="space-y-4">
                    @php
                        $availableMethods = \App\Models\ShippingMethod::active()->ordered()->get();
                    @endphp
                    
                    @foreach($availableMethods as $method)
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   name="methods[]" 
                                   value="{{ $method->id }}"
                                   id="method_{{ $method->id }}"
                                   checked
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="method_{{ $method->id }}" class="ml-3 text-sm font-medium text-gray-700">
                                {{ $method->name }}
                            </label>
                            <input type="number" 
                                   name="method_costs[{{ $method->id }}]" 
                                   placeholder="Cost"
                                   step="0.01"
                                   min="0"
                                   class="ml-4 w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            <span class="text-sm text-gray-500 ml-2">৳</span>
                        </div>
                    @endforeach
                </div>
                <p class="mt-2 text-sm text-gray-500">Set specific costs for each method in this zone (overrides default costs)</p>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('admin.shipping.zones') }}" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <i class="fas fa-save mr-2"></i>Create Zone
                </button>
            </div>
        </form>
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
