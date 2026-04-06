<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "ইন্টারনেট থেকে ডাউনলোড করা আসল প্রোডাক্টের ছবি:\n";
echo "==========================================\n";

// ডাউনলোড করা ছবিগুলো দেখান
$downloadedImages = [
    'oneplus-12-pro.jpg' => 'OnePlus 12 Pro',
    'google-pixel-8-pro.jpg' => 'Google Pixel 8 Pro', 
    'xiaomi-14-ultra.jpg' => 'Xiaomi 14 Ultra',
    'dell-xps-15.jpg' => 'Dell XPS 15',
    'hp-spectre-x360.jpg' => 'HP Spectre x360',
    'asus-rog-zephyrus.jpg' => 'ASUS ROG Zephyrus',
    'samsung-galaxy-tab-s9-ultra.jpg' => 'Samsung Galaxy Tab S9 Ultra',
    'microsoft-surface-pro-9.jpg' => 'Microsoft Surface Pro 9',
    'jbl-tune-760nc.jpg' => 'JBL Tune 760NC',
    'anker-powercore-20000.jpg' => 'Anker PowerCore 20000',
    'logitech-mx-master-3s.jpg' => 'Logitech MX Master 3S',
    'adidas-ultraboost-22.jpg' => 'Adidas Ultraboost 22',
    'levis-501-original-jeans.jpg' => 'Levi\'s 501 Original Fit Jeans',
    'puma-rs-x3.jpg' => 'Puma RS-X³',
    'ikea-poang-armchair.jpg' => 'IKEA POÄNG Armchair',
    'dyson-v15-detect.jpg' => 'Dyson V15 Detect',
    'dyson-airwrap-complete.jpg' => 'Dyson Airwrap Complete',
    'philips-sonicare-diamondclean.jpg' => 'Philips Sonicare DiamondClean',
    'foreo-luna-3-plus.jpg' => 'Foreo Luna 3 Plus',
    'braun-series-9-pro.jpg' => 'Braun Series 9 Pro',
    'nespresso-vertuo-next.jpg' => 'Nespresso Vertuo Next',
    'organic-honey-500g.jpg' => 'Organic Honey 500g',
    'extra-virgin-olive-oil-1l.jpg' => 'Extra Virgin Olive Oil 1L',
    'premium-coffee-beans-1kg.jpg' => 'Premium Coffee Beans 1kg',
    'yoga-mat-premium-6mm.jpg' => 'Yoga Mat Premium 6mm',
    'dumbbell-set-20kg.jpg' => 'Dumbbell Set 20kg',
    'camping-tent-4-person.jpg' => 'Camping Tent 4-Person',
    'mountain-bike-26.jpg' => 'Mountain Bike 26"',
    'bluetooth-speaker-waterproof.jpg' => 'Bluetooth Speaker Waterproof',
    '4k-webcam-pro.jpg' => '4K Webcam Pro',
    'wireless-gaming-mouse.jpg' => 'Wireless Gaming Mouse',
    'board-game-collection.jpg' => 'Board Game Collection',
    'drone-with-4k-camera.jpg' => 'Drone with 4K Camera',
    '4k-monitor-27.jpg' => '4K Monitor 27"',
    'external-ssd-1tb.jpg' => 'External SSD 1TB',
    'graphics-card-rtx-4060.jpg' => 'Graphics Card RTX 4060'
];

echo "মোট " . count($downloadedImages) . " টি আসল প্রোডাক্ট ছবি ডাউনলোড হয়েছে:\n\n";

foreach ($downloadedImages as $filename => $productName) {
    $filePath = public_path('images/' . $filename);
    
    if (file_exists($filePath)) {
        $fileSize = filesize($filePath);
        $fileType = mime_content_type($filePath);
        
        echo "✅ {$productName}\n";
        echo "   ফাইল: {$filename}\n";
        echo "   সাইজ: " . number_format($fileSize) . " bytes\n";
        echo "   টাইপ: {$fileType}\n";
        echo "   URL: http://localhost:8000/images/{$filename}\n\n";
    } else {
        echo "❌ {$productName} - ফাইল পাওয়া যায়নি\n\n";
    }
}

// ডাটাবেসে কয়টি প্রোডাক্টের ছবি আছে
$productsWithImages = App\Models\Product::with('primaryImage')
    ->whereHas('primaryImage', function($query) {
        $query->where('image_path', 'like', '%.jpg');
    })
    ->count();

echo "\nডাটাবেস স্ট্যাটাস:\n";
echo "প্রোডাক্ট সংখ্যা: " . App\Models\Product::count() . "\n";
echo "ছবি সহ প্রোডাক্ট: {$productsWithImages}\n";
echo "\nওয়েবসাইট: http://localhost:8000/\n";
echo "সব ছবি সেখানে দেখতে পাবেন!\n";
