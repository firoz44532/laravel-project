<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ShopBD') - ShopBD</title>
    <meta name="description" content="Shop online for electronics, fashion, groceries and more. Best prices in Bangladesh with fast delivery.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <!-- Top Bar -->
            <div class="flex justify-between items-center py-2 text-sm border-b">
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600"><i class="fas fa-phone"></i> Hotline: 12345</span>
                    <span class="text-gray-600"><i class="fas fa-envelope"></i> support@shop.com</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('account.dashboard') }}" class="text-gray-600 hover:text-primary">
                            <i class="fas fa-user"></i> {{ Auth::user()->name }}
                        </a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-primary">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-primary">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="text-gray-600 hover:text-primary">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                        
                    @endauth
                </div>
            </div>

            <!-- Main Header -->
            <div class="flex items-center justify-between py-4">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <img src="{{ asset('images/logo.svg') }}" alt="ShopBD" class="h-12 w-auto">
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="flex-1 max-w-2xl mx-8">
                    <form action="{{ route('products.index') }}" method="GET" class="relative">
                        <input type="text" name="search" placeholder="Search for products..." 
                               value="{{ request('search') }}"
                               class="w-full px-4 py-2 pr-12 border rounded-full focus:outline-none focus:border-primary">
                        <button type="submit" class="absolute right-0 top-0 h-full px-4 bg-primary text-white rounded-full hover:bg-orange-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <!-- Cart -->
                <div class="flex items-center space-x-4">
                    @auth
                    <a href="{{ route('wishlist.index') }}" class="relative text-gray-600 hover:text-primary">
                        <i class="fas fa-heart text-xl"></i>
                        <span id="wishlist-count" class="wishlist-count absolute -top-2 -right-2 bg-pink-500 text-white text-xs rounded-full px-1" style="display: none;">0</span>
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="relative text-gray-600 hover:text-primary" title="Login to view wishlist">
                        <i class="fas fa-heart text-xl"></i>
                        <span id="wishlist-count" class="wishlist-count absolute -top-2 -right-2 bg-pink-500 text-white text-xs rounded-full px-1" style="display: none;">0</span>
                    </a>
                    @endauth
                    <a href="{{ route('cart.index') }}" class="relative text-gray-600 hover:text-primary">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span id="cart-count" class="cart-count absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-1">0</span>
                    </a>
                </div>
            </div>

            <!-- Category Menu -->
            <nav class="py-3">
                <div class="flex items-center space-x-8">
                    <!-- Categories Dropdown -->
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-primary">
                            <i class="fas fa-bars"></i>
                            <span>All Categories</span>
                            <i class="fas fa-chevron-down text-xs"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="absolute left-0 top-full mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <div class="py-2">
                                <a href="{{ route('categories.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-primary">
                                    <i class="fas fa-th-large mr-2"></i>All Categories
                                </a>
                                @if(isset($categories) && $categories)
                                    @foreach($categories->take(10) as $category)
                                        <a href="{{ route('products.category', $category->slug) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-primary">
                                            <i class="fas fa-folder mr-2"></i>{{ $category->name }}
                                        </a>
                                    @endforeach
                                @endif
                                <a href="{{ route('brands.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-primary">
                                    <i class="fas fa-industry mr-2"></i>Shop by Brand
                                </a>
                                <a href="{{ route('products.vendors') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-primary">
                                    <i class="fas fa-store mr-2"></i>All Vendors
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Category Links -->
                    @if(isset($categories) && $categories)
                        @foreach($categories->take(5) as $category)
                            <a href="{{ route('products.category', $category->slug) }}" class="text-gray-700 hover:text-primary">{{ $category->name }}</a>
                        @endforeach
                    @endif
                    
                    <!-- All Vendors Link: show only on vendors listing page -->
                    @if(request()->routeIs('products.vendors'))
                        <a href="{{ route('products.vendors') }}" class="flex items-center space-x-2 text-purple-600 hover:text-purple-700 font-medium">
                            <i class="fas fa-store"></i>
                            <span>All Vendors</span>
                        </a>
                    @endif

                    <!-- Become a Seller button: only show on homepage -->
                    @if(request()->routeIs('home'))
                        <div class="ml-auto">
                            <a href="{{ route('merchant.register') }}" class="inline-flex items-center px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-black rounded-md font-semibold shadow-sm transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M3 3h4l1 2h6a1 1 0 011 1v7a2 2 0 01-2 2H6a2 2 0 01-2-2V3z"/></svg>
                                <span>Become a Seller</span>
                            </a>
                        </div>
                    @endif
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <a href="{{ route('home') }}" class="inline-block mb-4">
                        <img src="{{ asset('images/logo.svg') }}" alt="ShopBD" class="h-12 w-auto">
                    </a>
                    <p class="text-gray-400">{{ \App\Services\SettingsService::get('site_tagline', 'Your trusted online shopping destination in Bangladesh.') }}</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('about') }}" class="hover:text-white">About Us</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white">Contact</a></li>
                        <li><a href="{{ route('faq') }}" class="hover:text-white">FAQ</a></li>
                        <li><a href="{{ route('categories.index') }}" class="hover:text-white">Categories</a></li>
                        <li><a href="{{ route('brands.index') }}" class="hover:text-white">Brands</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Customer Service</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('shipping') }}" class="hover:text-white">Shipping Info</a></li>
                        <li><a href="{{ route('returns') }}" class="hover:text-white">Returns</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-white">Privacy Policy</a></li>
                        <li><a href="{{ route('terms') }}" class="hover:text-white">Terms & Conditions</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2026 {{ \App\Services\SettingsService::get('site_name', 'ShopBD') }}. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @vite('resources/js/app.js')

<script>
// Simple cart functionality
function updateCartCount(count) {
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;
        cartCountElement.style.display = count > 0 ? 'block' : 'none';
    }
}

// Update cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    fetch('/api/cart-count')
        .then(response => response.json())
        .then(data => {
            updateCartCount(data.count);
        })
        .catch(error => {
            console.error('Error fetching cart count:', error);
        });

    // Update wishlist count on page load - check if user is authenticated
    @if(auth()->check())
    fetch('/wishlist/count')
        .then(response => response.json())
        .then(data => {
            updateWishlistCount(data.count);
        })
        .catch(error => {
            console.error('Error fetching wishlist count:', error);
        });
    @endif
});

// Function to update wishlist count in the UI
function updateWishlistCount(count) {
    const wishlistCountElement = document.getElementById('wishlist-count');
    if (wishlistCountElement) {
        wishlistCountElement.textContent = count;
        wishlistCountElement.style.display = count > 0 ? 'block' : 'none';
    }
    
    // Also update any elements with wishlist-count class
    const wishlistCountElements = document.querySelectorAll('.wishlist-count');
    wishlistCountElements.forEach(element => {
        element.textContent = count;
        element.style.display = count > 0 ? 'block' : 'none';
    });
}
</script>

<!-- Cart Success Popup -->
<div id="cart-popup-overlay" class="fixed inset-0 bg-black/50 z-[9999] hidden items-center justify-center transition-opacity duration-300 opacity-0" onclick="closeCartPopup(event)">
    <div id="cart-popup" class="bg-white rounded-2xl shadow-2xl w-[90%] max-w-sm mx-auto transform scale-95 transition-transform duration-300 overflow-hidden">
        <!-- Header -->
        <div class="bg-green-500 px-5 py-3 flex items-center gap-2">
            <i class="fas fa-check-circle text-white text-lg"></i>
            <span class="text-white font-semibold">Added to Cart</span>
            <button onclick="closeCartPopup()" class="ml-auto text-white/80 hover:text-white text-xl leading-none">&times;</button>
        </div>
        <!-- Product Info -->
        <div class="p-5 flex gap-4 items-center">
            <img id="cart-popup-img" src="" alt="" class="w-20 h-20 object-cover rounded-lg border flex-shrink-0">
            <div class="min-w-0">
                <p id="cart-popup-name" class="font-semibold text-gray-900 text-sm line-clamp-2"></p>
                <p class="text-xs text-gray-500 mt-1">Qty: <span id="cart-popup-qty">1</span></p>
                <p id="cart-popup-price" class="text-primary font-bold mt-1"></p>
            </div>
        </div>
        <!-- Actions -->
        <div class="px-5 pb-5 flex gap-3">
            <button onclick="closeCartPopup()" class="flex-1 border border-gray-300 text-gray-700 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-50 transition">Continue Shopping</button>
            <a href="{{ route('cart.index') }}" class="flex-1 bg-primary text-white py-2.5 rounded-lg text-sm font-medium text-center hover:bg-orange-600 transition">View Cart</a>
        </div>
    </div>
</div>

<script>
function showCartPopup(product) {
    var overlay = document.getElementById('cart-popup-overlay');
    var popup = document.getElementById('cart-popup');
    var img = document.getElementById('cart-popup-img');
    var name = document.getElementById('cart-popup-name');
    var qty = document.getElementById('cart-popup-qty');
    var price = document.getElementById('cart-popup-price');

    name.textContent = product.name || 'Product';
    qty.textContent = product.quantity || 1;
    price.textContent = '৳' + parseFloat(product.price || 0).toFixed(2);

    if (product.image) {
        img.src = product.image;
        img.alt = product.name || 'Product';
        img.style.display = 'block';
    } else {
        img.style.display = 'none';
    }

    overlay.classList.remove('hidden');
    overlay.classList.add('flex');
    // Trigger reflow then animate in
    void overlay.offsetWidth;
    overlay.classList.remove('opacity-0');
    popup.classList.remove('scale-95');
    popup.classList.add('scale-100');
}

function closeCartPopup(e) {
    if (e && e.target !== e.currentTarget) return;
    var overlay = document.getElementById('cart-popup-overlay');
    var popup = document.getElementById('cart-popup');
    overlay.classList.add('opacity-0');
    popup.classList.remove('scale-100');
    popup.classList.add('scale-95');
    setTimeout(function() {
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
    }, 300);
}
</script>

</body>
</html>
