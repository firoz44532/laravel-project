<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Product Images Analysis:\n";
echo "=======================\n";

$products = App\Models\Product::with('images')->get();

foreach ($products as $product) {
    $imageCount = $product->images->count();
    $primaryImage = $product->primaryImage;
    
    echo "• {$product->name}: {$imageCount} image(s)";
    if ($primaryImage) {
        echo " [Primary: {$primaryImage->image_path}]";
    }
    echo "\n";
}

echo "\nSummary:\n";
echo "- Total Products: " . $products->count() . "\n";
echo "- Total Product Images: " . App\Models\ProductImage::count() . "\n";
echo "- Products with images: " . $products->filter(function($p) { return $p->images->count() > 0; })->count() . "\n";
echo "- Products without images: " . $products->filter(function($p) { return $p->images->count() == 0; })->count() . "\n";
