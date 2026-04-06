<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "ACTUAL IMAGE URLS BEING GENERATED:\n";
echo "===================================\n";

// Get first featured product
$product = App\Models\Product::with('primaryImage')
    ->where('is_featured', true)
    ->orderBy('created_at', 'desc')
    ->first();

if ($product && $product->primaryImage) {
    echo "Product: {$product->name}\n";
    echo "Image Path: {$product->primaryImage->image_path}\n";
    echo "Generated URL: {$product->primaryImage->image_url}\n";
    
    // Test if this URL works
    $url = $product->primaryImage->image_url;
    $headers = @get_headers($url, 1);
    
    if ($headers && strpos($headers[0], '200') !== false) {
        echo "Status: WORKING ✅\n";
    } else {
        echo "Status: NOT WORKING ❌\n";
        echo "Should be: http://localhost:8000/images/placeholder-product.jpg\n";
        
        // Test correct URL
        $correctUrl = 'http://localhost:8000/images/placeholder-product.jpg';
        $correctHeaders = @get_headers($correctUrl, 1);
        if ($correctHeaders && strpos($correctHeaders[0], '200') !== false) {
            echo "Correct URL works: ✅\n";
        }
    }
} else {
    echo "No featured product found\n";
}

echo "\nThe issue is URL generation - fix needed in configuration.\n";
