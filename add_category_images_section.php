<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "ক্যাটাগরি অনুযায়ী ছবি সেকশন যোগ করা হচ্ছে...\n";
echo "=====================================\n";

// ক্যাটাগরি গুলো নিন
$categories = App\Models\Category::where('is_active', true)
    ->whereNull('parent_id')
    ->with(['children' => function($query) {
        $query->where('is_active', true);
    }])
    ->take(8)
    ->get();

echo "পাওয়া গেছে " . $categories->count() . " টি মূল ক্যাটাগরি:\n\n";

$categoryData = [];
foreach ($categories as $category) {
    echo ($categoryData->count() + 1) . ". {$category->name}\n";
    echo "   Slug: {$category->slug}\n";
    
    // প্রতিটি ক্যাটাগরির জন্য একটি প্রোডাক্ট খুঁজুন
    $product = App\Models\Product::with('primaryImage')
        ->where('category_id', $category->id)
        ->where('is_active', true)
        ->whereHas('primaryImage', function($query) {
            $query->where('image_path', 'like', '%.jpg');
        })
        ->first();
    
    if ($product && $product->primaryImage) {
        echo "   প্রোডাক্ট: {$product->name}\n";
        echo "   ছবি: {$product->primaryImage->image_path}\n";
        
        $categoryData[] = [
            'category' => $category,
            'product' => $product,
            'image' => $product->primaryImage->image_path
        ];
    } else {
        echo "   প্রোডাক্ট: পাওয়া যায়নি\n";
        
        // কোনো প্রোডাক্ট না পেলে placeholder ছবি দিন
        $categoryData[] = [
            'category' => $category,
            'product' => null,
            'image' => 'images/4k-monitor-27.jpg'
        ];
    }
    echo "\n";
}

echo "মোট ক্যাটাগরি ডাটা: " . count($categoryData) . " টি\n";

// এখন view ফাইলে ক্যাটাগরি সেকশন যোগ করুন
$viewPath = resource_path('views/welcome.blade.php');
$currentContent = file_get_contents($viewPath);

// Best Sellers Section এর পরে ক্যাটাগরি সেকশন যোগ করুন
$categorySection = '
<!-- Shop by Category Section -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold">Shop by Category</h2>
            <a href="{{ route(\'categories.index\') }}" class="text-primary hover:underline">View All →</a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-6">
            @php
                $categoryImages = [
                    (object)[\'category\' => App\Models\Category::where(\'slug\', \'electronics\')->first(), \'image\' => \'images/oneplus-12-pro.jpg\'],
                    (object)[\'category\' => App\Models\Category::where(\'slug\', \'fashion\')->first(), \'image\' => \'images/adidas-ultraboost-22.jpg\'],
                    (object)[\'category\' => App\Models\Category::where(\'slug\', \'home-living\')->first(), \'image\' => \'images/ikea-poang-armchair.jpg\'],
                    (object)[\'category\' => App\Models\Category::where(\'slug\', \'beauty-personal-care\')->first(), \'image\' => \'images/foreo-luna-3-plus.jpg\'],
                    (object)[\'category\' => App\Models\Category::where(\'slug\', \'groceries\')->first(), \'image\' => \'images/nespresso-vertuo-next.jpg\'],
                    (object)[\'category\' => App\Models\Category::where(\'slug\', \'sports-outdoors\')->first(), \'image\' => \'images/yoga-mat-premium-6mm.jpg\'],
                    (object)[\'category\' => App\Models\Category::where(\'slug\', \'toys-games\')->first(), \'image\' => \'images/board-game-collection.jpg\'],
                    (object)[\'category\' => App\Models\Category::where(\'slug\', \'computer\')->first(), \'image\' => \'images/4k-monitor-27.jpg\']
                ];
            @endphp
            @foreach($categoryImages as $catData)
                @if($catData->category)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition cursor-pointer group">
                        <a href="{{ route(\'products.index\', [\'category\' => $catData->category->slug]) }}" class="block">
                            <div class="relative overflow-hidden rounded-t-lg">
                                <img src="{{ asset($catData->image) }}" 
                                     alt="{{ $catData->category->name }}" 
                                     class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                <div class="absolute bottom-4 left-4 text-white">
                                    <h3 class="font-bold text-lg">{{ $catData->category->name }}</h3>
                                    <p class="text-sm opacity-90">Shop Now</p>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>
';

// Best Sellers Section এর পরে যোগ করুন
$insertPosition = strpos($currentContent, '</section>', strpos($currentContent, '<!-- Best Sellers Section -->')) + strlen('</section>') + 2;

if ($insertPosition > 0) {
    $newContent = substr_replace($currentContent, $categorySection, $insertPosition, 0);
    file_put_contents($viewPath, $newContent);
    echo "✅ ক্যাটাগরি সেকশন যোগ হয়েছে\n";
} else {
    echo "❌ ক্যাটাগরি সেকশন যোগ করতে ব্যর্থ\n";
}

echo "\nওয়েবসাইট: http://localhost:8000/\n";
echo "প্রতিটি ক্যাটাগরি ছবি সহ দেখানো হবে!\n";
