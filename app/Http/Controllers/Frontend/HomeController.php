<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Banner;

class HomeController extends Controller
{
    public function index()
    {
        // Get hero banners
        $heroBanners = Banner::where('position', 'hero')
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('starts_at')
                      ->orWhere('starts_at', '<=', now());
            })
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>=', now());
            })
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get featured products
        $featuredProducts = Product::with(['primaryImage', 'category'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        // Get best selling products (for now, just random active products)
        $bestSellingProducts = Product::with(['primaryImage', 'category'])
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(20)
            ->get();

        // Get main categories
        $categories = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->where('is_active', true)->orderBy('created_at', 'desc');
            }])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // Get featured brands
        $brands = Brand::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('welcome', compact(
            'heroBanners',
            'featuredProducts',
            'bestSellingProducts',
            'categories',
            'brands'
        ));
    }
}
