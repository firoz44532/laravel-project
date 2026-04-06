@extends('frontend.layout')

@section('title', 'About Us')
@section('header', 'About Us')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">About {{ \App\Services\SettingsService::get('site_name', 'ShopBD') }}</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            {{ \App\Services\SettingsService::get('site_tagline', 'Your trusted online shopping destination in Bangladesh.') }}
        </p>
    </div>

    <!-- About Content -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Our Story</h2>
            <div class="prose prose prose-lg text-gray-600">
                <p>Welcome to {{ \App\Services\SettingsService::get('site_name', 'ShopBD') }}, your trusted online shopping destination in Bangladesh. We started our journey with a simple mission: to provide high-quality products at affordable prices, delivered right to your doorstep.</p>
                
                <p>Since our inception, we have grown from a small startup to a comprehensive e-commerce platform serving thousands of happy customers across Bangladesh. Our commitment to quality, customer service, and innovation has made us one of the most trusted names in online shopping.</p>
                
                <p>At {{ \App\Services\SettingsService::get('site_name', 'ShopBD') }}, we believe that shopping should be easy, enjoyable, and accessible to everyone. That's why we've invested heavily in creating a user-friendly platform that makes finding and purchasing your favorite products a breeze.</p>
            </div>
        </div>
        
        <div>
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Our Mission</h2>
            <div class="prose prose prose-lg text-gray-600">
                <p>Our mission is to revolutionize the online shopping experience in Bangladesh by:</p>
                <ul>
                    <li>Offering a wide selection of quality products at competitive prices</li>
                    <li>Providing exceptional customer service and support</li>
li>
                   >Ensuring fast and reliable delivery across Bangladesh</li>
                    <li>Maintaining the highest standards of quality and authenticity</li>
                    <li>Creating a seamless and enjoyable shopping experience</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Values Section -->
    <div class="bg-gray-50 rounded-lg p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Our Values</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-heart text-orange-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Customer First</h3>
                <p class="text-gray-600">We put our customers at the center of everything we do, ensuring their satisfaction and building lasting relationships.</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-blue-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Quality Assurance</h3>
                <p class="text-gray-600">Every product on our platform undergoes strict quality checks to ensure authenticity and customer satisfaction.</p>
            </div>
            
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-truck text-green-500 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Fast Delivery</h3>
                <p class="text-gray-600">We partner with reliable delivery services to ensure your orders reach you quickly and safely.</p>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <div class="text-center">
            <div class="text-3xl font-bold text-orange-600 mb-2">{{ number_format(10000) }}+</div>
            <p class="text-gray-600">Happy Customers</p>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-orange-600 mb-2">{{ number_format(5000) }}+</div>
            <p class="text-gray-600">Products Available</p>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-orange-600 mb-2">{{ number_format(50) }}+</div>
p class="text-gray-600">Partner Brands</p>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-orange-600 mb-2">24/7</div>
            <p class="text-gray-600">Customer Support</p>
        </div>
    </div>

    <!-- Team Section -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-900 text-center mb-8">Meet Our Team</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-24 h-24 bg-gray-200 rounded-full mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-user text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Dedicated Team</h3>
                <p class="text-gray-600">Our passionate team works around the clock to bring you the best shopping experience.</p>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-orange-500 rounded-lg p-8 text-center">
        <h2 class="text-2xl font-bold text-white mb-4">Join Our Community</h2>
        <p class="text-orange-100 mb-6 max-w-2xl mx-auto">
            Be the first to know about new products, exclusive deals, and special offers!
        </p>
        <div class="flex justify-center space-x-4">
            <a href="{{ route('register') }}" class="bg-white text-orange-500 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                Sign Up Now
            </a>
            <a href="{{ route('products.index') }}" class="bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-700 transition-colors">
                Start Shopping
            </a>
        </div>
    </div>
</div>
@endsection
