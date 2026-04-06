<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "আরও ছবি ডাউনলোড করা হচ্ছে...\n";
echo "============================\n";

// বাকি প্রোডাক্টগুলোর জন্য নতুন URL
$moreProductImages = [
    'google-pixel-8-pro' => 'https://images.pexels.com/photos/1876999/pexels-photo-1876999.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'asus-rog-zephyrus' => 'https://images.pexels.com/photos/414172/pexels-photo-414172.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'anker-powercore-20000' => 'https://images.pexels.com/photos/414547/pexels-photo-414547.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'logitech-mx-master-3s' => 'https://images.pexels.com/photos/2115256/pexels-photo-2115256.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'ray-ban-aviator-classic' => 'https://images.pexels.com/photos/1122953/pexels-photo-1122953.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'philips-hue-starter-kit' => 'https://images.pexels.com/photos/696987/pexels-photo-696987.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'dyson-v15-detect' => 'https://images.pexels.com/photos/415291/pexels-photo-415291.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'dyson-airwrap-complete' => 'https://images.pexels.com/photos/3780681/pexels-photo-3780681.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'philips-sonicare-diamondclean' => 'https://images.pexels.com/photos/3861969/pexels-photo-3861969.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'foreo-luna-3-plus' => 'https://images.pexels.com/photos/3184291/pexels-photo-3184291.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'braun-series-9-pro' => 'https://images.pexels.com/photos/3861968/pexels-photo-3861968.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'organic-honey-500g' => 'https://images.pexels.com/photos/1439211/pexels-photo-1439211.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'extra-virgin-olive-oil-1l' => 'https://images.pexels.com/photos/266957/pexels-photo-266957.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'camping-tent-4-person' => 'https://images.pexels.com/photos/2398220/pexels-photo-2398220.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'kindle-paperwhite-11th-gen' => 'https://images.pexels.com/photos/1557268/pexels-photo-1557268.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    '4k-webcam-pro' => 'https://images.pexels.com/photos/326424/pexels-photo-326424.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'wireless-gaming-mouse' => 'https://images.pexels.com/photos/2115257/pexels-photo-2115257.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'lego-creator-expert' => 'https://images.pexels.com/photos/163352/lego-blocks-toy-plastic-163352.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'remote-control-car-1-10' => 'https://images.pexels.com/photos/136724/pexels-photo-136724.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'board-game-collection' => 'https://images.pexels.com/photos/815557/pexels-photo-815557.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'drone-with-4k-camera' => 'https://images.pexels.com/photos/442579/pexels-photo-442579.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'gaming-mechanical-keyboard' => 'https://images.pexels.com/photos/2115271/pexels-photo-2115271.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    '4k-monitor-27' => 'https://images.pexels.com/photos/415291/pexels-photo-415291.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'external-ssd-1tb' => 'https://images.pexels.com/photos/1229861/pexels-photo-1229861.jpeg?auto=compress&cs=tinysrgb&w=400&h=400',
    'graphics-card-rtx-4060' => 'https://images.pexels.com/photos/414547/pexels-photo-414547.jpeg?auto=compress&cs=tinysrgb&w=400&h=400'
];

$downloadedCount = 0;
$failedCount = 0;

foreach ($moreProductImages as $slug => $imageUrl) {
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

// সব মিলিয়ে চেক করুন
$totalProducts = App\Models\Product::with('primaryImage')->where('is_featured', true)->count();
$realImages = App\Models\Product::with('primaryImage')
    ->where('is_featured', true)
    ->whereHas('primaryImage', function($query) {
        $query->where('image_path', 'like', '%.jpg');
    })
    ->count();

echo "\nমোট ফিচার্ড প্রোডাক্ট: {$totalProducts}\n";
echo "আসল ছবি সহ: {$realImages}\n";
echo "\nওয়েবসাইট: http://localhost:8000/\n";
