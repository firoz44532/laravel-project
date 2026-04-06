<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $category = $request->get('category');
        $brand = $request->get('brand');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $sortBy = $request->get('sort', 'relevance');

        $products = Product::with(['primaryImage', 'category', 'brand'])
            ->where('is_active', true);

        // Search query
        if ($query) {
            $products->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('short_description', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            });
        }

        // Category filter
        if ($category) {
            $products->whereHas('category', function($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        // Brand filter
        if ($brand) {
            $products->whereHas('brand', function($q) use ($brand) {
                $q->where('slug', $brand);
            });
        }

        // Price filter
        if ($minPrice) {
            $products->where('price', '>=', $minPrice);
        }
        if ($maxPrice) {
            $products->where('price', '<=', $maxPrice);
        }

        // Sorting
        switch ($sortBy) {
            case 'price_low':
                $products->orderBy('price', 'asc');
                break;
            case 'price_high':
                $products->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $products->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $products->orderBy('name', 'desc');
                break;
            case 'newest':
                $products->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $products->orderBy('created_at', 'asc');
                break;
            case 'relevance':
            default:
                if ($query) {
                    $products->orderByRaw("CASE 
                        WHEN name LIKE '{$query}%' THEN 1 
                        WHEN name LIKE '%{$query}%' THEN 2 
                        WHEN description LIKE '%{$query}%' THEN 3 
                        ELSE 4 
                    END");
                }
                $products->orderBy('is_featured', 'desc')->orderBy('sort_order');
                break;
        }

        $products = $products->paginate(20);

        // Get filters data
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->get();

        $brands = Brand::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // Get price range
        $priceRange = Product::where('is_active', true)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        return view('frontend.search.index', compact(
            'products',
            'categories',
            'brands',
            'priceRange',
            'query',
            'category',
            'brand',
            'minPrice',
            'maxPrice',
            'sortBy'
        ));
    }

    public function autocomplete(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->with(['primaryImage', 'category'])
            ->limit(10)
            ->get();

        $results = $products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'image' => $product->primaryImage ? asset('storage/' . $product->primaryImage->image_path) : null,
                'category' => $product->category ? $product->category->name : null,
                'url' => route('products.show', $product->slug),
            ];
        });

        return response()->json($results);
    }

    public function suggestions(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Product suggestions
        $products = Product::where('is_active', true)
            ->where('name', 'LIKE', "%{$query}%")
            ->limit(5)
            ->pluck('name');

        // Category suggestions
        $categories = Category::where('is_active', true)
            ->where('name', 'LIKE', "%{$query}%")
            ->limit(3)
            ->pluck('name');

        // Brand suggestions
        $brands = Brand::where('is_active', true)
            ->where('name', 'LIKE', "%{$query}%")
            ->limit(3)
            ->pluck('name');

        $suggestions = [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ];

        return response()->json($suggestions);
    }

    public function advanced(Request $request)
    {
        $query = $request->get('q', '');
        $filters = $request->only([
            'category_id',
            'brand_id',
            'min_price',
            'max_price',
            'rating',
            'in_stock',
            'is_featured',
            'sort_by'
        ]);

        $products = Product::with(['primaryImage', 'category', 'brand'])
            ->where('is_active', true);

        // Search query
        if ($query) {
            $products->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('short_description', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            });
        }

        // Apply filters
        if ($filters['category_id']) {
            $products->where('category_id', $filters['category_id']);
        }

        if ($filters['brand_id']) {
            $products->where('brand_id', $filters['brand_id']);
        }

        if ($filters['min_price']) {
            $products->where('price', '>=', $filters['min_price']);
        }

        if ($filters['max_price']) {
            $products->where('price', '<=', $filters['max_price']);
        }

        if ($filters['rating']) {
            $products->whereHas('reviews', function($q) use ($filters) {
                $q->where('rating', '>=', $filters['rating'])
                  ->where('is_approved', true);
            });
        }

        if ($filters['in_stock']) {
            $products->where(function($q) {
                $q->where('stock_quantity', '>', 0)
                  ->orWhere('track_stock', false);
            });
        }

        if ($filters['is_featured']) {
            $products->where('is_featured', true);
        }

        // Sorting
        switch ($filters['sort_by']) {
            case 'price_low':
                $products->orderBy('price', 'asc');
                break;
            case 'price_high':
                $products->orderBy('price', 'desc');
                break;
            case 'rating':
                $products->withAvg('approvedReviews', 'rating')
                    ->orderBy('approved_reviews_avg_rating', 'desc');
                break;
            case 'newest':
                $products->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $products->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $products->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $products->orderBy('name', 'desc');
                break;
            default:
                if ($query) {
                    $products->orderByRaw("CASE 
                        WHEN name LIKE '{$query}%' THEN 1 
                        WHEN name LIKE '%{$query}%' THEN 2 
                        WHEN description LIKE '%{$query}%' THEN 3 
                        ELSE 4 
                    END");
                }
                $products->orderBy('is_featured', 'desc')->orderBy('sort_order');
                break;
        }

        $products = $products->paginate(20);

        return response()->json([
            'products' => $products,
            'filters' => $filters,
            'query' => $query,
        ]);
    }
}
