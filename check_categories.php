<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Categories and their product counts:\n";
echo "=====================================\n";

$categories = App\Models\Category::with('products')->get();

foreach ($categories as $category) {
    echo $category->name . ': ' . $category->products->count() . ' products' . PHP_EOL;
}

echo "\nTotal Categories: " . $categories->count() . PHP_EOL;
echo "Total Products: " . App\Models\Product::count() . PHP_EOL;
