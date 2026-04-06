<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Fixing Featured Products Images:\n";
echo "===============================\n";

// Get all new products I added (the ones with placeholder images)
$newProductSlugs = [
    'oneplus-12-pro', 'google-pixel-8-pro', 'xiaomi-14-ultra',
    'dell-xps-15', 'hp-spectre-x360', 'asus-rog-zephyrus',
    'samsung-galaxy-tab-s9-ultra', 'microsoft-surface-pro-9',
    'jbl-tune-760nc', 'anker-powercore-20000', 'logitech-mx-master-3s',
    'adidas-ultraboost-22', 'levis-501-original-jeans', 'ray-ban-aviator-classic',
    'ikea-poang-armchair', 'philips-hue-starter-kit', 'dyson-v15-detect'
];

echo "Updating new products to be featured...\n";

foreach ($newProductSlugs as $slug) {
    $product = App\Models\Product::where('slug', $slug)->first();
    if ($product) {
        $product->update(['is_featured' => true]);
        echo "✓ Marked as featured: {$product->name}\n";
    } else {
        echo "✗ Product not found: {$slug}\n";
    }
}

// Now check featured products
echo "\nUpdated Featured Products:\n";
$featuredProducts = App\Models\Product::with('primaryImage')
    ->where('is_featured', true)
    ->take(10)
    ->get();

foreach ($featuredProducts as $product) {
    echo "\n{$product->name}\n";
    if ($product->primaryImage) {
        echo "Image Path: {$product->primaryImage->image_path}\n";
        if ($product->primaryImage->image_path === 'images/placeholder-product.jpg') {
            echo "Status: ✅ Using placeholder (should be visible)\n";
        } else {
            echo "Status: ❌ Using missing custom image\n";
        }
    } else {
        echo "Status: ❌ No image\n";
    }
}

echo "\nTotal Featured Products: " . $featuredProducts->count() . "\n";
echo "Visit: http://localhost:8000/ to see the changes\n";
