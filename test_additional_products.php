<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing AdditionalProductSeeder...\n";
echo "==================================\n";

// Check current state before running seeder
echo "Current database state:\n";
echo "- Categories: " . App\Models\Category::count() . "\n";
echo "- Products: " . App\Models\Product::count() . "\n";
echo "- Product Images: " . App\Models\ProductImage::count() . "\n\n";

// Test category mapping
echo "Testing category mapping:\n";
$testSlugs = ['mobile-phones', 'laptops', 'tablets', 'electronics-accessories', 'fashion', 'home-living', 'beauty-personal-care', 'groceries', 'sports-outdoors', 'books-media', 'toys-games', 'computer'];

foreach ($testSlugs as $slug) {
    $category = App\Models\Category::where('slug', $slug)->first();
    if ($category) {
        echo "✓ Found: {$category->name} ({$slug})\n";
    } else {
        echo "✗ Missing: {$slug}\n";
    }
}

echo "\nTesting for duplicate SKUs:\n";
$testSKUs = ['ONEPLUS-12P-256', 'GOOGLE-PIX8P-256', 'XIAOMI-14U-512'];
foreach ($testSKUs as $sku) {
    $existing = App\Models\Product::where('sku', $sku)->first();
    if ($existing) {
        echo "✗ Duplicate SKU found: {$sku}\n";
    } else {
        echo "✓ SKU available: {$sku}\n";
    }
}

echo "\nReady to run AdditionalProductSeeder!\n";
