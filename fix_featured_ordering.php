<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Fixing featured products ordering issue\n\n";

echo "Current problem:\n";
echo "- 12 featured products exist\n";
echo "- HomeController only shows first 8\n";
echo "- All have sort_order = 0, so ordering is by ID (oldest first)\n";
echo "- New products appear last and don't make the cut\n\n";

echo "Solution: Update HomeController to show newest featured products first\n\n";

// Show the current HomeController logic
echo "--- Current HomeController Query ---\n";
echo "Product::with(['primaryImage', 'category'])\n";
echo "    ->where('is_active', true)\n";
echo "    ->where('is_featured', true)\n";
echo "    ->orderBy('sort_order')\n";
echo "    ->take(8)\n";
echo "    ->get();\n\n";

echo "--- Proposed Solution 1: Order by creation date (newest first) ---\n";
$featuredByDate = \App\Models\Product::with(['primaryImage', 'category'])
    ->where('is_active', true)
    ->where('is_featured', true)
    ->orderBy('created_at', 'desc')
    ->take(8)
    ->get();

echo "Results with newest first:\n";
foreach ($featuredByDate as $index => $product) {
    echo ($index + 1) . ". ID:{$product->id} - {$product->name} (Created: {$product->created_at})\n";
}

echo "\n--- Proposed Solution 2: Increase limit to 12 ---\n";
$featuredAll = \App\Models\Product::with(['primaryImage', 'category'])
    ->where('is_active', true)
    ->where('is_featured', true)
    ->orderBy('sort_order')
    ->take(12)
    ->get();

echo "Results with increased limit:\n";
foreach ($featuredAll as $index => $product) {
    echo ($index + 1) . ". ID:{$product->id} - {$product->name}\n";
}

echo "\n--- Recommended Fix ---\n";
echo "Update HomeController to order by created_at DESC so new featured products appear first\n";
echo "This ensures newly marked featured products are immediately visible\n\n";

// Update the HomeController file
$homeControllerPath = app_path('Http/Controllers/Frontend/HomeController.php');
$homeControllerContent = file_get_contents($homeControllerPath);

// Replace the ordering logic
$newContent = str_replace(
    "->orderBy('sort_order')",
    "->orderBy('created_at', 'desc')",
    $homeControllerContent
);

if ($newContent !== $homeControllerContent) {
    file_put_contents($homeControllerPath, $newContent);
    echo "✅ HomeController updated successfully!\n";
    echo "🔄 New featured products will now appear first on homepage\n";
} else {
    echo "❌ Failed to update HomeController\n";
}

echo "\n--- Testing the fix ---\n";
$testResults = \App\Models\Product::with(['primaryImage', 'category'])
    ->where('is_active', true)
    ->where('is_featured', true)
    ->orderBy('created_at', 'desc')
    ->take(8)
    ->get();

echo "New featured products order:\n";
foreach ($testResults as $index => $product) {
    $marker = ($product->id >= 20) ? "<<< NEW PRODUCT!" : "";
    echo ($index + 1) . ". ID:{$product->id} - {$product->name} {$marker}\n";
}
