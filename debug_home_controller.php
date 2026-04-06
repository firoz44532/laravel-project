<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing HomeController featured products query...\n";

// Test the exact query from HomeController
$featuredProducts = \App\Models\Product::with(['primaryImage', 'category'])
    ->where('is_active', true)
    ->where('is_featured', true)
    ->orderBy('sort_order')
    ->take(8)
    ->get();

echo "Featured products found: " . $featuredProducts->count() . "\n\n";

if ($featuredProducts->count() > 0) {
    echo "Featured products details:\n";
    foreach ($featuredProducts as $product) {
        echo "- ID: {$product->id}\n";
        echo "  Name: {$product->name}\n";
        echo "  Active: " . ($product->is_active ? 'Yes' : 'No') . "\n";
        echo "  Featured: " . ($product->is_featured ? 'Yes' : 'No') . "\n";
        echo "  Sort Order: {$product->sort_order}\n";
        echo "  Primary Image: " . ($product->primaryImage ? 'Yes' : 'No') . "\n";
        echo "  Category: " . ($product->category ? $product->category->name : 'None') . "\n";
        echo "  ---\n";
    }
} else {
    echo "No featured products found with the HomeController query.\n";
}

// Test without relationships
$featuredSimple = \App\Models\Product::where('is_active', true)
    ->where('is_featured', true)
    ->get();

echo "\nSimple featured products query (without relationships): " . $featuredSimple->count() . "\n";

// Test individual conditions
$activeProducts = \App\Models\Product::where('is_active', true)->count();
$featuredProductsOnly = \App\Models\Product::where('is_featured', true)->count();

echo "\nActive products: {$activeProducts}\n";
echo "Products with featured flag: {$featuredProductsOnly}\n";
