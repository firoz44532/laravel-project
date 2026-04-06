<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Images with Running Server:\n";
echo "===================================\n";

// Test server status
$serverUrl = 'http://localhost:8000/';
$headers = @get_headers($serverUrl, 1);
if ($headers && strpos($headers[0], '200') !== false) {
    echo "✅ Web Server: RUNNING on port 8000\n";
} else {
    echo "❌ Web Server: NOT accessible\n";
}

// Test placeholder image
$placeholderUrl = 'http://localhost:8000/images/placeholder-product.jpg';
echo "\nTesting placeholder image:\n";
echo "URL: {$placeholderUrl}\n";

$headers = @get_headers($placeholderUrl, 1);
if ($headers && strpos($headers[0], '200') !== false) {
    echo "✅ Placeholder Image: ACCESSIBLE\n";
} else {
    echo "❌ Placeholder Image: NOT ACCESSIBLE\n";
    if ($headers) {
        echo "Response: " . $headers[0] . "\n";
    }
}

// Test a few new products
$newProducts = App\Models\Product::with('primaryImage')
    ->whereIn('slug', ['oneplus-12-pro', 'google-pixel-8-pro', 'xiaomi-14-ultra'])
    ->get();

echo "\nTesting new product images:\n";
foreach ($newProducts as $product) {
    echo "\nProduct: {$product->name}\n";
    if ($product->primaryImage) {
        $imageUrl = str_replace('http://localhost/', 'http://localhost:8000/', $product->primaryImage->image_url);
        echo "Image URL: {$imageUrl}\n";
        
        $headers = @get_headers($imageUrl, 1);
        if ($headers && strpos($headers[0], '200') !== false) {
            echo "✅ Image: ACCESSIBLE\n";
        } else {
            echo "❌ Image: NOT ACCESSIBLE\n";
        }
    }
}

echo "\nFrontend URL: http://localhost:8000/\n";
echo "Admin Panel: http://localhost:8000/admin\n";
