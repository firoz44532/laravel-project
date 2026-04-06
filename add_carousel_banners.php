<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Carousel Banner যোগ করা হচ্ছে...\n";
echo "===============================\n";

// Hot selling প্রোডাক্টের জন্য banner তৈরি করুন
$bannerData = [
    [
        'title' => 'Mega Electronics Sale',
        'description' => 'Up to 50% OFF on smartphones, laptops & accessories',
        'image' => 'banners/electronics-sale.jpg',
        'link' => '/products?category=electronics',
        'position' => 'hero',
        'is_active' => true,
        'sort_order' => 1
    ],
    [
        'title' => 'Fashion Festival',
        'description' => 'Latest trends at unbeatable prices',
        'image' => 'banners/fashion-festival.jpg',
        'link' => '/products?category=fashion',
        'position' => 'hero',
        'is_active' => true,
        'sort_order' => 2
    ],
    [
        'title' => 'Home Essentials',
        'description' => 'Transform your space with premium products',
        'image' => 'banners/home-essentials.jpg',
        'link' => '/products?category=home-living',
        'position' => 'hero',
        'is_active' => true,
        'sort_order' => 3
    ],
    [
        'title' => 'Beauty & Care',
        'description' => 'Premium skincare and personal care items',
        'image' => 'banners/beauty-care.jpg',
        'link' => '/products?category=beauty-personal-care',
        'position' => 'hero',
        'is_active' => true,
        'sort_order' => 4
    ]
];

$addedCount = 0;

foreach ($bannerData as $banner) {
    // চেক করুন ইতিমধ্যে আছে কিনা
    $existingBanner = App\Models\Banner::where('title', $banner['title'])->first();
    
    if (!$existingBanner) {
        App\Models\Banner::create($banner);
        echo "✅ Banner যোগ হয়েছে: {$banner['title']}\n";
        $addedCount++;
    } else {
        echo "⚠️ Banner আগে থেকেই আছে: {$banner['title']}\n";
    }
}

echo "\nমোট নতুন Banner: {$addedCount} টি\n";

// Banner ছবি ডাউনলোড করুন
echo "\nBanner ছবি ডাউনলোড করা হচ্ছে...\n";

$bannerImages = [
    'banners/electronics-sale.jpg' => 'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=1200&h=600&fit=crop',
    'banners/fashion-festival.jpg' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200&h=600&fit=crop',
    'banners/home-essentials.jpg' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=1200&h=600&fit=crop',
    'banners/beauty-care.jpg' => 'https://images.unsplash.com/photo-1522337360788-8b13dee73837?w=1200&h=600&fit=crop'
];

$imageDownloaded = 0;

foreach ($bannerImages as $imagePath => $imageUrl) {
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
    
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $imageData) {
        file_put_contents($fullPath, $imageData);
        echo "✅ ছবি ডাউনলোড হয়েছে: {$imagePath}\n";
        $imageDownloaded++;
    } else {
        echo "❌ ছবি ডাউনলোড ব্যর্থ: {$imagePath}\n";
    }
}

echo "\nসম্পন্ন!\n";
echo "Banner যোগ হয়েছে: {$addedCount} টি\n";
echo "ছবি ডাউনলোড হয়েছে: {$imageDownloaded} টি\n";
echo "\nওয়েবসাইট: http://localhost:8000/\n";
echo "Carousel এগিয়ে দেখুন!\n";
