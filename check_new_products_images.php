<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking New Products Images:\n";
echo "============================\n";

// Check the specific new products I added
$newProductSlugs = [
    'oneplus-12-pro', 'google-pixel-8-pro', 'xiaomi-14-ultra',
    'dell-xps-15', 'hp-spectre-x360', 'asus-rog-zephyrus',
    'samsung-galaxy-tab-s9-ultra', 'microsoft-surface-pro-9'
];

foreach ($newProductSlugs as $slug) {
    $product = App\Models\Product::with('primaryImage')->where('slug', $slug)->first();
    if ($product) {
        echo "\nProduct: {$product->name}\n";
        if ($product->primaryImage) {
            echo "Image path: {$product->primaryImage->image_path}\n";
            echo "Image URL: {$product->primaryImage->image_url}\n";
            
            // Check if it's still the placeholder URL
            if ($product->primaryImage->image_path === 'https://via.placeholder.com/400x400') {
                echo "Status: Still using placeholder URL - NEEDS FIX\n";
            } else {
                echo "Status: Fixed\n";
            }
        } else {
            echo "Status: No primary image\n";
        }
    } else {
        echo "Product not found: {$slug}\n";
    }
}

echo "\n\nChecking all products with placeholder URLs:\n";
$placeholderCount = App\Models\ProductImage::where('image_path', 'https://via.placeholder.com/400x400')->count();
echo "Products still using placeholder URL: {$placeholderCount}\n";

if ($placeholderCount > 0) {
    echo "\nFixing remaining placeholder images...\n";
    
    App\Models\ProductImage::where('image_path', 'https://via.placeholder.com/400x400')
        ->update(['image_path' => 'images/placeholder-product.jpg']);
    
    echo "Fixed {$placeholderCount} images\n";
    
    // Verify fix
    $remainingCount = App\Models\ProductImage::where('image_path', 'https://via.placeholder.com/400x400')->count();
    echo "Remaining placeholder URLs: {$remainingCount}\n";
}
