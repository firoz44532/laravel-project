<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Carousel Banner চেক করা হচ্ছে...\n";
echo "===============================\n";

// সব hero banner চেক করুন
$banners = App\Models\Banner::where('position', 'hero')->where('is_active', true)->orderBy('sort_order')->get();

echo "মোট Banner: " . $banners->count() . " টি\n\n";

foreach ($banners as $index => $banner) {
    echo ($index + 1) . ". {$banner->title}\n";
    echo "   ছবি পাথ: {$banner->image}\n";
    echo "   লিংক: {$banner->link}\n";
    
    // ছবি ফাইল আছে কিনা চেক করুন
    $fullPath = public_path($banner->image);
    echo "   ফাইল আছে: " . (file_exists($fullPath) ? 'হ্যাঁ ✅' : 'না ❌') . "\n";
    
    if (file_exists($fullPath)) {
        echo "   ফাইল সাইজ: " . number_format(filesize($fullPath)) . " bytes\n";
        echo "   ফাইল টাইপ: " . mime_content_type($fullPath) . "\n";
        
        // ওয়েব URL টেস্ট করুন
        $imageUrl = 'http://localhost:8000/' . $banner->image;
        $headers = @get_headers($imageUrl, 1);
        if ($headers && strpos($headers[0], '200') !== false) {
            echo "   ওয়েব URL: কাজ করছে ✅\n";
        } else {
            echo "   ওয়েব URL: কাজ করছে না ❌\n";
        }
    }
    echo "\n";
}

echo "\nসমস্যা সমাধান:\n";
echo "=============\n";

// যেসব ছবি নেই সেগুলোর জন্য placeholder ছবি ব্যবহার করুন
$missingImages = [];
foreach ($banners as $banner) {
    $fullPath = public_path($banner->image);
    if (!file_exists($fullPath)) {
        $missingImages[] = $banner;
    }
}

if (!empty($missingImages)) {
    echo "ছবি নেই এমন " . count($missingImages) . " টি Banner:\n";
    
    foreach ($missingImages as $banner) {
        echo "- {$banner->title}\n";
        
        // placeholder ছবি দিন
        $banner->update(['image' => 'images/placeholder-product.jpg']);
        echo "  ✅ Placeholder ছবি যোগ হয়েছে\n";
    }
} else {
    echo "সব ছবি আছে ✅\n";
}

echo "\nওয়েবসাইট: http://localhost:8000/\n";
