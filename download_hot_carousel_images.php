<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Carousel এর জন্য Hot Selling ছবি ডাউনলোড করা হচ্ছে...\n";
echo "===============================================\n";

// Hot selling প্রোডাক্টের জন্য আকর্ষণীয় ছবি URL
$hotCarouselImages = [
    'banners/electronics-sale.jpg' => 'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=1200&h=600&fit=crop&auto=format',
    'banners/fashion-festival.jpg' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200&h=600&fit=crop&auto=format',
    'banners/home-essentials.jpg' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=1200&h=600&fit=crop&auto=format',
    'banners/beauty-care.jpg' => 'https://images.unsplash.com/photo-1522337360788-8b13dee73837?w=1200&h=600&fit=crop&auto=format',
    'banners/groceries-sale.jpg' => 'https://images.unsplash.com/photo-1542838132-92c533dec4b5?w=1200&h=600&fit=crop&auto=format',
    'banners/sports-fitness.jpg' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=1200&h=600&fit=crop&auto=format',
    'banners/toys-games.jpg' => 'https://images.unsplash.com/photo-1596424986406-4b7993cbe4a1?w=1200&h=600&fit=crop&auto=format',
    'banners/computer-deal.jpg' => 'https://images.unsplash.com/photo-1519389950471-1ba77f4a4d39?w=1200&h=600&fit=crop&auto=format'
];

$downloadedCount = 0;
$failedCount = 0;

foreach ($hotCarouselImages as $imagePath => $imageUrl) {
    echo "ডাউনলোড হচ্ছে: {$imagePath}\n";
    
    $fullPath = public_path($imagePath);
    $dir = dirname($fullPath);
    
    // ডিরেক্টরি তৈরি করুন
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // cURL দিয়ে ডাউনলোড করুন
    $ch = curl_init($imageUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $imageData) {
        file_put_contents($fullPath, $imageData);
        
        // ফাইল সাইজ চেক করুন
        $fileSize = filesize($fullPath);
        
        if ($fileSize > 5000) { // 5KB এর বেশি হলে ভালো ছবি
            echo "✅ সফল: {$imagePath} (" . number_format($fileSize) . " bytes)\n";
            $downloadedCount++;
        } else {
            echo "⚠️ ছবি ছোট: {$imagePath} (" . number_format($fileSize) . " bytes)\n";
            unlink($fullPath); // ছোট ছবি ডিলিট করুন
            $failedCount++;
        }
    } else {
        echo "❌ ব্যর্থ: {$imagePath} (HTTP {$httpCode})\n";
        $failedCount++;
    }
}

echo "\nডাউনলোড সম্পন্ন!\n";
echo "সফল: {$downloadedCount} টি\n";
echo "ব্যর্থ: {$failedCount} টি\n";

// এখন এই ছবিগুলো দিয়ে নতুন banner তৈরি করুন
echo "\nHot Selling Banner তৈরি করা হচ্ছে...\n";

$hotBanners = [
    [
        'title' => '🔥 Mega Electronics Sale',
        'description' => 'Up to 60% OFF - Smartphones, Laptops & More!',
        'image' => 'banners/electronics-sale.jpg',
        'link' => '/products?category=electronics',
        'position' => 'hero',
        'is_active' => true,
        'sort_order' => 1
    ],
    [
        'title' => '👗 Fashion Festival',
        'description' => 'Trending Styles at Unbeatable Prices!',
        'image' => 'banners/fashion-festival.jpg',
        'link' => '/products?category=fashion',
        'position' => 'hero',
        'is_active' => true,
        'sort_order' => 2
    ],
    [
        'title' => '🏠 Home Essentials',
        'description' => 'Transform Your Space - Premium Quality!',
        'image' => 'banners/home-essentials.jpg',
        'link' => '/products?category=home-living',
        'position' => 'hero',
        'is_active' => true,
        'sort_order' => 3
    ],
    [
        'title' => '💄 Beauty Bonanza',
        'description' => 'Premium Skincare & Personal Care - 50% OFF!',
        'image' => 'banners/beauty-care.jpg',
        'link' => '/products?category=beauty-personal-care',
        'position' => 'hero',
        'is_active' => true,
        'sort_order' => 4
    ],
    [
        'title' => '🛒 Grocery Deals',
        'description' => 'Fresh & Organic Products - Special Prices!',
        'image' => 'banners/groceries-sale.jpg',
        'link' => '/products?category=groceries',
        'position' => 'hero',
        'is_active' => true,
        'sort_order' => 5
    ],
    [
        'title' => '⚽ Sports & Fitness',
        'description' => 'Get Fit - Premium Equipment at Great Prices!',
        'image' => 'banners/sports-fitness.jpg',
        'link' => '/products?category=sports-outdoors',
        'position' => 'hero',
        'is_active' => true,
        'sort_order' => 6
    ],
    [
        'title' => '🎮 Toys & Games',
        'description' => 'Fun for All Ages - Amazing Collection!',
        'image' => 'banners/toys-games.jpg',
        'link' => '/products?category=toys-games',
        'position' => 'hero',
        'is_active' => true,
        'sort_order' => 7
    ],
    [
        'title' => '💻 Computer Deals',
        'description' => 'High-Performance Hardware - Hot Deals!',
        'image' => 'banners/computer-deal.jpg',
        'link' => '/products?category=computer',
        'position' => 'hero',
        'is_active' => true,
        'sort_order' => 8
    ]
];

$bannerAdded = 0;

foreach ($hotBanners as $banner) {
    // চেক করুন ছবি আছে কিনা
    if (file_exists(public_path($banner['image']))) {
        // চেক করুন banner আগে থেকে আছে কিনা
        $existingBanner = App\Models\Banner::where('title', $banner['title'])->first();
        
        if (!$existingBanner) {
            App\Models\Banner::create($banner);
            echo "✅ Banner যোগ হয়েছে: {$banner['title']}\n";
            $bannerAdded++;
        } else {
            // আপডেট করুন
            $existingBanner->update($banner);
            echo "🔄 Banner আপডেট হয়েছে: {$banner['title']}\n";
            $bannerAdded++;
        }
    } else {
        echo "❌ Banner যোগ ব্যর্থ: {$banner['title']} (ছবি নেই)\n";
    }
}

echo "\nসম্পন্ন!\n";
echo "ছবি ডাউনলোড: {$downloadedCount} টি\n";
echo "Banner যোগ/আপডেট: {$bannerAdded} টি\n";
echo "\n🔥 Hot Selling Carousel Ready!\n";
echo "ওয়েবসাইট: http://localhost:8000/\n";
