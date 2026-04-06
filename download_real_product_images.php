<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "আসল প্রোডাক্টের ছবি ডাউনলোড করা হচ্ছে...\n";
echo "====================================\n";

// প্রোডাক্ট অনুযায়ী আসল ছবির URL
$productImages = [
    // Mobile Phones
    'oneplus-12-pro' => 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=400&h=400&fit=crop',
    'google-pixel-8-pro' => 'https://images.unsplash.com/photo-1598320096376-7c208909c934?w=400&h=400&fit=crop',
    'xiaomi-14-ultra' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=400&h=400&fit=crop',
    
    // Laptops
    'dell-xps-15' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=400&h=400&fit=crop',
    'hp-spectre-x360' => 'https://images.unsplash.com/photo-1541807084-5c52b6b3adef?w=400&h=400&fit=crop',
    'asus-rog-zephyrus' => 'https://images.unsplash.com/photo-1593642632827-cfda12e2c1a1?w=400&h=400&fit=crop',
    
    // Tablets
    'samsung-galaxy-tab-s9-ultra' => 'https://images.unsplash.com/photo-1544244015-0df4b3ffc6b0?w=400&h=400&fit=crop',
    'microsoft-surface-pro-9' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=400&h=400&fit=crop',
    
    // Accessories
    'jbl-tune-760nc' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
    'anker-powercore-20000' => 'https://images.unsplash.com/photo-1596458097967-8576e5057838?w=400&h=400&fit=crop',
    'logitech-mx-master-3s' => 'https://images.unsplash.com/photo-1615638379389-8ade924024b7?w=400&h=400&fit=crop',
    
    // Fashion
    'adidas-ultraboost-22' => 'https://images.unsplash.com/photo-1549298916-b41d501d3772?w=400&h=400&fit=crop',
    'levis-501-original-jeans' => 'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=400&h=400&fit=crop',
    'ray-ban-aviator-classic' => 'https://images.unsplash.com/photo-1511499767150-a6a8a425bb9b?w=400&h=400&fit=crop',
    'puma-rs-x3' => 'https://images.unsplash.com/photo-1606107557195-0e29a4b5b4aa?w=400&h=400&fit=crop',
    
    // Home & Living
    'ikea-poang-armchair' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=400&h=400&fit=crop',
    'philips-hue-starter-kit' => 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400&h=400&fit=crop',
    'dyson-v15-detect' => 'https://images.unsplash.com/photo-1622398959706-f5d6e3f15d5b?w=400&h=400&fit=crop',
    'instant-pot-duo-7-in-1' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=400&h=400&fit=crop',
    
    // Beauty & Personal Care
    'dyson-airwrap-complete' => 'https://images.unsplash.com/photo-1522337360788-8b13dee73837?w=400&h=400&fit=crop',
    'philips-sonicare-diamondclean' => 'https://images.unsplash.com/photo-1608198093002-ad4e00e39c63?w=400&h=400&fit=crop',
    'foreo-luna-3-plus' => 'https://images.unsplash.com/photo-1596462502278-27d54b9ace11?w=400&h=400&fit=crop',
    'braun-series-9-pro' => 'https://images.unsplash.com/photo-1620916566398-39f8622ba10b?w=400&h=400&fit=crop',
    
    // Groceries
    'nespresso-vertuo-next' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&h=400&fit=crop',
    'organic-honey-500g' => 'https://images.unsplash.com/photo-1587049352243-6543832afbff?w=400&h=400&fit=crop',
    'extra-virgin-olive-oil-1l' => 'https://images.unsplash.com/photo-1532316304251-1cca0761a9ad?w=400&h=400&fit=crop',
    'premium-coffee-beans-1kg' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400&h=400&fit=crop',
    
    // Sports & Outdoors
    'yoga-mat-premium-6mm' => 'https://images.unsplash.com/photo-1545205597-3d9d02c29597?w=400&h=400&fit=crop',
    'dumbbell-set-20kg' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=400&h=400&fit=crop',
    'camping-tent-4-person' => 'https://images.unsplash.com/photo-1504280390367-08169e9cc2b1?w=400&h=400&fit=crop',
    'mountain-bike-26' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=400&fit=crop',
    
    // Books & Media
    'kindle-paperwhite-11th-gen' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=400&h=400&fit=crop',
    'bluetooth-speaker-waterproof' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=400&h=400&fit=crop',
    '4k-webcam-pro' => 'https://images.unsplash.com/photo-1596177392502-43b4e3b3e478?w=400&h=400&fit=crop',
    'wireless-gaming-mouse' => 'https://images.unsplash.com/photo-1615660164034-c5b8893cbeaf?w=400&h=400&fit=crop',
    
    // Toys & Games
    'lego-creator-expert' => 'https://images.unsplash.com/photo-1596474031368-1f9880a8d6d9?w=400&h=400&fit=crop',
    'remote-control-car-1-10' => 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?w=400&h=400&fit=crop',
    'board-game-collection' => 'https://images.unsplash.com/photo-1520810637414-77e7f9b3e4c4?w=400&h=400&fit=crop',
    'drone-with-4k-camera' => 'https://images.unsplash.com/photo-1478147427352-3a1cb4030365?w=400&h=400&fit=crop',
    
    // Computer
    'gaming-mechanical-keyboard' => 'https://images.unsplash.com/photo-1596424986406-4b7993e06c26?w=400&h=400&fit=crop',
    '4k-monitor-27' => 'https://images.unsplash.com/photo-1527443224154-4a865b0b4d1f?w=400&h=400&fit=crop',
    'external-ssd-1tb' => 'https://images.unsplash.com/photo-1598930347212-2297d5b6161e?w=400&h=400&fit=crop',
    'graphics-card-rtx-4060' => 'https://images.unsplash.com/photo-1598930347212-2297d5b6161e?w=400&h=400&fit=crop'
];

$downloadedCount = 0;
$failedCount = 0;

foreach ($productImages as $slug => $imageUrl) {
    echo "ডাউনলোড হচ্ছে: {$slug}\n";
    
    // প্রোডাক্ট খুঁজুন
    $product = App\Models\Product::where('slug', $slug)->first();
    
    if ($product && $product->primaryImage) {
        $fileName = $slug . '.jpg';
        $filePath = public_path('images/' . $fileName);
        
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
            // ফাইল সেভ করুন
            file_put_contents($filePath, $imageData);
            
            // ডাটাবেস আপডেট করুন
            $product->primaryImage->update(['image_path' => 'images/' . $fileName]);
            
            echo "✅ সফল: {$product->name}\n";
            $downloadedCount++;
        } else {
            echo "❌ ব্যর্থ: HTTP {$httpCode}\n";
            $failedCount++;
        }
    } else {
        echo "❌ প্রোডাক্ট পাওয়া যায়নি: {$slug}\n";
        $failedCount++;
    }
}

echo "\nসম্পন্ন!\n";
echo "সফল: {$downloadedCount} টি\n";
echo "ব্যর্থ: {$failedCount} টি\n";
echo "\nওয়েবসাইট: http://localhost:8000/\n";
