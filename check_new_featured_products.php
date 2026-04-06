<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking new featured products issue\n\n";

// Get all featured products ordered by creation date
echo "--- All Featured Products (ordered by creation date) ---\n";
$allFeatured = \App\Models\Product::where('is_featured', true)
    ->where('is_active', true)
    ->orderBy('created_at', 'desc')
    ->get();

foreach ($allFeatured as $index => $product) {
    echo ($index + 1) . ". ID:{$product->id} - Created:{$product->created_at} - Sort:{$product->sort_order} - {$product->name}\n";
}

echo "\n--- HomeController Query Results (first 8 by sort_order) ---\n";
$featuredProducts = \App\Models\Product::with(['primaryImage', 'category'])
    ->where('is_active', true)
    ->where('is_featured', true)
    ->orderBy('sort_order')
    ->take(8)
    ->get();

foreach ($featuredProducts as $index => $product) {
    echo ($index + 1) . ". ID:{$product->id} - Sort:{$product->sort_order} - Created:{$product->created_at} - {$product->name}\n";
}

echo "\n--- Analysis ---\n";
$totalFeatured = $allFeatured->count();
$inHomeController = $featuredProducts->count();
echo "Total featured products: {$totalFeatured}\n";
echo "Shown in HomeController: {$inHomeController}\n";
echo "Not shown: " . ($totalFeatured - $inHomeController) . "\n";

if ($totalFeatured > $inHomeController) {
    echo "\n--- Products NOT shown in homepage ---\n";
    $shownIds = $featuredProducts->pluck('id')->toArray();
    $notShown = $allFeatured->whereNotIn('id', $shownIds);
    
    foreach ($notShown as $product) {
        echo "❌ ID:{$product->id} - {$product->name} (Created: {$product->created_at}, Sort: {$product->sort_order})\n";
    }
}

echo "\n--- Recent Products (last 24 hours) ---\n";
$recent = \App\Models\Product::where('created_at', '>=', now()->subDay())
    ->where('is_featured', true)
    ->where('is_active', true)
    ->get();

if ($recent->count() > 0) {
    foreach ($recent as $product) {
        echo "🆕 ID:{$product->id} - {$product->name} (Featured: " . ($product->is_featured ? 'Yes' : 'No') . ", Sort: {$product->sort_order})\n";
    }
} else {
    echo "No featured products created in the last 24 hours.\n";
}

echo "\n--- Sort Order Analysis ---\n";
$sortGroups = $allFeatured->groupBy('sort_order');
foreach ($sortGroups as $sortOrder => $products) {
    echo "Sort Order {$sortOrder}: " . $products->count() . " products\n";
    foreach ($products as $product) {
        echo "  - ID:{$product->id} - {$product->name}\n";
    }
}
