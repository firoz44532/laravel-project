<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Current Featured Products (as loaded by HomeController):\n";
echo "====================================================\n";

// Simulate HomeController query
$featuredProducts = App\Models\Product::with(['primaryImage', 'category'])
    ->where('is_active', true)
    ->where('is_featured', true)
    ->orderBy('created_at', 'desc')
    ->take(20)
    ->get();

echo "Featured Products Count: " . $featuredProducts->count() . "\n\n";

foreach ($featuredProducts as $index => $product) {
    echo ($index + 1) . ". {$product->name}\n";
    echo "   Created: {$product->created_at}\n";
    echo "   Is Featured: " . ($product->is_featured ? 'YES' : 'NO') . "\n";
    
    if ($product->primaryImage) {
        echo "   Image Path: {$product->primaryImage->image_path}\n";
        if ($product->primaryImage->image_path === 'images/placeholder-product.jpg') {
            echo "   Image Status: ✅ Should be visible (placeholder)\n";
        } else {
            echo "   Image Status: ❌ Missing custom image\n";
        }
    } else {
        echo "   Image Status: ❌ No image\n";
    }
    echo "\n";
}

echo "First 5 products should appear on homepage.\n";
echo "Visit: http://localhost:8000/\n";
