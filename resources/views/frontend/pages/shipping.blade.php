@extends('frontend.layout')

@section('title', 'Shipping Information')
@section('header', 'Shipping Information')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Shipping & Delivery</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Everything you need to know about getting your orders delivered safely and quickly
        </p>
    </div>

    <!-- Delivery Options -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-truck text-orange-500 text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Standard Delivery</h2>
            </div>
            <ul class="space-y-3 text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                    <span>2-3 business days within Dhaka</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                    <span>3-5 business days outside Dhaka</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                    <span>৳60 for Dhaka Metro</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                    <span>৳100-৳150 for other areas</span>
                </li>
            </ul>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-bolt text-blue-500 text-xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900">Express Delivery</h2>
            </div>
            <ul class="space-y-3 text-gray-600">
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                    <span>1-2 business days nationwide</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                    <span>Priority handling</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                    <span>Real-time tracking</span>
                </li>
                <li class="flex items-start">
                    <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                    <span>৳150-৳200 based on location</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Free Shipping -->
    <div class="bg-green-50 rounded-lg p-8 mb-12">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Free Shipping</h2>
                <p class="text-gray-600">Get free delivery on orders over ৳2000</p>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600 mb-2">৳2000+</div>
                <p class="text-sm text-gray-600">Free Delivery</p>
            </div>
        </div>
    </div>

    <!-- Delivery Process -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">How It Works</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-orange-500 font-bold text-xl">1</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Place Order</h3>
                <p class="text-gray-600 text-sm">Browse and select your favorite products</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-blue-500 font-bold text-xl">2</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Processing</h3>
                <p class="text-gray-600 text-sm">We prepare your order (1-2 hours)</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-green-500 font-bold text-xl">3</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Shipping</h3>
                <p class="text-gray-600 text-sm">Your order is on its way</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-purple-500 font-bold text-xl">4</span>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">Delivery</h3>
                <p class="text-gray-600 text-sm">Receive your order</p>
            </div>
        </div>
    </div>

    <!-- Delivery Areas -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Delivery Areas</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Dhaka Metro</h3>
                <ul class="space-y-2 text-gray-600">
                    <li>• Dhanmondi, Gulshan, Banani, Baridhara</li>
                    <li>• Mirpur, Mohammadpur, Uttara</li>
                    <li>• Farmgate, Shahbag, New Market</li>
                    <li>• And all other areas within Dhaka city</li>
                </ul>
                <p class="text-sm text-orange-600 mt-3">Delivery: 1-2 days | Charge: ৳60</p>
            </div>
            
            <div>
                <h3 class="font-semibold text-gray-900 mb-4">Major Cities</h3>
                <ul class="space-y-2 text-gray-600">
                    <li>• Chittagong, Sylhet, Rajshahi</li>
                    <li>• Khulna, Barisal, Rangpur</li>
                    <li>• Mymensingh, Comilla</li>
                    <li>• And all other district headquarters</li>
                </ul>
                <p class="text-sm text-blue-600 mt-3">Delivery: 2-3 days | Charge: ৳100</p>
            </div>
        </div>
    </div>

    <!-- Important Information -->
    <div class="bg-gray-50 rounded-lg p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Important Information</h2>
        <div class="space-y-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Delivery Time</h3>
                    <p class="text-gray-600">Delivery times are estimates and may vary during peak seasons, holidays, or due to weather conditions.</p>
                </div>
            </div>
            
            <div class="flex items-start">
                <i class="fas fa-clock text-orange-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Delivery Hours</h3>
                    <p class="text-gray-600">Deliveries are made between 9:00 AM and 7:00 PM, Saturday to Thursday. Friday deliveries may have limited hours.</p>
                </div>
            </div>
            
            <div class="flex items-start">
                <i class="fas fa-phone text-green-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Contact Information</h3>
                    <p class="text-gray-600">Please provide a valid phone number. Our delivery team will call you before arrival.</p>
                </div>
            </div>
            
            <div class="flex items-start">
                <i class="fas fa-exchange-alt text-purple-500 mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Failed Delivery</h3>
                    <p class="text-gray-600">If delivery fails due to customer unavailability, a re-delivery charge of ৳50 may apply.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- International Shipping -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">International Shipping</h2>
        <p class="text-gray-600 mb-4">
            Currently, we only deliver within Bangladesh. We are working on expanding our services to international destinations. 
            For international orders, please contact our customer support team for assistance.
        </p>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                For international shipping inquiries, please email us at 
                <a href="mailto:{{ \App\Services\SettingsService::get('contact_email', 'international@shopbd.com') }}" class="font-semibold">
                    {{ \App\Services\SettingsService::get('contact_email', 'international@shopbd.com') }}
                </a>
            </p>
        </div>
    </div>

    <!-- Track Your Order -->
    <div class="bg-orange-50 rounded-lg p-8 text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Track Your Order</h2>
        <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
            Want to know where your order is? Track your package in real-time using your tracking number.
        </p>
        <div class="flex justify-center space-x-4">
            <a href="#" class="bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors">
                Track Order
            </a>
            <a href="{{ route('contact') }}" class="bg-white text-orange-500 border border-orange-500 px-6 py-3 rounded-lg font-semibold hover:bg-orange-50 transition-colors">
                Contact Support
            </a>
        </div>
    </div>
</div>
@endsection
