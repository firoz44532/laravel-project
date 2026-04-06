<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Fixing Instant Pot featured display issue\n\n";

// Get the Instant Pot product
$instantPot = \App\Models\Product::find(20);

if ($instantPot) {
    echo "Found Instant Pot:\n";
    echo "- Name: {$instantPot->name}\n";
    echo "- Current sort_order: {$instantPot->sort_order}\n";
    
    // Set sort_order to -1 to make it appear first
    $instantPot->sort_order = -1;
    $instantPot->save();
    
    echo "- Updated sort_order to: {$instantPot->sort_order}\n\n";
    
    // Test the featured products query again
    $featuredProducts = \App\Models\Product::with(['primaryImage', 'category'])
        ->where('is_active', true)
        ->where('is_featured', true)
        ->orderBy('sort_order')
        ->take(8)
        ->get();
    
    echo "Featured products after fix:\n";
    foreach ($featuredProducts as $index => $product) {
        $marker = ($product->id == 20) ? "<<< INSTANT POT NOW VISIBLE!" : "";
        echo ($index + 1) . ". ID:{$product->id} - Sort:{$product->sort_order} - {$product->name} {$marker}\n";
    }
    
    echo "\n✅ Instant Pot should now appear in the featured products section on homepage!\n";
    echo "🔄 Please refresh your browser to see the changes.\n";
    
} else {
    echo "Instant Pot product not found!\n";
}
