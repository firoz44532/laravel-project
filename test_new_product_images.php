<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing New Product Images:\n";
echo "==========================\n";

// Test the new products I added
$newProducts = App\Models\Product::with('primaryImage')
    ->whereIn('slug', ['oneplus-12-pro', 'google-pixel-8-pro', 'xiaomi-14-ultra'])
    ->get();

foreach ($newProducts as $product) {
    echo "\nProduct: {$product->name}\n";
    if ($product->primaryImage) {
        echo "Image path: {$product->primaryImage->image_path}\n";
        echo "Image URL: {$product->primaryImage->image_url}\n";
        
        // Check if the file exists
        if (str_starts_with($product->primaryImage->image_path, 'images/')) {
            $fullPath = public_path($product->primaryImage->image_path);
        } else {
            $fullPath = public_path('storage/' . $product->primaryImage->image_path);
        }
        
        echo "File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
        echo "Full path: {$fullPath}\n";
        
        // Test if the image is accessible via web
        $imageUrl = $product->primaryImage->image_url;
        echo "Web URL: {$imageUrl}\n";
    }
}

// Test the placeholder image directly
echo "\n\nTesting placeholder image directly:\n";
$placeholderPath = 'images/placeholder-product.jpg';
$placeholderUrl = asset($placeholderPath);
echo "Path: {$placeholderPath}\n";
echo "URL: {$placeholderUrl}\n";
echo "File exists: " . (file_exists(public_path($placeholderPath)) ? 'YES' : 'NO') . "\n";
