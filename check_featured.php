<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking featured products...\n";

$featuredProducts = \App\Models\Product::where('is_featured', true)
    ->where('is_active', true)
    ->get();

echo "Total featured products: " . $featuredProducts->count() . "\n\n";

if ($featuredProducts->count() > 0) {
    echo "Featured products:\n";
    foreach ($featuredProducts->take(5) as $product) {
        echo "- ID: {$product->id}, Name: {$product->name}, Featured: " . ($product->is_featured ? 'Yes' : 'No') . ", Active: " . ($product->is_active ? 'Yes' : 'No') . "\n";
    }
} else {
    echo "No featured products found.\n";
}

// Check if any products exist at all
$totalProducts = \App\Models\Product::count();
echo "\nTotal products in database: {$totalProducts}\n";

// Check products with is_featured = 1
$featuredOnly = \App\Models\Product::where('is_featured', true)->count();
echo "Products with is_featured = 1: {$featuredOnly}\n";
