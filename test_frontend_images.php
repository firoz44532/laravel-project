<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Frontend Image URLs:\n";
echo "===========================\n";

// Get featured products that should appear on homepage
$featuredProducts = App\Models\Product::with('primaryImage')
    ->where('is_featured', true)
    ->take(5)
    ->get();

echo "Featured Products:\n";
foreach ($featuredProducts as $product) {
    echo "\nProduct: {$product->name}\n";
    echo "Slug: {$product->slug}\n";
    echo "Is Featured: " . ($product->is_featured ? 'YES' : 'NO') . "\n";
    
    if ($product->primaryImage) {
        echo "Image path: {$product->primaryImage->image_path}\n";
        echo "Image URL: {$product->primaryImage->image_url}\n";
        
        // Test if the URL is accessible
        $url = $product->primaryImage->image_url;
        $context = stream_context_create([
            'http' => [
                'timeout' => 3,
                'method' => 'HEAD'
            ]
        ]);
        
        $headers = @get_headers($url, 1, $context);
        if ($headers && strpos($headers[0], '200') !== false) {
            echo "URL Status: ACCESSIBLE ✅\n";
        } else {
            echo "URL Status: NOT ACCESSIBLE ❌\n";
            if ($headers) {
                echo "HTTP Response: " . $headers[0] . "\n";
            } else {
                echo "HTTP Response: No response\n";
            }
        }
    } else {
        echo "Image Status: NO PRIMARY IMAGE ❌\n";
    }
}

// Check if the placeholder image is accessible via web
echo "\n\nTesting placeholder image directly:\n";
$placeholderUrl = asset('images/placeholder-product.jpg');
echo "URL: {$placeholderUrl}\n";

$headers = @get_headers($placeholderUrl, 1);
if ($headers && strpos($headers[0], '200') !== false) {
    echo "Status: ACCESSIBLE ✅\n";
} else {
    echo "Status: NOT ACCESSIBLE ❌\n";
    if ($headers) {
        echo "HTTP Response: " . $headers[0] . "\n";
    }
}

// Check web server status
echo "\n\nWeb Server Check:\n";
$webUrl = 'http://localhost/';
$headers = @get_headers($webUrl, 1);
if ($headers && strpos($headers[0], '200') !== false) {
    echo "Web Server: RUNNING ✅\n";
} else {
    echo "Web Server: NOT RUNNING ❌\n";
}
