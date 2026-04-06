<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" 
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    
    <!-- Homepage -->
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    
    <!-- Static Pages -->
    <url>
        <loc>{{ url('/about') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <url>
        <loc>{{ url('/contact') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <url>
        <loc>{{ url('/faq') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    
    <url>
        <loc>{{ url('/shipping') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    
    <url>
        <loc>{{ url('/returns') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
    </url>
    
    <url>
        <loc>{{ url('/privacy') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    
    <url>
        <loc>{{ url('/terms') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.5</priority>
    </url>
    
    <!-- Products Listing -->
    <url>
        <loc>{{ url('/products') }}</loc>
        <lastmod>{{ now()->format('Y-m-d') }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
    
    <!-- Categories -->
    @foreach($categories as $category)
        <url>
            <loc>{{ url('/category/' . $category->slug) }}</loc>
            <lastmod>{{ $category->updated_at->format('Y-m-d') }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
            @if($category->image)
                <image:image>
                    <image:loc>{{ asset('storage/' . $category->image) }}</image:loc>
                    <image:title>{{ $category->name }}</image:title>
                    <image:caption>{{ $category->name }} category</image:caption>
                </image:image>
            @endif
        </url>
    @endforeach
    
    <!-- Brands -->
    @foreach($brands as $brand)
        <url>
            <loc>{{ url('/brand/' . $brand->slug) }}</loc>
            <lastmod>{{ $brand->updated_at->format('Y-m-d') }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
            @if($brand->logo)
                <image:image>
                    <image:loc>{{ asset($brand->logo) }}</image:loc>
                    <image:title>{{ $brand->name }}</image:title>
                    <image:caption>{{ $brand->name }} brand logo</image:caption>
                </image:image>
            @endif
        </url>
    @endforeach
    
    <!-- Individual Products -->
    @foreach($products as $product)
        <url>
            <loc>{{ url('/products/' . $product->slug) }}</loc>
            <lastmod>{{ $product->updated_at->format('Y-m-d') }}</lastmod>
            <changefreq>daily</changefreq>
            <priority>0.9</priority>
            @if($product->primaryImage)
                <image:image>
                    <image:loc>{{ $product->primaryImage->image_url }}</image:loc>
                    <image:title>{{ $product->name }}</image:title>
                    <image:caption>{{ Str::limit($product->description ?? $product->name, 160) }}</image:caption>
                </image:image>
            @endif
            <!-- Additional product images -->
            @if($product->images && $product->images->count() > 1)
                @foreach($product->images->skip(1) as $image)
                    <image:image>
                        <image:loc>{{ $image->image_url }}</image:loc>
                        <image:title>{{ $product->name }} - Image {{ $loop->iteration + 1 }}</image:title>
                        <image:caption>{{ $product->name }} product image {{ $loop->iteration + 1 }}</image:caption>
                    </image:image>
                @endforeach
            @endif
        </url>
    @endforeach
    
    <!-- Vendors/Merchants -->
    @foreach($merchants as $merchant)
        <url>
            <loc>{{ url('/vendor/' . $merchant->merchant_slug) }}</loc>
            <lastmod>{{ $merchant->updated_at->format('Y-m-d') }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.7</priority>
        </url>
    @endforeach
    
</urlset>
