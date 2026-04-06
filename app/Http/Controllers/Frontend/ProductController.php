<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['primaryImage', 'category', 'brand'])
            ->where('is_active', true);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        // Category filter
        if ($request->has('category')) {
            $categories = $request->get('category');
            if (is_array($categories)) {
                $categoryIds = Category::whereIn('slug', $categories)->pluck('id');
                $query->whereIn('category_id', $categoryIds);
            } else {
                $category = Category::where('slug', $categories)->first();
                if ($category) {
                    $query->where('category_id', $category->id);
                }
            }
        }

        // Brand filter
        if ($request->has('brand')) {
            $brands = $request->get('brand');
            if (is_array($brands)) {
                $brandIds = Brand::whereIn('slug', $brands)->pluck('id');
                $query->whereIn('brand_id', $brandIds);
            } else {
                $brand = Brand::where('slug', $brands)->first();
                if ($brand) {
                    $query->where('brand_id', $brand->id);
                }
            }
        }

        // Price range filter
        if ($request->filled('min_price') && is_numeric($request->get('min_price'))) {
            $query->where('price', '>=', $request->get('min_price'));
        }
        if ($request->filled('max_price') && is_numeric($request->get('max_price'))) {
            $query->where('price', '<=', $request->get('max_price'));
        }

        // Rating filter
        if ($request->filled('rating') && is_numeric($request->get('rating'))) {
            $rating = $request->get('rating');
            $query->where('average_rating', '>=', $rating);
        }

        // Sort functionality
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'reviews':
                $query->orderBy('review_count', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'featured':
                $query->where('is_featured', true)->orderBy('created_at', 'desc');
                break;
            case 'popular':
                $query->orderBy('view_count', 'desc')->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(20);
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();

        return view('frontend.products.index', compact('products', 'categories', 'brands'));
    }

    public function show($slug)
    {
        // Debug: Log the incoming slug
        \Log::info('Product show request for slug: ' . $slug);
        
        $product = Product::with(['primaryImage', 'images', 'category', 'brand', 'approvedReviews'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        \Log::info('Product found: ' . $product->name . ' (ID: ' . $product->id . ')');

        // Get related products from same category
        $relatedProducts = Product::with(['primaryImage', 'category'])
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->get();

        \Log::info('Related products count: ' . $relatedProducts->count());

        return view('frontend.products.show', compact('product', 'relatedProducts'));
    }

    public function category($slug, Request $request)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $query = Product::with(['primaryImage', 'brand'])
            ->where('category_id', $category->id)
            ->where('is_active', true);

        // Sort functionality
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'reviews':
                $query->orderBy('review_count', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'featured':
                $query->where('is_featured', true)->orderBy('created_at', 'desc');
                break;
            case 'popular':
                $query->orderBy('view_count', 'desc')->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(20);

        return view('frontend.products.category', compact('category', 'products'));
    }

    public function brand($slug, Request $request)
    {
        $brand = Brand::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $query = Product::with(['primaryImage', 'category'])
            ->where('brand_id', $brand->id)
            ->where('is_active', true);

        // Sort functionality
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'reviews':
                $query->orderBy('review_count', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'featured':
                $query->where('is_featured', true)->orderBy('created_at', 'desc');
                break;
            case 'popular':
                $query->orderBy('view_count', 'desc')->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(20);

        return view('frontend.products.brand', compact('brand', 'products'));
    }

    public function vendors(Request $request)
    {
        $query = \App\Models\Merchant::with(['user', 'activeProducts'])
            ->where('status', 'approved');

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('store_name', 'LIKE', "%{$search}%")
                  ->orWhere('store_description', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Sort functionality
        $sort = $request->get('sort', 'products_desc');
        switch ($sort) {
            case 'products_desc':
                $query->withCount('activeProducts')->orderBy('active_products_count', 'desc');
                break;
            case 'products_asc':
                $query->withCount('activeProducts')->orderBy('active_products_count', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('store_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('store_name', 'desc');
                break;
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->withCount('activeProducts')->orderBy('active_products_count', 'desc');
        }

        $vendors = $query->paginate(20);

        return view('frontend.products.vendors', compact('vendors'));
    }

    public function vendorProducts($merchant_slug, Request $request)
    {
        $merchant = \App\Models\Merchant::with(['user'])
            ->where('store_slug', $merchant_slug)
            ->where('status', 'approved')
            ->firstOrFail();

        $query = Product::with(['primaryImage', 'category', 'brand'])
            ->where('merchant_id', $merchant->id)
            ->where('is_active', true);

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%");
            });
        }

        // Category filter
        if ($request->has('category')) {
            $categories = $request->get('category');
            if (is_array($categories)) {
                $categoryIds = Category::whereIn('slug', $categories)->pluck('id');
                $query->whereIn('category_id', $categoryIds);
            } else {
                $category = Category::where('slug', $categories)->first();
                if ($category) {
                    $query->where('category_id', $category->id);
                }
            }
        }

        // Brand filter
        if ($request->has('brand')) {
            $brands = $request->get('brand');
            if (is_array($brands)) {
                $brandIds = Brand::whereIn('slug', $brands)->pluck('id');
                $query->whereIn('brand_id', $brandIds);
            } else {
                $brand = Brand::where('slug', $brands)->first();
                if ($brand) {
                    $query->where('brand_id', $brand->id);
                }
            }
        }

        // Price range filter
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->get('min_price'));
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->get('max_price'));
        }

        // Sort functionality
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'reviews':
                $query->orderBy('review_count', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'featured':
                $query->where('is_featured', true)->orderBy('created_at', 'desc');
                break;
            case 'popular':
                $query->orderBy('view_count', 'desc')->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(20);
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $brands = Brand::where('is_active', true)->orderBy('name')->get();

        return view('frontend.products.vendor', compact('merchant', 'products', 'categories', 'brands'));
    }

    public function brands()
    {
        $featuredBrands = Brand::active()->featured()->withCount('activeProducts')->orderBy('name')->get();
        $allBrands = Brand::active()->withCount('activeProducts')->orderBy('name')->paginate(20);
        
        return view('frontend.products.brands', compact('featuredBrands', 'allBrands'));
    }

    public function categories()
    {
        $featuredCategories = Category::active()->featured()->withCount('activeProducts')->orderBy('sort_order')->get();
        $allCategories = Category::active()->withCount('activeProducts')->orderBy('sort_order')->paginate(20);
        
        return view('frontend.products.categories', compact('featuredCategories', 'allCategories'));
    }
}
