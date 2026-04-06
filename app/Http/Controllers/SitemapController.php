<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Http\Request;

class SitemapController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();
        $merchants = User::where('is_merchant', true)->whereNotNull('merchant_slug')->get();
        
        return response()->view('sitemap', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'merchants' => $merchants
        ])->header('Content-Type', 'text/xml');
    }
    
    public function products()
    {
        $products = Product::where('is_active', true)->orderBy('updated_at', 'desc')->get();
        
        return response()->view('sitemaps.products', [
            'products' => $products
        ])->header('Content-Type', 'text/xml');
    }
    
    public function categories()
    {
        $categories = Category::where('is_active', true)->orderBy('updated_at', 'desc')->get();
        
        return response()->view('sitemaps.categories', [
            'categories' => $categories
        ])->header('Content-Type', 'text/xml');
    }
    
    public function brands()
    {
        $brands = Brand::where('is_active', true)->orderBy('updated_at', 'desc')->get();
        
        return response()->view('sitemaps.brands', [
            'brands' => $brands
        ])->header('Content-Type', 'text/xml');
    }
}
