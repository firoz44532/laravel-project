<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "ইমেজ স্ট্যাটাস (বাংলায়):\n";
echo "========================\n";

// প্রথম ৫টি ফিচার্ড প্রোডাক্ট দেখান
$featuredProducts = App\Models\Product::with('primaryImage')
    ->where('is_featured', true)
    ->orderBy('created_at', 'desc')
    ->take(5)
    ->get();

echo "মোট ফিচার্ড প্রোডাক্ট: " . $featuredProducts->count() . " টি\n\n";

foreach ($featuredProducts as $index => $product) {
    echo ($index + 1) . ". " . $product->name . "\n";
    
    if ($product->primaryImage) {
        echo "   ইমেজ পাথ: " . $product->primaryImage->image_path . "\n";
        echo "   ইমেজ URL: " . $product->primaryImage->image_url . "\n";
        
        // ইমেজ কাজ করছে কিনা চেক করুন
        $url = $product->primaryImage->image_url;
        $headers = @get_headers($url, 1);
        
        if ($headers && strpos($headers[0], '200') !== false) {
            echo "   স্ট্যাটাস: ✅ ইমেজ দেখা যাচ্ছে\n";
        } else {
            echo "   স্ট্যাটাস: ❌ ইমেজ দেখা যাচ্ছে না\n";
        }
    } else {
        echo "   স্ট্যাটাস: ❌ কোনো ইমেজ নেই\n";
    }
    echo "\n";
}

echo "ওয়েবসাইট লিংক: http://localhost:8000/\n";
echo "উপরের লিংকে গেলে আপনি ইমেজগুলো দেখতে পাবেন\n";
