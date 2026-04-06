<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Merchant\MerchantController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\UserController;
use App\Http\Controllers\Frontend\TrackingController;
use App\Http\Controllers\Frontend\AddressController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\BrandingController;
use App\Http\Controllers\Admin\PaymentSettingController;
use App\Http\Controllers\Frontend\SupportController;
use App\Http\Controllers\Admin\SupportController as AdminSupportController;
use App\Http\Controllers\Admin\SuspiciousCustomerController;
use App\Http\Controllers\Admin\CommissionController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\CourierIntegrationController;
use App\Http\Controllers\Admin\ShippingController;
use App\Http\Controllers\Frontend\TestPaymentController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ImageOptimizationController;
use App\Http\Controllers\NetworkOptimizationController;

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/vendors', [ProductController::class, 'vendors'])->name('products.vendors');
Route::get('/vendor/{merchant_slug}', [ProductController::class, 'vendorProducts'])->name('products.vendor');
Route::get('/category/{slug}', [ProductController::class, 'category'])->name('products.category');
Route::get('/brand/{slug}', [ProductController::class, 'brand'])->name('products.brand');
Route::get('/brands', [ProductController::class, 'brands'])->name('brands.index');
Route::get('/categories', [ProductController::class, 'categories'])->name('categories.index');

// Static Pages
Route::get('/about', [\App\Http\Controllers\Frontend\PageController::class, 'about'])->name('about');
Route::get('/contact', [\App\Http\Controllers\Frontend\PageController::class, 'contact'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\Frontend\PageController::class, 'submitContact'])->name('contact.submit');
Route::get('/faq', [\App\Http\Controllers\Frontend\PageController::class, 'faq'])->name('faq');
Route::get('/shipping', [\App\Http\Controllers\Frontend\PageController::class, 'shipping'])->name('shipping');
Route::get('/returns', [\App\Http\Controllers\Frontend\PageController::class, 'returns'])->name('returns');
Route::get('/privacy', [\App\Http\Controllers\Frontend\PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [\App\Http\Controllers\Frontend\PageController::class, 'terms'])->name('terms');

// Cart Routes with Rate Limiting
Route::middleware('rate.limit')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::put('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::get('/api/cart-count', [CartController::class, 'getCartCount'])->name('cart.count');
});

// Checkout Routes with Rate Limiting
Route::middleware('rate.limit')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/validate-coupon', [CheckoutController::class, 'validateCoupon'])->name('checkout.validate.coupon');
    Route::post('/checkout/calculate-shipping', [CheckoutController::class, 'calculateShipping'])->name('checkout.calculate.shipping');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');
});

// Payment callback route
Route::get('/payment/callback/{gateway}', [CheckoutController::class, 'paymentCallback'])->name('payment.callback');

// Sandbox test payment (non-production only)
if (!app()->environment('production')) {
    Route::get('/test-payment/{transactionId}', [TestPaymentController::class, 'show'])->name('test-payment.show');
    Route::post('/test-payment/{transactionId}', [TestPaymentController::class, 'process'])->name('test-payment.process');
}

// Tracking Routes
Route::get('/track', [TrackingController::class, 'index'])->name('tracking.index');
Route::post('/track', [TrackingController::class, 'trackByOrderNumber'])->name('tracking.track');
Route::get('/track/{orderNumber}', [TrackingController::class, 'apiTrack'])->name('tracking.api');
Route::get('/api/track/{orderNumber}', [TrackingController::class, 'apiTrack'])->name('tracking.api.json');

// User Authentication Routes with Rate Limiting
Route::middleware('rate.limit')->group(function () {
    Route::middleware(['fake.customer.detection'])->group(function () {
        Route::get('/register', [UserController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [UserController::class, 'register']);
    });
    Route::get('/login', [UserController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
});

// User Account Routes
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses');
    Route::get('/addresses/create', [AddressController::class, 'create'])->name('addresses.create');
    Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::get('/addresses/{address}/edit', [AddressController::class, 'edit'])->name('addresses.edit');
    Route::put('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('/addresses/{address}/set-default', [AddressController::class, 'setDefault'])->name('addresses.set-default');
    Route::get('/orders', [UserController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [UserController::class, 'orderDetail'])->name('orders.show');
});

// Wishlist Routes (accessible without auth middleware, but controllers handle auth)
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('wishlist.add');
Route::delete('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');
Route::post('/wishlist/clear', [WishlistController::class, 'clear'])->name('wishlist.clear');
Route::get('/wishlist/count', [WishlistController::class, 'getWishlistCount'])->name('wishlist.count');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

// Support Routes
Route::middleware(['auth'])->prefix('support')->name('support.')->group(function () {
    Route::get('/', [SupportController::class, 'index'])->name('index');
    Route::get('/create', [SupportController::class, 'create'])->name('create');
    Route::post('/', [SupportController::class, 'store'])->name('store');
    Route::get('/{id}', [SupportController::class, 'show'])->name('show');
    Route::post('/{id}/reply', [SupportController::class, 'reply'])->name('reply');
    Route::post('/{id}/close', [SupportController::class, 'close'])->name('close');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    
    // Product Management
    Route::get('/products/export', [AdminProductController::class, 'export'])->name('products.export');
    Route::resource('products', AdminProductController::class);
    Route::post('/products/{product}/images', [AdminProductController::class, 'uploadImage'])->name('products.uploadImage');
    Route::delete('/products/images/{image}', [AdminProductController::class, 'deleteImage'])->name('products.deleteImage');
    Route::post('/products/images/{image}/set-primary', [AdminProductController::class, 'setPrimaryImage'])->name('products.setPrimaryImage');
    Route::get('/test-form', function() {
        return view('admin.products.test_form');
    })->name('products.test-form');
    
    // Category Management
    Route::resource('categories', CategoryController::class);
    Route::post('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggleStatus');
    
    // Brand Management
    Route::resource('brands', BrandController::class);
    Route::post('/brands/{brand}/toggle-status', [BrandController::class, 'toggleStatus'])->name('brands.toggleStatus');
    
    // Order Management
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}/print', [OrderController::class, 'printInvoice'])->name('orders.print');
    Route::post('/orders/{order}/send-invoice', [OrderController::class, 'sendInvoiceEmail'])->name('orders.send-invoice');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    
    // Order Tracking Management
    Route::get('/tracking', [\App\Http\Controllers\Admin\TrackingController::class, 'index'])->name('tracking.index');
    Route::get('/tracking/{orderNumber}', [\App\Http\Controllers\Admin\TrackingController::class, 'show'])->name('tracking.show');
    Route::put('/tracking/{orderNumber}/update-status', [\App\Http\Controllers\Admin\TrackingController::class, 'updateStatus'])->name('tracking.update-status');
    Route::post('/tracking/bulk-update', [\App\Http\Controllers\Admin\TrackingController::class, 'bulkUpdateStatus'])->name('tracking.bulk-update');
    Route::get('/api/tracking/search', [\App\Http\Controllers\Admin\TrackingController::class, 'searchOrders'])->name('tracking.search');
    
    // Courier Integration Management
    Route::get('/courier-integrations', [CourierIntegrationController::class, 'index'])->name('courier-integrations.index');
    Route::get('/courier-integrations/create/{orderId}', [CourierIntegrationController::class, 'create'])->name('courier-integrations.create');
    Route::post('/courier-integrations', [CourierIntegrationController::class, 'store'])->name('courier-integrations.store');
    Route::get('/courier-integrations/{integration}', [CourierIntegrationController::class, 'show'])->name('courier-integrations.show');
    Route::post('/courier-integrations/{integration}/cancel', [CourierIntegrationController::class, 'cancel'])->name('courier-integrations.cancel');
    Route::post('/courier-integrations/{integration}/retry', [CourierIntegrationController::class, 'retry'])->name('courier-integrations.retry');
    Route::post('/courier-integrations/bulk-integrate', [CourierIntegrationController::class, 'bulkIntegrate'])->name('courier-integrations.bulk-integrate');
    Route::get('/courier-integrations/stats', [CourierIntegrationController::class, 'stats'])->name('courier-integrations.stats');
    
    // Banner Management
    Route::resource('banners', BannerController::class);
    Route::post('/banners/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banners.toggleStatus');
    
    // Coupon Management
    Route::resource('coupons', CouponController::class);
    Route::post('/coupons/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('coupons.toggleStatus');
    Route::post('/coupons/validate', [CouponController::class, 'validateCoupon'])->name('coupons.validate');
    
    // Review Management
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
    Route::put('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    
    // Settings Management
    Route::resource('settings', SettingsController::class);
    Route::post('/settings/bulk-update', [SettingsController::class, 'bulkUpdate'])->name('settings.bulkUpdate');
    Route::get('/api/settings/public', [SettingsController::class, 'getPublicSettings'])->name('settings.public');
    
    // Payment Settings Management
    Route::get('/payment-settings', [PaymentSettingController::class, 'index'])->name('payment-settings.index');
    Route::get('/payment-settings/{gateway}/edit', [PaymentSettingController::class, 'edit'])->name('payment-settings.edit');
    Route::put('/payment-settings/{gateway}', [PaymentSettingController::class, 'update'])->name('payment-settings.update');
    Route::post('/payment-settings/{gateway}/toggle', [PaymentSettingController::class, 'toggleStatus'])->name('payment-settings.toggle');
    Route::post('/payment-settings/update-order', [PaymentSettingController::class, 'updateOrder'])->name('payment-settings.updateOrder');
    
    // Branding Management
    Route::get('/branding', [BrandingController::class, 'index'])->name('branding.index');
    Route::post('/logo/upload', [BrandingController::class, 'uploadLogo'])->name('logo.upload');
    Route::post('/branding/update', [BrandingController::class, 'updateBranding'])->name('branding.update');
    
    // Support Management
    Route::get('/support/dashboard', [AdminSupportController::class, 'dashboard'])->name('support.dashboard');
    Route::get('/support', [AdminSupportController::class, 'index'])->name('support.index');
    Route::get('/support/{id}', [AdminSupportController::class, 'show'])->name('support.show');
    Route::post('/support/{id}/reply', [AdminSupportController::class, 'reply'])->name('support.reply');
    Route::post('/support/{id}/assign', [AdminSupportController::class, 'assign'])->name('support.assign');
    Route::post('/support/{id}/status', [AdminSupportController::class, 'updateStatus'])->name('support.updateStatus');
    Route::post('/support/{id}/priority', [AdminSupportController::class, 'updatePriority'])->name('support.updatePriority');
    Route::delete('/support/{id}', [AdminSupportController::class, 'destroy'])->name('support.destroy');
    
    // Suspicious Customer Management (specific paths before {suspiciousCustomer} to avoid "analytics" being matched as ID)
    Route::get('/suspicious-customers', [SuspiciousCustomerController::class, 'index'])->name('suspicious-customers.index');
    Route::get('/suspicious-customers/analytics', [SuspiciousCustomerController::class, 'analytics'])->name('suspicious-customers.analytics');
    Route::get('/suspicious-customers/create', [SuspiciousCustomerController::class, 'create'])->name('suspicious-customers.create');
    Route::post('/suspicious-customers', [SuspiciousCustomerController::class, 'store'])->name('suspicious-customers.store');
    Route::get('/suspicious-customers/{suspiciousCustomer}', [SuspiciousCustomerController::class, 'show'])->name('suspicious-customers.show');
    Route::post('/suspicious-customers/{suspiciousCustomer}/ban', [SuspiciousCustomerController::class, 'ban'])->name('suspicious-customers.ban');
    Route::post('/suspicious-customers/{suspiciousCustomer}/unban', [SuspiciousCustomerController::class, 'unban'])->name('suspicious-customers.unban');
    Route::post('/suspicious-customers/{suspiciousCustomer}/update-notes', [SuspiciousCustomerController::class, 'updateNotes'])->name('suspicious-customers.update-notes');
    Route::post('/suspicious-customers/bulk-action', [SuspiciousCustomerController::class, 'bulkAction'])->name('suspicious-customers.bulk-action');
    
    // Merchant Management
    Route::get('/merchants', [\App\Http\Controllers\Admin\MerchantController::class, 'index'])->name('merchants.index');
    Route::get('/merchants/{merchant}', [\App\Http\Controllers\Admin\MerchantController::class, 'show'])->name('merchants.show');
    Route::put('/merchants/{merchant}/approve', [\App\Http\Controllers\Admin\MerchantController::class, 'approve'])->name('merchants.approve');
    Route::put('/merchants/{merchant}/reject', [\App\Http\Controllers\Admin\MerchantController::class, 'reject'])->name('merchants.reject');
    Route::put('/merchants/{merchant}/suspend', [\App\Http\Controllers\Admin\MerchantController::class, 'suspend'])->name('merchants.suspend');
    Route::put('/merchants/{merchant}/reactivate', [\App\Http\Controllers\Admin\MerchantController::class, 'reactivate'])->name('merchants.reactivate');
    Route::get('/merchants/export', [\App\Http\Controllers\Admin\MerchantController::class, 'export'])->name('merchants.export')->withoutMiddleware(['admin']);
    
    // Commission Management
    Route::get('/commissions', [CommissionController::class, 'index'])->name('commissions.index');
    Route::post('/commissions/update', [CommissionController::class, 'update'])->name('commissions.update');
    
    // Inventory Management
    Route::get('/inventory/dashboard', [InventoryController::class, 'dashboard'])->name('inventory.dashboard');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/adjust-stock/{product}', [InventoryController::class, 'adjustStock'])->name('inventory.adjust-stock');
    Route::get('/inventory/history/{product}', [InventoryController::class, 'stockHistory'])->name('inventory.history');
    Route::get('/inventory/bulk-update', [InventoryController::class, 'bulkUpdate'])->name('inventory.bulk-update');
    Route::post('/inventory/bulk-update', [InventoryController::class, 'bulkUpdateStore'])->name('inventory.bulk-update.store');
    Route::get('/inventory/alerts', [InventoryController::class, 'alerts'])->name('inventory.alerts');
    Route::put('/inventory/alerts/{alert}', [InventoryController::class, 'updateAlert'])->name('inventory.alerts.update');
    Route::get('/inventory/reports', [InventoryController::class, 'reports'])->name('inventory.reports');
    Route::post('/commissions/settings', [CommissionController::class, 'updateSettings'])->name('commissions.updateSettings');
    Route::get('/commissions/reports', [CommissionController::class, 'reports'])->name('commissions.reports');
    Route::get('/commissions/payouts', [CommissionController::class, 'payouts'])->name('commissions.payouts');
    Route::post('/commissions/process-payout', [CommissionController::class, 'processPayout'])->name('commissions.processPayout');
    
    // Shipping Management
    Route::get('/shipping', [ShippingController::class, 'index'])->name('shipping.index');
    Route::get('/shipping/zones', [ShippingController::class, 'zones'])->name('shipping.zones');
    Route::get('/shipping/zones/create', [ShippingController::class, 'createZone'])->name('shipping.zones.create');
    Route::post('/shipping/zones', [ShippingController::class, 'storeZone'])->name('shipping.zones.store');
    Route::get('/shipping/zones/{zone}/edit', [ShippingController::class, 'editZone'])->name('shipping.zones.edit');
    Route::put('/shipping/zones/{zone}', [ShippingController::class, 'updateZone'])->name('shipping.zones.update');
    Route::delete('/shipping/zones/{zone}', [ShippingController::class, 'destroyZone'])->name('shipping.zones.destroy');
    
    Route::get('/shipping/methods', [ShippingController::class, 'methods'])->name('shipping.methods');
    Route::get('/shipping/methods/create', [ShippingController::class, 'createMethod'])->name('shipping.methods.create');
    Route::post('/shipping/methods', [ShippingController::class, 'storeMethod'])->name('shipping.methods.store');
    Route::get('/shipping/methods/{method}/edit', [ShippingController::class, 'editMethod'])->name('shipping.methods.edit');
    Route::put('/shipping/methods/{method}', [ShippingController::class, 'updateMethod'])->name('shipping.methods.update');
    Route::delete('/shipping/methods/{method}', [ShippingController::class, 'destroyMethod'])->name('shipping.methods.destroy');
    
    Route::get('/shipping/settings', [ShippingController::class, 'settings'])->name('shipping.settings');
    Route::put('/shipping/settings', [ShippingController::class, 'updateSettings'])->name('shipping.settings.update');
    Route::post('/api/shipping/calculate', [ShippingController::class, 'calculateShipping'])->name('shipping.calculate');
    Route::get('/api/shipping/methods', [ShippingController::class, 'getShippingMethods'])->name('shipping.methods.api');

    });


// Merchant export route (moved products export into admin group)
Route::get('/merchants/export', [\App\Http\Controllers\Admin\MerchantController::class, 'export'])->name('merchants.export');

// Public Merchant Registration Route
Route::middleware(['auth'])->prefix('merchant')->name('merchant.')->group(function () {
    Route::get('/register', [MerchantController::class, 'register'])->name('register');
    Route::post('/register', [MerchantController::class, 'store'])->name('register.store');
});

// Merchant Routes
Route::middleware(['auth', 'merchant'])->prefix('merchant')->name('merchant.')->group(function () {
    Route::get('/dashboard', [MerchantController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [MerchantController::class, 'profile'])->name('profile');
    Route::post('/profile', [MerchantController::class, 'store'])->name('profile.store');
    Route::put('/profile', [MerchantController::class, 'update'])->name('profile.update');
    Route::get('/products', [MerchantController::class, 'products'])->name('products.index');
    Route::get('/products/create', [MerchantController::class, 'createProduct'])->name('products.create');
    Route::post('/products', [MerchantController::class, 'storeProduct'])->name('products.store');
    Route::get('/orders', [MerchantController::class, 'orders'])->name('orders.index');
    Route::get('/earnings', [MerchantController::class, 'earnings'])->name('earnings');
});

// Sitemap Routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/sitemap-products.xml', [SitemapController::class, 'products'])->name('sitemap.products');
Route::get('/sitemap-categories.xml', [SitemapController::class, 'categories'])->name('sitemap.categories');
Route::get('/sitemap-brands.xml', [SitemapController::class, 'brands'])->name('sitemap.brands');

// Image Optimization Routes
Route::get('/images/optimize/{path}', [ImageOptimizationController::class, 'serve'])->where('path', '.*');
Route::get('/images/webp/{path}', [ImageOptimizationController::class, 'webp'])->where('path', '.*');
Route::get('/images/thumb/{path}/{size?}', [ImageOptimizationController::class, 'thumbnail'])->where('path', '.*');

// Network Optimization Routes
Route::get('/api/network-optimization', [NetworkOptimizationController::class, 'getOptimizedAssets']);
Route::get('/images/network-optimized/{path}/{quality?}', [NetworkOptimizationController::class, 'serveOptimizedImage'])->where('path', '.*');
Route::get('/api/5g-metrics', [NetworkOptimizationController::class, 'get5GMetrics']);
