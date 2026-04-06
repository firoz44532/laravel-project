<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Debugging Product Images:\n";
echo "========================\n";

// Check a few new products
$newProducts = App\Models\Product::with('primaryImage')
    ->whereIn('slug', ['oneplus-12-pro', 'google-pixel-8-pro', 'xiaomi-14-ultra'])
    ->get();

foreach ($newProducts as $product) {
    echo "\nProduct: {$product->name}\n";
    echo "Slug: {$product->slug}\n";
    
    if ($product->primaryImage) {
        echo "Has primary image: YES\n";
        echo "Image path: {$product->primaryImage->image_path}\n";
        echo "Image URL: {$product->primaryImage->image_url}\n";
        echo "Is URL valid: " . (filter_var($product->primaryImage->image_url, FILTER_VALIDATE_URL) ? 'YES' : 'NO') . "\n";
    } else {
        echo "Has primary image: NO\n";
    }
}

echo "\nTesting placeholder URL directly:\n";
$placeholderUrl = 'https://via.placeholder.com/400x400';
echo "URL: {$placeholderUrl}\n";
echo "Is valid: " . (filter_var($placeholderUrl, FILTER_VALIDATE_URL) ? 'YES' : 'NO') . "\n";

// Test if we can access the placeholder
$context = stream_context_create([
    'http' => [
        'timeout' => 5,
        'method' => 'HEAD'
    ]
]);

$headers = @get_headers($placeholderUrl, 1, $context);
if ($headers) {
    echo "HTTP Status: " . $headers[0] . "\n";
} else {
    echo "Cannot access placeholder URL\n";
}
