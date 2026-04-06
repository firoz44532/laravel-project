<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Debugging featured products ordering issue\n\n";

// The exact query from HomeController
$featuredProducts = \App\Models\Product::with(['primaryImage', 'category'])
    ->where('is_active', true)
    ->where('is_featured', true)
    ->orderBy('sort_order')
    ->take(8)
    ->get();

echo "HomeController query results (first 8):\n";
foreach ($featuredProducts as $index => $product) {
    $marker = ($product->id == 20) ? "<<< INSTANT POT" : "";
    echo ($index + 1) . ". ID:{$product->id} - {$product->name} (Sort:{$product->sort_order}) {$marker}\n";
}

echo "\n--- All featured products with sort_order analysis ---\n";
$allFeatured = \App\Models\Product::where('is_featured', true)
    ->where('is_active', true)
    ->orderBy('sort_order', 'asc')
    ->orderBy('id', 'asc')
    ->get();

foreach ($allFeatured as $index => $product) {
    $marker = ($product->id == 20) ? "<<< INSTANT POT" : "";
    echo ($index + 1) . ". ID:{$product->id} - Sort:{$product->sort_order} - Created:{$product->created_at} - {$product->name} {$marker}\n";
}

echo "\n--- The problem ---\n";
echo "The Instant Pot (ID:20) is the 11th featured product, but HomeController only takes first 8.\n";
echo "It's marked as featured correctly, but it's not in the top 8 due to ordering.\n";

echo "\n--- Solution options ---\n";
echo "1. Set sort_order for Instant Pot to a lower number (0-7)\n";
echo "2. Increase the take(8) limit in HomeController\n";
echo "3. Use different ordering logic (newest first, random, etc.)\n";

// Test if we can update the sort_order
echo "\n--- Updating Instant Pot sort_order to 1 ---\n";
$instantPot = \App\Models\Product::find(20);
if ($instantPot) {
    $instantPot->sort_order = 1;
    $instantPot->save();
    echo "Updated Instant Pot sort_order to 1\n";
    
    // Test the query again
    $featuredProductsAfter = \App\Models\Product::with(['primaryImage', 'category'])
        ->where('is_active', true)
        ->where('is_featured', true)
        ->orderBy('sort_order')
        ->take(8)
        ->get();
    
    echo "\nAfter update - First 8 featured products:\n";
    foreach ($featuredProductsAfter as $index => $product) {
        $marker = ($product->id == 20) ? "<<< NOW INCLUDED!" : "";
        echo ($index + 1) . ". ID:{$product->id} - {$product->name} (Sort:{$product->sort_order}) {$marker}\n";
    }
}
