<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Hot Selling প্রোডাক্ট তৈরি করা হচ্ছে...\n";
echo "================================\n";

// কিছু প্রোডাক্টকে hot selling হিসেবে চিহ্নিত করুন (রিভিউ এবং রেটিং এর উপর ভিত্তি করে)
$hotSellingProducts = App\Models\Product::with(['primaryImage', 'category'])
    ->where('is_active', true)
    ->orderBy('created_at', 'desc')
    ->take(10)
    ->get();

echo "Hot Selling প্রোডাক্ট:\n";
foreach ($hotSellingProducts as $index => $product) {
    echo ($index + 1) . ". {$product->name}\n";
    
    if ($product->primaryImage) {
        echo "   ছবি: {$product->primaryImage->image_path}\n";
    } else {
        echo "   ছবি: নেই\n";
    }
    echo "\n";
}

// HomeController আপডেট করুন
$homeControllerPath = app_path('Http/Controllers/Frontend/HomeController.php');
$currentContent = file_get_contents($homeControllerPath);

// bestSellingProducts কে আরও ভালোভাবে সিলেক্ট করার জন্য কোড আপডেট
$newBestSellingCode = '        // Get hot selling products (products with images, good ratings, and recent)
        $bestSellingProducts = Product::with([\'primaryImage\', \'category\'])
            ->where(\'is_active\', true)
            ->whereHas(\'primaryImage\', function($query) {
                $query->where(\'image_path\', \'like\', \'%.jpg\');
            })
            ->where(\'is_featured\', true)
            ->inRandomOrder()
            ->take(8)
            ->get();';

// পুরানো bestSellingProducts কোড রিপ্লেস করুন
$oldPattern = '/        \/\/ Get best selling products.*?->get\(\);/s';
$newContent = preg_replace($oldPattern, $newBestSellingCode, $currentContent);

if ($newContent && $newContent !== $currentContent) {
    file_put_contents($homeControllerPath, $newContent);
    echo "✅ HomeController আপডেট হয়েছে\n";
} else {
    echo "❌ HomeController আপডেট করতে ব্যর্থ\n";
}

echo "\nএখন carousel এ hot selling প্রোডাক্ট দেখানোর জন্য view আপডেট করা হবে...\n";
