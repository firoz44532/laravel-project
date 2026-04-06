@extends('frontend.layout')

@section('title', 'Home - Mega Sale Up To 70% OFF | ' . \App\Services\SettingsService::get('site_name', 'ShopBD'))

@push('meta-tags')
    <!-- Homepage-specific SEO meta tags -->
    <meta name="description" content="{{ \App\Services\SettingsService::get('site_description', 'Shop online for electronics, fashion, groceries and more. Best prices in Bangladesh with fast delivery. Mega sale up to 70% OFF!') }}">
    <meta name="keywords" content="online shopping, Bangladesh, e-commerce, mega sale, electronics, fashion, groceries, best prices, fast delivery, up to 70% off">
    
    <!-- Homepage Open Graph tags -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Mega Sale Up To 70% OFF - {{ \App\Services\SettingsService::get('site_name', 'ShopBD') }}">
    <meta property="og:description" content="{{ \App\Services\SettingsService::get('site_description', 'Shop online for electronics, fashion, groceries and more. Best prices in Bangladesh with fast delivery. Mega sale up to 70% OFF!') }}">
    <meta property="og:image" content="{{ asset('images/logo.svg') }}">
    <meta property="og:image:width" content="200">
    <meta property="og:image:height" content="60">
    
@endpush

@section('content')
<!-- Hero Banner Section -->
@if($heroBanners && $heroBanners->count() > 0)
    <!-- Banner Carousel -->
    <div class="relative overflow-hidden" id="bannerCarousel">
        @foreach($heroBanners as $key => $banner)
            @if($key != 7)
            <div class="banner-slide {{ $key == 0 ? 'active' : '' }}" data-slide="{{ $key }}">
                @if($banner->link)
                    <a href="{{ $banner->link }}" class="block">
                @endif
                <section class="relative bg-gradient-to-r from-orange-500 to-orange-600 text-white @if($banner->link) hover:from-orange-600 hover:to-orange-700 transition-colors cursor-pointer @endif">
                    <div class="container mx-auto px-4 py-16">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                            <div class="order-2 md:order-1 relative">
                                @if($banner->image)
                                    <img src="{{ asset($banner->image) }}" 
                                         alt="{{ $banner->title }}" 
                                         class="rounded-lg shadow-2xl w-full h-96 object-cover"
                                         onerror="this.src='{{ asset('images/4k-monitor-27.jpg') }}'; this.onerror=null;">
                                @else
                                    <img src="{{ asset('images/4k-monitor-27.jpg') }}" 
                                         alt="{{ $banner->title }}" 
                                         class="rounded-lg shadow-2xl w-full h-96 object-cover">
                                @endif
                                <!-- Left Corner Badge -->
                                <div class="absolute -top-4 -left-4 bg-red-500 text-white px-4 py-2 rounded-full font-bold animate-pulse">
                                    Limited Time!
                                </div>
                                @if($banner->link)
                                    <!-- Right Corner Badge for clickable banners -->
                                    <div class="absolute -top-4 -right-4 bg-blue-500 text-white px-4 py-2 rounded-full font-bold animate-pulse">
                                        Click to Shop
                                    </div>
                                @endif
                            </div>
                            <div class="order-1 md:order-2">
                                <h1 class="text-4xl md:text-6xl font-bold mb-4">{{ $banner->title }}</h1>
                                @if($banner->description)
                                    <p class="text-lg mb-6">{{ $banner->description }}</p>
                                @endif
                                <div class="flex space-x-4">
                                    @if($banner->link)
                                        <a href="{{ $banner->link }}" 
                                           class="bg-white text-orange-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition" 
                                           onclick="event.stopPropagation()">
                                            Shop Now
                                        </a>
                                    @endif
                                    <a href="{{ route('products.index') }}" 
                                       class="border-2 border-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-orange-600 transition">
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
            </div>
            @endif
        @endforeach
        
        <!-- Carousel Navigation -->
        @if($heroBanners->count() > 1)
            <!-- Carousel Indicators -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                @foreach($heroBanners as $key => $banner)
                    @if($key != 7)
                        <button class="carousel-indicator {{ $key == 0 ? 'bg-white' : 'bg-white bg-opacity-50' }} w-3 h-3 rounded-full transition-all" 
                                data-slide="{{ $key }}"
                                onclick="showSlide({{ $key }})"></button>
                    @endif
                @endforeach
            </div>
            
            <!-- Carousel Controls -->
            <button class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 transition" 
                    onclick="previousSlide()">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-70 transition" 
                    onclick="nextSlide()">
                <i class="fas fa-chevron-right"></i>
            </button>
        @endif
    </div>
@else
    <!-- Fallback hardcoded banner -->
    <section class="relative bg-gradient-to-r from-orange-500 to-orange-600 text-white">
        <div class="container mx-auto px-4 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">Mega Sale</h1>
                    <h2 class="text-2xl md:text-3xl mb-4">Up To 70% OFF</h2>
                    <p class="text-lg mb-6">Biggest sale of the year! Grab amazing deals on electronics, fashion, groceries and more.</p>
                    <div class="flex space-x-4">
                        <a href="{{ route('products.index') }}" 
                           class="bg-white text-orange-600 px-8 py-3 rounded-full font-semibold hover:bg-gray-100 transition">
                            Shop Now
                        </a>
                        <a href="{{ route('products.index') }}" 
                           class="border-2 border-white px-8 py-3 rounded-full font-semibold hover:bg-white hover:text-orange-600 transition">
                            View Deals
                        </a>
                    </div>
                </div>
                <div class="relative">
                    <img src="{{ asset('images/placeholder-product.jpg') }}" 
                         alt="Sale Banner" 
                         class="rounded-lg shadow-2xl w-full h-96 object-cover">
                    <div class="absolute -top-4 -right-4 bg-red-500 text-white px-4 py-2 rounded-full font-bold animate-pulse">
                        Limited Time!
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

<!-- Banner Carousel Styles + Script -->
<style>
/* Banner Carousel Styles */
.banner-slide {
    display: none;
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
}

.banner-slide.active {
    display: block;
    opacity: 1;
}

.carousel-indicator {
    transition: all 0.3s ease;
}

.carousel-indicator:hover {
    transform: scale(1.2);
}

/* Ensure proper positioning for carousel controls */
#bannerCarousel {
    position: relative;
}

#bannerCarousel .carousel-controls button {
    z-index: 10;
}

#bannerCarousel .carousel-indicators {
    z-index: 10;
}

/* Force hide 8th carousel indicator (by data-slide or position) */
#bannerCarousel .carousel-indicator[data-slide="8"],
#bannerCarousel .carousel-indicator[data-slide="7"],
#bannerCarousel .carousel-indicator:nth-of-type(8) {
    display: none !important;
}

/* Hide all carousel indicators (dots) while keeping auto-rotate active */
#bannerCarousel .carousel-indicator,
#bannerCarousel .carousel-indicators {
    display: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentSlide = 0;
    let autoRotateInterval;

    const carousel = document.getElementById('bannerCarousel');
    if (!carousel) return;

    function logCounts() {
        const slides = carousel.querySelectorAll('.banner-slide');
        const indicators = carousel.querySelectorAll('.carousel-indicator');
        console.log('Banner Debug Info:');
        console.log('Total slides found:', slides.length);
        console.log('Total indicators found:', indicators.length);
    }

    function rebuildIndicatorsIfMismatch() {
        const slides = Array.from(carousel.querySelectorAll('.banner-slide'));
        let indicators = Array.from(carousel.querySelectorAll('.carousel-indicator'));
        if (indicators.length === slides.length) return;

        // find existing indicator container (a div containing .carousel-indicator)
        const existingContainer = Array.from(carousel.querySelectorAll('div')).find(d => d.querySelector && d.querySelector('.carousel-indicator'));

        // build new container
        const container = document.createElement('div');
        container.className = 'absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 carousel-indicators';

        slides.forEach((_, i) => {
            const btn = document.createElement('button');
            btn.className = (i === 0 ? 'carousel-indicator bg-white' : 'carousel-indicator bg-white bg-opacity-50') + ' w-3 h-3 rounded-full transition-all';
            btn.setAttribute('data-slide', i);
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                showSlide(i);
            });
            container.appendChild(btn);
        });

        if (existingContainer && existingContainer.parentNode) {
            existingContainer.parentNode.replaceChild(container, existingContainer);
        } else {
            // append near end of carousel
            carousel.appendChild(container);
        }
    }

    function showSlide(index) {
        const slides = Array.from(carousel.querySelectorAll('.banner-slide'));
        const indicators = Array.from(carousel.querySelectorAll('.carousel-indicator'));
        if (slides.length === 0) return;

        // wrap index
        index = ((index % slides.length) + slides.length) % slides.length;

        slides.forEach(s => s.classList.remove('active'));
        slides[index].classList.add('active');

        indicators.forEach(ind => {
            ind.classList.remove('bg-white');
            ind.classList.add('bg-white', 'bg-opacity-50');
        });
        if (indicators[index]) {
            indicators[index].classList.remove('bg-opacity-50');
            indicators[index].classList.add('bg-white');
        }
        currentSlide = index;
    }

    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    function previousSlide() {
        showSlide(currentSlide - 1);
    }

    function startAutoRotate() {
        stopAutoRotate();
        autoRotateInterval = setInterval(nextSlide, 5000);
    }

    function stopAutoRotate() {
        if (autoRotateInterval) {
            clearInterval(autoRotateInterval);
            autoRotateInterval = null;
        }
    }

    // Expose for existing onclick handlers in template
    window.showSlide = showSlide;
    window.nextSlide = nextSlide;
    window.previousSlide = previousSlide;

    // rebuild indicators if mismatch, then wire up click handlers
    rebuildIndicatorsIfMismatch();

    // attach click handlers if any left without listeners
    carousel.querySelectorAll('.carousel-indicator').forEach((btn, i) => {
        if (!btn._hasListener) {
            btn.addEventListener('click', function(e) { e.stopPropagation(); showSlide(i); });
            btn._hasListener = true;
        }
    });

    // pause on hover
    carousel.addEventListener('mouseenter', stopAutoRotate);
    carousel.addEventListener('mouseleave', startAutoRotate);

    logCounts();
    showSlide(0);
    startAutoRotate();
});
</script>

<!-- Categories Section - E-commerce Category Grid -->
<section class="py-6 bg-gradient-to-b from-gray-50 to-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-5">
            <h2 class="text-2xl font-bold text-gray-800">Your Favorite Items Are Here</h2>
            <p class="text-gray-500 text-sm mt-1">Browse categories and find what you need</p>
        </div>
        
        <div class="flex flex-wrap justify-center gap-3">
            @if($categories)
                @foreach($categories as $category)
                    @php
                        $categoryImage = null;
                        if($category->image) {
                            $categoryImage = asset('storage/' . $category->image);
                        } else {
                            $product = \App\Models\Product::with('primaryImage')
                                ->where('category_id', $category->id)
                                ->where('is_active', true)
                                ->whereHas('primaryImage')
                                ->first();
                            if($product && $product->primaryImage) {
                                $categoryImage = asset($product->primaryImage->image_path);
                            }
                        }
                        if(!$categoryImage) {
                            $slugImages = [
                                'electronics' => 'images/oneplus-12-pro.jpg',
                                'fashion' => 'images/adidas-ultraboost-22.jpg',
                                'home-living' => 'images/ikea-poang-armchair.jpg',
                                'beauty-personal-care' => 'images/foreo-luna-3-plus.jpg',
                                'groceries' => 'images/nespresso-vertuo-next.jpg',
                                'sports-outdoors' => 'images/yoga-mat-premium-6mm.jpg',
                                'toys-games' => 'images/board-game-collection.jpg',
                                'computer' => 'images/4k-monitor-27.jpg',
                            ];
                            $categoryImage = asset($slugImages[$category->slug] ?? 'images/4k-monitor-27.jpg');
                        }
                    @endphp
                    <a href="{{ route('products.category', $category->slug) }}" class="block" style="width: 210px;">
                        <div class="bg-white rounded-lg overflow-hidden shadow-md" style="height: 230px;">
                            <!-- Image -->
                            <div style="height: 190px; overflow: hidden;">
                                <img src="{{ $categoryImage }}" 
                                     alt="{{ $category->name }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            
                            <!-- Category Name -->
                            <div style="height: 40px; background: white; display: flex; align-items: center; justify-content: center; padding: 0 10px;">
                                <span style="font-size: 14px; font-weight: bold; color: #1f2937; text-align: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $category->name }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            @endif
        </div>
        
        <!-- View All Button -->
        <div class="text-center mt-5">
            <a href="{{ route('categories.index') }}" class="inline-flex items-center px-5 py-2 bg-orange-500 text-white text-sm font-medium rounded-full hover:bg-orange-600 transition-colors shadow-md hover:shadow-lg">
                View All Categories
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="pt-6 pb-12 bg-gray-100">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold">Featured Products</h2>
            <a href="{{ route('products.index') }}" class="text-primary hover:underline">View All →</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @isset($featuredProducts)
                @if($featuredProducts->count() > 0)
                @foreach($featuredProducts as $product)
                    @include('components.daraz-product-card', ['product' => $product])
                @endforeach
                @else
                    <div class="col-span-full text-center py-8">
                        <p class="text-gray-500">No featured products available</p>
                    </div>
                @endif
            @else
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500">Featured products not loaded</p>
                </div>
            @endisset
        </div>
    </div>
</section>

<!-- Best Sellers Section -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold">Best Sellers</h2>
            <a href="{{ route('products.index') }}" class="text-primary hover:underline">View All →</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @if($bestSellingProducts->count() > 0)
                @foreach($bestSellingProducts as $product)
                    @include('components.daraz-product-card', ['product' => $product])
                @endforeach
            @else
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500">No best selling products available</p>
                </div>
            @endif
        </div>
    </div>
</section>


<!-- Shop by Category Section -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold">Shop by Category</h2>
            <a href="{{ route('products.index') }}" class="text-primary hover:underline">View All →</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
            @php
                $categoryImages = [
                    (object)['category' => App\Models\Category::where('slug', 'electronics')->first(), 'image' => 'images/oneplus-12-pro.jpg'],
                    (object)['category' => App\Models\Category::where('slug', 'fashion')->first(), 'image' => 'images/adidas-ultraboost-22.jpg'],
                    (object)['category' => App\Models\Category::where('slug', 'home-living')->first(), 'image' => 'images/ikea-poang-armchair.jpg'],
                    (object)['category' => App\Models\Category::where('slug', 'beauty-personal-care')->first(), 'image' => 'images/foreo-luna-3-plus.jpg'],
                    (object)['category' => App\Models\Category::where('slug', 'groceries')->first(), 'image' => 'images/nespresso-vertuo-next.jpg'],
                    (object)['category' => App\Models\Category::where('slug', 'sports-outdoors')->first(), 'image' => 'images/yoga-mat-premium-6mm.jpg'],
                    (object)['category' => App\Models\Category::where('slug', 'toys-games')->first(), 'image' => 'images/board-game-collection.jpg'],
                    (object)['category' => App\Models\Category::where('slug', 'computer')->first(), 'image' => 'images/4k-monitor-27.jpg']
                ];
            @endphp
            @foreach($categoryImages as $catData)
                @if($catData->category)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition cursor-pointer group">
                        <a href="{{ route('products.index', ['category' => $catData->category->slug]) }}" class="block">
                            <div class="relative overflow-hidden rounded-t-lg">
                                <img src="{{ asset($catData->image) }}" 
                                     alt="{{ $catData->category->name }}" 
                                     class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                <div class="absolute bottom-4 left-4 text-white">
                                    <h3 class="font-bold text-lg">{{ $catData->category->name }}</h3>
                                    <p class="text-sm opacity-90">Shop Now</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
<!-- Brands Section -->
<section class="py-12 bg-gray-100">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-8">Top Brands</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-6 items-stretch">
            @if($brands && $brands->count() > 0)
                @foreach($brands as $brand)
                    <a href="{{ route('products.brand', $brand->slug) }}" class="bg-white rounded-lg p-3 flex items-center justify-center hover:shadow-lg transition group h-20">
                        @if($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}" 
                                 alt="{{ $brand->name }}" 
                                 class="max-h-12 max-w-full object-contain mx-auto transition-transform group-hover:scale-110"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <!-- Fallback for broken logo -->
                            <div class="h-12 w-full bg-gray-100 rounded flex items-center justify-center" style="display:none;">
                                <span class="text-xs font-semibold text-gray-600">{{ Str::limit($brand->name, 12) }}</span>
                            </div>
                        @else
                            <div class="h-12 w-full bg-gray-100 rounded flex items-center justify-center">
                                <span class="text-xs font-semibold text-gray-600">{{ Str::limit($brand->name, 12) }}</span>
                            </div>
                        @endif
                    </a>
                @endforeach
            @else
                <!-- Fallback when no brands exist -->
                @for($i = 1; $i <= 6; $i++)
                    <div class="bg-white rounded-lg p-4 flex items-center justify-center h-20">
                        <div class="h-12 w-full bg-gray-100 rounded flex items-center justify-center">
                            <span class="text-xs font-semibold text-gray-400">Brand {{ $i }}</span>
                        </div>
                    </div>
                @endfor
            @endif
        </div>
        @if($brands && $brands->count() > 0)
            <div class="text-center mt-8">
                <a href="{{ route('brands.index') }}" class="inline-flex items-center px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
                    View All Brands
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        @endif
    </div>
</section>

<!-- Features Section -->
<section class="py-12 bg-primary text-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
            <div>
                <i class="fas fa-truck text-4xl mb-4"></i>
                <h3 class="font-semibold mb-2">Fast Delivery</h3>
                <p class="text-sm">Quick delivery across Bangladesh</p>
            </div>
            <div>
                <i class="fas fa-shield-alt text-4xl mb-4"></i>
                <h3 class="font-semibold mb-2">Secure Payment</h3>
                <p class="text-sm">100% secure payment methods</p>
            </div>
            <div>
                <i class="fas fa-undo text-4xl mb-4"></i>
                <h3 class="font-semibold mb-2">Easy Returns</h3>
                <p class="text-sm">7 days return policy</p>
            </div>
            <div>
                <i class="fas fa-headset text-4xl mb-4"></i>
                <h3 class="font-semibold mb-2">24/7 Support</h3>
                <p class="text-sm">Dedicated customer support</p>
            </div>
        </div>
    </div>
</section>

<!-- Removed Become a Merchant CTA Section per request; button moved to homepage menubar -->
@endsection

<style>
/* Banner Carousel Styles */
.banner-slide {
    display: none;
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
}

.banner-slide.active {
    display: block;
    opacity: 1;
}

.carousel-indicator {
    transition: all 0.3s ease;
}

.carousel-indicator:hover {
    transform: scale(1.2);
}

/* Ensure proper positioning for carousel controls */
#bannerCarousel {
    position: relative;
}

#bannerCarousel .carousel-controls button {
    z-index: 10;
}

#bannerCarousel .carousel-indicators {
    z-index: 10;
}

/* Force hide 8th carousel indicator (by data-slide or position) */
#bannerCarousel .carousel-indicator[data-slide="8"],
#bannerCarousel .carousel-indicator[data-slide="7"],
#bannerCarousel .carousel-indicator:nth-of-type(8) {
    display: none !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Banner Carousel Functionality
    let currentSlide = 0;
    let autoRotateInterval;
    
    function initCarousel() {
        const slides = document.querySelectorAll('.banner-slide');
        const indicators = document.querySelectorAll('.carousel-indicator');
        
        console.log('Banner Debug Info:');
        console.log('Total slides found:', slides.length);
        console.log('Total indicators found:', indicators.length);
        
        if (slides.length === 0) {
            console.warn('No banner slides found - check if $heroBanners data is available');
            return;
        }
        
        function showSlide(index) {
            // Hide all slides
            slides.forEach(slide => slide.classList.remove('active'));
            indicators.forEach(indicator => {
                indicator.classList.remove('bg-white');
                indicator.classList.add('bg-white', 'bg-opacity-50');
            });
            
            // Show current slide
            if (slides[index]) {
                slides[index].classList.add('active');
            }
            if (indicators[index]) {
                indicators[index].classList.remove('bg-opacity-50');
                indicators[index].classList.add('bg-white');
            }
            
            currentSlide = index;
        }
        
        function nextSlide() {
            const totalSlides = slides.length;
            if (totalSlides > 0) {
                currentSlide = (currentSlide + 1) % totalSlides;
                showSlide(currentSlide);
            }
        }
        
        function previousSlide() {
            const totalSlides = slides.length;
            if (totalSlides > 0) {
                currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
                showSlide(currentSlide);
            }
        }
        
        function startAutoRotate() {
            if (slides.length > 1) {
                autoRotateInterval = setInterval(nextSlide, 5000);
            }
        }
        
        function stopAutoRotate() {
            if (autoRotateInterval) {
                clearInterval(autoRotateInterval);
            }
        }
        
        // Start auto-rotation
        startAutoRotate();
        
        // Pause auto-rotation on hover
        const carousel = document.getElementById('bannerCarousel');
        if (carousel) {
            carousel.addEventListener('mouseenter', stopAutoRotate);
            carousel.addEventListener('mouseleave', startAutoRotate);
        }
        
        // Log indicators for debugging and hide the 8th indicator
        try {
            indicators.forEach((btn, i) => {
                console.log('indicator', i, btn, btn.dataset ? btn.dataset.slide : null);
                if (!btn) return;
                // Hide if dataset matches 7 or 8, or if it's the 8th element (index 7)
                if ((btn.dataset && (btn.dataset.slide === '7' || btn.dataset.slide === '8')) || i === 7) {
                    // remove the element entirely to avoid display/style override issues
                    try { btn.remove(); } catch (err) { btn.style.display = 'none'; }
                }
            });

            // Extra explicit hiding in case NodeList indexing differs
            if (indicators.length >= 8 && indicators[7]) {
                try { indicators[7].remove(); } catch (err) { indicators[7].style.display = 'none'; }
            }
        } catch (e) {
            console.warn('Could not hide carousel button 8/7:', e);
        }

        // Make functions global for onclick handlers
        window.showSlide = showSlide;
        window.nextSlide = nextSlide;
        window.previousSlide = previousSlide;
        
        // Initialize first slide
        showSlide(0);
    }
    
    // Initialize carousel
    initCarousel();
    
    // Wishlist functionality for featured products
    document.querySelectorAll('.wishlist-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            toggleWishlist(productId, this);
        });
    });
    
    // Buy Now functionality for featured products
    document.querySelectorAll('.buy-now-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productSlug = this.dataset.productSlug;
            window.location.href = `/products/${productSlug}`;
        });
    });
    
    // Add to Cart functionality for featured products
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            
            // Show loading state
            const originalText = this.textContent;
            this.textContent = 'Adding...';
            this.disabled = true;
            
            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1 // Default quantity for quick add
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    updateCartCount(data.cart_count);
                } else {
                    showNotification(data.message, 'error');
                }
                
                // Restore button state
                this.textContent = originalText;
                this.disabled = false;
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                showNotification('Error adding product to cart', 'error');
                
                // Restore button state
                this.textContent = originalText;
                this.disabled = false;
            });
        });
    });
    
    function toggleWishlist(productId, button) {
        // Show loading state
        const icon = button.querySelector('.wishlist-icon');
        const originalClass = icon.className;
        icon.className = 'fas fa-spinner fa-spin wishlist-icon';
        
        fetch('/wishlist/toggle', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update button state
                button.dataset.isInWishlist = data.is_in_wishlist ? 'true' : 'false';
                
                // Update icon
                if (data.is_in_wishlist) {
                    icon.className = 'fas fa-heart wishlist-icon text-red-500';
                } else {
                    icon.className = 'far fa-heart wishlist-icon text-gray-400';
                }
                
                // Show notification
                showNotification(data.message, data.is_in_wishlist ? 'success' : 'info');
                
                // Update wishlist count if exists
                if (typeof updateWishlistCount === 'function') {
                    updateWishlistCount(data.wishlist_count);
                }
            } else {
                // Restore original icon
                icon.className = originalClass;
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Wishlist error:', error);
            // Restore original icon
            icon.className = originalClass;
            showNotification('Error updating wishlist', 'error');
        });
    }
    
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300 translate-x-full`;
        
        // Set color based on type
        const colors = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            info: 'bg-blue-500 text-white',
            warning: 'bg-yellow-500 text-white'
        };
        
        notification.className += ' ' + colors[type];
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
});
</script>
