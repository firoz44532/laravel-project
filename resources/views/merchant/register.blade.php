@extends('frontend.layout')

@section('title', 'Become a Seller')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Become a Seller</h1>
            <p class="text-lg text-gray-600">Join our marketplace and start selling your products to thousands of customers</p>
        </div>

        <!-- Benefits -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="text-center p-6 bg-white rounded-lg shadow">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-line text-white text-2xl"></i>
                </div>
                <h3 class="font-semibold text-lg mb-2">Grow Your Business</h3>
                <p class="text-gray-600">Reach thousands of customers and increase your sales</p>
            </div>
            <div class="text-center p-6 bg-white rounded-lg shadow">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-white text-2xl"></i>
                </div>
                <h3 class="font-semibold text-lg mb-2">Secure Payments</h3>
                <p class="text-gray-600">Get paid securely and on time with our reliable payment system</p>
            </div>
            <div class="text-center p-6 bg-white rounded-lg shadow">
                <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-white text-2xl"></i>
                </div>
                <h3 class="font-semibold text-lg mb-2">24/7 Support</h3>
                <p class="text-gray-600">Get dedicated support to help you succeed</p>
            </div>
        </div>

        <!-- Registration Form -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold mb-6">Merchant Registration</h2>
            
            <form method="POST" action="{{ route('merchant.register.store') }}" class="space-y-6">
                @csrf
                
                <!-- Store Information -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold mb-4">Store Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="store_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Store Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="store_name" 
                                   name="store_name" 
                                   required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"
                                   placeholder="Enter your store name">
                            @error('store_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="store_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Store Email
                            </label>
                            <input type="email" 
                                   id="store_email" 
                                   name="store_email" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"
                                   placeholder="store@example.com">
                            @error('store_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="store_description" class="block text-sm font-medium text-gray-700 mb-2">
                            Store Description
                        </label>
                        <textarea id="store_description" 
                                  name="store_description" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"
                                  placeholder="Tell us about your store and what you sell..."></textarea>
                        @error('store_description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold mb-4">Contact Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="store_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input type="tel" 
                                   id="store_phone" 
                                   name="store_phone" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"
                                   placeholder="+880 1XXX XXXXXX">
                            @error('store_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="store_city" class="block text-sm font-medium text-gray-700 mb-2">
                                City
                            </label>
                            <input type="text" 
                                   id="store_city" 
                                   name="store_city" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"
                                   placeholder="Dhaka">
                            @error('store_city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="store_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address
                        </label>
                        <input type="text" 
                               id="store_address" 
                               name="store_address" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"
                               placeholder="Enter your store address">
                        @error('store_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <label for="store_country" class="block text-sm font-medium text-gray-700 mb-2">
                            Country
                        </label>
                        <input type="text" 
                               id="store_country" 
                               name="store_country" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"
                               placeholder="Bangladesh">
                        @error('store_country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Payment Information -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Payment Information (for receiving payments)</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                                Payment Method
                            </label>
                            <select id="payment_method" 
                                    name="payment_method" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                                <option value="">Select payment method</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="rocket">Rocket</option>
                            </select>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="account_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Account Holder Name
                            </label>
                            <input type="text" 
                                   id="account_name" 
                                   name="account_name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"
                                   placeholder="John Doe">
                            @error('account_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="account_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Account/Phone Number
                            </label>
                            <input type="text" 
                                   id="account_number" 
                                   name="account_number" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"
                                   placeholder="01XXX XXXXXX">
                            @error('account_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="bank_fields" style="display: none;">
                            <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Bank Name
                            </label>
                            <input type="text" 
                                   id="bank_name" 
                                   name="bank_name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"
                                   placeholder="Dutch-Bangla Bank">
                            @error('bank_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Terms and Submit -->
                <div class="pt-6">
                    <div class="mb-6">
                        <label class="flex items-start">
                            <input type="checkbox" 
                                   required
                                   class="mt-1 mr-2 text-primary focus:ring-primary">
                            <span class="text-sm text-gray-600">
                                I agree to the <a href="#" class="text-primary hover:underline">Terms and Conditions</a> 
                                and <a href="#" class="text-primary hover:underline">Seller Agreement</a>
                            </span>
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" 
                                class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-orange-600 transition">
                            Submit Application
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Info Section -->
        <div class="mt-12 bg-blue-50 rounded-lg p-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 text-xl mt-1 mr-3"></i>
                <div>
                    <h4 class="font-semibold text-blue-900 mb-2">What happens next?</h4>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Your application will be reviewed within 24-48 hours</li>
                        <li>• We'll verify your business information and payment details</li>
                        <li>• Once approved, you'll get access to your merchant dashboard</li>
                        <li>• You can start adding products and selling immediately</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethod = document.getElementById('payment_method');
    const bankFields = document.getElementById('bank_fields');
    
    paymentMethod.addEventListener('change', function() {
        if (this.value === 'bank_transfer') {
            bankFields.style.display = 'block';
        } else {
            bankFields.style.display = 'none';
        }
    });
});
</script>
@endsection
