@extends('frontend.layout')

@section('title', 'Order Tracking')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h1 class="text-2xl font-bold flex items-center">
                    <i class="fas fa-truck text-primary mr-3"></i>
                    Track Your Order
                </h1>
                <p class="text-gray-600 mt-2">Enter your order number to track your package</p>
            </div>
            
            <div class="p-6">
                <!-- Tracking Method Selection -->
                <div class="mb-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="button" onclick="showTrackingMethod('order_number')" 
                                id="order-number-tab" 
                                class="flex-1 py-2 px-4 bg-primary text-white rounded-lg font-medium transition">
                            <i class="fas fa-hashtag mr-2"></i>Track by Order Number
                        </button>
                        <button type="button" onclick="showTrackingMethod('name_phone')" 
                                id="name-phone-tab"
                                class="flex-1 py-2 px-4 bg-gray-200 text-gray-700 rounded-lg font-medium transition hover:bg-gray-300">
                            <i class="fas fa-user-phone mr-2"></i>Track by Name & Mobile
                        </button>
                    </div>
                </div>

                <form id="tracking-form" class="space-y-4">
                    @csrf
                    
                    <!-- Order Number Tracking Method -->
                    <div id="order-number-method">
                        <div>
                            <label for="order_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Order Number <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="order_number" name="order_number"
                                   placeholder="ORD-XXXXXXXX"
                                   class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                            <p class="text-xs text-gray-500 mt-1">You can find this in your order confirmation email</p>
                        </div>
                        
                        @if(!Auth::check())
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Address
                                    </label>
                                    <input type="email" id="email" name="email"
                                           placeholder="your@email.com"
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                </div>
                                
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone Number
                                    </label>
                                    <input type="tel" id="phone" name="phone"
                                           placeholder="01XXXXXXXXX"
                                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-sm text-blue-800">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    Please provide either your email address or phone number for security verification
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Name & Phone Tracking Method -->
                    <div id="name-phone-method" class="hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="customer_name" name="customer_name"
                                       placeholder="Enter your full name"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                <p class="text-xs text-gray-500 mt-1">Name as used during order placement</p>
                            </div>
                            
                            <div>
                                <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Mobile Number <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" id="mobile_number" name="phone"
                                       placeholder="01XXXXXXXXX"
                                       pattern="01[3-9][0-9]{8}"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:border-primary">
                                <p class="text-xs text-gray-500 mt-1">Mobile number used for the order</p>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <p class="text-sm text-green-800">
                                <i class="fas fa-lightbulb mr-2"></i>
                                This method helps you find all orders placed with your name and mobile number, even if you don't have the order number
                            </p>
                        </div>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-primary text-white py-3 rounded-lg hover:bg-orange-600 transition font-semibold">
                        <i class="fas fa-search mr-2"></i>Track Order
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <i class="fas fa-user-circle text-4xl text-gray-400 mb-4"></i>
                <h3 class="font-semibold mb-2">Have an Account?</h3>
                <p class="text-gray-600 text-sm mb-4">Login to track all your orders easily</p>
                <a href="{{ route('login') }}" class="text-primary hover:text-orange-600 text-sm font-medium">
                    Login Now
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <i class="fas fa-shopping-bag text-4xl text-gray-400 mb-4"></i>
                <h3 class="font-semibold mb-2">Need Help?</h3>
                <p class="text-gray-600 text-sm mb-4">Contact our customer support team</p>
                <a href="#" class="text-primary hover:text-orange-600 text-sm font-medium">
                    Contact Support
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <i class="fas fa-phone text-4xl text-gray-400 mb-4"></i>
                <h3 class="font-semibold mb-2">Call Us</h3>
                <p class="text-gray-600 text-sm mb-4">Call our helpline for assistance</p>
                <div class="text-primary font-semibold">+880 1234 567890</div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle between tracking methods
function showTrackingMethod(method) {
    const orderNumberTab = document.getElementById('order-number-tab');
    const namePhoneTab = document.getElementById('name-phone-tab');
    const orderNumberMethod = document.getElementById('order-number-method');
    const namePhoneMethod = document.getElementById('name-phone-method');

    if (method === 'order_number') {
        orderNumberTab.className = 'flex-1 py-2 px-4 bg-primary text-white rounded-lg font-medium transition';
        namePhoneTab.className = 'flex-1 py-2 px-4 bg-gray-200 text-gray-700 rounded-lg font-medium transition hover:bg-gray-300';
        orderNumberMethod.classList.remove('hidden');
        namePhoneMethod.classList.add('hidden');
    } else {
        namePhoneTab.className = 'flex-1 py-2 px-4 bg-primary text-white rounded-lg font-medium transition';
        orderNumberTab.className = 'flex-1 py-2 px-4 bg-gray-200 text-gray-700 rounded-lg font-medium transition hover:bg-gray-300';
        namePhoneMethod.classList.remove('hidden');
        orderNumberMethod.classList.add('hidden');
    }
}

document.getElementById('tracking-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const activeMethod = document.getElementById('order-number-method').classList.contains('hidden') ? 'name_phone' : 'order_number';
    
    // Validate based on active method
    if (activeMethod === 'name_phone') {
        const customerName = formData.get('customer_name');
        const phone = formData.get('phone');
        
        if (!customerName || !phone) {
            alert('Please provide both name and mobile number for tracking.');
            return;
        }
    } else {
        const orderNumber = formData.get('order_number');
        if (!orderNumber) {
            alert('Please provide order number for tracking.');
            return;
        }
    }
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Tracking...';
    
    fetch('/track', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.search_method === 'name_phone' || data.orders) {
                // Multiple orders found, redirect with all parameters
                const params = new URLSearchParams();
                params.append('customer_name', formData.get('customer_name') || '');
                params.append('phone', formData.get('phone') || '');
                window.location.href = '/track?' + params.toString();
            } else {
                // Single order found
                window.location.href = '/track?order_number=' + formData.get('order_number') + 
                    (formData.get('email') ? '&email=' + formData.get('email') : '') +
                    (formData.get('phone') ? '&phone=' + formData.get('phone') : '');
            }
        } else {
            alert(data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-search mr-2"></i>Track Order';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error tracking order. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-search mr-2"></i>Track Order';
    });
});
</script>
@endsection
