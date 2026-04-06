@extends('admin.layout')

@section('title', 'Shipping Settings')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Shipping Settings</h1>
        <p class="text-gray-600 mt-2">Configure tax rates and general shipping settings</p>
    </div>

    <form method="POST" action="{{ route('admin.shipping.settings.update') }}" class="space-y-8" id="shipping-settings-form">
        @csrf
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
        <!-- General Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-cog mr-2 text-blue-500"></i>General Settings
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="default_shipping_cost" class="block text-sm font-medium text-gray-700 mb-2">
                            Default Shipping Cost (৳)
                        </label>
                        <input type="number" 
                               id="default_shipping_cost" 
                               name="default_shipping_cost" 
                               value="{{ $settings['default_shipping_cost'] ?? 50 }}"
                               step="0.01"
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Applied when no shipping zone matches</p>
                    </div>
                    
                    <div>
                        <label for="free_shipping_threshold" class="block text-sm font-medium text-gray-700 mb-2">
                            Free Shipping Threshold (৳)
                        </label>
                        <input type="number" 
                               id="free_shipping_threshold" 
                               name="free_shipping_threshold" 
                               value="{{ $settings['free_shipping_threshold'] ?? 2000 }}"
                               step="0.01"
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">Order amount for free shipping</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="weight_based_enabled" 
                               name="weight_based_enabled" 
                               value="1"
                               {{ ($settings['weight_based_enabled'] ?? false) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="weight_based_enabled" class="ml-3 text-sm font-medium text-gray-700">
                            Enable Weight-Based Shipping
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="order_value_based_enabled" 
                               name="order_value_based_enabled" 
                               value="1"
                               {{ ($settings['order_value_based_enabled'] ?? true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="order_value_based_enabled" class="ml-3 text-sm font-medium text-gray-700">
                            Enable Order Value-Based Shipping
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tax Settings -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-percentage mr-2 text-green-500"></i>Tax Settings
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="tax_enabled" 
                               name="tax_enabled" 
                               value="1"
                               {{ ($taxSettings['tax_enabled'] ?? true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="tax_enabled" class="ml-3 text-sm font-medium text-gray-700">
                            Enable Tax Calculation
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="shipping_taxable" 
                               name="shipping_taxable" 
                               value="1"
                               {{ ($taxSettings['shipping_taxable'] ?? true) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="shipping_taxable" class="ml-3 text-sm font-medium text-gray-700">
                            Apply Tax on Shipping Cost
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="vat_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            VAT Rate (%)
                        </label>
                        <input type="number" 
                               id="vat_rate" 
                               name="vat_rate" 
                               value="{{ $taxSettings['vat_rate'] ?? 15 }}"
                               step="0.01"
                               min="0"
                               max="100"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-sm text-gray-500">VAT percentage for orders</p>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" 
                               id="tax_inclusive" 
                               name="tax_inclusive" 
                               value="1"
                               {{ ($taxSettings['tax_inclusive'] ?? false) ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="tax_inclusive" class="ml-3 text-sm font-medium text-gray-700">
                            Prices Include Tax
                        </label>
                    </div>
                </div>

                <!-- Tax Calculation Example -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Tax Calculation Example</h3>
                    <div class="text-sm text-gray-600 space-y-1">
                        <p>Example Order: ৳1000 + ৳60 shipping = ৳1060</p>
                        <p>VAT ({{ $taxSettings['vat_rate'] ?? 15 }}%): ৳{{ number_format(1060 * (($taxSettings['vat_rate'] ?? 15) / 100), 2) }}</p>
                        <p class="font-semibold">Total: ৳{{ number_format(1060 + (1060 * (($taxSettings['vat_rate'] ?? 15) / 100)), 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>
</div>

<script>
// Toggle tax settings based on tax enabled checkbox
document.getElementById('tax_enabled').addEventListener('change', function() {
    const taxFields = document.querySelectorAll('#vat_rate, #shipping_taxable, #tax_inclusive');
    taxFields.forEach(field => {
        field.disabled = !this.checked;
        field.closest('div').classList.toggle('opacity-50', !this.checked);
    });
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const taxEnabled = document.getElementById('tax_enabled').checked;
    const taxFields = document.querySelectorAll('#vat_rate, #shipping_taxable, #tax_inclusive');
    taxFields.forEach(field => {
        field.disabled = !taxEnabled;
        field.closest('div').classList.toggle('opacity-50', !taxEnabled);
    });
    
    // Debug form submission
    const form = document.getElementById('shipping-settings-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitting...');
            console.log('Form action:', form.action);
            console.log('Form method:', form.method);
            
            const methodInput = form.querySelector('input[name="_method"]');
            console.log('_method input value:', methodInput ? methodInput.value : 'NOT FOUND');
            
            const tokenInput = form.querySelector('input[name="_token"]');
            console.log('_token input value:', tokenInput ? tokenInput.value : 'NOT FOUND');
        });
    }
});
</script>
@endsection
