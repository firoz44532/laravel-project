@extends('frontend.layout')

@section('title', 'Contact Us')
@section('header', 'Contact Us')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-8">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Contact Us</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            We're here to help! Get in touch with our team for any questions, concerns, or feedback.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <!-- Contact Form -->
        <div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Send us a Message</h2>
                <form method="POST" action="{{ route('contact.submit') }}" class="space-y-6">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Your Name *</label>
                        <input type="text" id="name" name="name" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" id="phone" name="phone"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                        <input type="text" id="subject" name="subject" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                        <textarea id="message" name="message" rows="6" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Contact Information -->
        <div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Get in Touch</h2>
                
                <div class="space-y-6">
                    <!-- Phone -->
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-phone text-orange-500"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Phone</h3>
                            <p class="text-gray-600">{{ \App\Services\SettingsService::get('contact_phone', '+880 1234 5678') }}</p>
                            <p class="text-sm text-gray-500">Mon-Fri: 9AM-6PM</p>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-blue-500"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Email</h3>
                            <p class="text-gray-600">{{ \App\Services\SettingsService::get('contact_email', 'support@shopbd.com') }}</p>
                            <p class="text-sm text-gray-500">We respond within 24 hours</p>
                        </div>
                    </div>
                    
                    <!-- Address -->
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-green-500"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">Address</h3>
                            <p class="text-gray-600">{{ \App\Services\SettingsService::get('address', '123 Main Road, Dhaka, Bangladesh') }}</p>
                            <p class="text-sm text-gray-500">Visit our showroom</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="bg-gray-50 rounded-lg p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Frequently Asked Questions</h2>
        <div class="space-y-6">
            <div class="bg-white rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-2">How do I place an order?</h3>
                <p class="text-gray-600">Simply browse our products, add items to your cart, and proceed to checkout. We accept multiple payment methods including cash on delivery.</p>
            </div>
            
            <div class="bg-white rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-2">What are your delivery options?</h3>
                <p class="text-gray-600">We offer home delivery across Bangladesh with standard delivery in 2-3 business days and express delivery in 1-2 days.</p>
            </div>
            
            <div class="bg-white rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-2">How can I track my order?</h3>
                <p class="text-gray-600">Once your order is shipped, you'll receive a tracking number via email that you can use to monitor your delivery status.</p>
            </div>
            
            <div class="bg-white rounded-lg p-6">
                <h3 class="font-semibold text-gray-900 mb-2">What is your return policy?</h3>
                <p class="text-gray-600">We offer a 7-day return policy for most items. Please check our returns page for detailed information.</p>
            </div>
        </div>
    </div>

    <!-- Social Media -->
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Follow Us</h2>
        <div class="flex justify-center space-x-6">
            <a href="#" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition-colors">
                <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" class="w-10 h-10 bg-pink-600 text-white rounded-full flex items-center justify-center hover:bg-pink-700 transition-colors">
                <i class="fab fa-instagram"></i>
            </a>
            <a href="#" class="w-10 h-10 bg-red-600 text-white rounded-full flex items-center justify-center hover:bg-red-700 transition-colors">
                <i class="fab fa-youtube"></i>
            </a>
        </div>
    </div>
</div>
@endsection
