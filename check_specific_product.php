<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking specific product: instant-pot-duo-7-in-1-electric-pressure-cooker\n\n";

$product = \App\Models\Product::where('slug', 'instant-pot-duo-7-in-1-electric-pressure-cooker')->first();

if ($product) {
    echo "Product found:\n";
    echo "- ID: {$product->id}\n";
    echo "- Name: {$product->name}\n";
    echo "- Slug: {$product->slug}\n";
    echo "- Active: " . ($product->is_active ? 'Yes' : 'No') . "\n";
    echo "- Featured: " . ($product->is_featured ? 'Yes' : 'No') . "\n";
    echo "- Sort Order: {$product->sort_order}\n";
    echo "- Category ID: {$product->category_id}\n";
    echo "- Brand ID: {$product->brand_id}\n";
    echo "- Price: {$product->price}\n";
    echo "- Stock: {$product->stock_quantity}\n";
    echo "- Created: {$product->created_at}\n";
    echo "- Updated: {$product->updated_at}\n";
    
    // Check if it appears in featured products query
    echo "\n--- Featured Query Test ---\n";
    
    $featuredProducts = \App\Models\Product::with(['primaryImage', 'category'])
        ->where('is_active', true)
        ->where('is_featured', true)
        ->orderBy('sort_order')
        ->take(8)
        ->get();
    
    echo "Featured products count: " . $featuredProducts->count() . "\n";
    
    $isInFeatured = $featuredProducts->contains('id', $product->id);
    echo "Is this product in featured results: " . ($isInFeatured ? 'Yes' : 'No') . "\n";
    
    if ($isInFeatured) {
        echo "Position in featured list: " . ($featuredProducts->search(function($item) use ($product) {
            return $item->id === $product->id;
        }) + 1) . "\n";
    }
    
    // Check all featured products for comparison
    echo "\n--- All Featured Products ---\n";
    $allFeatured = \App\Models\Product::where('is_featured', true)
        ->where('is_active', true)
        ->orderBy('sort_order', 'asc')
        ->orderBy('id', 'asc')
        ->get();
    
    foreach ($allFeatured as $index => $featured) {
        $marker = ($featured->id == $product->id) ? "<<< THIS PRODUCT" : "";
        echo ($index + 1) . ". ID:{$featured->id} - {$featured->name} (Sort:{$featured->sort_order}) {$marker}\n";
    }
    
} else {
    echo "Product not found with slug: instant-pot-duo-7-in-1-electric-pressure-cooker\n";
    
    // Try to find similar products
    echo "\nSearching for products with 'instant' or 'pot' in name:\n";
    $similar = \App\Models\Product::where('name', 'like', '%instant%')
        ->orWhere('name', 'like', '%pot%')
        ->get();
    
    if ($similar->count() > 0) {
        foreach ($similar as $p) {
            echo "- ID:{$p->id} - {$p->name} (Slug: {$p->slug})\n";
        }
    } else {
        echo "No similar products found.\n";
    }
}
