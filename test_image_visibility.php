<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Image Visibility:\n";
echo "========================\n";

// Test the asset() function
echo "Testing asset() function:\n";
echo "asset('images/placeholder-product.jpg') = " . asset('images/placeholder-product.jpg') . "\n";

// Test a few products
$products = App\Models\Product::with('primaryImage')->take(3)->get();

foreach ($products as $product) {
    echo "\nProduct: {$product->name}\n";
    if ($product->primaryImage) {
        echo "Image path: {$product->primaryImage->image_path}\n";
        echo "Image URL: {$product->primaryImage->image_url}\n";
        
        // Check if the file exists
        $fullPath = public_path($product->primaryImage->image_path);
        echo "File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
        echo "Full path: {$fullPath}\n";
    }
}

// Check if storage link exists
echo "\nStorage link check:\n";
$storageLink = public_path('storage');
echo "Public/storage exists: " . (is_link($storageLink) ? 'YES (symlink)' : (is_dir($storageLink) ? 'YES (directory)' : 'NO')) . "\n";

if (is_link($storageLink)) {
    echo "Storage link target: " . readlink($storageLink) . "\n";
}
