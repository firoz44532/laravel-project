@extends('admin.layout')

@section('title', 'Shipping Settings')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Shipping Settings</h1>
                <p class="text-gray-600 mt-2">Configure tax rates and general shipping settings</p>
            </div>
            <div class="flex items-center space-x-2">
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                    <i class="fas fa-check-circle mr-1"></i>System Active
                </span>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Default Shipping</p>
                    <p class="text-2xl font-bold">৳{{ $settings['default_shipping_cost'] ?? 50 }}</p>
                </div>
                <i class="fas fa-truck text-3xl text-blue-200"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Free Shipping</p>
                    <p class="text-2xl font-bold">৳{{ $settings['free_shipping_threshold'] ?? 2000 }}</p>
                </div>
                <i class="fas fa-gift text-3xl text-green-200"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">VAT Rate</p>
                    <p class="text-2xl font-bold">{{ $taxSettings['vat_rate'] ?? 15 }}%</p>
                </div>
                <i class="fas fa-percentage text-3xl text-purple-200"></i>
            </div>
        </div>
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Tax Status</p>
                    <p class="text-2xl font-bold">{{ ($taxSettings['tax_enabled'] ?? true) ? 'Active' : 'Inactive' }}</p>
                </div>
                <i class="fas fa-calculator text-3xl text-orange-200"></i>
            </div>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="mt-8">
        <form method="POST" action="{{ route('admin.shipping.settings.update') }}" id="settings-form">
            @csrf
            <input type="hidden" name="_method" value="PUT">
            
            <!-- General Settings -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white">
                        <i class="fas fa-cog mr-2"></i>General Settings
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <label for="default_shipping_cost" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-truck mr-2 text-blue-500"></i>Default Shipping Cost (৳): <span class="text-blue-600 font-bold">৳{{ $settings['default_shipping_cost'] ?? 50 }}</span>
                            </label>
                            <input type="number" 
                                   id="default_shipping_cost" 
                                   name="default_shipping_cost" 
                                   value="{{ $settings['default_shipping_cost'] ?? 50 }}"
                                   step="0.01"
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm">
                            <p class="mt-2 text-sm text-gray-500"><i class="fas fa-info-circle mr-1"></i>Applied when no shipping zone matches</p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <label for="free_shipping_threshold" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-gift mr-2 text-green-500"></i>Free Shipping Threshold (৳): <span class="text-blue-600 font-bold">৳{{ $settings['free_shipping_threshold'] ?? 2000 }}</span>
                            </label>
                            <input type="number" 
                                   id="free_shipping_threshold" 
                                   name="free_shipping_threshold" 
                                   value="{{ $settings['free_shipping_threshold'] ?? 2000 }}"
                                   step="0.01"
                                   min="0"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm">
                            <p class="mt-2 text-sm text-gray-500"><i class="fas fa-info-circle mr-1"></i>Order amount for free shipping</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="weight_based_enabled" 
                                           name="weight_based_enabled" 
                                           value="1"
                                           {{ ($settings['weight_based_enabled'] ?? false) ? 'checked' : '' }}
                                           class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="weight_based_enabled" class="ml-3 text-sm font-semibold text-gray-700">
                                        <i class="fas fa-weight mr-2 text-purple-500"></i>Enable Weight-Based Shipping
                                    </label>
                                </div>
                                <span class="bg-{{ ($settings['weight_based_enabled'] ?? false) ? 'green' : 'red' }}-100 text-{{ ($settings['weight_based_enabled'] ?? false) ? 'green' : 'red' }}-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    {{ ($settings['weight_based_enabled'] ?? false) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="order_value_based_enabled" 
                                           name="order_value_based_enabled" 
                                           value="1"
                                           {{ ($settings['order_value_based_enabled'] ?? true) ? 'checked' : '' }}
                                           class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="order_value_based_enabled" class="ml-3 text-sm font-semibold text-gray-700">
                                        <i class="fas fa-shopping-cart mr-2 text-orange-500"></i>Enable Order Value-Based Shipping
                                    </label>
                                </div>
                                <span class="bg-{{ ($settings['order_value_based_enabled'] ?? true) ? 'green' : 'red' }}-100 text-{{ ($settings['order_value_based_enabled'] ?? true) ? 'green' : 'red' }}-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    {{ ($settings['order_value_based_enabled'] ?? true) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tax Settings -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden mt-8">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                    <h2 class="text-lg font-semibold text-white">
                        <i class="fas fa-percentage mr-2"></i>Tax Settings
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="tax_enabled" 
                                           name="tax_enabled" 
                                           value="1"
                                           {{ ($taxSettings['tax_enabled'] ?? true) ? 'checked' : '' }}
                                           class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                    <label for="tax_enabled" class="ml-3 text-sm font-semibold text-gray-700">
                                        <i class="fas fa-calculator mr-2 text-green-500"></i>Enable Tax Calculation
                                    </label>
                                </div>
                                <span class="bg-{{ ($taxSettings['tax_enabled'] ?? true) ? 'green' : 'red' }}-100 text-{{ ($taxSettings['tax_enabled'] ?? true) ? 'green' : 'red' }}-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    {{ ($taxSettings['tax_enabled'] ?? true) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="shipping_taxable" 
                                           name="shipping_taxable" 
                                           value="1"
                                           {{ ($taxSettings['shipping_taxable'] ?? true) ? 'checked' : '' }}
                                           class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                    <label for="shipping_taxable" class="ml-3 text-sm font-semibold text-gray-700">
                                        <i class="fas fa-truck mr-2 text-blue-500"></i>Apply Tax on Shipping Cost
                                    </label>
                                </div>
                                <span class="bg-{{ ($taxSettings['shipping_taxable'] ?? true) ? 'green' : 'red' }}-100 text-{{ ($taxSettings['shipping_taxable'] ?? true) ? 'green' : 'red' }}-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    {{ ($taxSettings['shipping_taxable'] ?? true) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <label for="vat_rate" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-percentage mr-2 text-purple-500"></i>VAT Rate (%): <span class="text-blue-600 font-bold">{{ $taxSettings['vat_rate'] ?? 15 }}%</span>
                            </label>
                            <input type="number" 
                                   id="vat_rate" 
                                   name="vat_rate" 
                                   value="{{ $taxSettings['vat_rate'] ?? 15 }}"
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white shadow-sm">
                            <p class="mt-2 text-sm text-gray-500"><i class="fas fa-info-circle mr-1"></i>VAT percentage for orders</p>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="tax_inclusive" 
                                           name="tax_inclusive" 
                                           value="1"
                                           {{ ($taxSettings['tax_inclusive'] ?? false) ? 'checked' : '' }}
                                           class="h-5 w-5 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                    <label for="tax_inclusive" class="ml-3 text-sm font-semibold text-gray-700">
                                        <i class="fas fa-tag mr-2 text-orange-500"></i>Prices Include Tax
                                    </label>
                                </div>
                                <span class="bg-{{ ($taxSettings['tax_inclusive'] ?? false) ? 'green' : 'red' }}-100 text-{{ ($taxSettings['tax_inclusive'] ?? false) ? 'green' : 'red' }}-800 px-2 py-1 rounded-full text-xs font-semibold">
                                    {{ ($taxSettings['tax_inclusive'] ?? false) ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Tax Calculation Example -->
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-4 border border-purple-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3"><i class="fas fa-chart-line mr-2 text-purple-500"></i>Tax Calculation Example</h3>
                        <div class="text-sm text-gray-600 space-y-1">
                            <p><strong>Example Order:</strong> ৳1000 + ৳60 shipping = ৳1060</p>
                            <p><strong>VAT ({{ $taxSettings['vat_rate'] ?? 15 }}%):</strong> ৳{{ number_format(1060 * (($taxSettings['vat_rate'] ?? 15) / 100), 2) }}</p>
                            <p class="font-semibold text-purple-600"><strong>Total:</strong> ৳{{ number_format(1060 + (1060 * (($taxSettings['vat_rate'] ?? 15) / 100)), 2) }}</p>
                        </div>
                    </div>

                    <!-- Tax Calculation Test -->
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg p-4 border border-blue-200">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3"><i class="fas fa-calculator mr-2 text-blue-500"></i>Tax Calculation Test</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div class="bg-white rounded-lg p-3 border border-blue-100">
                                <label for="test_cart_total" class="block text-sm font-medium text-gray-700 mb-1">Cart Total: <span class="text-blue-600 font-semibold">৳1000</span></label>
                                <input type="number" id="test_cart_total" value="1000" step="0.01" min="0" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-blue-100">
                                <label for="test_shipping_cost" class="block text-sm font-medium text-gray-700 mb-1">Shipping Cost: <span class="text-blue-600 font-semibold">৳60</span></label>
                                <input type="number" id="test_shipping_cost" value="60" step="0.01" min="0" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                            <div class="bg-white rounded-lg p-3 border border-blue-100">
                                <label for="test_weight" class="block text-sm font-medium text-gray-700 mb-1">Weight: <span class="text-blue-600 font-semibold">1 kg</span></label>
                                <input type="number" id="test_weight" value="1" step="0.01" min="0" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                        </div>
                        <div class="mb-4 p-3 bg-white rounded-lg border border-blue-200">
                            <p class="text-sm text-gray-600">
                                <strong>Current VAT Rate:</strong> <span class="text-blue-600 font-semibold">{{ $taxSettings['vat_rate'] ?? 15 }}%</span> | 
                                <strong>Tax Enabled:</strong> <span class="text-green-600 font-semibold">{{ ($taxSettings['tax_enabled'] ?? true) ? 'Yes' : 'No' }}</span> | 
                                <strong>Shipping Tax:</strong> <span class="text-green-600 font-semibold">{{ ($taxSettings['shipping_taxable'] ?? true) ? 'Yes' : 'No' }}</span>
                            </p>
                        </div>
                        <button type="button" id="test-tax-btn" 
                                class="bg-gradient-to-r from-green-500 to-green-600 text-white px-6 py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition-all font-medium text-sm shadow-lg">
                            <i class="fas fa-calculator mr-2"></i>Calculate Tax
                        </button>
                        <div id="tax-test-result" class="mt-4 hidden">
                            <div class="bg-white rounded-lg p-4 border border-blue-200 shadow-lg">
                                <h4 class="text-sm font-semibold text-gray-800 mb-2"><i class="fas fa-chart-line mr-2 text-blue-500"></i>Calculation Result:</h4>
                                <div id="tax-test-details" class="text-sm text-gray-600 space-y-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-8 py-4 rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all font-medium shadow-lg">
                    <i class="fas fa-save mr-2"></i>Save Settings
                </button>
            </div>
        </form>
    </div>
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

// Tax calculation test functionality
document.getElementById('test-tax-btn').addEventListener('click', function() {
    const cartTotal = parseFloat(document.getElementById('test_cart_total').value) || 0;
    const shippingCost = parseFloat(document.getElementById('test_shipping_cost').value) || 0;
    const weight = parseFloat(document.getElementById('test_weight').value) || 0;
    const vatRate = parseFloat(document.getElementById('vat_rate').value) || 15;
    const taxEnabled = document.getElementById('tax_enabled').checked;
    const shippingTaxable = document.getElementById('shipping_taxable').checked;
    const taxInclusive = document.getElementById('tax_inclusive').checked;
    
    // Calculate tax
    let subtotal = cartTotal;
    let totalTax = 0;
    let shippingTax = 0;
    
    if (taxEnabled) {
        if (taxInclusive) {
            // Prices already include tax
            totalTax = subtotal * (vatRate / (100 + vatRate));
            if (shippingTaxable) {
                shippingTax = shippingCost * (vatRate / (100 + vatRate));
            }
        } else {
            // Add tax on top
            totalTax = subtotal * (vatRate / 100);
            if (shippingTaxable) {
                shippingTax = shippingCost * (vatRate / 100);
            }
        }
    }
    
    const totalAmount = subtotal + shippingCost + totalTax + shippingTax;
    
    // Display results
    const resultDiv = document.getElementById('tax-test-result');
    const detailsDiv = document.getElementById('tax-test-details');
    
    detailsDiv.innerHTML = `
        <p><strong>Cart Total:</strong> ৳${subtotal.toFixed(2)}</p>
        <p><strong>Shipping Cost:</strong> ৳${shippingCost.toFixed(2)}</p>
        <p><strong>Subtotal:</strong> ৳${(subtotal + shippingCost).toFixed(2)}</p>
        <p><strong>VAT Rate:</strong> ${vatRate}%</p>
        <p><strong>Product Tax:</strong> ৳${totalTax.toFixed(2)}</p>
        <p><strong>Shipping Tax:</strong> ৳${shippingTax.toFixed(2)}</p>
        <p><strong>Total Tax:</strong> ৳${(totalTax + shippingTax).toFixed(2)}</p>
        <p class="font-semibold text-blue-600"><strong>Grand Total:</strong> ৳${totalAmount.toFixed(2)}</p>
    `;
    
    resultDiv.classList.remove('hidden');
});

// Update displayed values when inputs change
function updateDisplayedValues() {
    const cartTotal = parseFloat(document.getElementById('test_cart_total').value) || 0;
    const shippingCost = parseFloat(document.getElementById('test_shipping_cost').value) || 0;
    const weight = parseFloat(document.getElementById('test_weight').value) || 0;
    
    // Update label spans with current values
    const cartLabel = document.querySelector('label[for="test_cart_total"] span');
    const shippingLabel = document.querySelector('label[for="test_shipping_cost"] span');
    const weightLabel = document.querySelector('label[for="test_weight"] span');
    
    if (cartLabel) cartLabel.textContent = `৳${cartTotal.toFixed(2)}`;
    if (shippingLabel) shippingLabel.textContent = `৳${shippingCost.toFixed(2)}`;
    if (weightLabel) weightLabel.textContent = `${weight.toFixed(2)} kg`;
}

// Add event listeners to update values in real-time
document.getElementById('test_cart_total').addEventListener('input', updateDisplayedValues);
document.getElementById('test_shipping_cost').addEventListener('input', updateDisplayedValues);
document.getElementById('test_weight').addEventListener('input', updateDisplayedValues);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const taxEnabled = document.getElementById('tax_enabled').checked;
    const taxFields = document.querySelectorAll('#vat_rate, #shipping_taxable, #tax_inclusive');
    taxFields.forEach(field => {
        field.disabled = !taxEnabled;
        field.closest('div').classList.toggle('opacity-50', !this.checked);
    });
    
    // Initialize displayed values
    updateDisplayedValues();
    
    // Move all form inputs into the form
    const form = document.getElementById('settings-form');
    const inputs = document.querySelectorAll('input:not(#settings-form input)');
    inputs.forEach(input => {
        if (input.type !== 'checkbox' || input.checked) {
            form.appendChild(input.cloneNode(true));
            input.style.display = 'none';
        }
    });
});
</script>
@endsection
