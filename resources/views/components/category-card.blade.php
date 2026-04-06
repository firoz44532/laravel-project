@props(['category', 'variant' => 'default'])
@php
    $categoryImage = null;
    if(!empty($category->image)) {
        $categoryImage = asset('storage/' . $category->image);
    } else {
        $product = \App\Models\Product::with('primaryImage')
            ->where('category_id', $category->id)
            ->where('is_active', true)
            ->whereHas('primaryImage')
            ->first();
        if($product && $product->primaryImage) {
            $categoryImage = asset($product->primaryImage->image_path ?? $product->primaryImage->image_url ?? '');
        }
    }
    if(!$categoryImage) {
        $slugImages = [
            'electronics' => 'images/oneplus-12-pro.jpg',
            'fashion' => 'images/adidas-ultraboost-22.jpg',
            'home-living' => 'images/ikea-poang-armchair.jpg',
            'beauty-personal-care' => 'images/foreo-luna-3-plus.jpg',
            'groceries' => 'images/nespresso-vertuo-next.jpg',
            'sports-outdoors' => 'images/yoga-mat-premium-6mm.jpg',
            'toys-games' => 'images/board-game-collection.jpg',
            'computer' => 'images/4k-monitor-27.jpg',
        ];
        $categoryImage = asset($slugImages[$category->slug] ?? 'images/4k-monitor-27.jpg');
    }
@endphp
    <style>
        .cat-img-wrapper{position:relative;padding-top:85%;overflow:hidden}
        @media (min-width:768px){ .cat-img-wrapper{padding-top:100%} }
        .cat-img-wrapper img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
    </style>

    <a href="{{ route('products.category', $category->slug) }}" class="group block" aria-label="View {{ $category->name }} category">
    <div class="bg-white rounded-lg overflow-hidden shadow hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
            <div class="cat-img-wrapper">
                <img src="{{ $categoryImage }}" alt="{{ $category->name }}" loading="lazy" class="group-hover:scale-110 transition-transform duration-300">
            </div>
        <div class="p-4 text-center">
            <h3 class="font-semibold text-gray-800 group-hover:text-primary transition">{{ $category->name }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $category->active_products_count ?? 0 }} Products</p>
            @if(!empty($category->is_featured))
                <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full mt-2 w-full text-center">
                    <i class="fas fa-star mr-1"></i>Featured
                </span>
            @endif
        </div>
    </div>
</a>
