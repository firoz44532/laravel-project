<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Product Image Links:\n";
echo "===================\n";

// Get all products with their image URLs
$products = App\Models\Product::with('primaryImage')->get();

foreach ($products as $product) {
    echo "\n{$product->name}\n";
    if ($product->primaryImage) {
        $imageUrl = str_replace('http://localhost/', 'http://localhost:8000/', $product->primaryImage->image_url);
        echo "{$imageUrl}\n";
    } else {
        echo "No image\n";
    }
}

echo "\nTotal: " . $products->count() . " products\n";
