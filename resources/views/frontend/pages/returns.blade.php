@extends('frontend.layout')

@section('title', 'Returns & Refunds')
@section('header', 'Returns & Refunds')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Returns & Refunds</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Our hassle-free return policy ensures you can shop with confidence
        </p>
    </div>

    <!-- Return Policy Summary -->
    <div class="bg-green-50 rounded-lg p-8 mb-12">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">7-Day Return Policy</h2>
                <p class="text-gray-600">Return any item within 7 days of delivery for a full refund or exchange</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">7 Days</div>
                <p class="text-sm text-gray-600">Return Window</p>
            </div>
        </div>
    </div>

    <!-- Return Conditions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Return Conditions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="font-semibold text-gray-900 mb-4 text-green-600">✓ Eligible for Return</h3>
                <ul class="space-y-3 text-gray-600">
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Unused and unworn items</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Original packaging and tags intact</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Within 7 days of delivery</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>Proof of purchase (order number)</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                        <span>No damage or alterations</span>
                    </li>
                </ul>
            </div>
            
            <div>
                <h3 class="font-semibold text-gray-900 mb-4 text-red-600">✗ Not Eligible for Return</h3>
                <ul class="space-y-3 text-gray-600">
                    <li class="flex items-start">
                        <i class="fas fa-times text-red-500 mt-1 mr-3"></i>
                        <span>Used or worn items</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-times text-red-500 mt-1 mr-3"></i>
                        <span>Missing tags or packaging</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-times text-red-500 mt-1 mr-3"></i>
                        <span>After 7 days from delivery</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-times text-red-500 mt-1 mr-3"></i>
                        <span>Personalized or custom items</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-times text-red-500 mt-1 mr-3"></i>
                        <span>Perishable items (food, cosmetics)</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Return Process -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">How to Return</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-orange-500 font-bold text-xl">1</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Contact Us</h3>
                <p class="text-gray-600 text-sm">Call or email our customer service to initiate your return</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-blue-500 font-bold text-xl">2</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Package Item</h3>
                <p class="text-gray-600 text-sm">Pack the item securely in original packaging</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-green-500 font-bold text-xl">3</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Ship to Us</h3>
                <p class="text-gray-600 text-sm">Send the package to our return center</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-purple-500 font-bold text-xl">4</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Get Refund</h3>
                <p class="text-gray-600 text-sm">Receive your refund or exchange within 5-7 days</p>
            </div>
        </div>
    </div>

    <!-- Refund Options -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-money-bill-wave text-green-500 text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Full Refund</h2>
            </div>
            <ul class="space-y-3 text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                    <span>Refund to original payment method</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                    <span>Processed within 5-7 business days</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                    <span>Return shipping fee may apply</span>
                </li>
            </ul>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-exchange-alt text-blue-500 text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Exchange</h2>
            </div>
            <ul class="space-y-3 text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                    <span>Exchange for different size or color</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                    <span>No additional shipping charges</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                    <span>Subject to availability</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Contact Information -->
    <div class="bg-gray-50 rounded-lg p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Contact Us for Returns</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Customer Service</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-phone text-orange-500 mr-3"></i>
                        <span class="text-gray-600">{{ \App\Services\SettingsService::get('contact_phone', '+880 1234 5678') }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-blue-500 mr-3"></i>
                        <span class="text-gray-600">{{ \App\Services\SettingsService::get('contact_email', 'returns@shopbd.com') }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock text-green-500 mr-3"></i>
                        <span class="text-gray-600">Available: Sat-Thu, 9AM-6PM</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Return Address</h3>
                <div class="bg-white rounded-lg p-4 border border-gray-200">
                    <p class="text-gray-600">
                        {{ \App\Services\SettingsService::get('site_name', 'ShopBD') }} Returns<br>
                        {{ \App\Services\SettingsService::get('address', '123 Main Road, Dhaka, Bangladesh') }}<br>
                        Attention: Returns Department<br>
                        Phone: {{ \App\Services\SettingsService::get('contact_phone', '+880 1234 5678') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Important Notes -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Important Notes</h2>
        <div class="space-y-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Quality Check</h3>
                    <p class="text-gray-600">All returned items undergo quality inspection. Items that don't meet return conditions will be returned to the customer.</p>
                </div>
            </div>
            
            <div class="flex items-start">
                <i class="fas fa-shipping-fast text-orange-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Return Shipping</h3>
                    <p class="text-gray-600">Customers are responsible for return shipping costs unless the item is defective or was sent in error.</p>
                </div>
            </div>
            
            <div class="flex items-start">
                <i class="fas fa-credit-card text-green-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Refund Processing</h3>
                    <p class="text-gray-600">Refunds are processed within 5-7 business days after we receive and inspect the returned item.</p>
                </div>
            </div>
            
            <div class="flex items-start">
                <i class="fas fa-box text-purple-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Damaged Items</h3>
                    <p class="text-gray-600">If you receive a damaged item, please contact us immediately. We'll arrange for a replacement or full refund.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-orange-50 rounded-lg p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Common Questions</h2>
        <div class="space-y-4">
            <div class="bg-white rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-2">How long does the return process take?</h3>
                <p class="text-gray-600">Typically 5-7 business days from when we receive your returned item.</p>
            </div>
            
            <div class="bg-white rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-2">Can I return items bought on sale?</h3>
                <p class="text-gray-600">Yes, sale items can be returned within 7 days if they meet our return conditions.</p>
            </div>
            
            <div class="bg-white rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-2">What if I lost my return label?</h3>
                <p class="text-gray-600">Contact our customer service team and we'll email you a new return label.</p>
            </div>
        </div>
    </div>
</div>
@endsection
