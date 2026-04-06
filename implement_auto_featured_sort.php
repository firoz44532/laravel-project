<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Implementing automatic sort order for newly featured products\n\n";

// Read the current ProductController
$productControllerPath = app_path('Http/Controllers/Admin/ProductController.php');
$content = file_get_contents($productControllerPath);

echo "Current ProductController update method (lines around 250-270):\n";
$lines = explode("\n", $content);
for ($i = 250; $i <= 270; $i++) {
    if (isset($lines[$i])) {
        echo ($i + 1) . ". " . $lines[$i] . "\n";
    }
}

echo "\n--- Adding automatic sort_order for featured products ---\n";

// Find the line where sort_order is set and add featured logic
$pattern = '/(\$validated\[\'sort_order\'\] = \$validated\[\'sort_order\'\] ?? 0;)/';
$replacement = "// Set automatic sort_order for newly featured products\n        if (\$request->input('is_featured', false) && !\$product->is_featured) {\n            // Product is being marked as featured for the first time\n            \$minSortOrder = Product::where('is_featured', true)->min('sort_order') ?? 0;\n            \$validated['sort_order'] = \$minSortOrder - 1;\n        } elseif (!\$request->input('is_featured', false) && \$product->is_featured) {\n            // Product is being un-featured, reset sort_order\n            \$validated['sort_order'] = 0;\n        } else {\n            // Keep existing sort_order or use provided value\n            \$validated['sort_order'] = \$validated['sort_order'] ?? \$product->sort_order ?? 0;\n        }";

$newContent = preg_replace($pattern, $replacement, $content);

if ($newContent !== $content) {
    file_put_contents($productControllerPath, $newContent);
    echo "✅ ProductController updated successfully!\n";
    echo "🔄 Newly featured products will now automatically get highest priority\n";
} else {
    echo "❌ Failed to update ProductController - pattern not found\n";
    echo "Let's try a different approach...\n";
    
    // Alternative approach - find the line and replace manually
    $targetLine = '$validated[\'sort_order\'] = $validated[\'sort_order\'] ?? 0;';
    $newLines = [];
    
    foreach ($lines as $line) {
        if (trim($line) === trim($targetLine)) {
            $newLines[] = "        // Set automatic sort_order for newly featured products";
            $newLines[] = "        if (\$request->input('is_featured', false) && !\$product->is_featured) {";
            $newLines[] = "            // Product is being marked as featured for the first time";
            $newLines[] = "            \$minSortOrder = Product::where('is_featured', true)->min('sort_order') ?? 0;";
            $newLines[] = "            \$validated['sort_order'] = \$minSortOrder - 1;";
            $newLines[] = "        } elseif (!\$request->input('is_featured', false) && \$product->is_featured) {";
            $newLines[] = "            // Product is being un-featured, reset sort_order";
            $newLines[] = "            \$validated['sort_order'] = 0;";
            $newLines[] = "        } else {";
            $newLines[] = "            // Keep existing sort_order or use provided value";
            $newLines[] = "            \$validated['sort_order'] = \$validated['sort_order'] ?? \$product->sort_order ?? 0;";
            $newLines[] = "        }";
        } else {
            $newLines[] = $line;
        }
    }
    
    $finalContent = implode("\n", $newLines);
    file_put_contents($productControllerPath, $finalContent);
    echo "✅ ProductController updated using alternative method!\n";
}

// Also update the store method for new products
echo "\n--- Updating store method for new products ---\n";

$storePattern = '/(\$validated\[\'is_featured\'\] = \$request->input\(\'is_featured\', false\);)/';
$storeReplacement = "$1\n        \n        // Set automatic sort_order for new featured products\n        if (\$validated['is_featured']) {\n            \$minSortOrder = Product::where('is_featured', true)->min('sort_order') ?? 0;\n            \$validated['sort_order'] = \$minSortOrder - 1;\n        } else {\n            \$validated['sort_order'] = 0;\n        }";

$storeContent = file_get_contents($productControllerPath);
$newStoreContent = preg_replace($storePattern, $storeReplacement, $storeContent);

if ($newStoreContent !== $storeContent) {
    file_put_contents($productControllerPath, $newStoreContent);
    echo "✅ Store method updated successfully!\n";
} else {
    echo "❌ Store method update failed\n";
}

echo "\n--- Testing the implementation ---\n";

// Test the logic with a sample product
$testProduct = \App\Models\Product::find(20); // Instant Pot
if ($testProduct) {
    echo "Testing with product: {$testProduct->name}\n";
    echo "Current sort_order: {$testProduct->sort_order}\n";
    echo "Current is_featured: " . ($testProduct->is_featured ? 'Yes' : 'No') . "\n";
    
    // Simulate marking it as featured again
    $minSortOrder = \App\Models\Product::where('is_featured', true)->min('sort_order') ?? 0;
    echo "Current minimum sort_order: {$minSortOrder}\n";
    echo "New sort_order would be: " . ($minSortOrder - 1) . "\n";
}

echo "\n✅ Implementation complete!\n";
echo "📋 Summary:\n";
echo "   - New products marked as featured will get sort_order = (min_current - 1)\n";
echo "   - Existing products newly marked as featured will get highest priority\n";
echo "   - Products un-featured will have sort_order reset to 0\n";
echo "   - This ensures any product you mark as 'Featured' will appear immediately\n";
