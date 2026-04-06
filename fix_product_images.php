<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Fixing Product Images:\n";
echo "=====================\n";

// Update all products with placeholder URLs to use local image
$productsUpdated = App\Models\ProductImage::where('image_path', 'https://via.placeholder.com/400x400')
    ->update(['image_path' => 'images/placeholder-product.jpg']);

echo "Updated {$productsUpdated} product images to use local placeholder\n";

// Verify the fix
$sampleProducts = App\Models\Product::with('primaryImage')
    ->take(5)
    ->get();

echo "\nVerification:\n";
foreach ($sampleProducts as $product) {
    echo "• {$product->name}: ";
    if ($product->primaryImage) {
        echo "Image path: {$product->primaryImage->image_path}, ";
        echo "Image URL: {$product->primaryImage->image_url}\n";
    } else {
        echo "No image\n";
    }
}

echo "\nTotal products with images: " . App\Models\Product::whereHas('images')->count() . "\n";
echo "Total products: " . App\Models\Product::count() . "\n";
