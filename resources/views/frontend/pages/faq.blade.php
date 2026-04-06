@extends('frontend.layout')

@section('title', 'FAQ')
@section('header', 'Frequently Asked Questions')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Find answers to common questions about shopping with {{ \App\Services\SettingsService::get('site_name', 'ShopBD') }}
        </p>
    </div>

    <!-- Search FAQ -->
    <div class="mb-8">
        <div class="max-w-2xl mx-auto">
            <div class="relative">
                <input type="text" id="faq-search" placeholder="Search FAQ..." 
                       class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                <button class="absolute right-0 top-0 h-full px-4 bg-orange-500 text-white rounded-r-lg hover:bg-orange-600">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- FAQ Categories -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-orange-50 rounded-lg p-6 text-center cursor-pointer hover:bg-orange-100 transition-colors" onclick="filterFAQ('ordering')">
            <i class="fas fa-shopping-cart text-orange-500 text-3xl mb-3"></i>
            <h3 class="font-semibold text-gray-900">Ordering</h3>
            <p class="text-sm text-gray-600 mt-1">Questions about placing orders</p>
        </div>
        
        <div class="bg-blue-50 rounded-lg p-6 text-center cursor-pointer hover:bg-blue-100 transition-colors" onclick="filterFAQ('payment')">
            <i class="fas fa-credit-card text-blue-500 text-3xl mb-3"></i>
            <h3 class="font-semibold text-gray-900">Payment</h3>
            <p class="text-sm text-gray-600 mt-1">Payment methods and security</p>
        </div>
        
        <div class="bg-green-50 rounded-lg p-6 text-center cursor-pointer hover:bg-green-100 transition-colors" onclick="filterFAQ('delivery')">
            <i class="fas fa-truck text-green-500 text-3xl mb-3"></i>
            <h3 class="font-semibold text-gray-900">Delivery</h3>
            <p class="text-sm text-gray-600 mt-1">Shipping and delivery info</p>
        </div>
    </div>

    <!-- FAQ Items -->
    <div class="space-y-4">
        <!-- Ordering Questions -->
        <div class="faq-item bg-white rounded-lg shadow-sm border border-gray-200" data-category="ordering">
            <button class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50" onclick="toggleFAQ(this)">
                <span class="font-semibold text-gray-900">How do I place an order?</span>
                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
            </button>
            <div class="hidden px-6 pb-4">
                <p class="text-gray-600">Placing an order is simple! Browse our products, select the items you want, add them to your cart, and proceed to checkout. Follow the steps to enter your shipping information and select your preferred payment method.</p>
            </div>
        </div>

        <div class="faq-item bg-white rounded-lg shadow-sm border border-gray-200" data-category="ordering">
            <button class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50" onclick="toggleFAQ(this)">
                <span class="font-semibold text-gray-900">Can I modify or cancel my order?</span>
                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
            </button>
            <div class="hidden px-6 pb-4">
                <p class="text-gray-600">You can modify or cancel your order within 2 hours of placing it. After this time, the order enters our fulfillment process and cannot be changed. Please contact our customer service team immediately if you need assistance.</p>
            </div>
        </div>

        <div class="faq-item bg-white rounded-lg shadow-sm border border-gray-200" data-category="ordering">
            <button class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50" onclick="toggleFAQ(this)">
                <span class="font-semibold text-gray-900">How do I know if my order was successful?</span>
                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
            </button>
            <div class="hidden px-6 pb-4">
                <p class="text-gray-600">After placing your order, you'll receive a confirmation email with your order details. You can also check your order status in your account dashboard under "My Orders".</p>
            </div>
        </div>

        <!-- Payment Questions -->
        <div class="faq-item bg-white rounded-lg shadow-sm border border-gray-200" data-category="payment">
            <button class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50" onclick="toggleFAQ(this)">
                <span class="font-semibold text-gray-900">What payment methods do you accept?</span>
                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
            </button>
            <div class="hidden px-6 pb-4">
                <p class="text-gray-600">We accept multiple payment methods including:</p>
                <ul class="list-disc list-inside mt-2 text-gray-600">
                    <li>Cash on Delivery (COD)</li>
                    <li>Credit/Debit Cards</li>
                    <li>Mobile Banking (bKash, Rocket, Nagad)</li>
                    <li>Bank Transfer</li>
                </ul>
            </div>
        </div>

        <div class="faq-item bg-white rounded-lg shadow-sm border border-gray-200" data-category="payment">
            <button class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50" onclick="toggleFAQ(this)">
                <span class="font-semibold text-gray-900">Is my payment information secure?</span>
                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
            </button>
            <div class="hidden px-6 pb-4">
                <p class="text-gray-600">Yes, absolutely! We use industry-standard SSL encryption to protect your payment information. Our payment gateway is PCI DSS compliant, and we never store your credit card details on our servers.</p>
            </div>
        </div>

        <div class="faq-item bg-white rounded-lg shadow-sm border border-gray-200" data-category="payment">
            <button class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50" onclick="toggleFAQ(this)">
                <span class="font-semibold text-gray-900">What if my payment fails?</span>
                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
            </button>
            <div class="hidden px-6 pb-4">
                <p class="text-gray-600">If your payment fails, don't worry! Your order will remain in your cart for 30 minutes, giving you time to try again with a different payment method or contact your bank if needed.</p>
            </div>
        </div>

        <!-- Delivery Questions -->
        <div class="faq-item bg-white rounded-lg shadow-sm border border-gray-200" data-category="delivery">
            <button class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50" onclick="toggleFAQ(this)">
                <span class="font-semibold text-gray-900">How long does delivery take?</span>
                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
            </button>
            <div class="hidden px-6 pb-4">
                <p class="text-gray-600">Delivery times vary based on your location:</p>
                <ul class="list-disc list-inside mt-2 text-gray-600">
                    <li>Dhaka: 1-2 business days</li>
                    <li>Major cities: 2-3 business days</li>
                    <li>Remote areas: 3-5 business days</li>
                </ul>
            </div>
        </div>

        <div class="faq-item bg-white rounded-lg shadow-sm border border-gray-200" data-category="delivery">
            <button class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50" onclick="toggleFAQ(this)">
                <span class="font-semibold text-gray-900">How much does delivery cost?</span>
                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
            </button>
            <div class="hidden px-6 pb-4">
                <p class="text-gray-600">Delivery charges depend on your order value and location:</p>
                <ul class="list-disc list-inside mt-2 text-gray-600">
                    <li>Orders over ৳2000: Free delivery</li>
                    <li>Dhaka Metro: ৳60</li>
                    <li>Outside Dhaka: ৳100-৳150</li>
                </ul>
            </div>
        </div>

        <div class="faq-item bg-white rounded-lg shadow-sm border border-gray-200" data-category="delivery">
            <button class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50" onclick="toggleFAQ(this)">
                <span class="font-semibold text-gray-900">Can I track my order?</span>
                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
            </button>
            <div class="hidden px-6 pb-4">
                <p class="text-gray-600">Yes! Once your order is shipped, you'll receive a tracking number via email. You can use this number on our website or the courier's website to track your delivery in real-time.</p>
            </div>
        </div>

        <!-- General Questions -->
        <div class="faq-item bg-white rounded-lg shadow-sm border border-gray-200" data-category="general">
            <button class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50" onclick="toggleFAQ(this)">
                <span class="font-semibold text-gray-900">What is your return policy?</span>
                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
            </button>
            <div class="hidden px-6 pb-4">
                <p class="text-gray-600">We offer a 7-day return policy for most items. Products must be unused, in original packaging, and with all tags attached. Please check our Returns page for detailed information and exceptions.</p>
            </div>
        </div>

        <div class="faq-item bg-white rounded-lg shadow-sm border border-gray-200" data-category="general">
            <button class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50" onclick="toggleFAQ(this)">
                <span class="font-semibold text-gray-900">How do I create an account?</span>
                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
            </button>
            <div class="hidden px-6 pb-4">
                <p class="text-gray-600">Click on "Register" in the top menu, fill in your details, and verify your email. Having an account allows you to track orders, save addresses, and enjoy exclusive member benefits.</p>
            </div>
        </div>

        <div class="faq-item bg-white rounded-lg shadow-sm border border-gray-200" data-category="general">
            <button class="w-full px-6 py-4 text-left flex items-center justify-between hover:bg-gray-50" onclick="toggleFAQ(this)">
                <span class="font-semibold text-gray-900">How can I contact customer support?</span>
                <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
            </button>
            <div class="hidden px-6 pb-4">
                <p class="text-gray-600">You can reach our customer support team via:</p>
                <ul class="list-disc list-inside mt-2 text-gray-600">
                    <li>Phone: {{ \App\Services\SettingsService::get('contact_phone', '+880 1234 5678') }}</li>
                    <li>Email: {{ \App\Services\SettingsService::get('contact_email', 'support@shopbd.com') }}</li>
                    <li>Live chat: Available on our website</li>
                    <li>WhatsApp: {{ \App\Services\SettingsService::get('whatsapp', '+880 1234 5678') }}</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Still Need Help -->
    <div class="bg-orange-50 rounded-lg p-8 text-center mt-12">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Still Need Help?</h2>
        <p class="text-gray-600 mb-6 max-w-2xl mx-auto">
            Can't find the answer you're looking for? Our customer support team is here to help!
        </p>
        <div class="flex justify-center space-x-4">
            <a href="{{ route('contact') }}" class="bg-orange-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-orange-600 transition-colors">
                Contact Support
            </a>
            <a href="tel:{{ \App\Services\SettingsService::get('contact_phone', '+88012345678') }}" class="bg-white text-orange-500 border border-orange-500 px-6 py-3 rounded-lg font-semibold hover:bg-orange-50 transition-colors">
                Call Us
            </a>
        </div>
    </div>
</div>

<script>
function toggleFAQ(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('i');
    
    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

function filterFAQ(category) {
    const items = document.querySelectorAll('.faq-item');
    
    items.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Search functionality
document.getElementById('faq-search').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const items = document.querySelectorAll('.faq-item');
    
    items.forEach(item => {
        const text = item.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>
@endsection
