<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Carousel-এ Real Product এর ছবি ব্যবহার করা হচ্ছে...\n";
echo "===============================================\n";

// সেরা product গুলো বাছাই করুন (যাদের আসল ছবি আছে)
$featuredProducts = App\Models\Product::with(['primaryImage', 'category'])
    ->where('is_active', true)
    ->where('is_featured', true)
    ->whereHas('primaryImage', function($query) {
        $query->where('image_path', 'like', '%.jpg')
               ->where('image_path', 'not like', '%placeholder%');
    })
    ->orderBy('created_at', 'desc')
    ->take(8)
    ->get();

echo "পাওয়া গেছে " . $featuredProducts->count() . " টি Product যাদের আসল ছবি আছে:\n\n";

$productBanners = [];
$sortOrder = 1;

foreach ($featuredProducts as $product) {
    echo ($sortOrder) . ". {$product->name}\n";
    echo "   ছবি: {$product->primaryImage->image_path}\n";
    echo "   ক্যাটাগরি: " . ($product->category ? $product->category->name : 'N/A') . "\n";
    echo "   দাম: ৳" . number_format($product->price, 2) . "\n\n";
    
    // Banner ডাটা তৈরি করুন
    $productBanners[] = [
        'title' => '🔥 ' . $product->name,
        'description' => 'Premium Quality - Only ৳' . number_format($product->price, 2) . '!',
        'image' => $product->primaryImage->image_path,
        'link' => '/products/' . $product->slug,
        'position' => 'hero',
        'is_active' => true,
        'sort_order' => $sortOrder++
    ];
}

// আগের banner গুলো ডিলিট করুন
echo "আগের banner গুলো ডিলিট করা হচ্ছে...\n";
App\Models\Banner::where('position', 'hero')->delete();
echo "✅ আগের banner ডিলিট হয়েছে\n\n";

// নতুন product banner যোগ করুন
echo "Product banner যোগ করা হচ্ছে...\n";
$bannerAdded = 0;

foreach ($productBanners as $banner) {
    App\Models\Banner::create($banner);
    echo "✅ Banner যোগ হয়েছে: {$banner['title']}\n";
    $bannerAdded++;
}

echo "\nসম্পন্ন!\n";
echo "Product Banner যোগ হয়েছে: {$bannerAdded} টি\n";

// মোট banner চেক করুন
$totalBanners = App\Models\Banner::where('position', 'hero')->where('is_active', true)->count();
echo "\nমোট Hero Banner: {$totalBanners} টি\n";

echo "\n🔥 Carousel এগিয়ে Real Product এর ছবি দেখানো হচ্ছে!\n";
echo "ওয়েবসাইট: http://localhost:8000/\n";
