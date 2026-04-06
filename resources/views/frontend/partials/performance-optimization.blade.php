<!-- Performance Optimization Scripts -->
<script>
// Core Web Vitals Optimization
(function() {
    'use strict';
    
    // Lazy Loading for Images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                }
            });
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            const lazyImages = document.querySelectorAll('img.lazy');
            lazyImages.forEach(img => imageObserver.observe(img));
        });
    }
    
    // Preload Critical Resources
    function preloadResource(url, as, type = null) {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.href = url;
        link.as = as;
        if (type) link.type = type;
        if (as === 'font') link.crossOrigin = 'anonymous';
        document.head.appendChild(link);
    }
    
    // Service Worker Registration (if available)
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('SW registered: ', registration);
                })
                .catch(function(registrationError) {
                    console.log('SW registration failed: ', registrationError);
                });
        });
    }
    
    // Critical CSS Inlining Helper
    function loadCSS(href) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        link.media = 'print';
        link.onload = function() {
            this.media = 'all';
        };
        document.head.appendChild(link);
    }
    
    // Defer Non-Critical JavaScript
    function deferScript(src) {
        const script = document.createElement('script');
        script.src = src;
        script.async = true;
        script.defer = true;
        document.body.appendChild(script);
    }
    
    // Monitor Core Web Vitals
    function reportWebVitals(metric) {
        // Send to analytics or console for debugging
        console.log('Web Vital:', metric.name, metric.value, metric.rating);
        
        // You can send this to your analytics service
        if (window.gtag) {
            window.gtag('event', metric.name, {
                value: Math.round(metric.value),
                event_category: 'Web Vitals'
            });
        }
    }
    
    // Load Web Vitals monitoring library
    if (typeof import === 'function') {
        import('web-vitals').then(({getCLS, getFID, getFCP, getLCP, getTTFB}) => {
            getCLS(reportWebVitals);
            getFID(reportWebVitals);
            getFCP(reportWebVitals);
            getLCP(reportWebVitals);
            getTTFB(reportWebVitals);
        });
    }
    
    // Optimize Images with WebP support
    function getOptimizedImageUrl(originalUrl) {
        if (supportsWebP()) {
            return originalUrl.replace(/\.(jpg|jpeg|png)$/i, '.webp');
        }
        return originalUrl;
    }
    
    function supportsWebP() {
        const canvas = document.createElement('canvas');
        canvas.width = 1;
        canvas.height = 1;
        return canvas.toDataURL('image/webp').indexOf('data:image/webp') === 0;
    }
    
    // Network-aware optimization for 5G and other networks
    function getNetworkOptimizationLevel() {
        const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        
        if (!connection) {
            return 'standard'; // Fallback for browsers that don't support Network API
        }
        
        const effectiveType = connection.effectiveType;
        const downlink = connection.downlink; // Mbps
        const rtt = connection.rtt; // Round-trip time in ms
        
        // 5G detection and optimization levels
        if (effectiveType === '4g' && downlink >= 10 && rtt <= 50) {
            return '5g-excellent';
        } else if (effectiveType === '4g' && downlink >= 5 && rtt <= 100) {
            return '5g-good';
        } else if (effectiveType === '4g' && downlink >= 2 && rtt <= 200) {
            return '4g-standard';
        } else if (effectiveType === '3g') {
            return '3g-slow';
        } else {
            return '2g-very-slow';
        }
    }
    
    // Adaptive loading based on network conditions
    function adaptiveAssetLoading() {
        const optimizationLevel = getNetworkOptimizationLevel();
        
        switch (optimizationLevel) {
            case '5g-excellent':
                // Load high-quality assets immediately
                loadHighQualityAssets();
                enableAdvancedFeatures();
                break;
            case '5g-good':
                // Load high-quality assets with slight delay
                setTimeout(loadHighQualityAssets, 100);
                enableAdvancedFeatures();
                break;
            case '4g-standard':
                // Load standard quality assets
                loadStandardAssets();
                enableBasicFeatures();
                break;
            case '3g-slow':
                // Load compressed assets
                loadCompressedAssets();
                disableHeavyFeatures();
                break;
            case '2g-very-slow':
                // Load essential assets only
                loadEssentialAssets();
                disableAllNonEssentialFeatures();
                break;
            default:
                loadStandardAssets();
                enableBasicFeatures();
        }
        
        console.log('Network optimization level:', optimizationLevel);
    }
    
    function loadHighQualityAssets() {
        // Load full-resolution images
        document.querySelectorAll('img[data-high-res]').forEach(img => {
            img.src = img.dataset.highRes;
        });
        
        // Load 4K videos if available
        document.querySelectorAll('video[data-4k]').forEach(video => {
            video.src = video.dataset['4k'];
        });
        
        // Enable HD image galleries
        enableHDImageGalleries();
    }
    
    function loadStandardAssets() {
        // Load standard resolution images
        document.querySelectorAll('img[data-standard-res]').forEach(img => {
            img.src = img.dataset.standardRes;
        });
    }
    
    function loadCompressedAssets() {
        // Load low-resolution images
        document.querySelectorAll('img[data-low-res]').forEach(img => {
            img.src = img.dataset.lowRes;
        });
        
        // Disable animations
        document.querySelectorAll('.animate').forEach(el => {
            el.classList.remove('animate');
        });
    }
    
    function loadEssentialAssets() {
        // Load only critical images
        document.querySelectorAll('img[data-critical]').forEach(img => {
            img.src = img.dataset.critical;
        });
        
        // Remove all non-essential images
        document.querySelectorAll('img:not([data-critical])').forEach(img => {
            img.style.display = 'none';
        });
    }
    
    function enableAdvancedFeatures() {
        // Enable 5G-optimized features
        enableInstantPageTransitions();
        enablePredictivePreloading();
        enableEnhancedAnimations();
        enableRealTimeFeatures();
    }
    
    function enableBasicFeatures() {
        // Standard features for all networks
        enableBasicAnimations();
        enableStandardPreloading();
    }
    
    function disableHeavyFeatures() {
        // Disable resource-intensive features
        document.querySelectorAll('.video-background').forEach(el => {
            el.remove();
        });
        document.querySelectorAll('.parallax').forEach(el => {
            el.classList.remove('parallax');
        });
    }
    
    function disableAllNonEssentialFeatures() {
        // Disable all non-essential features for very slow networks
        document.querySelectorAll('[data-non-essential]').forEach(el => {
            el.remove();
        });
    }
    
    // 5G-specific optimizations
    function enableInstantPageTransitions() {
        // Preload next likely pages
        const links = document.querySelectorAll('a[href^="/products/"], a[href^="/category/"]');
        links.forEach(link => {
            link.addEventListener('mouseenter', function() {
                preloadResource(this.href, 'document');
            }, { once: true });
        });
    }
    
    function enablePredictivePreloading() {
        // Predict user behavior and preload resources
        const productGrid = document.querySelector('.products-grid');
        if (productGrid) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Preload product details for visible items
                        const productLinks = entry.target.querySelectorAll('a[href^="/products/"]');
                        productLinks.forEach(link => {
                            setTimeout(() => preloadResource(link.href, 'document'), 100);
                        });
                    }
                });
            });
            observer.observe(productGrid);
        }
    }
    
    function enableEnhancedAnimations() {
        // Enable smooth 60fps animations for 5G
        document.body.style.setProperty('--animation-duration', '0.3s');
        document.body.classList.add('enhanced-animations');
    }
    
    function enableRealTimeFeatures() {
        // Enable real-time updates for inventory, prices, etc.
        enableRealTimeInventoryUpdates();
        enableLivePriceUpdates();
    }
    
    function enableHDImageGalleries() {
        // Load high-resolution gallery images
        document.querySelectorAll('.gallery img').forEach(img => {
            if (img.dataset.hd) {
                img.src = img.dataset.hd;
            }
        });
    }
    
    function enableBasicAnimations() {
        // Standard animations
        document.body.style.setProperty('--animation-duration', '0.5s');
    }
    
    function enableStandardPreloading() {
        // Standard preloading
        const criticalLinks = document.querySelectorAll('a[href="/"], a[href="/cart"]');
        criticalLinks.forEach(link => {
            link.addEventListener('mouseenter', function() {
                preloadResource(this.href, 'document');
            }, { once: true });
        });
    }
    
    function enableRealTimeInventoryUpdates() {
        // WebSocket connection for real-time inventory
        if (window.WebSocket) {
            const ws = new WebSocket('wss://your-domain.com/ws/inventory');
            ws.onmessage = function(event) {
                const data = JSON.parse(event.data);
                updateInventoryDisplay(data);
            };
        }
    }
    
    function enableLivePriceUpdates() {
        // Real-time price updates
        setInterval(() => {
            fetch('/api/live-prices')
                .then(response => response.json())
                .then(prices => updatePriceDisplay(prices))
                .catch(() => {}); // Silent fail
        }, 30000); // Update every 30 seconds on 5G
    }
    
    function updateInventoryDisplay(data) {
        const stockElements = document.querySelectorAll(`[data-product-id="${data.productId}"] .stock-status`);
        stockElements.forEach(el => {
            el.textContent = data.stock > 0 ? `In Stock (${data.stock})` : 'Out of Stock';
            el.className = data.stock > 0 ? 'text-green-600' : 'text-red-600';
        });
    }
    
    function updatePriceDisplay(prices) {
        prices.forEach(price => {
            const priceElements = document.querySelectorAll(`[data-product-id="${price.productId}"] .price`);
            priceElements.forEach(el => {
                el.textContent = `৳${price.price}`;
            });
        });
    }
    
    // Network change monitoring
    function monitorNetworkChanges() {
        const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        
        if (connection) {
            connection.addEventListener('change', function() {
                console.log('Network changed:', connection.effectiveType, connection.downlink + 'Mbps');
                adaptiveAssetLoading();
            });
        }
    }
    
    // 5G-specific performance monitoring
    function monitor5GPerformance() {
        const optimizationLevel = getNetworkOptimizationLevel();
        
        // Enhanced monitoring for 5G networks
        if (optimizationLevel.includes('5g')) {
            // Monitor ultra-fast loading times
            const observer = new PerformanceObserver((list) => {
                list.getEntries().forEach((entry) => {
                    if (entry.entryType === 'navigation') {
                        const loadTime = entry.loadEventEnd - entry.loadEventStart;
                        if (loadTime > 1000) { // 5G should load in under 1 second
                            console.warn('5G performance warning: Load time', loadTime + 'ms');
                        }
                    }
                });
            });
            observer.observe({ entryTypes: ['navigation'] });
        }
    }
    // Initialize optimizations
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize network-aware loading
        adaptiveAssetLoading();
        monitorNetworkChanges();
        monitor5GPerformance();
        
        // Convert images to optimized versions
        const images = document.querySelectorAll('img[data-optimize]');
        images.forEach(img => {
            if (img.dataset.src) {
                img.dataset.src = getOptimizedImageUrl(img.dataset.src);
            } else {
                img.src = getOptimizedImageUrl(img.src);
            }
        });
        
        // Add loading="lazy" to images that don't have it
        const lazyImages = document.querySelectorAll('img:not([loading])');
        lazyImages.forEach(img => {
            if (!img.closest('.hero-section')) { // Don't lazy load hero images
                img.loading = 'lazy';
            }
        });
    });
    
    // Performance Monitoring
    window.addEventListener('load', function() {
        setTimeout(function() {
            const perfData = performance.getEntriesByType('navigation')[0];
            const loadTime = perfData.loadEventEnd - perfData.loadEventStart;
            console.log('Page Load Time:', loadTime + 'ms');
            
            // Report load time to analytics
            if (window.gtag) {
                window.gtag('event', 'page_load_time', {
                    value: Math.round(loadTime),
                    event_category: 'Performance'
                });
            }
        }, 0);
    });
    
})();
</script>

<!-- Critical CSS (inline for faster rendering) -->
<style>
/* Critical above-the-fold styles */
.hero-section { min-height: 400px; }
.loading-skeleton { 
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}
@keyframes loading {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
.lazy { 
    opacity: 0; 
    transition: opacity 0.3s;
}
.lazy.loaded { 
    opacity: 1; 
}
</style>
