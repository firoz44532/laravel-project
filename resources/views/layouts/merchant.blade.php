
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Merchant Dashboard') - Ecommerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#f97316',
                    }
                }
            }
        }
    </script>
    <style>
        .text-primary {
            color: #f97316;
        }
        .border-primary {
            border-color: #f97316;
        }
        .bg-primary {
            background-color: #f97316;
        }
        .hover\:bg-primary:hover {
            background-color: #f97316;
        }
        .focus\:ring-primary:focus {
            --tw-ring-color: #f97316;
        }
    </style>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home') }}" class="flex items-center">
                            <img src="{{ asset('images/logo.svg') }}" alt="ShopBD" class="h-10 w-auto">
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('merchant.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('merchant.dashboard') ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }} text-sm font-medium">
                            Dashboard
                        </a>
                        <a href="{{ route('merchant.products.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('merchant.products.*') ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }} text-sm font-medium">
                            Products
                        </a>
                        <a href="{{ route('merchant.orders.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('merchant.orders.*') ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }} text-sm font-medium">
                            Orders
                        </a>
                        <a href="{{ route('merchant.earnings') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('merchant.earnings') ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }} text-sm font-medium">
                            Earnings
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="ml-3 relative">
                        <div class="relative inline-block text-left">
                            <div>
                                <button type="button" class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary" id="user-menu" aria-expanded="false" aria-haspopup="true">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="h-8 w-8 rounded-full bg-primary flex items-center justify-center text-white">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                </button>
                            </div>
                            <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100" role="menu" aria-orientation="vertical" aria-labelledby="user-menu" style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('merchant.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Store Profile</a>
                                    <a href="{{ route('account.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Customer Account</a>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Banner Section -->
    @if(isset($heroBanners) && $heroBanners->count() > 0)
        @foreach($heroBanners as $banner)
            @if($banner->link)
                <a href="{{ $banner->link }}" class="block">
            @endif
            <section class="relative bg-gradient-to-r from-orange-500 to-orange-600 text-white @if($banner->link) hover:from-orange-600 hover:to-orange-700 transition-colors cursor-pointer @endif">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                        <div class="order-2 md:order-1 relative">
                            @if($banner->image)
                                <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" class="rounded-lg shadow-2xl w-full h-96 object-cover">
                            @else
                                <img src="{{ asset('images/placeholder-product.jpg') }}" alt="{{ $banner->title }}" class="rounded-lg shadow-2xl w-full h-96 object-cover">
                            @endif
                            <!-- Left Corner Badge -->
                            <div class="absolute -top-4 -left-4 bg-red-500 text-white px-4 py-2 rounded-full font-bold animate-pulse">
                                Limited Time!
                            </div>
                        </div>
                        <div class="order-1 md:order-2">
                            <h1 class="text-4xl md:text-6xl font-bold mb-4">{{ $banner->title }}</h1>
                            @if($banner->description)
                                <p class="text-lg mb-6">{{ $banner->description }}</p>
                            @endif
                            <div class="flex space-x-4">
                                @if($banner->link)
                                    <a href="{{ $banner->link }}" class="bg-white text-orange-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition" onclick="event.stopPropagation()">
                                        Shop Now
                                    </a>
                                @endif
                                <a href="{{ route('products.index') }}" class="border-2 border-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-orange-600 transition">
                                    View Deals
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @if($banner->link)
                </a>
            @endif
        @endforeach
    @else
        <!-- Fallback hardcoded banner -->
        <section class="relative bg-gradient-to-r from-orange-500 to-orange-600 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    <div>
                        <h1 class="text-4xl md:text-6xl font-bold mb-4">Merchant Dashboard</h1>
                        <h2 class="text-2xl md:text-3xl mb-4">Manage Your Business</h2>
                        <p class="text-lg mb-6">Welcome to your merchant dashboard. Manage products, orders, and track your earnings.</p>
                        <div class="flex space-x-4">
                            <a href="{{ route('merchant.products.index') }}" class="bg-white text-orange-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition">
                                Manage Products
                            </a>
                            <a href="{{ route('merchant.orders.index') }}" class="border-2 border-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-orange-600 transition">
                                View Orders
                            </a>
                        </div>
                    </div>
                    <div class="relative">
                        <img src="{{ asset('images/placeholder-product.jpg') }}" alt="Merchant Dashboard" class="rounded-lg shadow-2xl w-full h-96 object-cover">
                        <div class="absolute -top-4 -right-4 bg-red-500 text-white px-4 py-2 rounded-full font-bold animate-pulse">
                            Welcome Back!
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <main class="py-6">
        @yield('content')
    </main>

    <script>
        // Simple dropdown toggle
        document.getElementById('user-menu').addEventListener('click', function() {
            const dropdown = this.nextElementSibling;
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('user-menu');
            const dropdown = userMenu.nextElementSibling;
            if (!userMenu.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = 'none';
            }
        });
    </script>
</body>
</html>
