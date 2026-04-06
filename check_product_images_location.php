<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "প্রোডাক্টের ছবির লোকেশন চেক:\n";
echo "============================\n";

// প্রথম ৫টি ফিচার্ড প্রোডাক্ট চেক করুন
$featuredProducts = App\Models\Product::with('primaryImage')
    ->where('is_featured', true)
    ->orderBy('created_at', 'desc')
    ->take(5)
    ->get();

echo "ফিচার্ড প্রোডাক্টের ছবি:\n\n";

foreach ($featuredProducts as $index => $product) {
    echo ($index + 1) . ". {$product->name}\n";
    
    if ($product->primaryImage) {
        echo "   ডাটাবেসে সেভড পাথ: {$product->primaryImage->image_path}\n";
        
        // ফাইল আছে কিনা চেক করুন
        if (str_starts_with($product->primaryImage->image_path, 'images/')) {
            $fullPath = public_path($product->primaryImage->image_path);
        } else {
            $fullPath = public_path('storage/' . $product->primaryImage->image_path);
        }
        
        echo "   ফুল পাথ: {$fullPath}\n";
        echo "   ফাইল আছে: " . (file_exists($fullPath) ? 'হ্যাঁ ✅' : 'না ❌') . "\n";
        
        if (file_exists($fullPath)) {
            echo "   ফাইল সাইজ: " . filesize($fullPath) . " bytes\n";
            echo "   ফাইল টাইপ: " . mime_content_type($fullPath) . "\n";
        }
        
        // ওয়েব URL
        echo "   ওয়েব URL: {$product->primaryImage->image_url}\n";
        
        // ওয়েবে একসেসিবল কিনা
        $headers = @get_headers($product->primaryImage->image_url, 1);
        if ($headers && strpos($headers[0], '200') !== false) {
            echo "   ওয়েব স্ট্যাটাস: একসেসিবল ✅\n";
        } else {
            echo "   ওয়েব স্ট্যাটাস: একসেসিবল নয় ❌\n";
        }
    } else {
        echo "   স্ট্যাটাস: কোনো ছবি নেই ❌\n";
    }
    echo "\n";
}

// সব ইমেজ ফাইল চেক করুন
echo "\nসব ইমেজ ফাইলের লিস্ট:\n";
$imageDir = public_path('images');
if (is_dir($imageDir)) {
    $files = scandir($imageDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'jpg') {
            $filePath = $imageDir . '/' . $file;
            echo "- {$file} (" . filesize($filePath) . " bytes)\n";
        }
    }
}

echo "\nওয়েবসাইট লিংক: http://localhost:8000/\n";
echo "উপরের লিংকে গেলে আপনি প্রোডাক্টের ছবি দেখতে পাবেন\n";
