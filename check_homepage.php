<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Homepage Featured Products Check:\n";
echo "================================\n";

// Check what featured products are loaded
$featuredProducts = App\Models\Product::with('primaryImage')
    ->where('is_featured', true)
    ->take(10)
    ->get();

echo "Featured Products Count: " . $featuredProducts->count() . "\n\n";

foreach ($featuredProducts as $product) {
    echo "Product: {$product->name}\n";
    echo "Slug: {$product->slug}\n";
    echo "Is Featured: " . ($product->is_featured ? 'YES' : 'NO') . "\n";
    
    if ($product->primaryImage) {
        echo "Image Path: {$product->primaryImage->image_path}\n";
        echo "Image URL: {$product->primaryImage->image_url}\n";
        
        // Check if it's the placeholder
        if ($product->primaryImage->image_path === 'images/placeholder-product.jpg') {
            echo "Status: Using placeholder image ✅\n";
        } else {
            echo "Status: Using custom image\n";
        }
    } else {
        echo "Status: No image ❌\n";
    }
    echo "---\n";
}

echo "\nHomepage URL: http://localhost:8000/\n";
echo "Direct access to check: http://localhost:8000/";
